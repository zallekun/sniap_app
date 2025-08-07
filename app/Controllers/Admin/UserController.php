<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\ReviewModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;
    protected $registrationModel;
    protected $abstractModel;
    protected $reviewModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->registrationModel = new RegistrationModel();
        $this->abstractModel = new AbstractModel();
        $this->reviewModel = new ReviewModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display users list with pagination and filters
     */
    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $status = $this->request->getGet('status');
        $sort = $this->request->getGet('sort') ?? 'created_at';
        $order = $this->request->getGet('order') ?? 'DESC';

        $builder = $this->userModel;

        // Apply filters
        if ($search) {
            $builder = $builder->groupStart()
                ->like('full_name', $search)
                ->orLike('email', $search)
                ->orLike('institution', $search)
                ->groupEnd();
        }

        if ($role && $role !== 'all') {
            $builder = $builder->where('role', $role);
        }

        if ($status && $status !== 'all') {
            $isActive = $status === 'active';
            $builder = $builder->where('is_active', $isActive);
        }

        // Get users with pagination
        $users = $builder->orderBy($sort, $order)->paginate($perPage);
        $pager = $this->userModel->pager;

        // Get statistics
        $stats = [
            'total' => $this->userModel->countAll(),
            'active' => $this->userModel->where('is_active', true)->countAllResults(),
            'inactive' => $this->userModel->where('is_active', false)->countAllResults(),
            'admins' => $this->userModel->where('role', 'admin')->countAllResults(),
            'presenters' => $this->userModel->where('role', 'presenter')->countAllResults(),
            'audience' => $this->userModel->where('role', 'audience')->countAllResults(),
            'reviewers' => $this->userModel->where('role', 'reviewer')->countAllResults(),
        ];

        $data = [
            'title' => 'User Management - Admin',
            'users' => $users,
            'pager' => $pager,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'sort' => $sort,
                'order' => $order,
                'per_page' => $perPage
            ]
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Display user details
     */
    public function show($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        // Get user's additional data
        $userStats = [
            'registrations' => $this->registrationModel->where('user_id', $id)->countAllResults(),
            'abstracts' => $this->abstractModel->where('user_id', $id)->countAllResults(),
            'reviews' => $this->reviewModel->where('reviewer_id', $id)->countAllResults(),
        ];

        $recentActivities = $this->getUserActivities($id);

        $data = [
            'title' => 'User Details - ' . $user['full_name'],
            'user' => $user,
            'stats' => $userStats,
            'activities' => $recentActivities,
        ];

        return view('admin/users/show', $data);
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $data = [
            'title' => 'Create New User - Admin',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,presenter,audience,reviewer]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]',
            'is_active' => 'permit_empty|in_list[0,1]',
            'send_welcome_email' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => strtolower(trim($this->request->getPost('email'))),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'is_active' => $this->request->getPost('is_active') ? true : false,
            'email_verified' => true, // Admin created users are auto-verified
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user');
            }

            // Send welcome email if requested
            if ($this->request->getPost('send_welcome_email')) {
                $this->sendWelcomeEmail($userData['email'], $userData['full_name'], $this->request->getPost('password'));
            }

            // Log admin action
            $this->logAdminAction('create_user', $userId, "Created user: {$userData['full_name']} ({$userData['email']})");

            return redirect()->to('/admin/users')->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Admin user creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        $data = [
            'title' => 'Edit User - ' . $user['full_name'],
            'user' => $user,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,presenter,audience,reviewer]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]',
            'is_active' => 'permit_empty|in_list[0,1]',
            'password' => 'permit_empty|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => strtolower(trim($this->request->getPost('email'))),
            'role' => $this->request->getPost('role'),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'is_active' => $this->request->getPost('is_active') ? true : false,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update password if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            $this->userModel->update($id, $updateData);

            // Log admin action
            $this->logAdminAction('update_user', $id, "Updated user: {$updateData['full_name']} ({$updateData['email']})");

            return redirect()->to('/admin/users')->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Admin user update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Delete user (soft delete or hard delete)
     */
    public function delete($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Prevent deleting yourself
        if ($id == $this->session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete your own account']);
        }

        // Check if user has related data
        $hasRegistrations = $this->registrationModel->where('user_id', $id)->countAllResults() > 0;
        $hasAbstracts = $this->abstractModel->where('user_id', $id)->countAllResults() > 0;
        $hasReviews = $this->reviewModel->where('reviewer_id', $id)->countAllResults() > 0;

        if ($hasRegistrations || $hasAbstracts || $hasReviews) {
            // Soft delete - deactivate user
            $this->userModel->update($id, ['is_active' => false]);
            $action = 'deactivated';
        } else {
            // Hard delete
            $this->userModel->delete($id);
            $action = 'deleted';
        }

        // Log admin action
        $this->logAdminAction('delete_user', $id, "User {$action}: {$user['full_name']} ({$user['email']})");

        return $this->response->setJSON(['success' => true, 'message' => "User {$action} successfully"]);
    }

    /**
     * Bulk actions for multiple users
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $userIds = $this->request->getPost('user_ids');

        if (!$action || !$userIds || !is_array($userIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $processed = 0;
        $currentUserId = $this->session->get('user_id');

        foreach ($userIds as $id) {
            // Skip current user
            if ($id == $currentUserId) {
                continue;
            }

            switch ($action) {
                case 'activate':
                    $this->userModel->update($id, ['is_active' => true]);
                    $processed++;
                    break;

                case 'deactivate':
                    $this->userModel->update($id, ['is_active' => false]);
                    $processed++;
                    break;

                case 'delete':
                    $this->userModel->delete($id);
                    $processed++;
                    break;

                case 'make_reviewer':
                    $this->userModel->update($id, ['role' => 'reviewer']);
                    $processed++;
                    break;
            }
        }

        // Log admin action
        $this->logAdminAction('bulk_action', null, "Bulk action '{$action}' performed on {$processed} users");

        return $this->response->setJSON([
            'success' => true, 
            'message' => "Action completed successfully for {$processed} users"
        ]);
    }

    /**
     * Export users data
     */
    public function export($format = 'csv')
    {
        $users = $this->userModel->select('id, full_name, email, role, institution, phone, is_active, email_verified, created_at')->findAll();

        if ($format === 'csv') {
            return $this->exportCSV($users);
        } else {
            return $this->exportExcel($users);
        }
    }

    /**
     * Send password reset to user
     */
    public function sendPasswordReset($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update user with reset token
        $this->userModel->update($id, [
            'reset_token' => $token,
            'reset_expires' => $expires
        ]);

        // Send password reset email
        $resetSent = $this->sendPasswordResetEmail($user['email'], $user['full_name'], $token);

        if ($resetSent) {
            // Log admin action
            $this->logAdminAction('send_password_reset', $id, "Sent password reset to: {$user['email']}");

            return $this->response->setJSON(['success' => true, 'message' => 'Password reset email sent successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to send password reset email']);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        // Prevent deactivating yourself
        if ($id == $this->session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot deactivate your own account']);
        }

        $newStatus = !$user['is_active'];
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activated' : 'deactivated';

        // Log admin action
        $this->logAdminAction('toggle_status', $id, "User {$statusText}: {$user['full_name']}");

        return $this->response->setJSON([
            'success' => true, 
            'message' => "User {$statusText} successfully",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Impersonate user (login as user)
     */
    public function impersonate($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || !$user['is_active']) {
            return redirect()->back()->with('error', 'Cannot impersonate this user');
        }

        // Store original admin session
        $this->session->set('impersonating', true);
        $this->session->set('original_user_id', $this->session->get('user_id'));
        $this->session->set('original_user_role', $this->session->get('user_role'));

        // Set user session
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('user_name', $user['full_name']);
        $this->session->set('user_role', $user['role']);

        // Log admin action
        $this->logAdminAction('impersonate_user', $id, "Started impersonating: {$user['full_name']}");

        // Redirect to user's dashboard
        return redirect()->to('/dashboard')->with('info', "You are now logged in as {$user['full_name']}. Click 'Stop Impersonating' to return to your admin account.");
    }

    /**
     * Stop impersonating user
     */
    public function stopImpersonating()
    {
        if (!$this->session->get('impersonating')) {
            return redirect()->to('/admin/dashboard');
        }

        // Restore original admin session
        $this->session->set('user_id', $this->session->get('original_user_id'));
        $this->session->set('user_role', $this->session->get('original_user_role'));
        
        // Clear impersonation data
        $this->session->remove('impersonating');
        $this->session->remove('original_user_id');
        $this->session->remove('original_user_role');

        return redirect()->to('/admin/users')->with('success', 'Stopped impersonating user');
    }

    /**
     * Get user statistics for AJAX
     */
    public function getStats()
    {
        $stats = [
            'total' => $this->userModel->countAll(),
            'active' => $this->userModel->where('is_active', true)->countAllResults(),
            'inactive' => $this->userModel->where('is_active', false)->countAllResults(),
            'verified' => $this->userModel->where('email_verified', true)->countAllResults(),
            'roles' => [
                'admin' => $this->userModel->where('role', 'admin')->countAllResults(),
                'presenter' => $this->userModel->where('role', 'presenter')->countAllResults(),
                'audience' => $this->userModel->where('role', 'audience')->countAllResults(),
                'reviewer' => $this->userModel->where('role', 'reviewer')->countAllResults(),
            ],
            'recent' => $this->userModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'this_month' => $this->userModel->where('created_at >=', date('Y-m-01'))->countAllResults(),
        ];

        return $this->response->setJSON($stats);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get user activities
     */
    private function getUserActivities($userId)
    {
        $activities = [];

        // Get registrations
        $registrations = $this->registrationModel
            ->select('registrations.*, events.title as event_title')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.user_id', $userId)
            ->orderBy('registrations.created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($registrations as $reg) {
            $activities[] = [
                'type' => 'registration',
                'message' => "Registered for event: {$reg['event_title']}",
                'date' => $reg['created_at'],
                'status' => $reg['status']
            ];
        }

        // Get abstracts
        $abstracts = $this->abstractModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($abstracts as $abstract) {
            $activities[] = [
                'type' => 'abstract',
                'message' => "Submitted abstract: {$abstract['title']}",
                'date' => $abstract['created_at'],
                'status' => $abstract['status']
            ];
        }

        // Get reviews (if user is reviewer)
        $reviews = $this->reviewModel
            ->select('reviews.*, abstracts.title as abstract_title')
            ->join('abstracts', 'abstracts.id = reviews.abstract_id')
            ->where('reviews.reviewer_id', $userId)
            ->orderBy('reviews.created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($reviews as $review) {
            $activities[] = [
                'type' => 'review',
                'message' => "Reviewed abstract: {$review['abstract_title']}",
                'date' => $review['created_at'],
                'status' => $review['recommendation']
            ];
        }

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Send welcome email to new user
     */
    private function sendWelcomeEmail($email, $name, $password)
    {
        $emailService = \Config\Services::email();

        $message = "
            <h2>Welcome to SNIA Conference!</h2>
            <p>Hello {$name},</p>
            <p>Your account has been created by an administrator. Here are your login credentials:</p>
            <p><strong>Email:</strong> {$email}<br>
            <strong>Password:</strong> {$password}</p>
            <p>Please login and change your password immediately for security.</p>
            <p><a href='" . base_url('login') . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login Now</a></p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject('Welcome to SNIA Conference - Account Created');
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send welcome email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $name, $token)
    {
        $emailService = \Config\Services::email();
        
        $resetUrl = base_url("auth/reset-password/{$token}");
        
        $message = "
            <h2>Password Reset Request</h2>
            <p>Hello {$name},</p>
            <p>An administrator has initiated a password reset for your account.</p>
            <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
            <p>Or copy and paste this URL into your browser:</p>
            <p>{$resetUrl}</p>
            <p>This link will expire in 1 hour.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject('Password Reset - SNIA Conference');
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log admin actions
     */
    private function logAdminAction($action, $targetId, $description)
    {
        $adminId = $this->session->get('user_id');
        $adminName = $this->session->get('user_name');
        
        log_message('info', "Admin Action - Admin: {$adminName} (ID: {$adminId}), Action: {$action}, Target: {$targetId}, Description: {$description}");
        
        // You could also save to a dedicated admin_logs table
        /*
        $adminLogModel = new AdminLogModel();
        $adminLogModel->insert([
            'admin_id' => $adminId,
            'action' => $action,
            'target_id' => $targetId,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        */
    }

    /**
     * Export users as CSV
     */
    private function exportCSV($users)
    {
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, ['ID', 'Full Name', 'Email', 'Role', 'Institution', 'Phone', 'Active', 'Email Verified', 'Created At']);
        
        // Write data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['full_name'],
                $user['email'],
                $user['role'],
                $user['institution'],
                $user['phone'],
                $user['is_active'] ? 'Yes' : 'No',
                $user['email_verified'] ? 'Yes' : 'No',
                $user['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export users as Excel (placeholder)
     */
    private function exportExcel($users)
    {
        // For now, fallback to CSV
        return $this->exportCSV($users);
    }
}