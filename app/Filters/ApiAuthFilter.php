<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiAuthFilter implements FilterInterface
{
    protected $jwtKey;
    protected $jwtAlgorithm = 'HS256';

    public function __construct()
    {
        $this->jwtKey = getenv('JWT_SECRET_KEY') ?: 'snia_conference_jwt_secret_key_2024_super_secure';
    }

    /**
     * Authenticate API requests using JWT
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return ResponseInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        
        // Get token from header
        $token = $this->getTokenFromHeader($request);
        
        if (!$token) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Authorization token required'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            // Decode JWT token
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jwtAlgorithm));
            
            // Verify user exists using direct database query
            $db = \Config\Database::connect();
            $user = $db->query('SELECT id, email, role FROM users WHERE id = ?', [$decoded->user_id])->getRowArray();
            
            if (!$user) {
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // REMOVED: is_active check since column doesn't exist in users table
            // if (!$user['is_active']) {
            //     return $response->setJSON([
            //         'status' => 'error',
            //         'message' => 'Account is deactivated'
            //     ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            // }

            // Check role-based access if specified
            if (!empty($arguments) && !empty($arguments[0])) {
                $requiredRole = $arguments[0];
                
                // Handle multiple roles (comma-separated)
                $allowedRoles = explode(',', $requiredRole);
                $allowedRoles = array_map('trim', $allowedRoles);
                
                if (!in_array($user['role'], $allowedRoles)) {
                    return $response->setJSON([
                        'status' => 'error',
                        'message' => 'Insufficient privileges'
                    ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }
            }

            // Store user data in request for controller access
            $request->api_user = $user;
            $request->user_id = $user['id'];
            $request->user_role = $user['role'];

        } catch (\Firebase\JWT\ExpiredException $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Token has expired'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid token signature'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);

        } catch (\Exception $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid token: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * After filter - nothing to do here for API auth
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after request
    }

    /**
     * Extract JWT token from Authorization header
     *
     * @param RequestInterface $request
     * @return string|null
     */
    private function getTokenFromHeader(RequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (empty($header)) {
            // Also check for token in query parameter (for webhooks/callbacks)
            $token = $request->getGet('token');
            return $token ?: null;
        }

        // Format: "Bearer <token>"
        $parts = explode(' ', $header);
        
        if (count($parts) !== 2 || strtolower($parts[0]) !== 'bearer') {
            return null;
        }

        return $parts[1];
    }
}

/**
 * Helper function for controllers to get authenticated user
 */
if (!function_exists('getApiUser')) {
    function getApiUser()
    {
        $request = service('request');
        return $request->getGlobal('api_user');
    }
}

if (!function_exists('getJwtPayload')) {
    function getJwtPayload()
    {
        $request = service('request');
        return $request->getGlobal('jwt_payload');
    }
}