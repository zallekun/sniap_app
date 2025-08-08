<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DebugController extends BaseController
{
    /**
     * Simple profile test - NO JWT, NO UserModel, NOTHING
     * GET /api/v1/debug/profile
     */
    public function profile()
    {
        try {
            // Hardcode user ID 21 
            $db = \Config\Database::connect();
            $sql = "SELECT id, email, first_name, last_name, phone, institution, role FROM users WHERE id = ?";
            $query = $db->query($sql, [21]);
            $user = $query->getRowArray();
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User 21 not found'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'WORKING! No JWT, no UserModel, just pure SQL',
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
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Test JWT processing step by step
     * GET /api/v1/debug/jwt
     */
    public function jwt()
    {
        try {
            // Step 1: Get auth header
            $header = $this->request->getHeaderLine('Authorization');
            
            if (empty($header)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'step' => 1,
                    'message' => 'No Authorization header'
                ]);
            }
            
            // Step 2: Extract token
            $parts = explode(' ', $header);
            if (count($parts) !== 2 || strtolower($parts[0]) !== 'bearer') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'step' => 2,
                    'message' => 'Invalid Authorization format'
                ]);
            }
            
            $token = $parts[1];
            
            // Step 3: Try JWT decode with manual key
            $jwtKey = 'snia_conference_jwt_super_secret_key_2024_secure_random_string';
            
            // Manual decode to avoid any imports issues
            require_once ROOTPATH . 'vendor/firebase/php-jwt/src/JWT.php';
            require_once ROOTPATH . 'vendor/firebase/php-jwt/src/Key.php';
            
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtKey, 'HS256'));
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'JWT decode SUCCESS!',
                'data' => [
                    'user_id' => $decoded->user_id,
                    'email' => $decoded->email,
                    'role' => $decoded->role,
                    'expires_at' => date('Y-m-d H:i:s', $decoded->exp)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'JWT Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Test complete JWT profile flow
     * GET /api/v1/debug/profile-jwt
     */
    public function profileJwt()
    {
        try {
            // Step 1: Get and decode JWT
            $header = $this->request->getHeaderLine('Authorization');
            if (empty($header)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No Authorization header'
                ]);
            }
            
            $parts = explode(' ', $header);
            if (count($parts) !== 2 || strtolower($parts[0]) !== 'bearer') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid Authorization format'
                ]);
            }
            
            $token = $parts[1];
            $jwtKey = 'snia_conference_jwt_super_secret_key_2024_secure_random_string';
            
            require_once ROOTPATH . 'vendor/firebase/php-jwt/src/JWT.php';
            require_once ROOTPATH . 'vendor/firebase/php-jwt/src/Key.php';
            
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtKey, 'HS256'));
            
            // Step 2: Get user from database using JWT user_id
            $db = \Config\Database::connect();
            $sql = "SELECT id, email, first_name, last_name, phone, institution, role FROM users WHERE id = ?";
            $query = $db->query($sql, [$decoded->user_id]);
            $user = $query->getRowArray();
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found for JWT user_id: ' . $decoded->user_id
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'JWT Profile SUCCESS!',
                'jwt_data' => [
                    'user_id' => $decoded->user_id,
                    'email' => $decoded->email,
                    'role' => $decoded->role
                ],
                'db_data' => [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => trim($user['first_name'] . ' ' . $user['last_name']),
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'phone' => $user['phone'],
                    'institution' => $user['institution']
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'JWT Profile Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
 * Debug JWT key comparison
 * GET /api/v1/debug/jwt-keys
 */
public function jwtKeys()
{
    // Check different key sources
    $envKey = getenv('JWT_SECRET_KEY');
    $configKey = $_ENV['JWT_SECRET_KEY'] ?? null;
    $hardcodedKey = 'snia_conference_jwt_super_secret_key_2024_secure_random_string';
    $fallbackKey = 'snia_conference_jwt_secret_key_2024_super_secure'; // from .env
    
    return $this->response->setJSON([
        'status' => 'debug',
        'keys' => [
            'getenv' => $envKey,
            'env_array' => $configKey,
            'hardcoded_1' => $hardcodedKey,
            'hardcoded_2' => $fallbackKey,
            'are_same_env_hardcoded1' => ($envKey === $hardcodedKey),
            'are_same_env_hardcoded2' => ($envKey === $fallbackKey)
        ]
    ]);
}

/**
 * Test JWT with different keys
 * GET /api/v1/debug/jwt-test-keys
 */
public function jwtTestKeys()
{
    try {
        $header = $this->request->getHeaderLine('Authorization');
        
        if (empty($header)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No Authorization header'
            ]);
        }
        
        $parts = explode(' ', $header);
        $token = $parts[1];
        
        // Test different keys
        $keys = [
            'env_key' => getenv('JWT_SECRET_KEY'),
            'hardcoded_1' => 'snia_conference_jwt_super_secret_key_2024_secure_random_string',
            'hardcoded_2' => 'snia_conference_jwt_secret_key_2024_super_secure'
        ];
        
        $results = [];
        
        require_once ROOTPATH . 'vendor/firebase/php-jwt/src/JWT.php';
        require_once ROOTPATH . 'vendor/firebase/php-jwt/src/Key.php';
        
        foreach ($keys as $keyName => $keyValue) {
            try {
                if ($keyValue) {
                    $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($keyValue, 'HS256'));
                    $results[$keyName] = [
                        'status' => 'SUCCESS',
                        'user_id' => $decoded->user_id,
                        'email' => $decoded->email,
                        'key_used' => $keyValue
                    ];
                } else {
                    $results[$keyName] = [
                        'status' => 'NULL_KEY',
                        'key_used' => 'null'
                    ];
                }
            } catch (\Exception $e) {
                $results[$keyName] = [
                    'status' => 'FAILED',
                    'error' => $e->getMessage(),
                    'key_used' => $keyValue
                ];
            }
        }
        
        return $this->response->setJSON([
            'status' => 'debug',
            'message' => 'JWT key test results',
            'results' => $results
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
}