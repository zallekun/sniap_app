<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EmailService;
use App\Services\QRCodeService;

class RegisterController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display registration form
     */
    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Register - SNIA Conference',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/register', $data);
    }

    /**
     * Process registration
     */
    public function store()
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]',
            'role' => 'required|in_list[presenter,audience]',
            'terms' => 'required'
        ];

        $messages = [
            'full_name' => [
                'required' => 'Full name is required',
                'min_length' => 'Full name must be at least 3 characters',
                'max_length' => 'Full name cannot exceed 100 characters'
            ],
            'email' => [
                'required' => 'Email is required',
                'valid_email' => 'Please enter a valid email address',
                'is_unique' => 'This email is already registered'
            ],
            'password' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 6 characters'
            ],
            'confirm_password' => [
                'required' => 'Password confirmation is required',
                'matches' => 'Password confirmation does not match'
            ],
            'phone' => [
                'min_length' => 'Phone number must be at least 10 digits',
                'max_length' => 'Phone number cannot exceed 15 digits'
            ],
            'institution' => [
                'max_length' => 'Institution name cannot exceed 200 characters'
            ],
            'role' => [
                'required' => 'Please select a role',
                'in_list' => 'Please select a valid role'
            ],
            'terms' => [
                'required' => 'You must accept the terms and conditions'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare user data
        $userData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => strtolower(trim($this->request->getPost('email'))),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'role' => $this->request->getPost('role'),
            'is_active' => true,
            'email_verified' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Insert user
            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Send verification email
            $this->sendVerificationEmail($userData['email'], $userData['full_name'], $userId);

            // Auto login after registration (optional)
            $autoLogin = true; // You can make this configurable
            
            if ($autoLogin) {
                $sessionData = [
                    'user_id' => $userId,
                    'user_email' => $userData['email'],
                    'user_name' => $userData['full_name'],
                    'user_role' => $userData['role'],
                    'is_logged_in' => true
                ];

                $this->session->set($sessionData);

                $redirectUrl = $this->getRedirectUrl($userData['role']);
                return redirect()->to($redirectUrl)->with('success', 'Registration successful! Welcome to SNIA Conference.');
            } else {
                return redirect()->to('/login')->with('success', 'Registration successful! Please check your email for verification instructions.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    /**
     * Verify email address
     */
    public function verifyEmail($token = null)
    {
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Invalid verification token.');
        }

        // Decode user ID from token (simple implementation)
        $userId = $this->decodeVerificationToken($token);

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Invalid verification token.');
        }

        // Find user
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        if ($user['email_verified']) {
            return redirect()->to('/login')->with('info', 'Email already verified. You can login now.');
        }

        // Update user as verified
        $this->userModel->update($userId, [
            'email_verified' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/login')->with('success', 'Email verified successfully! You can now login to your account.');
    }

    /**
     * Resend verification email
     */
    public function resendVerification()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email not found.');
        }

        if ($user['email_verified']) {
            return redirect()->back()->with('info', 'Email is already verified.');
        }

        // Send verification email again
        $this->sendVerificationEmail($user['email'], $user['full_name'], $user['id']);

        return redirect()->back()->with('success', 'Verification email has been resent.');
    }

    /**
     * Check email availability (AJAX)
     */
    public function checkEmail()
    {
        $email = $this->request->getGet('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Invalid email format'
            ]);
        }

        $exists = $this->userModel->where('email', $email)->first();

        return $this->response->setJSON([
            'available' => !$exists,
            'message' => $exists ? 'Email is already registered' : 'Email is available'
        ]);
    }

    /**
     * Registration statistics (for admin)
     */
    public function stats()
    {
        // Only allow admin access
        if (!$this->session->get('user_role') || $this->session->get('user_role') !== 'admin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $stats = [
            'total_users' => $this->userModel->countAll(),
            'verified_users' => $this->userModel->where('email_verified', true)->countAllResults(),
            'presenters' => $this->userModel->where('role', 'presenter')->countAllResults(),
            'audience' => $this->userModel->where('role', 'audience')->countAllResults(),
            'reviewers' => $this->userModel->where('role', 'reviewer')->countAllResults(),
            'recent_registrations' => $this->userModel
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->find()
        ];

        return $this->response->setJSON($stats);
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Send email verification
     */
    private function sendVerificationEmail($email, $name, $userId)
{
    try {
        $token = $this->generateVerificationToken($userId);
        
        // Use professional EmailService instead of basic email
        $emailService = new EmailService();
        
        $result = $emailService->sendVerificationEmail($email, $name, $token);
        
        if ($result['success']) {
            log_message('info', "Verification email sent successfully to: {$email}");
            return true;
        } else {
            log_message('error', "Failed to send verification email: " . $result['message']);
            return false;
        }
        
    } catch (\Exception $e) {
        log_message('error', 'Verification email error: ' . $e->getMessage());
        return false;
    }
}

    /**
     * Generate verification token
     */
    private function generateVerificationToken($userId)
    {
        // Simple token generation (you might want to use JWT or more secure method)
        return base64_encode($userId . '|' . time() . '|' . bin2hex(random_bytes(16)));
    }

    /**
     * Decode verification token
     */
    private function decodeVerificationToken($token)
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) !== 3) {
                return false;
            }

            $userId = (int)$parts[0];
            $timestamp = (int)$parts[1];

            // Check if token is not older than 24 hours
            if (time() - $timestamp > 86400) {
                return false;
            }

            return $userId;
        } catch (\Exception $e) {
            return false;
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
                return '/dashboard';
        }
    }
}