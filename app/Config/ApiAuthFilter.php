<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = \Config\Services::response();
        
        // Check for Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            return $response->setJSON([
                'error' => 'Unauthorized',
                'message' => 'Authorization header is required'
            ])->setStatusCode(401);
        }

        // Extract token from header (Bearer token or API key)
        $token = null;
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } elseif (preg_match('/ApiKey\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            return $response->setJSON([
                'error' => 'Unauthorized',
                'message' => 'Invalid authorization header format'
            ])->setStatusCode(401);
        }

        // Validate token
        $user = $this->validateToken($token);
        
        if (!$user) {
            return $response->setJSON([
                'error' => 'Unauthorized',
                'message' => 'Invalid or expired token'
            ])->setStatusCode(401);
        }

        // Check if user is active
        if (!$user['is_active']) {
            return $response->setJSON([
                'error' => 'Forbidden',
                'message' => 'Account is inactive'
            ])->setStatusCode(403);
        }

        // Set user data in request for controllers to use
        $request->user = $user;
        
        // Store user info for logging
        $request->setGlobal('api_user_id', $user['id']);
        $request->setGlobal('api_user_role', $user['role']);

        // Check role-based permissions if specified
        if (!empty($arguments)) {
            $requiredRoles = is_array($arguments) ? $arguments : [$arguments];
            
            if (!in_array($user['role'], $requiredRoles)) {
                return $response->setJSON([
                    'error' => 'Forbidden',
                    'message' => 'Insufficient permissions'
                ])->setStatusCode(403);
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Log API usage
        $this->logApiUsage($request, $response);
        
        // Add CORS headers if needed
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        // Add rate limiting headers
        $response->setHeader('X-RateLimit-Limit', '1000');
        $response->setHeader('X-RateLimit-Remaining', '999');
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Validate API token
     */
    private function validateToken($token)
    {
        // Simple session-based validation (for web sessions)
        if ($this->validateSessionToken($token)) {
            return $this->getUserFromSession($token);
        }

        // JWT token validation (if implementing JWT)
        if ($this->validateJWTToken($token)) {
            return $this->getUserFromJWT($token);
        }

        // API key validation (for long-term API access)
        if ($this->validateApiKey($token)) {
            return $this->getUserFromApiKey($token);
        }

        return false;
    }

    /**
     * Validate session-based token
     */
    private function validateSessionToken($token)
    {
        // For web sessions, we can use the session ID or a generated token
        $session = \Config\Services::session();
        return $session->get('api_token') === $token;
    }

    /**
     * Get user from session
     */
    private function getUserFromSession($token)
    {
        $session = \Config\Services::session();
        
        if ($session->get('api_token') === $token && $session->get('is_logged_in')) {
            $userModel = new \App\Models\UserModel();
            return $userModel->find($session->get('user_id'));
        }

        return false;
    }

    /**
     * Validate JWT token (placeholder for JWT implementation)
     */
    private function validateJWTToken($token)
    {
        // TODO: Implement JWT validation
        // This would involve decoding the JWT, validating signature, checking expiration, etc.
        return false;
    }

    /**
     * Get user from JWT token
     */
    private function getUserFromJWT($token)
    {
        // TODO: Implement JWT user extraction
        return false;
    }

    /**
     * Validate API key
     */
    private function validateApiKey($apiKey)
    {
        // Check if API key exists in database
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('api_key', $apiKey)->first();
        
        return $user !== null;
    }

    /**
     * Get user from API key
     */
    private function getUserFromApiKey($apiKey)
    {
        $userModel = new \App\Models\UserModel();
        return $userModel->where('api_key', $apiKey)->first();
    }

    /**
     * Log API usage
     */
    private function logApiUsage($request, $response)
    {
        $userId = $request->getGlobal('api_user_id');
        $endpoint = $request->getUri()->getPath();
        $method = $request->getMethod();
        $statusCode = $response->getStatusCode();
        $userAgent = $request->getUserAgent();
        $ip = $request->getIPAddress();

        // Log to database or file
        log_message('info', "API Usage - User: {$userId}, Endpoint: {$method} {$endpoint}, Status: {$statusCode}, IP: {$ip}");
        
        // You could also log to a dedicated API usage table
        /*
        $apiLogModel = new \App\Models\ApiLogModel();
        $apiLogModel->insert([
            'user_id' => $userId,
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'user_agent' => $userAgent,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        */
    }
}