<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthApiController extends BaseController
{
    protected $jwtKey;
    protected $jwtAlgorithm = 'HS256';
    protected $jwtExpire;

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY') ?: 'snia_conference_jwt_secret_key_2024_super_secure';
        $this->jwtExpire = getenv('JWT_EXPIRE_TIME') ?: 7200; // 2 hours default
    }

    /**
     * API Register - CLEAN VERSION (NO UserModel)
     * POST /api/v1/auth/register
     */
    public function register()
    {
        $jsonInput = $this->request->getJSON(true);
        
        if (empty($jsonInput)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No JSON data received'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Validation
        $errors = [];
        if (empty($jsonInput['email']) || !filter_var($jsonInput['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        if (empty($jsonInput['password']) || strlen($jsonInput['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        if (empty($jsonInput['confirm_password']) || $jsonInput['password'] !== $jsonInput['confirm_password']) {
            $errors['confirm_password'] = 'Password confirmation does not match';
        }
        if (empty($jsonInput['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        if (empty($jsonInput['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        if (empty($jsonInput['role']) || !in_array($jsonInput['role'], ['presenter', 'audience', 'reviewer'])) {
            $errors['role'] = 'Invalid role';
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Check email uniqueness
        $db = \Config\Database::connect();
        $existingUser = $db->query('SELECT id FROM users WHERE email = ?', [$jsonInput['email']])->getRowArray();
        
        if ($existingUser) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email already exists'
            ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
        }

        // Use direct first_name and last_name
        $firstName = trim($jsonInput['first_name']);
        $lastName = trim($jsonInput['last_name']);

        try {
            // Direct SQL insert
            $sql = "INSERT INTO users (email, password, first_name, last_name, phone, institution, role) 
                    VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id";
            
            $result = $db->query($sql, [
                $jsonInput['email'],
                password_hash($jsonInput['password'], PASSWORD_DEFAULT),
                $firstName,
                $lastName,
                $jsonInput['phone_number'] ?? null,
                $jsonInput['institution'] ?? null,
                $jsonInput['role']
            ]);
            
            $insertResult = $result->getRowArray();
            $userId = $insertResult['id'];

            // Auto-login: Generate JWT token after successful registration
            $payload = [
                'user_id' => (int)$userId,
                'email' => $jsonInput['email'],
                'role' => $jsonInput['role'],
                'iat' => time(),
                'exp' => time() + $this->jwtExpire
            ];

            $jwt = \Firebase\JWT\JWT::encode($payload, $this->jwtKey, 'HS256');

            // Send verification email
            try {
                $emailService = new \App\Services\EmailService();
                $token = base64_encode($userId . '|' . time() . '|' . bin2hex(random_bytes(16)));
                $emailService->sendVerificationEmail($jsonInput['email'], $firstName . ' ' . $lastName, $token, 1);
            } catch (\Exception $e) {
                // Email failed but registration successful - log error
                log_message('error', 'Verification email failed: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration successful! Welcome to SNIA Conference.',
                'data' => [
                    'user_id' => (int)$userId,
                    'email' => $jsonInput['email'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'role' => $jsonInput['role'],
                    'token' => $jwt,
                    'user' => [
                        'id' => (int)$userId,
                        'email' => $jsonInput['email'],
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'role' => $jsonInput['role'],
                        'institution' => $jsonInput['institution'] ?? null,
                        'phone' => $jsonInput['phone_number'] ?? null
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API Login - CLEAN VERSION (NO UserModel)
     * POST /api/v1/auth/login
     */
    public function login()
    {
        $jsonInput = $this->request->getJSON(true);
        
        if (empty($jsonInput['email']) || empty($jsonInput['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email and password are required'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            // Direct SQL query - NO UserModel
            $db = \Config\Database::connect();
            $user = $db->query('SELECT id, email, password, first_name, last_name, role FROM users WHERE email = ?', [$jsonInput['email']])->getRowArray();
            
            if (!$user || !password_verify($jsonInput['password'], $user['password'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Generate JWT token
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'iat' => time(),
                'exp' => time() + $this->jwtExpire
            ];

            $token = JWT::encode($payload, $this->jwtKey, $this->jwtAlgorithm);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => trim($user['first_name'] . ' ' . $user['last_name']),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => $this->jwtExpire,
                    'expires_at' => date('Y-m-d H:i:s', time() + $this->jwtExpire)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Login failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify JWT Token - CLEAN VERSION
     * POST /api/v1/auth/verify
     */
    public function verify()
    {
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token not provided'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jwtAlgorithm));
            
            // Get fresh user data
            $db = \Config\Database::connect();
            $user = $db->query('SELECT id, email, first_name, last_name, role FROM users WHERE id = ?', [$decoded->user_id])->getRowArray();
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid token - user not found'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Token is valid',
                'data' => [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => trim($user['first_name'] . ' ' . $user['last_name']),
                    'expires_at' => date('Y-m-d H:i:s', $decoded->exp)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid token: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Get user profile - CLEAN VERSION
     * GET /api/v1/auth/profile
     */
    public function profile()
    {
        try {
            $token = $this->getTokenFromHeader();
            
            if (!$token) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Token not provided'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Decode JWT token
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jwtAlgorithm));
            
            // Get user from database using direct query
            $db = \Config\Database::connect();
            $user = $db->query('SELECT id, email, first_name, last_name, phone, institution, role FROM users WHERE id = ?', [$decoded->user_id])->getRowArray();
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => trim($user['first_name'] . ' ' . $user['last_name']),
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'phone' => $user['phone'],
                    'institution' => $user['institution']
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Profile access failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Helper: Get JWT token from Authorization header
     */
    private function getTokenFromHeader()
    {
        $header = $this->request->getHeaderLine('Authorization');
        
        if (empty($header)) {
            return null;
        }

        $parts = explode(' ', $header);
        
        if (count($parts) !== 2 || strtolower($parts[0]) !== 'bearer') {
            return null;
        }

        return $parts[1];
    }

    /**
     * Refresh token
     * POST /api/v1/auth/refresh
     */
    public function refresh()
    {
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token not provided'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jwtAlgorithm));
            
            $payload = [
                'user_id' => $decoded->user_id,
                'email' => $decoded->email,
                'role' => $decoded->role,
                'iat' => time(),
                'exp' => time() + $this->jwtExpire
            ];

            $newToken = JWT::encode($payload, $this->jwtKey, $this->jwtAlgorithm);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Token refreshed',
                'data' => [
                    'token' => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => $this->jwtExpire,
                    'expires_at' => date('Y-m-d H:i:s', time() + $this->jwtExpire)
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token refresh failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout
     * POST /api/v1/auth/logout
     */
    public function logout()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Logout successful'
        ]);
    }
}