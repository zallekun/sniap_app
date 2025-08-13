<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display main dashboard
     */
    public function index()
    {
        // Try to get user from session first
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$userId) {
            // Check for JWT token in frontend (will be handled by JavaScript)
            // For now, redirect to login if no session
            return redirect()->to('/login');
        }

        // Get user data
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->session->destroy();
            return redirect()->to('/login')->with('error', 'User account not found.');
        }

        $data = [
            'title' => 'Dashboard - SNIA Conference',
            'user' => $user,
            'userRole' => $userRole,
            'userName' => trim($user['first_name'] . ' ' . $user['last_name'])
        ];

        return view('dashboard/index', $data);
    }

    /**
     * API endpoint to get user profile data
     */
    public function profile()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        // Remove sensitive data
        unset($user['password']);
        unset($user['remember_token']);
        unset($user['reset_token']);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $updateData = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->userModel->update($userId, $updateData);

            // Update session data
            $fullName = $updateData['first_name'] . ' ' . $updateData['last_name'];
            $this->session->set('user_name', $fullName);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $user = $this->userModel->find($userId);
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ]);
        }

        try {
            $this->userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');

        try {
            $stats = [];

            // Get role-specific statistics
            if ($userRole === 'presenter') {
                $stats = [
                    'registrations' => $this->getRegistrationCount($userId),
                    'abstracts' => $this->getAbstractCount($userId),
                    'certificates' => $this->getCertificateCount($userId),
                    'payments' => $this->getPaymentCount($userId)
                ];
            } else {
                $stats = [
                    'registrations' => $this->getRegistrationCount($userId),
                    'qr_codes' => $this->getQRCodeCount($userId),
                    'certificates' => $this->getCertificateCount($userId),
                    'attendances' => $this->getAttendanceCount($userId)
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard stats error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load statistics'
            ]);
        }
    }

    // ==================== PRIVATE METHODS ====================

    private function getRegistrationCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('registrations')
                  ->where('user_id', $userId)
                  ->countAllResults();
    }

    private function getAbstractCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('abstracts a')
                  ->join('registrations r', 'r.id = a.registration_id')
                  ->where('r.user_id', $userId)
                  ->countAllResults();
    }

    private function getCertificateCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('certificates c')
                  ->join('registrations r', 'r.id = c.registration_id')
                  ->where('r.user_id', $userId)
                  ->countAllResults();
    }

    private function getPaymentCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('payments p')
                  ->join('registrations r', 'r.id = p.registration_id')
                  ->where('r.user_id', $userId)
                  ->where('p.payment_status', 'success')
                  ->countAllResults();
    }

    private function getQRCodeCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('qr_codes')
                  ->where('user_id', $userId)
                  ->countAllResults();
    }

    private function getAttendanceCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('qr_scans qs')
                  ->join('qr_codes qc', 'qc.id = qs.qr_code_id')
                  ->where('qc.user_id', $userId)
                  ->where('qs.scan_result', 'success')
                  ->countAllResults();
    }
}