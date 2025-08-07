<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemSettingModel;
use App\Models\UserModel;
use App\Models\EventModel;
use App\Models\AbstractCategoryModel;
use App\Models\VoucherModel;
use CodeIgniter\HTTP\ResponseInterface;

class SystemController extends BaseController
{
    protected $systemModel;
    protected $userModel;
    protected $eventModel;
    protected $categoryModel;
    protected $voucherModel;
    protected $session;

    public function __construct()
    {
        $this->systemModel = new SystemSettingModel();
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
        $this->categoryModel = new AbstractCategoryModel();
        $this->voucherModel = new VoucherModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Delete abstract category
     */
    public function deleteCategory($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category not found']);
        }

        // Check if category is used by any abstracts
        $abstractsUsingCategory = $this->abstractModel->where('category_id', $id)->countAllResults();

        if ($abstractsUsingCategory > 0) {
            // Soft delete - deactivate category
            $this->categoryModel->update($id, ['is_active' => false]);
            $message = 'Category deactivated (used by existing abstracts)';
            $action = 'deactivated';
        } else {
            // Hard delete
            $this->categoryModel->delete($id);
            $message = 'Category deleted successfully';
            $action = 'deleted';
        }

        // Log admin action
        $this->logAdminAction('delete_category', $id, "Category {$action}: {$category['name']}");

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    /**
     * Voucher management
     */
    public function vouchers()
    {
        $vouchers = $this->voucherModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Voucher Management - Admin',
            'vouchers' => $vouchers,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/system/vouchers', $data);
    }

    /**
     * Create voucher
     */
    public function createVoucher()
    {
        $rules = [
            'code' => 'required|min_length[3]|max_length[20]|is_unique[vouchers.code]',
            'type' => 'required|in_list[percentage,fixed]',
            'value' => 'required|numeric|greater_than[0]',
            'description' => 'permit_empty|max_length[255]',
            'max_uses' => 'permit_empty|integer|greater_than[0]',
            'valid_from' => 'permit_empty|valid_date',
            'valid_until' => 'permit_empty|valid_date',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $voucherData = [
                'code' => strtoupper($this->request->getPost('code')),
                'type' => $this->request->getPost('type'),
                'value' => $this->request->getPost('value'),
                'description' => $this->request->getPost('description'),
                'max_uses' => $this->request->getPost('max_uses') ?: null,
                'valid_from' => $this->request->getPost('valid_from') ?: null,
                'valid_until' => $this->request->getPost('valid_until') ?: null,
                'is_active' => $this->request->getPost('is_active') ? true : false,
                'used_count' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $voucherId = $this->voucherModel->insert($voucherData);

            if (!$voucherId) {
                throw new \Exception('Failed to create voucher');
            }

            // Log admin action
            $this->logAdminAction('create_voucher', $voucherId, "Created voucher: {$voucherData['code']}");

            return redirect()->back()->with('success', 'Voucher created successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Voucher creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create voucher. Please try again.');
        }
    }

    /**
     * Update voucher
     */
    public function updateVoucher($id)
    {
        $voucher = $this->voucherModel->find($id);

        if (!$voucher) {
            return redirect()->back()->with('error', 'Voucher not found');
        }

        $rules = [
            'code' => "required|min_length[3]|max_length[20]|is_unique[vouchers.code,id,{$id}]",
            'type' => 'required|in_list[percentage,fixed]',
            'value' => 'required|numeric|greater_than[0]',
            'description' => 'permit_empty|max_length[255]',
            'max_uses' => 'permit_empty|integer|greater_than[0]',
            'valid_from' => 'permit_empty|valid_date',
            'valid_until' => 'permit_empty|valid_date',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $updateData = [
                'code' => strtoupper($this->request->getPost('code')),
                'type' => $this->request->getPost('type'),
                'value' => $this->request->getPost('value'),
                'description' => $this->request->getPost('description'),
                'max_uses' => $this->request->getPost('max_uses') ?: null,
                'valid_from' => $this->request->getPost('valid_from') ?: null,
                'valid_until' => $this->request->getPost('valid_until') ?: null,
                'is_active' => $this->request->getPost('is_active') ? true : false,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->voucherModel->update($id, $updateData);

            // Log admin action
            $this->logAdminAction('update_voucher', $id, "Updated voucher: {$updateData['code']}");

            return redirect()->back()->with('success', 'Voucher updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Voucher update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update voucher. Please try again.');
        }
    }

    /**
     * Delete voucher
     */
    public function deleteVoucher($id)
    {
        $voucher = $this->voucherModel->find($id);

        if (!$voucher) {
            return $this->response->setJSON(['success' => false, 'message' => 'Voucher not found']);
        }

        try {
            $this->voucherModel->delete($id);

            // Log admin action
            $this->logAdminAction('delete_voucher', $id, "Deleted voucher: {$voucher['code']}");

            return $this->response->setJSON(['success' => true, 'message' => 'Voucher deleted successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Voucher deletion error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete voucher']);
        }
    }

    /**
     * System maintenance
     */
    public function maintenance()
    {
        $data = [
            'title' => 'System Maintenance - Admin',
            'database_info' => $this->getDatabaseInfo(),
            'system_info' => $this->getSystemInfo(),
            'log_files' => $this->getLogFiles(),
        ];

        return view('admin/system/maintenance', $data);
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            // Clear CodeIgniter cache
            $cache = \Config\Services::cache();
            $cache->clean();

            // Clear views cache
            $this->clearViewsCache();

            // Clear session files (optional)
            // $this->clearSessionFiles();

            // Log admin action
            $this->logAdminAction('clear_cache', null, 'Cleared system cache');

            return $this->response->setJSON(['success' => true, 'message' => 'Cache cleared successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Cache clearing error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to clear cache']);
        }
    }

    /**
     * Clear logs
     */
    public function clearLogs()
    {
        try {
            $logPath = WRITEPATH . 'logs/';
            $files = glob($logPath . 'log-*.php');

            $cleared = 0;
            foreach ($files as $file) {
                if (unlink($file)) {
                    $cleared++;
                }
            }

            // Log admin action
            $this->logAdminAction('clear_logs', null, "Cleared {$cleared} log files");

            return $this->response->setJSON(['success' => true, 'message' => "Cleared {$cleared} log files"]);

        } catch (\Exception $e) {
            log_message('error', 'Log clearing error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to clear logs']);
        }
    }

    /**
     * Database backup
     */
    public function databaseBackup()
    {
        try {
            $dbutil = \Config\Database::utils();
            $backup = $dbutil->backup();

            $filename = 'snia_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = WRITEPATH . 'backups/';

            // Create backup directory if it doesn't exist
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            if (write_file($backupPath . $filename, $backup)) {
                // Log admin action
                $this->logAdminAction('database_backup', null, "Created database backup: {$filename}");

                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Database backup created successfully',
                    'filename' => $filename
                ]);
            } else {
                throw new \Exception('Failed to write backup file');
            }

        } catch (\Exception $e) {
            log_message('error', 'Database backup error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Database backup failed']);
        }
    }

    /**
     * System information
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => date_default_timezone_get(),
            'current_time' => date('Y-m-d H:i:s'),
        ];

        return $this->response->setJSON($info);
    }

    /**
     * Generate system report
     */
    public function generateReport()
    {
        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'users' => [
                'total' => $this->userModel->countAll(),
                'active' => $this->userModel->where('is_active', true)->countAllResults(),
                'by_role' => $this->getUsersByRole(),
            ],
            'events' => [
                'total' => $this->eventModel->countAll(),
                'active' => $this->eventModel->where('is_active', true)->countAllResults(),
                'upcoming' => $this->eventModel->where('start_date >', date('Y-m-d'))->countAllResults(),
            ],
            'system' => [
                'php_version' => PHP_VERSION,
                'database_version' => $this->getDatabaseVersion(),
                'disk_usage' => $this->getDiskUsage(),
                'memory_usage' => memory_get_usage(true),
            ],
        ];

        // Log admin action
        $this->logAdminAction('generate_report', null, 'Generated system report');

        return $this->response->setJSON($report);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        $db = \Config\Database::connect();
        
        return [
            'version' => $this->getDatabaseVersion(),
            'size' => $this->getDatabaseSize(),
            'tables' => $this->getTableCount(),
            'connections' => $this->getActiveConnections(),
        ];
    }

    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free' => disk_free_space('/'),
            'disk_total' => disk_total_space('/'),
            'load_average' => sys_getloadavg(),
        ];
    }

    /**
     * Get log files
     */
    private function getLogFiles()
    {
        $logPath = WRITEPATH . 'logs/';
        $files = glob($logPath . 'log-*.php');
        $logFiles = [];

        foreach ($files as $file) {
            $logFiles[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'modified' => filemtime($file),
            ];
        }

        return $logFiles;
    }

    /**
     * Clear views cache
     */
    private function clearViewsCache()
    {
        $cachePath = WRITEPATH . 'cache/';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Get database version
     */
    private function getDatabaseVersion()
    {
        $db = \Config\Database::connect();
        $query = $db->query('SELECT version() as version');
        $result = $query->getRow();
        return $result ? $result->version : 'Unknown';
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
        $result = $query->getRow();
        return $result ? $result->size : 'Unknown';
    }

    /**
     * Get table count
     */
    private function getTableCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public'");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    /**
     * Get active database connections
     */
    private function getActiveConnections()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as count FROM pg_stat_activity WHERE state = 'active'");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    /**
     * Get users by role
     */
    private function getUsersByRole()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        $results = $query->getResultArray();
        
        $roleStats = [];
        foreach ($results as $result) {
            $roleStats[$result['role']] = $result['count'];
        }
        
        return $roleStats;
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'percentage' => round(($used / $total) * 100, 2)
        ];
    }

    /**
     * Log admin actions
     */
    private function logAdminAction($action, $targetId, $description)
    {
        $adminId = $this->session->get('user_id');
        $adminName = $this->session->get('user_name');
        
        log_message('info', "Admin Action - Admin: {$adminName} (ID: {$adminId}), Action: {$action}, Target: {$targetId}, Description: {$description}");
    }

    // System settings main page
    public function index()
    {
        // Get all system settings
        $settings = $this->systemModel->findAll();
        $settingsArray = [];
        
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }

        $data = [
            'title' => 'System Settings - Admin',
            'settings' => $settingsArray,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/system/index', $data);
    }

     /**
     * Update system settings
     */
    public function updateSettings()
    {
        $rules = [
            'site_name' => 'required|min_length[3]|max_length[100]',
            'site_description' => 'permit_empty|max_length[500]',
            'contact_email' => 'required|valid_email',
            'contact_phone' => 'permit_empty|max_length[20]',
            'timezone' => 'required',
            'date_format' => 'required',
            'time_format' => 'required',
            'currency' => 'required|max_length[3]',
            'registration_enabled' => 'permit_empty|in_list[0,1]',
            'abstract_submission_enabled' => 'permit_empty|in_list[0,1]',
            'email_notifications_enabled' => 'permit_empty|in_list[0,1]',
            'maintenance_mode' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $settings = [
                'site_name' => $this->request->getPost('site_name'),
                'site_description' => $this->request->getPost('site_description'),
                'contact_email' => $this->request->getPost('contact_email'),
                'contact_phone' => $this->request->getPost('contact_phone'),
                'timezone' => $this->request->getPost('timezone'),
                'date_format' => $this->request->getPost('date_format'),
                'time_format' => $this->request->getPost('time_format'),
                'currency' => $this->request->getPost('currency'),
                'registration_enabled' => $this->request->getPost('registration_enabled') ? 'true' : 'false',
                'abstract_submission_enabled' => $this->request->getPost('abstract_submission_enabled') ? 'true' : 'false',
                'email_notifications_enabled' => $this->request->getPost('email_notifications_enabled') ? 'true' : 'false',
                'maintenance_mode' => $this->request->getPost('maintenance_mode') ? 'true' : 'false',
                'max_file_size' => $this->request->getPost('max_file_size') ?: '10',
                'allowed_file_types' => $this->request->getPost('allowed_file_types') ?: 'pdf,doc,docx',
                'items_per_page' => $this->request->getPost('items_per_page') ?: '20',
            ];

            foreach ($settings as $key => $value) {
                $this->systemModel->setSetting($key, $value);
            }

            // Log admin action
            $this->logAdminAction('update_system_settings', null, 'Updated system settings');

            return redirect()->back()->with('success', 'System settings updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'System settings update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update settings. Please try again.');
        }
    }

     /**
     * Email settings page
     */
    public function emailSettings()
    {
        $settings = $this->systemModel->findAll();
        $settingsArray = [];
        
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }

        $data = [
            'title' => 'Email Settings - Admin',
            'settings' => $settingsArray,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/system/email', $data);
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings()
    {
        $rules = [
            'smtp_host' => 'required|max_length[100]',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|valid_email',
            'smtp_password' => 'permit_empty',
            'smtp_crypto' => 'required|in_list[tls,ssl]',
            'mail_from_name' => 'required|max_length[100]',
            'mail_from_email' => 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $emailSettings = [
                'smtp_host' => $this->request->getPost('smtp_host'),
                'smtp_port' => $this->request->getPost('smtp_port'),
                'smtp_username' => $this->request->getPost('smtp_username'),
                'smtp_crypto' => $this->request->getPost('smtp_crypto'),
                'mail_from_name' => $this->request->getPost('mail_from_name'),
                'mail_from_email' => $this->request->getPost('mail_from_email'),
                'smtp_configured' => 'true',
            ];

            // Only update password if provided
            $password = $this->request->getPost('smtp_password');
            if (!empty($password)) {
                $emailSettings['smtp_password'] = $password;
            }

            foreach ($emailSettings as $key => $value) {
                $this->systemModel->setSetting($key, $value);
            }

            // Log admin action
            $this->logAdminAction('update_email_settings', null, 'Updated email settings');

            return redirect()->back()->with('success', 'Email settings updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Email settings update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update email settings. Please try again.');
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail()
    {
        $testEmail = $this->request->getPost('test_email');

        if (!$testEmail || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Valid email address is required']);
        }

        try {
            $emailService = \Config\Services::email();
            
            $message = "
                <h2>Email Configuration Test</h2>
                <p>This is a test email to verify your SMTP configuration.</p>
                <p><strong>Sent at:</strong> " . date('Y-m-d H:i:s') . "</p>
                <p><strong>From:</strong> SNIA Conference System</p>
                <p>If you received this email, your email configuration is working correctly!</p>
            ";

            $emailService->setTo($testEmail);
            $emailService->setSubject('Email Configuration Test - SNIA Conference');
            $emailService->setMessage($message);

            if ($emailService->send()) {
                // Log admin action
                $this->logAdminAction('test_email', null, "Sent test email to: {$testEmail}");

                return $this->response->setJSON(['success' => true, 'message' => 'Test email sent successfully!']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to send test email']);
            }

        } catch (\Exception $e) {
            log_message('error', 'Email test error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Email test failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Abstract categories management
     */
    public function categories()
    {
        $categories = $this->categoryModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Abstract Categories - Admin',
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/system/categories', $data);
    }

    /**
     * Create abstract category
     */
    public function createCategory()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $categoryData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? true : false,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $categoryId = $this->categoryModel->insert($categoryData);

            if (!$categoryId) {
                throw new \Exception('Failed to create category');
            }

            // Log admin action
            $this->logAdminAction('create_category', $categoryId, "Created category: {$categoryData['name']}");

            return redirect()->back()->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Category creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Update abstract category
     */
    public function updateCategory($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $updateData = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? true : false,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->categoryModel->update($id, $updateData);

            // Log admin action
            $this->logAdminAction('update_category', $id, "Updated category: {$updateData['name']}");

            return redirect()->back()->with('success', 'Category updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Category update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }
    }
}
    

   

   

    

    

    

    