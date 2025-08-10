<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminApiController extends BaseController
{
    /**
     * Middleware to check admin access
     */
    private function checkAdminAccess()
    {
        $request = service('request');
        $user = $request->api_user ?? null;
        
        if (!$user || !in_array($user['role'], ['admin', 'reviewer'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Admin access required'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }
        
        return $user;
    }

    /**
     * Get all users (admin only)
     * GET /api/v1/admin/users
     */
    public function users()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $limit = min(50, max(1, (int)($this->request->getGet('limit') ?? 20)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            $search = $this->request->getGet('search');
            $role = $this->request->getGet('role');

            $db = \Config\Database::connect();
            $builder = $db->table('users u');
            $builder->select('u.id, u.first_name, u.last_name, u.email, u.role, u.phone, u.institution, u.is_verified, u.created_at');

            if ($search) {
                $builder->groupStart()
                    ->like('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->orLike('u.institution', $search)
                    ->groupEnd();
            }

            if ($role) {
                $builder->where('u.role', $role);
            }

            $totalCount = $builder->countAllResults(false);
            $users = $builder->orderBy('u.created_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            // Get registration counts for each user
            foreach ($users as &$userData) {
                $userData['registrations_count'] = $db->table('registrations')
                    ->where('user_id', $userData['id'])
                    ->countAllResults();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get users: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all abstracts (admin only)
     * GET /api/v1/admin/abstracts
     */
    public function abstracts()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $limit = min(50, max(1, (int)($this->request->getGet('limit') ?? 20)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            $status = $this->request->getGet('status');
            $search = $this->request->getGet('search');

            $db = \Config\Database::connect();
            $builder = $db->table('abstracts a');
            $builder->select('a.*, u.first_name, u.last_name, u.email, r.registration_type, ac.name as category_name, rev.status as review_status');
            $builder->join('users u', 'u.id = a.user_id');
            $builder->join('registrations r', 'r.id = a.registration_id', 'LEFT');
            $builder->join('abstract_categories ac', 'ac.id = a.category_id', 'LEFT');
            $builder->join('reviews rev', 'rev.abstract_id = a.id', 'LEFT');

            if ($status) {
                $builder->where('a.status', $status);
            }

            if ($search) {
                $builder->groupStart()
                    ->like('a.title', $search)
                    ->orLike('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->groupEnd();
            }

            $totalCount = $builder->countAllResults(false);
            $abstracts = $builder->orderBy('a.submitted_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get abstracts: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign reviewer to abstract
     * PUT /api/v1/admin/abstracts/{id}/assign
     */
    public function assignReviewer($abstractId)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $reviewerId = $this->request->getPost('reviewer_id');
            
            if (!$reviewerId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Reviewer ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Check if abstract exists
            $abstract = $db->table('abstracts')->where('id', $abstractId)->get()->getRowArray();
            if (!$abstract) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if reviewer exists
            $reviewer = $db->table('users')->where('id', $reviewerId)->where('role', 'reviewer')->get()->getRowArray();
            if (!$reviewer) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Reviewer not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if already assigned
            $existingReview = $db->table('reviews')->where('abstract_id', $abstractId)->get()->getRowArray();
            
            if ($existingReview) {
                // Update existing assignment
                $db->table('reviews')
                    ->where('abstract_id', $abstractId)
                    ->update([
                        'reviewer_id' => $reviewerId,
                        'assigned_by' => $user['id'],
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'status' => 'assigned',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Create new review assignment
                $db->table('reviews')->insert([
                    'abstract_id' => $abstractId,
                    'reviewer_id' => $reviewerId,
                    'assigned_by' => $user['id'],
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'status' => 'assigned',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update abstract status
            $db->table('abstracts')
                ->where('id', $abstractId)
                ->update([
                    'status' => 'under_review',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Send notification email to reviewer
            $emailService = new \App\Services\EmailService();
            $emailService->sendReviewerAssignment(
                $reviewer['email'],
                $reviewer['first_name'] . ' ' . $reviewer['last_name'],
                $abstract['title'],
                $abstractId
            );

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Reviewer assigned successfully',
                'data' => [
                    'abstract_id' => $abstractId,
                    'reviewer_id' => $reviewerId,
                    'reviewer_name' => $reviewer['first_name'] . ' ' . $reviewer['last_name'],
                    'assigned_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to assign reviewer: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get dashboard statistics
     * GET /api/v1/admin/dashboard
     */
    public function dashboard()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'users' => [
                    'total' => $db->table('users')->countAllResults(),
                    'verified' => $db->table('users')->where('is_verified', true)->countAllResults(),
                    'presenters' => $db->table('users')->where('role', 'presenter')->countAllResults(),
                    'attendees' => $db->table('users')->where('role', 'audience')->countAllResults()
                ],
                'registrations' => [
                    'total' => $db->table('registrations')->countAllResults(),
                    'pending' => $db->table('registrations')->where('registration_status', 'pending')->countAllResults(),
                    'confirmed' => $db->table('registrations')->where('registration_status', 'confirmed')->countAllResults(),
                    'attended' => $db->table('registrations')->where('attended', true)->countAllResults()
                ],
                'abstracts' => [
                    'total' => $db->table('abstracts')->countAllResults(),
                    'pending' => $db->table('abstracts')->where('status', 'pending')->countAllResults(),
                    'under_review' => $db->table('abstracts')->where('status', 'under_review')->countAllResults(),
                    'accepted' => $db->table('abstracts')->where('status', 'accepted')->countAllResults(),
                    'rejected' => $db->table('abstracts')->where('status', 'rejected')->countAllResults()
                ],
                'payments' => [
                    'total' => $db->table('payments')->countAllResults(),
                    'pending' => $db->table('payments')->where('status', 'pending')->countAllResults(),
                    'completed' => $db->table('payments')->where('status', 'success')->countAllResults(),
                    'failed' => $db->table('payments')->where('status', 'failed')->countAllResults(),
                    'total_amount' => $db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'success'")->getRow()->total ?? 0
                ],
                'certificates' => [
                    'requested' => $db->table('certificates')->where('status', 'requested')->countAllResults(),
                    'issued' => $db->table('certificates')->where('status', 'issued')->countAllResults()
                ]
            ];

            // Recent activities
            $recentUsers = $db->table('users')
                ->select('first_name, last_name, email, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $recentAbstracts = $db->table('abstracts a')
                ->select('a.title, a.submitted_at, u.first_name, u.last_name')
                ->join('users u', 'u.id = a.user_id')
                ->orderBy('a.submitted_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'statistics' => $stats,
                    'recent_activities' => [
                        'new_users' => $recentUsers,
                        'new_abstracts' => $recentAbstracts
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get dashboard data: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export data
     * GET /api/v1/admin/export/{type}
     */
    public function export($type)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $format = $this->request->getGet('format') ?? 'csv';
            $db = \Config\Database::connect();
            
            switch ($type) {
                case 'users':
                    $data = $db->table('users')
                        ->select('id, first_name, last_name, email, role, phone, institution, is_verified, created_at')
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'registrations':
                    $data = $db->table('registrations r')
                        ->select('r.id, u.first_name, u.last_name, u.email, e.title as event_title, r.registration_type, r.registration_status, r.payment_status, r.attended, r.created_at')
                        ->join('users u', 'u.id = r.user_id')
                        ->join('events e', 'e.id = r.event_id')
                        ->orderBy('r.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'abstracts':
                    $data = $db->table('abstracts a')
                        ->select('a.id, a.title, u.first_name, u.last_name, u.email, a.status, ac.name as category, a.submitted_at')
                        ->join('users u', 'u.id = a.user_id')
                        ->join('abstract_categories ac', 'ac.id = a.category_id', 'LEFT')
                        ->orderBy('a.submitted_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'payments':
                    $data = $db->table('payments p')
                        ->select('p.id, u.first_name, u.last_name, u.email, p.amount, p.status, p.payment_method, p.transaction_id, p.created_at')
                        ->join('registrations r', 'r.id = p.registration_id')
                        ->join('users u', 'u.id = r.user_id')
                        ->orderBy('p.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid export type'
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            if ($format === 'csv') {
                // Generate CSV
                $filename = "snia_{$type}_export_" . date('Y-m-d_H-i-s') . '.csv';
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                
                if (!empty($data)) {
                    // Write headers
                    fputcsv($output, array_keys($data[0]));
                    
                    // Write data
                    foreach ($data as $row) {
                        fputcsv($output, $row);
                    }
                }
                
                fclose($output);
                exit;
            } else {
                // Return JSON
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'count' => count($data),
                    'export_type' => $type,
                    'exported_at' => date('Y-m-d H:i:s')
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get system settings
     * GET /api/v1/admin/settings
     */
    public function getSettings()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $db = \Config\Database::connect();
            $settings = $db->table('system_settings')
                ->get()
                ->getResultArray();

            $formattedSettings = [];
            foreach ($settings as $setting) {
                $formattedSettings[$setting['setting_key']] = [
                    'value' => $setting['setting_value'],
                    'description' => $setting['description'],
                    'updated_at' => $setting['updated_at']
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedSettings
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get settings: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update system settings
     * PUT /api/v1/admin/settings
     */
    public function updateSettings()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $settings = $this->request->getPost('settings');
            
            if (!$settings || !is_array($settings)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Settings data is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            $updated = [];
            
            foreach ($settings as $key => $value) {
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
                'message' => 'Settings updated successfully',
                'data' => [
                    'updated_settings' => $updated,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}