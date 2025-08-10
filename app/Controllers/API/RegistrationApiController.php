<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\EventModel;
use CodeIgniter\HTTP\ResponseInterface;

class RegistrationApiController extends BaseController
{
    protected $eventModel;
    protected $registrationModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->registrationModel = new \App\Models\RegistrationModel();
    }

    /**
     * Get user's registrations
     * GET /api/v1/registrations
     */
    public function index()
    {
        try {
            // Get authenticated user from service request
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get user registrations using the model
            $registrations = $this->registrationModel->getUserRegistrations($user['id']);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $registrations,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total_items' => count($registrations),
                    'total_pages' => 1,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch registrations: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register for event as presenter/attendee
     * POST /api/v1/registrations
     */
    public function register()
    {
        try {
            // Get authenticated user from service request
            $request = service('request');
            $userAuth = $request->api_user ?? null;
            
            if (!$userAuth) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
            
            // Get complete user data from database
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $userAuth['id'])->get()->getRowArray();
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $eventId = $this->request->getPost('event_id') ?: 1; // Default event
            $registrationType = $this->request->getPost('registration_type') ?: 'presenter'; // presenter, attendee
            
            // Check if already registered
            $existingRegistration = $db->table('registrations')
                ->where('user_id', $user['id'])
                ->where('event_id', $eventId)
                ->get()
                ->getRowArray();
                
            if ($existingRegistration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Already registered for this event',
                    'data' => $existingRegistration
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Get event details
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check registration deadline
            if ($event['registration_deadline'] && date('Y-m-d') > $event['registration_deadline']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration deadline has passed'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Determine registration fee based on type and user role
            $registrationFee = $this->calculateRegistrationFee($registrationType, $user['role'], $event);

            // Create registration
            $registrationData = [
                'user_id' => $user['id'],
                'event_id' => $eventId,
                'registration_type' => $registrationType,
                'registration_status' => 'pending',
                'payment_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $registrationId = $db->table('registrations')->insert($registrationData);

            if ($registrationId) {
                // Send confirmation email (skip for now to avoid email issues)
                try {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendRegistrationConfirmation(
                        $user['email'],
                        $user['first_name'] . ' ' . $user['last_name'],
                        $event['title'] ?? 'Conference Event',
                        $registrationType,
                        $registrationFee,
                        1
                    );
                } catch (\Exception $e) {
                    // Log email error but don't fail registration
                    log_message('error', 'Registration email failed: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Registration successful',
                    'data' => [
                        'registration_id' => $registrationId,
                        'event_title' => $event['title'],
                        'registration_type' => $registrationType,
                        'event_registration_fee' => $registrationFee,
                        'payment_status' => 'pending'
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create registration'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register for an event - ULTRA CLEAN VERSION
     * POST /api/v1/registrations (alias)
     */
    public function create()
    {
        return $this->register();
    }

    /**
     * Get specific registration details
     * GET /api/v1/registrations/{id}
     */
    public function show($registrationId)
    {
        try {
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get registration using direct SQL
            $db = \Config\Database::connect();
            $registration = $db->query('
                SELECT r.*, e.title as event_title, e.event_date, e.location 
                FROM registrations r 
                JOIN events e ON e.id = r.event_id 
                WHERE r.id = ? AND r.user_id = ?', 
                [$registrationId, $user['id']]
            )->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $registration
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch registration: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel registration - ULTIMATE SAFE VERSION
     * DELETE /api/v1/registrations/{id}
     */
    public function cancel($registrationId)
    {
        try {
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get registration using direct SQL
            $db = \Config\Database::connect();
            $registration = $db->query('
                SELECT * FROM registrations 
                WHERE id = ? AND user_id = ?', 
                [$registrationId, $user['id']]
            )->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Since we can't update to 'cancelled', we'll use a different approach
            // Option 1: Try to set a flag column (if exists)
            // Option 2: Delete the record entirely
            // Option 3: Set status to a "disabled" state
            
            $cancellationApproaches = [
                // Try approach 1: Set attended = false and add note
                [
                    'sql' => 'UPDATE registrations SET attended = ? WHERE id = ?',
                    'params' => [false, $registrationId],
                    'message' => 'Registration marked as not attending'
                ],
                // Try approach 2: Just delete the record (if business logic allows)
                [
                    'sql' => 'DELETE FROM registrations WHERE id = ?',
                    'params' => [$registrationId],
                    'message' => 'Registration deleted successfully'
                ]
            ];

            $cancellationSuccess = false;
            $successMessage = '';
            $method = '';

            // First, check if user already has payments for this registration
            $hasPayments = $db->query('SELECT COUNT(*) as count FROM payments WHERE registration_id = ?', [$registrationId])->getRow()->count ?? 0;

            if ($hasPayments > 0) {
                // If there are payments, just mark as not attending (soft cancel)
                $updated = $db->query('UPDATE registrations SET attended = ? WHERE id = ?', [false, $registrationId]);
                
                if ($updated) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Registration cancelled (marked as not attending)',
                        'data' => [
                            'registration_id' => $registrationId,
                            'attended' => false,
                            'cancelled_at' => date('Y-m-d H:i:s'),
                            'method' => 'soft_cancel',
                            'note' => 'Registration marked as not attending due to existing payments'
                        ]
                    ])->setStatusCode(ResponseInterface::HTTP_OK);
                }
            } else {
                // If no payments, safe to delete the registration entirely
                $deleted = $db->query('DELETE FROM registrations WHERE id = ?', [$registrationId]);
                
                if ($deleted) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Registration cancelled successfully',
                        'data' => [
                            'registration_id' => $registrationId,
                            'cancelled_at' => date('Y-m-d H:i:s'),
                            'method' => 'hard_delete',
                            'note' => 'Registration deleted as no payments were made'
                        ]
                    ])->setStatusCode(ResponseInterface::HTTP_OK);
                }
            }

            // If all approaches fail
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unable to cancel registration due to database constraints',
                'data' => [
                    'registration_id' => $registrationId,
                    'suggestion' => 'Contact administrator for manual cancellation',
                    'current_status' => $registration['registration_status'] ?? 'unknown'
                ]
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cancellation failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get registration stats for user - ENUM SAFE VERSION
     * GET /api/v1/registrations/stats
     */
    public function stats()
    {
        try {
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get stats using direct SQL with SAFE enum values
            $db = \Config\Database::connect();
            
            $totalRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ?', [$user['id']])->getRow()->count;
            
            $pendingRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND registration_status = ?', [$user['id'], 'pending'])->getRow()->count;
            
            $approvedRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND registration_status = ?', [$user['id'], 'approved'])->getRow()->count;
            $rejectedRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND registration_status = ?', [$user['id'], 'rejected'])->getRow()->count;
            
            // Get payment-based "completed" registrations (paid registrations)
            $paidRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND payment_status = ?', [$user['id'], 'paid'])->getRow()->count ?? 0;
            
            // Calculate active registrations (pending + approved)
            $activeRegistrations = $pendingRegistrations + $approvedRegistrations;

            $stats = [
                'total_registrations' => (int)$totalRegistrations,
                'pending_registrations' => (int)$pendingRegistrations,
                'approved_registrations' => (int)$approvedRegistrations,
                'rejected_registrations' => (int)$rejectedRegistrations,
                'active_registrations' => (int)$activeRegistrations,
                'paid_registrations' => (int)$paidRegistrations,
                'breakdown' => [
                    'pending_payment' => (int)$pendingRegistrations,
                    'payment_completed' => (int)$paidRegistrations,
                    'approved' => (int)$approvedRegistrations,
                    'rejected' => (int)$rejectedRegistrations
                ]
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update registration (placeholder)
     * PUT /api/v1/registrations/{id}
     */
    public function update($registrationId)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Registration update feature not implemented yet',
            'data' => [
                'registration_id' => $registrationId,
                'available_updates' => ['registration_type', 'special_requirements']
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    /**
     * Download certificate (placeholder)
     * GET /api/v1/registrations/{id}/certificate
     */
    public function certificate($registrationId)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Certificate feature not implemented yet',
            'data' => [
                'registration_id' => $registrationId,
                'certificate_url' => base_url("certificates/{$registrationId}.pdf"),
                'available_after' => 'Event completion and attendance confirmation'
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    /**
     * Calculate registration fee based on type and user role
     */
    private function calculateRegistrationFee($registrationType, $userRole, $event)
    {
        // Base fees from event or defaults
        $baseFees = [
            'presenter' => $event['presenter_fee'] ?? 500000,
            'attendee' => $event['attendee_fee'] ?? 100000,
        ];

        $fee = $baseFees[$registrationType] ?? $baseFees['attendee'];

        // Apply discounts based on user role
        if ($userRole === 'student') {
            $fee *= 0.8; // 20% discount for students
        }

        return (int) $fee;
    }
}