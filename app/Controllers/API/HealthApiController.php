<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HealthApiController extends BaseController
{
    /**
     * API Health Check
     * GET /api/v1/health
     */
    public function check()
    {
        try {
            // Check database connection
            $db = \Config\Database::connect();
            $dbConnected = $db->connect() !== false;
            
            // Check writable directory
            $writableDir = WRITEPATH;
            $writableExists = is_dir($writableDir) && is_writable($writableDir);
            
            // Get system info
            $systemInfo = [
                'php_version' => PHP_VERSION,
                'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
                'server_time' => date('Y-m-d H:i:s T'),
                'timezone' => date_default_timezone_get(),
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                'memory_limit' => ini_get('memory_limit')
            ];

            // Overall health status
            $isHealthy = $dbConnected && $writableExists;

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $isHealthy ? 'API is running healthy' : 'API has issues',
                'data' => [
                    'healthy' => $isHealthy,
                    'timestamp' => date('Y-m-d H:i:s T'),
                    'checks' => [
                        'database' => $dbConnected,
                        'writable_directory' => $writableExists,
                        'php_version_ok' => version_compare(PHP_VERSION, '7.4.0', '>=')
                    ],
                    'system' => $systemInfo
                ]
            ])->setStatusCode($isHealthy ? ResponseInterface::HTTP_OK : ResponseInterface::HTTP_SERVICE_UNAVAILABLE);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Health check failed',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s T')
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API Version Information
     * GET /api/v1/version
     */
    public function version()
    {
        try {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'api_version' => '1.0.0',
                    'app_name' => 'SNIA Conference Management System',
                    'app_version' => '1.0.0',
                    'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
                    'php_version' => PHP_VERSION,
                    'release_date' => '2024-01-15',
                    'environment' => ENVIRONMENT,
                    'endpoints' => [
                        'authentication' => '/api/v1/auth/*',
                        'events' => '/api/v1/events/*',
                        'registrations' => '/api/v1/registrations/*',
                        'payments' => '/api/v1/payments/*',
                        'health' => '/api/v1/health',
                        'documentation' => '/api/v1/docs'
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get version info',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API Documentation/Endpoints List
     * GET /api/v1/docs
     */
    public function documentation()
    {
        try {
            $endpoints = [
                'Authentication' => [
                    'POST /api/v1/auth/register' => 'Register new user',
                    'POST /api/v1/auth/login' => 'User login (returns JWT token)',
                    'POST /api/v1/auth/verify' => 'Verify JWT token',
                    'POST /api/v1/auth/refresh' => 'Refresh JWT token',
                    'POST /api/v1/auth/logout' => 'User logout',
                    'GET /api/v1/auth/profile' => 'Get user profile (requires JWT)'
                ],
                'Events (Public)' => [
                    'GET /api/v1/events' => 'List all events (with pagination & filters)',
                    'GET /api/v1/events/{id}' => 'Get event details',
                    'GET /api/v1/events/search' => 'Search events (?q=keyword)',
                    'GET /api/v1/events/{id}/schedule' => 'Get event schedule',
                    'GET /api/v1/events/upcoming' => 'Get upcoming events',
                    'GET /api/v1/events/categories' => 'Get event categories'
                ],
                'Registrations (Protected)' => [
                    'GET /api/v1/registrations' => 'Get user registrations',
                    'POST /api/v1/registrations' => 'Register for event',
                    'GET /api/v1/registrations/{id}' => 'Get registration details',
                    'PUT /api/v1/registrations/{id}' => 'Update registration',
                    'DELETE /api/v1/registrations/{id}' => 'Cancel registration',
                    'GET /api/v1/registrations/{id}/certificate' => 'Download certificate',
                    'GET /api/v1/registrations/stats' => 'Get registration statistics'
                ],
                'Payments (Protected)' => [
                    'GET /api/v1/payments' => 'Get payment history',
                    'POST /api/v1/payments' => 'Create payment',
                    'GET /api/v1/payments/{id}' => 'Get payment details',
                    'POST /api/v1/payments/{id}/verify' => 'Verify payment status',
                    'GET /api/v1/payments/{id}/invoice' => 'Download invoice',
                    'GET /api/v1/payments/stats' => 'Get payment statistics'
                ],
                'System' => [
                    'GET /api/v1/health' => 'API health check',
                    'GET /api/v1/version' => 'API version information',
                    'GET /api/v1/docs' => 'API documentation (this endpoint)'
                ],
                'Webhooks' => [
                    'POST /api/v1/webhooks/midtrans' => 'Midtrans payment webhook'
                ]
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'api_name' => 'SNIA Conference Management System API',
                    'version' => '1.0.0',
                    'base_url' => base_url('api/v1'),
                    'authentication' => 'JWT Bearer Token',
                    'content_type' => 'application/json',
                    'endpoints' => $endpoints,
                    'notes' => [
                        'Protected endpoints require JWT token in Authorization header',
                        'Format: Authorization: Bearer {your_jwt_token}',
                        'Get JWT token by calling POST /api/v1/auth/login',
                        'All responses are in JSON format',
                        'HTTP status codes follow RESTful conventions'
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get documentation',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Database Connection Test
     * GET /api/v1/health/database
     */
    public function database()
    {
        try {
            $db = \Config\Database::connect();
            
            // Test basic query
            $query = $db->query("SELECT 1 as test");
            $result = $query->getRow();
            
            // Get database info
            $dbInfo = [
                'driver' => $db->DBDriver,
                'database' => $db->getDatabase(),
                'hostname' => $db->hostname,
                'platform' => $db->getPlatform(),
                'version' => $db->getVersion()
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Database connection is healthy',
                'data' => [
                    'connected' => true,
                    'test_query_result' => $result->test ?? null,
                    'database_info' => $dbInfo,
                    'timestamp' => date('Y-m-d H:i:s T')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s T')
            ])->setStatusCode(ResponseInterface::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * JWT Configuration Test
     * GET /api/v1/health/jwt
     */
    public function jwt()
    {
        try {
            $jwtKey = getenv('JWT_SECRET_KEY') ?: 'snia_conference_jwt_secret_key_2024';
            
            $hasJwtLib = class_exists('Firebase\JWT\JWT');
            $hasSecretKey = !empty($jwtKey) && $jwtKey !== 'snia_conference_jwt_secret_key_2024';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'JWT configuration check',
                'data' => [
                    'jwt_library_installed' => $hasJwtLib,
                    'secret_key_configured' => $hasSecretKey,
                    'secret_key_length' => strlen($jwtKey),
                    'recommendations' => [
                        'jwt_library' => $hasJwtLib ? 'OK' : 'Run: composer require firebase/php-jwt',
                        'secret_key' => $hasSecretKey ? 'OK' : 'Set JWT_SECRET_KEY in .env file with strong random key'
                    ],
                    'timestamp' => date('Y-m-d H:i:s T')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'JWT configuration check failed',
                'error' => $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}