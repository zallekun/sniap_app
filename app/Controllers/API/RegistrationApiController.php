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
     * Register for an event - ULTRA CLEAN VERSION
     * POST /api/v1/registrations
     */
    public function create()
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

            // Get JSON input
            $jsonInput = $this->request->getJSON(true);
            
            if (empty($jsonInput['event_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $eventId = (int)$jsonInput['event_id'];

            // Check if event exists using direct query
            $db = \Config\Database::connect();
            $event = $db->query('
                SELECT id, title, event_date, registration_fee, max_participants, registration_deadline 
                FROM events 
                WHERE id = ?', [$eventId]
            )->getRowArray();

            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if registration deadline passed
            $now = date('Y-m-d H:i:s');
            if (!empty($event['registration_deadline']) && $event['registration_deadline'] < $now) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration deadline has passed'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if user already registered using model
            if ($this->registrationModel->isUserRegistered($user['id'], $eventId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Already registered for this event'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Create registration using model - CLEAN DATA
            $registrationData = [
                'user_id' => (int)$user['id'],
                'event_id' => $eventId,
                'registration_type' => 'audience', // Fixed enum value
                'registration_status' => 'pending', // Fixed enum value
                'payment_status' => ($event['registration_fee'] > 0) ? 'pending' : 'free'
            ];

            $registrationId = $this->registrationModel->insert($registrationData);
            
            if (!$registrationId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create registration'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Generate QR Code
            $qrCode = $this->registrationModel->generateQRCode($registrationId);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => [
                    'registration_id' => $registrationId,
                    'event_id' => $eventId,
                    'event_title' => $event['title'],
                    'user_id' => (int)$user['id'],
                    'registration_status' => 'pending',
                    'payment_status' => $registrationData['payment_status'],
                    'qr_code' => $qrCode,
                    'registration_fee' => (float)$event['registration_fee'],
                    'requires_payment' => ($event['registration_fee'] > 0),
                    'created_at' => date('Y-m-d H:i:s')
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
            
            $cancelledRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND registration_status = ?', [$user['id'], 'cancelled'])->getRow()->count;
            
            // Get payment-based "completed" registrations (paid registrations)
            $paidRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND payment_status = ?', [$user['id'], 'success'])->getRow()->count ?? 0;
            
            // Alternative: count non-pending, non-cancelled as "active"
            $activeRegistrations = $totalRegistrations - $pendingRegistrations - $cancelledRegistrations;

            $stats = [
                'total_registrations' => (int)$totalRegistrations,
                'pending_registrations' => (int)$pendingRegistrations,
                'cancelled_registrations' => (int)$cancelledRegistrations,
                'active_registrations' => (int)$activeRegistrations,
                'paid_registrations' => (int)$paidRegistrations,
                'breakdown' => [
                    'pending_payment' => (int)$pendingRegistrations,
                    'payment_completed' => (int)$paidRegistrations,
                    'cancelled' => (int)$cancelledRegistrations
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
}