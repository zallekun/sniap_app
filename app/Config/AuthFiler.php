<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
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
        $session = \Config\Services::session();
        $uri = $request->getUri();
        
        // Check if user is logged in
        if (!$session->get('is_logged_in')) {
            // Check for remember me cookie
            if (!$this->checkRememberMe($session)) {
                // Store intended URL for redirect after login
                $session->set('redirect_url', (string)$uri);
                
                // Redirect to login with message
                return redirect()->to('/login')->with('error', 'Please login to access this page.');
            }
        }

        // If arguments are provided, check for specific roles
        if (!empty($arguments)) {
            $userRole = $session->get('user_role');
            $requiredRoles = is_array($arguments) ? $arguments : [$arguments];
            
            if (!in_array($userRole, $requiredRoles)) {
                // User doesn't have required role
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page.');
            }
        }

        // Check if user account is still active
        if (!$this->validateUserSession($session)) {
            // User account has been deactivated, logout
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Your account has been deactivated. Please contact administrator.');
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
        // Update last activity timestamp
        $session = \Config\Services::session();
        if ($session->get('is_logged_in')) {
            $session->set('last_activity', time());
        }
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Check remember me cookie and auto-login user
     */
    private function checkRememberMe($session)
    {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }

        $token = $_COOKIE['remember_token'];
        
        // Load user model
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('remember_token', $token)->first();

        if (!$user || !$user['is_active']) {
            // Invalid token or inactive user, clear cookie
            setcookie('remember_token', '', time() - 3600, '/');
            return false;
        }

        // Auto-login user
        $sessionData = [
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'user_name' => $user['full_name'],
            'user_role' => $user['role'],
            'is_logged_in' => true,
            'auto_login' => true
        ];

        $session->set($sessionData);

        // Update last login
        $userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Validate if user session is still valid
     */
    private function validateUserSession($session)
    {
        $userId = $session->get('user_id');
        
        if (!$userId) {
            return false;
        }

        // Check if user still exists and is active
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        return $user && $user['is_active'];
    }
}