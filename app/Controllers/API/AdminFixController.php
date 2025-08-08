<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFixController extends BaseController
{
    /**
     * Check sync status between registrations and payments - FIXED ENUM VALUES
     * GET /api/v1/admin/check-sync-status
     */
    public function checkSyncStatus()
    {
        try {
            $db = \Config\Database::connect();
            
            // Fixed query with correct enum values
            $query = $db->query("
                SELECT DISTINCT
                    r.id as registration_id,
                    r.payment_status as reg_payment_status,
                    p.payment_status as actual_payment_status,
                    p.id as payment_id,
                    e.title as event_title,
                    u.email as user_email,
                    p.paid_at,
                    p.transaction_id
                FROM registrations r
                JOIN payments p ON r.id = p.registration_id  
                JOIN events e ON r.event_id = e.id
                JOIN users u ON r.user_id = u.id
                WHERE p.payment_status = 'success' 
                AND r.payment_status != 'paid'
                ORDER BY r.id
            ");
            
            $outOfSync = $query->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'out_of_sync_count' => count($outOfSync),
                    'out_of_sync_registrations' => $outOfSync,
                    'recommendation' => count($outOfSync) > 0 
                        ? 'Run /admin/fix-registration-status to sync statuses' 
                        : 'All registrations are in sync!'
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Check failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fix registration payment status based on actual payments - ENUM SAFE
     * POST /api/v1/admin/fix-registration-status
     */
    public function fixRegistrationStatus()
    {
        try {
            $db = \Config\Database::connect();
            
            // Step 1: Find out-of-sync registrations with correct enum values
            $outOfSyncQuery = $db->query("
                SELECT DISTINCT
                    r.id as registration_id,
                    r.payment_status as current_reg_status,
                    p.payment_status as actual_payment_status,
                    p.id as payment_id
                FROM registrations r
                JOIN payments p ON r.id = p.registration_id
                WHERE p.payment_status = 'success' 
                AND r.payment_status != 'paid'
            ");
            
            $outOfSync = $outOfSyncQuery->getResultArray();
            $fixResults = [];
            $fixed = 0;
            $failed = 0;
            
            // Step 2: Fix each out-of-sync registration
            foreach ($outOfSync as $record) {
                try {
                    $updateResult = $db->query("
                        UPDATE registrations 
                        SET payment_status = 'paid',
                            updated_at = NOW()
                        WHERE id = ? 
                        AND payment_status != 'paid'
                    ", [$record['registration_id']]);
                    
                    if ($updateResult) {
                        $fixResults[] = [
                            'registration_id' => $record['registration_id'],
                            'payment_id' => $record['payment_id'],
                            'fixed' => true,
                            'new_status' => 'paid',
                            'previous_status' => $record['current_reg_status']
                        ];
                        $fixed++;
                    } else {
                        $fixResults[] = [
                            'registration_id' => $record['registration_id'],
                            'payment_id' => $record['payment_id'],
                            'fixed' => false,
                            'error' => 'Update failed'
                        ];
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $fixResults[] = [
                        'registration_id' => $record['registration_id'],
                        'payment_id' => $record['payment_id'],
                        'fixed' => false,
                        'error' => $e->getMessage()
                    ];
                    $failed++;
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration status sync completed',
                'data' => [
                    'total_out_of_sync' => count($outOfSync),
                    'fix_results' => $fixResults,
                    'summary' => [
                        'fixed' => $fixed,
                        'failed' => $failed
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Fix failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get database enum values for debugging
     * GET /api/v1/admin/enum-values
     */
    public function getEnumValues()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all enum types and their values
            $enumQuery = $db->query("
                SELECT 
                    t.typname as enum_name,
                    array_agg(e.enumlabel ORDER BY e.enumsortorder) as enum_values
                FROM pg_type t 
                JOIN pg_enum e ON t.oid = e.enumtypid  
                WHERE t.typname LIKE '%_type' OR t.typname LIKE '%_status%'
                GROUP BY t.typname
                ORDER BY t.typname
            ");
            
            $enums = $enumQuery->getResultArray();
            
            // Format for easier reading
            $formattedEnums = [];
            foreach ($enums as $enum) {
                // Convert PostgreSQL array format to PHP array
                $values = str_replace(['{', '}'], '', $enum['enum_values']);
                $formattedEnums[$enum['enum_name']] = explode(',', $values);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'database_enums' => $formattedEnums,
                    'note' => 'These are the exact enum values available in your database'
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get enum values: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Complete system health check
     * GET /api/v1/admin/system-health
     */
    public function systemHealth()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get comprehensive system stats
            $stats = [];
            
            // Users count
            $usersQuery = $db->query("SELECT COUNT(*) as total FROM users");
            $stats['users'] = $usersQuery->getRow()->total;
            
            // Events count
            $eventsQuery = $db->query("SELECT COUNT(*) as total FROM events");
            $stats['events'] = $eventsQuery->getRow()->total;
            
            // Registrations count by status
            $regsQuery = $db->query("
                SELECT 
                    registration_status,
                    payment_status,
                    COUNT(*) as count
                FROM registrations 
                GROUP BY registration_status, payment_status
                ORDER BY registration_status, payment_status
            ");
            $stats['registrations'] = $regsQuery->getResultArray();
            
            // Payments count by status
            $paymentsQuery = $db->query("
                SELECT 
                    payment_status,
                    COUNT(*) as count,
                    SUM(final_amount) as total_amount
                FROM payments 
                GROUP BY payment_status
                ORDER BY payment_status
            ");
            $stats['payments'] = $paymentsQuery->getResultArray();
            
            // Sync status check
            $syncQuery = $db->query("
                SELECT COUNT(*) as out_of_sync_count
                FROM registrations r
                JOIN payments p ON r.id = p.registration_id
                WHERE p.payment_status = 'success' 
                AND r.payment_status != 'paid'
            ");
            $stats['sync_status'] = [
                'out_of_sync_count' => $syncQuery->getRow()->out_of_sync_count,
                'is_healthy' => $syncQuery->getRow()->out_of_sync_count == 0
            ];
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'system_health' => 'good',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'statistics' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Health check failed: ' . $e->getMessage(),
                'system_health' => 'error'
            ]);
        }
    }
}