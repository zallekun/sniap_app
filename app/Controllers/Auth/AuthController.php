<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display login form
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - SNIA Conference',
            'hideNavbar' => true,
            'hideFooter' => true,
            'validation' => \Config\Services::validation()
        ];

        return view('shared/auth/login_clean', $data);
    }

    /**
     * Process login attempt
     */
    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember_me');

        // Find user by email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email not found.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Email not found.');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid password.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Invalid password.');
        }

        // Check if email is verified
        if (!$user['is_verified']) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please verify your email address before logging in. Check your email for verification instructions.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Please verify your email address before logging in. Check your email for verification instructions.');
        }

        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'user_name' => trim($user['first_name'] . ' ' . $user['last_name']),
            'user_role' => $user['role'],
            'is_logged_in' => true
        ];

        $this->session->set($sessionData);

        // Handle remember me
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }

        // Update last login (only update if column exists)
        try {
            $this->userModel->update($user['id'], [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Column might not exist, ignore
            log_message('warning', 'Could not update last login: ' . $e->getMessage());
        }

        // Redirect based on role
        $redirectUrl = $this->getRedirectUrl($user['role']);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect' => $redirectUrl
            ]);
        }
        
        return redirect()->to($redirectUrl)->with('success', 'Login successful!');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Clear remember me cookie
        $this->clearRememberMeCookie();
        
        // Destroy session
        $this->session->destroy();
        
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Forgot password form
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password - SNIA Conference',
            'validation' => \Config\Services::validation()
        ];

        return view('shared/auth/forgot_password', $data);
    }

    /**
     * Send reset password email
     */
    public function sendResetEmail()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email not found in our records.');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update user with reset token
        $this->userModel->update($user['id'], [
            'reset_token' => $token,
            'reset_expires' => $expires
        ]);

        // Send email (implement your email service)
        $this->sendPasswordResetEmail($user['email'], $user['full_name'], $token);

        return redirect()->back()->with('success', 'Password reset link has been sent to your email.');
    }

    /**
     * Reset password form
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Invalid reset token.');
        }

        // Validate token
        $user = $this->userModel->where('reset_token', $token)
                                ->where('reset_expires >', date('Y-m-d H:i:s'))
                                ->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset token.');
        }

        $data = [
            'title' => 'Reset Password - SNIA Conference',
            'token' => $token,
            'validation' => \Config\Services::validation()
        ];

        return view('shared/auth/reset_password', $data);
    }

    /**
     * Process password reset
     */
    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Find user by token
        $user = $this->userModel->where('reset_token', $token)
                                ->where('reset_expires >', date('Y-m-d H:i:s'))
                                ->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid or expired reset token.');
        }

        // Update password
        $this->userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires' => null
        ]);

        return redirect()->to('/login')->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Set remember me cookie
     */
    private function setRememberMeCookie($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days

        // Save token to database with error handling
        try {
            $this->userModel->update($userId, ['remember_token' => $token]);
        } catch (\Exception $e) {
            // Column might not exist, log but continue
            log_message('warning', 'Could not update remember_token: ' . $e->getMessage());
        }

        // Set cookie regardless of database update success
        setcookie('remember_token', $token, $expires, '/', '', false, true);
    }

    /**
     * Clear remember me cookie
     */
    private function clearRememberMeCookie()
    {
        if (isset($_COOKIE['remember_token'])) {
            // Clear from database
            $user = $this->userModel->where('remember_token', $_COOKIE['remember_token'])->first();
            if ($user) {
                $this->userModel->update($user['id'], ['remember_token' => null]);
            }

            // Clear cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl($role)
    {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'reviewer':
                return '/reviewer/dashboard';
            case 'presenter':
                return '/presenter/dashboard';
            default:
                // For audience/participant users
                return '/audience/dashboard';
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
            <p>You requested a password reset for your SNIA Conference account.</p>
            <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
            <p>Or copy and paste this URL into your browser:</p>
            <p>{$resetUrl}</p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this reset, please ignore this email.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject('Password Reset - SNIA Conference');
        $emailService->setMessage($message);

        try {
            $emailService->send();
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }
}