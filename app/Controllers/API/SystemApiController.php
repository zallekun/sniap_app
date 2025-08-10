<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SystemApiController extends BaseController
{
    /**
     * Middleware to check admin access
     */
    private function checkAdminAccess()
    {
        $request = service('request');
        $user = $request->api_user ?? null;
        
        if (!$user || $user['role'] !== 'admin') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Admin access required'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }
        
        return $user;
    }

    /**
     * Get system configuration
     * GET /api/v1/system/config
     */
    public function getConfig()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $db = \Config\Database::connect();
            $settings = $db->table('system_settings')
                ->get()
                ->getResultArray();

            $config = [];
            foreach ($settings as $setting) {
                $config[$setting['setting_key']] = $setting['setting_value'];
            }

            // Add default values if not set
            $defaults = [
                'registration_open' => 'true',
                'abstract_submission_open' => 'true',
                'abstract_deadline' => date('Y-m-d', strtotime('+30 days')),
                'payment_deadline' => date('Y-m-d', strtotime('+45 days')),
                'event_mode' => 'offline',
                'event_location' => 'Universitas Jenderal Achmad Yani',
                'zoom_link' => '',
                'max_abstract_size' => '5',
                'allowed_file_types' => 'pdf,doc,docx',
                'presenter_fee' => '500000',
                'attendee_fee' => '100000',
                'certificate_auto_generate' => 'false',
                'email_notifications' => 'true'
            ];

            foreach ($defaults as $key => $defaultValue) {
                if (!isset($config[$key])) {
                    $config[$key] = $defaultValue;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $config
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get system configuration: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update system configuration
     * PUT /api/v1/system/config
     */
    public function updateConfig()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $config = $this->request->getJSON(true);
            
            if (!$config) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Configuration data is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            $updated = [];
            
            foreach ($config as $key => $value) {
                // Validate specific settings
                if ($key === 'registration_open' && !in_array($value, ['true', 'false'])) {
                    continue;
                }
                if ($key === 'event_mode' && !in_array($value, ['online', 'offline', 'hybrid'])) {
                    continue;
                }

                // Update or insert setting
                $existing = $db->table('system_settings')
                    ->where('setting_key', $key)
                    ->get()
                    ->getRowArray();
                
                if ($existing) {
                    $db->table('system_settings')
                        ->where('setting_key', $key)
                        ->update([
                            'setting_value' => $value,
                            'updated_by' => $user['id'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    $db->table('system_settings')
                        ->insert([
                            'setting_key' => $key,
                            'setting_value' => $value,
                            'description' => $this->getSettingDescription($key),
                            'created_by' => $user['id'],
                            'updated_by' => $user['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                }
                
                $updated[] = $key;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'System configuration updated successfully',
                'data' => [
                    'updated_settings' => $updated,
                    'updated_count' => count($updated),
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update system configuration: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle registration status
     * PUT /api/v1/system/registration/{status}
     */
    public function toggleRegistration($status)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            if (!in_array($status, ['on', 'off', 'open', 'close'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid status. Use: on, off, open, or close'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $isOpen = in_array($status, ['on', 'open']);
            $db = \Config\Database::connect();
            
            // Update registration status
            $this->updateSetting($db, 'registration_open', $isOpen ? 'true' : 'false', $user['id']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration ' . ($isOpen ? 'opened' : 'closed') . ' successfully',
                'data' => [
                    'registration_open' => $isOpen,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to toggle registration: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle abstract submission
     * PUT /api/v1/system/abstract/{status}
     */
    public function toggleAbstract($status)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            if (!in_array($status, ['on', 'off', 'open', 'close'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid status. Use: on, off, open, or close'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $isOpen = in_array($status, ['on', 'open']);
            $db = \Config\Database::connect();
            
            // Update abstract submission status
            $this->updateSetting($db, 'abstract_submission_open', $isOpen ? 'true' : 'false', $user['id']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Abstract submission ' . ($isOpen ? 'opened' : 'closed') . ' successfully',
                'data' => [
                    'abstract_submission_open' => $isOpen,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to toggle abstract submission: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Set event mode
     * PUT /api/v1/system/event-mode/{mode}
     */
    public function setEventMode($mode)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            if (!in_array($mode, ['online', 'offline', 'hybrid'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid mode. Use: online, offline, or hybrid'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            $this->updateSetting($db, 'event_mode', $mode, $user['id']);

            $location = $this->request->getPost('location');
            $zoomLink = $this->request->getPost('zoom_link');

            if ($mode === 'offline' && $location) {
                $this->updateSetting($db, 'event_location', $location, $user['id']);
            }

            if (in_array($mode, ['online', 'hybrid']) && $zoomLink) {
                $this->updateSetting($db, 'zoom_link', $zoomLink, $user['id']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Event mode set to ' . $mode . ' successfully',
                'data' => [
                    'event_mode' => $mode,
                    'location' => $location,
                    'zoom_link' => $zoomLink,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to set event mode: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Set deadlines
     * PUT /api/v1/system/deadlines
     */
    public function setDeadlines()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $abstractDeadline = $this->request->getPost('abstract_deadline');
            $paymentDeadline = $this->request->getPost('payment_deadline');

            if (!$abstractDeadline && !$paymentDeadline) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'At least one deadline is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            $updated = [];

            if ($abstractDeadline) {
                $this->updateSetting($db, 'abstract_deadline', $abstractDeadline, $user['id']);
                $updated['abstract_deadline'] = $abstractDeadline;
            }

            if ($paymentDeadline) {
                $this->updateSetting($db, 'payment_deadline', $paymentDeadline, $user['id']);
                $updated['payment_deadline'] = $paymentDeadline;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Deadlines updated successfully',
                'data' => array_merge($updated, [
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ])
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to set deadlines: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Configure email settings
     * PUT /api/v1/system/email-config
     */
    public function configureEmail()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $smtpHost = $this->request->getPost('smtp_host');
            $smtpUser = $this->request->getPost('smtp_user');
            $smtpPass = $this->request->getPost('smtp_password');
            $smtpPort = $this->request->getPost('smtp_port') ?: '587';
            $smtpCrypto = $this->request->getPost('smtp_crypto') ?: 'tls';
            
            if (!$smtpHost || !$smtpUser || !$smtpPass) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'SMTP host, user, and password are required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Update email settings
            $emailSettings = [
                'smtp_host' => $smtpHost,
                'smtp_user' => $smtpUser,
                'smtp_password' => $smtpPass, // Note: Should be encrypted in production
                'smtp_port' => $smtpPort,
                'smtp_crypto' => $smtpCrypto
            ];

            foreach ($emailSettings as $key => $value) {
                $this->updateSetting($db, $key, $value, $user['id']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Email configuration updated successfully',
                'data' => [
                    'smtp_host' => $smtpHost,
                    'smtp_user' => $smtpUser,
                    'smtp_port' => $smtpPort,
                    'smtp_crypto' => $smtpCrypto,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to configure email: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get system status/health
     * GET /api/v1/system/status
     */
    public function status()
    {
        try {
            $db = \Config\Database::connect();
            
            // Database connectivity
            $dbStatus = 'connected';
            try {
                $db->query("SELECT 1")->getResult();
            } catch (\Exception $e) {
                $dbStatus = 'error: ' . $e->getMessage();
            }

            // File upload capability
            $uploadsDir = WRITEPATH . '../uploads/';
            $uploadsWritable = is_writable($uploadsDir);

            // Get current settings
            $settings = $db->table('system_settings')
                ->whereIn('setting_key', ['registration_open', 'abstract_submission_open', 'event_mode'])
                ->get()
                ->getResultArray();

            $currentSettings = [];
            foreach ($settings as $setting) {
                $currentSettings[$setting['setting_key']] = $setting['setting_value'];
            }

            // System statistics
            $stats = [
                'total_users' => $db->table('users')->countAllResults(),
                'total_registrations' => $db->table('registrations')->countAllResults(),
                'total_abstracts' => $db->table('abstracts')->countAllResults(),
                'pending_reviews' => $db->table('reviews')->where('status', 'assigned')->countAllResults(),
                'completed_payments' => $db->table('payments')->where('status', 'success')->countAllResults()
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'system_health' => [
                        'database' => $dbStatus,
                        'uploads_directory' => $uploadsWritable ? 'writable' : 'not writable',
                        'php_version' => phpversion(),
                        'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
                        'server_time' => date('Y-m-d H:i:s'),
                        'timezone' => date_default_timezone_get()
                    ],
                    'current_settings' => array_merge([
                        'registration_open' => 'true',
                        'abstract_submission_open' => 'true',
                        'event_mode' => 'offline'
                    ], $currentSettings),
                    'statistics' => $stats
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get system status: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Helper method to update a setting
     */
    private function updateSetting($db, $key, $value, $userId)
    {
        $existing = $db->table('system_settings')
            ->where('setting_key', $key)
            ->get()
            ->getRowArray();
        
        if ($existing) {
            $db->table('system_settings')
                ->where('setting_key', $key)
                ->update([
                    'setting_value' => $value,
                    'updated_by' => $userId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            $db->table('system_settings')
                ->insert([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'description' => $this->getSettingDescription($key),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        }
    }

    /**
     * Helper method to get setting descriptions
     */
    private function getSettingDescription($key)
    {
        $descriptions = [
            'registration_open' => 'Controls whether user registration is open',
            'abstract_submission_open' => 'Controls whether abstract submission is open',
            'abstract_deadline' => 'Deadline for abstract submission',
            'payment_deadline' => 'Deadline for payment',
            'event_mode' => 'Event format: online, offline, or hybrid',
            'event_location' => 'Physical event location',
            'zoom_link' => 'Zoom meeting link for online events',
            'presenter_fee' => 'Registration fee for presenters',
            'attendee_fee' => 'Registration fee for attendees',
            'smtp_host' => 'SMTP server hostname',
            'smtp_user' => 'SMTP username',
            'smtp_password' => 'SMTP password',
            'smtp_port' => 'SMTP port number',
            'smtp_crypto' => 'SMTP encryption method'
        ];

        return $descriptions[$key] ?? 'System setting';
    }
}