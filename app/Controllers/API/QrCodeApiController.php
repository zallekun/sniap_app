<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\QRCodeService;

class QrCodeApiController extends BaseController
{
    protected $qrService;
    
    public function __construct()
    {
        $this->qrService = new QRCodeService();
    }

    /**
     * Generate QR Code for user
     * POST /api/v1/qr/generate
     */
    public function generate()
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

            $type = $this->request->getPost('type') ?? 'absensi'; // absensi, certificate, session_access
            $eventId = $this->request->getPost('event_id');
            
            // Get user's registration for the event
            $db = \Config\Database::connect();
            $registration = $db->table('registrations')
                ->where('user_id', $user['id'])
                ->where('event_id', $eventId ?: 1) // Default event
                ->get()
                ->getRowArray();
                
            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not registered for this event'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $result = $this->qrService->generateUserQRCode($registration['id'], [
                'type' => $type
            ]);

            if ($result['success']) {
                // Debug: log what we actually get from service
                log_message('debug', 'QR Service result: ' . json_encode($result));
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'QR code generated successfully',
                    'data' => [
                        'qr_code' => $result['qr_code'] ?? null,
                        'qr_data' => $result['qr_data'] ?? $result['qrData'] ?? null,
                        'debug_keys' => array_keys($result)
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message']
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get QR Code details
     * GET /api/v1/qr/{id}
     */
    public function show($id)
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
            $qrCode = $db->table('qr_codes qr')
                ->select('qr.*, u.first_name, u.last_name')
                ->join('users u', 'u.id = qr.user_id')
                ->where('qr.id', $id)
                ->where('qr.user_id', $user['id']) // Only user's own QR
                ->get()
                ->getRowArray();

            if (!$qrCode) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR code not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Decode QR data
            $qrCode['qr_data'] = json_decode($qrCode['qr_data'], true);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $qrCode
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get QR code: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get user's QR codes
     * GET /api/v1/qr/my-codes
     */
    public function myCodes()
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
            $qrCodes = $db->table('qr_codes qr')
                ->select('qr.*, r.event_id, e.title as event_title')
                ->join('registrations r', 'r.user_id = qr.user_id')
                ->join('events e', 'e.id = r.event_id')
                ->where('qr.user_id', $user['id'])
                ->orderBy('qr.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Decode QR data for each code
            foreach ($qrCodes as &$qr) {
                $qr['qr_data'] = json_decode($qr['qr_data'], true);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $qrCodes
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get QR codes: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Scan QR Code (Admin/Staff only)
     * POST /api/v1/qr/scan
     */
    public function scan()
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

            // Check if user is admin/staff or scanning their own QR
            // Get QR hash from multiple possible sources
            $qrHash = $this->request->getPost('qr_hash') 
                   ?? $this->request->getPost('hash')
                   ?? $this->request->getJSON(true)['qr_hash'] ?? null
                   ?? $this->request->getJSON(true)['hash'] ?? null;
                   
            $scanType = $this->request->getPost('scan_type') ?? 'attendance';
            
            if (!$qrHash) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR hash is required',
                    'debug' => [
                        'post_data' => $this->request->getPost(),
                        'json_data' => $this->request->getJSON(true)
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
            
            // Get QR code details to check ownership
            $db = \Config\Database::connect();
            
            // Debug: log what we're looking for
            log_message('debug', 'Looking for QR hash: ' . $qrHash);
            
            $qrCode = $db->table('qr_codes')->where('qr_hash', $qrHash)->get()->getRowArray();
            
            if (!$qrCode) {
                // Debug: show available QR codes
                $allQrCodes = $db->table('qr_codes')->select('id, user_id, qr_hash')->get()->getResultArray();
                
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid QR code',
                    'debug' => [
                        'searched_hash' => $qrHash,
                        'available_qr_codes' => $allQrCodes
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
            
            // For attendance scanning: Users can only scan their own QR code
            // For admin purposes: Admin/reviewer can scan any QR code
            if ($scanType === 'attendance' || $scanType === 'check_in') {
                // Self-attendance only
                if ($qrCode['user_id'] != $user['id']) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'You can only scan your own QR code for attendance'
                    ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }
            } else {
                // Admin scanning for other purposes
                if (!in_array($user['role'], ['admin', 'reviewer'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Insufficient permissions to scan QR codes'
                    ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }
            }

            // QR hash and scan type already retrieved above for permission check
            $location = $this->request->getPost('location') ?? 'Main Hall';
            $notes = $this->request->getPost('notes');

            if (!$qrHash) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR hash is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $result = $this->qrService->scanQRCode($qrHash, [
                'scanner_user_id' => $user['id'],
                'scan_type' => $scanType,
                'location' => $location,
                'notes' => $notes,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);

            if ($result['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['message'],
                    'data' => $result['scan_data'] ?? []
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message']
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to scan QR code: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get scan history
     * GET /api/v1/qr/scan-history
     */
    public function scanHistory()
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

            $limit = min(50, max(1, (int)($this->request->getGet('limit') ?? 20)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;

            $db = \Config\Database::connect();
            
            if ($user['role'] === 'admin') {
                // Admin can see all scans
                $scans = $db->table('qr_scans qs')
                    ->select('qs.*, qr.qr_hash, u.first_name, u.last_name, scanner.first_name as scanner_name')
                    ->join('qr_codes qr', 'qr.id = qs.qr_code_id')
                    ->join('users u', 'u.id = qr.user_id')
                    ->join('users scanner', 'scanner.id = qs.scanner_user_id', 'LEFT')
                    ->orderBy('qs.scanned_at', 'DESC')
                    ->limit($limit, $offset)
                    ->get()
                    ->getResultArray();
            } else {
                // Regular users see only their own scans
                $scans = $db->table('qr_scans qs')
                    ->select('qs.*, qr.qr_hash, scanner.first_name as scanner_name')
                    ->join('qr_codes qr', 'qr.id = qs.qr_code_id')
                    ->join('users scanner', 'scanner.id = qs.scanner_user_id', 'LEFT')
                    ->where('qr.user_id', $user['id']) // Direct filter on QR code owner
                    ->orderBy('qs.scanned_at', 'DESC')
                    ->limit($limit, $offset)
                    ->get()
                    ->getResultArray();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $scans,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get scan history: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}