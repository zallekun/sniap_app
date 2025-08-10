<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CertificateApiController extends BaseController
{
    /**
     * Get user's certificates
     * GET /api/v1/certificates
     */
    public function index()
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

            $db = \Config\Database::connect();
            $certificates = $db->table('certificates c')
                ->select('c.*, r.registration_type, e.title as event_title, e.event_date')
                ->join('registrations r', 'r.id = c.registration_id')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.user_id', $user['id'])
                ->where('c.certificate_number IS NOT NULL') // Only generated certificates
                ->orderBy('c.generated_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $certificates,
                'count' => count($certificates)
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get certificates: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Download certificate
     * GET /api/v1/certificates/{id}/download
     */
    public function download($certificateId)
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

            $db = \Config\Database::connect();
            $certificate = $db->table('certificates c')
                ->select('c.*, r.user_id, e.title as event_title, u.first_name, u.last_name')
                ->join('registrations r', 'r.id = c.registration_id')
                ->join('events e', 'e.id = r.event_id')
                ->join('users u', 'u.id = r.user_id')
                ->where('c.id', $certificateId)
                ->where('r.user_id', $user['id']) // Only user's own certificates
                ->where('c.certificate_number IS NOT NULL') // Only generated certificates
                ->get()
                ->getRowArray();

            if (!$certificate) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate not found or not available'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if file exists
            $filePath = WRITEPATH . '../uploads/certificates/' . $certificate['file_path'];
            
            if (!file_exists($filePath)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate file not found',
                    'suggested_action' => 'Contact administrator to regenerate certificate'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Log download
            $db->table('download_logs')->insert([
                'user_id' => $user['id'],
                'download_type' => 'all_participants', // Using existing enum value that makes sense
                'file_path' => $certificate['file_path'],
                'downloaded_at' => date('Y-m-d H:i:s')
            ]);

            // Return file
            return $this->response->download($filePath, null)->setFileName(
                'SNIA_Certificate_' . $certificate['first_name'] . '_' . $certificate['last_name'] . '.pdf'
            );

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to download certificate: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get certificate details
     * GET /api/v1/certificates/{id}
     */
    public function show($certificateId)
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

            $db = \Config\Database::connect();
            $certificate = $db->table('certificates c')
                ->select('c.*, r.registration_type, e.title as event_title, e.event_date, e.location')
                ->join('registrations r', 'r.id = c.registration_id')
                ->join('events e', 'e.id = r.event_id')
                ->where('c.id', $certificateId)
                ->where('r.user_id', $user['id'])
                ->get()
                ->getRowArray();

            if (!$certificate) {
                // Debug: check if certificate exists at all
                $debugCert = $db->table('certificates c')
                    ->select('c.*, r.user_id, r.registration_type')
                    ->join('registrations r', 'r.id = c.registration_id')
                    ->where('c.id', $certificateId)
                    ->get()
                    ->getRowArray();
                    
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate not found',
                    'debug' => [
                        'requested_certificate_id' => $certificateId,
                        'current_user_id' => $user['id'],
                        'certificate_exists' => $debugCert ? 'yes' : 'no',
                        'certificate_owner_id' => $debugCert['user_id'] ?? 'n/a'
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $certificate
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get certificate: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify certificate (public endpoint)
     * GET /api/v1/certificates/verify/{certificate_code}
     */
    public function verify($certificateCode)
    {
        try {
            $db = \Config\Database::connect();
            $certificate = $db->table('certificates c')
                ->select('c.certificate_number, c.generated_at, r.registration_type, 
                         e.title as event_title, e.event_date, 
                         u.first_name, u.last_name')
                ->join('registrations r', 'r.id = c.registration_id')
                ->join('events e', 'e.id = r.event_id')
                ->join('users u', 'u.id = r.user_id')
                ->where('c.certificate_number', $certificateCode)
                ->where('c.certificate_number IS NOT NULL')
                ->get()
                ->getRowArray();

            if (!$certificate) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate not found or invalid',
                    'is_valid' => false
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Certificate is valid',
                'is_valid' => true,
                'data' => [
                    'certificate_number' => $certificate['certificate_number'],
                    'participant_name' => $certificate['first_name'] . ' ' . $certificate['last_name'],
                    'event_title' => $certificate['event_title'],
                    'event_date' => $certificate['event_date'],
                    'participation_type' => $certificate['registration_type'],
                    'generated_date' => $certificate['generated_at']
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Certificate verification failed: ' . $e->getMessage(),
                'is_valid' => false
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Request certificate (for attended events)
     * POST /api/v1/certificates/request
     */
    public function request()
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

            // Get data from both POST and JSON
            $registrationId = $this->request->getPost('registration_id') 
                           ?? $this->request->getJSON(true)['registration_id'] ?? null;
            $certificateType = $this->request->getPost('certificate_type') 
                            ?? $this->request->getJSON(true)['certificate_type'] ?? 'participant';
            
            // Validate certificate type
            $validTypes = ['presenter', 'participant'];
            if (!in_array($certificateType, $validTypes)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid certificate type. Valid types: ' . implode(', ', $validTypes),
                    'received_type' => $certificateType
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
            
            if (!$registrationId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration ID is required',
                    'debug' => [
                        'post_data' => $this->request->getPost(),
                        'json_data' => $this->request->getJSON(true)
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Check if registration exists and belongs to user
            $registration = $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.event_date')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.id', $registrationId)
                ->where('r.user_id', $user['id'])
                ->where('r.attended', true) // Must have attended
                ->get()
                ->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found or event not attended'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if certificate already exists
            $existingCertificate = $db->table('certificates')
                ->where('registration_id', $registrationId)
                ->get()
                ->getRowArray();

            if ($existingCertificate) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate already exists for this registration',
                    'certificate_id' => $existingCertificate['id']
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Create certificate request
            $certificateNumber = 'SNIA-' . date('Y') . '-' . str_pad($registrationId, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($user['email']), 0, 4));
            
            $certificateData = [
                'registration_id' => $registrationId,
                'certificate_number' => $certificateNumber,
                'certificate_type' => $certificateType,
                'file_path' => '', // Empty path during request, will be updated when certificate is issued
                'generated_by' => $user['id'],
                'generated_at' => date('Y-m-d H:i:s')
            ];

            $certificateId = $db->table('certificates')->insert($certificateData);

            if ($certificateId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Certificate request submitted successfully',
                    'data' => [
                        'certificate_id' => $certificateId,
                        'certificate_number' => $certificateNumber,
                        'status' => 'requested',
                        'estimated_processing' => '1-3 business days'
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create certificate request'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Certificate request failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin: Issue certificate
     * PUT /api/v1/certificates/{id}/issue
     */
    public function issue($certificateId)
    {
        try {
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user || $user['role'] !== 'admin') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Admin access required'
                ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
            }

            $filePath = $this->request->getPost('file_path');
            
            if (!$filePath) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate file path is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Update certificate status
            $updated = $db->table('certificates')
                ->where('id', $certificateId)
                ->update([
                    'status' => 'issued',
                    'file_path' => $filePath,
                    'issued_at' => date('Y-m-d H:i:s'),
                    'issued_by' => $user['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($updated) {
                // Get certificate details for notification
                $certificate = $db->table('certificates c')
                    ->select('c.*, r.user_id, u.email, u.first_name, u.last_name, e.title as event_title')
                    ->join('registrations r', 'r.id = c.registration_id')
                    ->join('users u', 'u.id = r.user_id')
                    ->join('events e', 'e.id = r.event_id')
                    ->where('c.id', $certificateId)
                    ->get()
                    ->getRowArray();

                // Send notification email
                $emailService = new \App\Services\EmailService();
                $emailService->sendCertificateNotification(
                    $certificate['email'],
                    $certificate['first_name'] . ' ' . $certificate['last_name'],
                    $certificate['event_title'],
                    $certificate['certificate_number']
                );

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Certificate issued successfully',
                    'data' => [
                        'certificate_id' => $certificateId,
                        'status' => 'issued',
                        'file_path' => $filePath
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to issue certificate'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to issue certificate: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}