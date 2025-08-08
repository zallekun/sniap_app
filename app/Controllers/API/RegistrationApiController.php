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
     * Register for an event
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

            $eventId = $jsonInput['event_id'];

            // Check if event exists - using direct query to avoid is_active issues
            $db = \Config\Database::connect();
            $event = $db->query('SELECT id, title, event_date, event_time, registration_fee, max_participants, registration_deadline FROM events WHERE id = ?', [$eventId])->getRowArray();

            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if registration deadline passed
            $now = date('Y-m-d H:i:s');
            if ($event['registration_deadline'] && $event['registration_deadline'] < $now) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration deadline has passed'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // For now, create a simple registration record using direct SQL
            // (since we don't have RegistrationModel properly set up yet)
            
            // Check if user already registered using model
            if ($this->registrationModel->isUserRegistered($user['id'], $eventId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Already registered for this event'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Create registration using model - try different enum values
            $registrationData = [
                'user_id' => $user['id'],
                'event_id' => $eventId,
                'registration_type' => 'audience', // Try 'audience' (common enum value)
                'registration_status' => ($event['registration_fee'] > 0) ? 'pending' : 'confirmed',
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
                    'user_id' => $user['id'],
                    'status' => $registrationData['status'],
                    'amount' => $registrationData['amount'],
                    'qr_code' => $registrationData['qr_code'],
                    'registration_date' => $registrationData['registration_date'],
                    'requires_payment' => ($registrationData['amount'] > 0)
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
                SELECT r.*, e.title as event_title, e.event_date, e.event_time, e.location 
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
     * Cancel registration
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

            // Get registration
            $db = \Config\Database::connect();
            $registration = $db->query('SELECT * FROM registrations WHERE id = ? AND user_id = ?', [$registrationId, $user['id']])->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            if ($registration['status'] === 'cancelled') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration is already cancelled'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Update registration status
            $db->query('UPDATE registrations SET status = ?, cancelled_at = ? WHERE id = ?', 
                ['cancelled', date('Y-m-d H:i:s'), $registrationId]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration cancelled successfully',
                'data' => [
                    'registration_id' => $registrationId,
                    'status' => 'cancelled',
                    'cancelled_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cancellation failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get registration stats for user
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

            // Get stats using direct SQL
            $db = \Config\Database::connect();
            
            $totalRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ?', [$user['id']])->getRow()->count;
            $confirmedRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND status = ?', [$user['id'], 'confirmed'])->getRow()->count;
            $pendingRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND status = ?', [$user['id'], 'pending'])->getRow()->count;
            $cancelledRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE user_id = ? AND status = ?', [$user['id'], 'cancelled'])->getRow()->count;

            $stats = [
                'total_registrations' => $totalRegistrations,
                'confirmed_registrations' => $confirmedRegistrations,
                'pending_registrations' => $pendingRegistrations,
                'cancelled_registrations' => $cancelledRegistrations
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
            'message' => 'Registration update feature not implemented yet'
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
            'message' => 'Certificate feature not implemented yet'
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }
}