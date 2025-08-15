<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PendingRegistrationModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EmailService;
use App\Services\QRCodeService;

class RegisterController extends BaseController
{
    protected $userModel;
    protected $pendingModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pendingModel = new PendingRegistrationModel();
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
            'hideNavbar' => true,
            'hideFooter' => true,
            'validation' => \Config\Services::validation()
        ];

        return view('shared/auth/register_clean', $data);
    }

    /**
     * Process registration
     */
    public function store()
    {
        log_message('info', 'Registration store method called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]',
            'role' => 'required|in_list[presenter,audience]',
            'terms' => 'required'
        ];

        $messages = [
            'first_name' => [
                'required' => 'First name is required',
                'min_length' => 'First name must be at least 2 characters',
                'max_length' => 'First name cannot exceed 100 characters'
            ],
            'last_name' => [
                'required' => 'Last name is required',
                'min_length' => 'Last name must be at least 2 characters',
                'max_length' => 'Last name cannot exceed 100 characters'
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
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Check if email already exists in users or pending_registrations
        $email = strtolower(trim($this->request->getPost('email')));
        
        $existingUser = $this->userModel->findByEmail($email);
        $existingPending = $this->pendingModel->findByEmail($email);
        
        if ($existingUser) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Email sudah terdaftar. Silakan gunakan email lain atau login.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar. Silakan gunakan email lain atau login.');
        }
        
        if ($existingPending) {
            // Delete old pending registration
            $this->pendingModel->where('email', $email)->delete();
            log_message('info', "Deleted old pending registration for: {$email}");
        }
        
        log_message('info', 'Validation passed, proceeding with pending registration creation');

        // Prepare pending registration data
        $verificationCode = $this->generateVerificationCode();
        $userData = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'email' => $email,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'role' => $this->request->getPost('role'),
            'verification_code' => $verificationCode,
            'verification_code_expires' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
        ];

        try {
            // Insert pending registration
            $pendingId = $this->pendingModel->insert($userData);

            if (!$pendingId) {
                throw new \Exception('Failed to create pending registration');
            }
            
            log_message('info', "Pending registration created with ID: {$pendingId}, email: {$email}");

            // Send verification code email
            $fullName = trim($userData['first_name'] . ' ' . $userData['last_name']);
            
            log_message('info', "Generated verification code: {$verificationCode} for pending registration: {$email}");
            
            if (!$this->sendVerificationCode($email, $fullName, $verificationCode)) {
                log_message('warning', 'Failed to send verification email, but pending registration was created');
            }

            // Store email in session for verification page
            $this->session->set('verification_email', $email);

            // Redirect to verification code page
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Registration successful! Please check your email for verification code.',
                    'redirect' => '/auth/verify-code'
                ]);
            }
            return redirect()->to('/auth/verify-code')->with('success', 'Registration successful! Please check your email for verification code.');

        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration failed. Please try again.'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    /**
     * Show verification code page
     */
    public function verifyCodePage()
    {
        $email = $this->session->get('verification_email');
        
        if (!$email) {
            return redirect()->to('/register')->with('error', 'Please register first.');
        }

        $data = [
            'title' => 'Verify Email - SNIA Conference',
            'email' => $email,
            'hideNavbar' => true,
            'hideFooter' => true
        ];

        return view('shared/auth/verify-code', $data);
    }

    /**
     * Process verification code
     */
    public function verifyCode()
    {
        log_message('info', 'VerifyCode method called');
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('info', 'Raw input: ' . $this->request->getBody());
        
        $code = $this->request->getPost('verification_code');
        $email = $this->session->get('verification_email');

        if (!$email) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired. Please register again.'
            ]);
        }

        if (!$code || strlen($code) !== 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid 6-digit code.'
            ]);
        }

        // Find pending registration
        $pending = $this->pendingModel->findByEmail($email);
        
        if (!$pending) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran tidak ditemukan atau sudah diverifikasi.'
            ]);
        }

        // Check if code matches and is not expired
        if ($pending['verification_code'] !== $code) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kode verifikasi tidak valid.'
            ]);
        }

        if (!$pending['verification_code_expires'] || strtotime($pending['verification_code_expires']) < time()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kode verifikasi telah kadaluarsa. Silakan minta kode baru.'
            ]);
        }

        try {
            // Create final user account
            $userData = [
                'first_name' => $pending['first_name'],
                'last_name' => $pending['last_name'],
                'email' => $pending['email'],
                'password' => $pending['password'],
                'phone' => $pending['phone'],
                'institution' => $pending['institution'],
                'role' => $pending['role'],
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Delete pending registration
            $this->pendingModel->delete($pending['id']);
            
            log_message('info', "User account created successfully: {$email}, ID: {$userId}");
        } catch (\Exception $e) {
            log_message('error', 'Failed to create final user account: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.'
            ]);
        }

        // Clear session
        $this->session->remove('verification_email');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email verified successfully!'
        ]);
    }

    /**
     * Resend verification code
     */
    public function resendCode()
    {
        $email = $this->session->get('verification_email');
        
        if (!$email) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired. Please register again.'
            ]);
        }

        $pending = $this->pendingModel->findByEmail($email);
        
        if (!$pending) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran tidak ditemukan.'
            ]);
        }

        // Generate new code and update expires time
        $verificationCode = $this->generateVerificationCode();
        $this->pendingModel->update($pending['id'], [
            'verification_code' => $verificationCode,
            'verification_code_expires' => date('Y-m-d H:i:s', strtotime('+15 minutes'))
        ]);
        
        $fullName = trim($pending['first_name'] . ' ' . $pending['last_name']);
        $this->sendVerificationCode($pending['email'], $fullName, $verificationCode);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'New verification code sent!'
        ]);
    }

    /**
     * Verify email address (old method - keep for backward compatibility)
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

        if ($user['is_verified']) {
            return redirect()->to('/login')->with('info', 'Email already verified. You can login now.');
        }

        // Update user as verified
        $this->userModel->update($userId, [
            'is_verified' => true,
            'updated_at' => date('Y-m-d H:i:s')
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

        if ($user['is_verified']) {
            return redirect()->back()->with('info', 'Email is already verified.');
        }

        // Send verification email again
        $fullName = trim($user['first_name'] . ' ' . $user['last_name']);
        $this->sendVerificationEmail($user['email'], $fullName, $user['id']);

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

        $existsInUsers = $this->userModel->where('email', $email)->first();
        $existsInPending = $this->pendingModel->where('email', $email)->first();

        $exists = $existsInUsers || $existsInPending;
        
        return $this->response->setJSON([
            'available' => !$exists,
            'message' => $exists ? 'Email sudah terdaftar' : 'Email tersedia'
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
            'verified_users' => $this->userModel->where('is_verified', true)->countAllResults(),
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
        
        $result = $emailService->sendVerificationEmail($email, $name, $token, 1);
        
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
     * Generate 6-digit verification code
     */
    private function generateVerificationCode()
    {
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }


    /**
     * Send verification code via email
     */
    private function sendVerificationCode($email, $name, $code)
    {
        try {
            $emailService = new EmailService();
            
            $result = $emailService->sendVerificationCode($email, $name, $code, 1);
            
            if ($result['success']) {
                log_message('info', "Verification code sent successfully to: {$email}");
                return true;
            } else {
                log_message('error', "Failed to send verification code: " . $result['message']);
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Verification code email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate verification token (old method - keep for backward compatibility)
     */
    private function generateVerificationToken($userId)
    {
        // URL-safe token generation without = characters
        $data = $userId . '|' . time() . '|' . bin2hex(random_bytes(16));
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode verification token
     */
    private function decodeVerificationToken($token)
    {
        try {
            // Restore URL-safe base64
            $token = str_pad(strtr($token, '-_', '+/'), strlen($token) % 4, '=', STR_PAD_RIGHT);
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