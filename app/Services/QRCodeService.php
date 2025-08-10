<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeService
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Generate QR Code for user registration
     *
     * @param int $registrationId
     * @param array $options
     * @return array
     */
    public function generateUserQRCode(int $registrationId, array $options = []): array
    {
        try {
            // Get registration data with related info
            $registration = $this->db->table('registrations r')
                ->select('r.*, u.first_name, u.last_name, u.email, e.title as event_title')
                ->join('users u', 'u.id = r.user_id')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.id', $registrationId)
                ->get()
                ->getRowArray();

            if (!$registration) {
                return [
                    'success' => false,
                    'message' => 'Registration not found'
                ];
            }

            // Check if QR already exists and is active
            $existingQR = $this->db->table('qr_codes')
                ->where('user_id', $registration['user_id'])
                ->where('status', 'active')
                ->get()
                ->getRowArray();

            if ($existingQR) {
                return [
                    'success' => true,
                    'message' => 'QR Code already exists',
                    'qr_code' => $existingQR
                ];
            }

            // Create QR data payload
            $qrData = [
                'registration_id' => $registrationId,
                'user_id' => $registration['user_id'],
                'event_id' => $registration['event_id'],
                'registration_type' => $registration['registration_type'],
                'payment_status' => $registration['payment_status'],
                'generated_at' => time(),
                'name' => trim($registration['first_name'] . ' ' . $registration['last_name']),
                'email' => $registration['email'],
                'event_title' => $registration['event_title']
            ];

            // Create hash for security
            $qrHash = hash('sha256', json_encode($qrData) . env('encryption.key', 'default_key'));
            $qrData['hash'] = $qrHash;

            // Generate QR Code image using simple approach
            $qrImageBase64 = $this->generateQRImage(json_encode($qrData));

            // Save to database
            $qrCodeData = [
                'user_id' => $registration['user_id'],
                'qr_data' => json_encode($qrData),
                'qr_image' => $qrImageBase64,
                'qr_hash' => $qrHash,
                'status' => 'active',
                'is_verified' => $registration['payment_status'] === 'paid',
                'expires_at' => $options['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+1 year')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $qrCodeId = $this->db->table('qr_codes')->insert($qrCodeData);

            if (!$qrCodeId) {
                return [
                    'success' => false,
                    'message' => 'Failed to save QR code to database'
                ];
            }

            // Get inserted QR code
            $qrCode = $this->db->table('qr_codes')->where('id', $this->db->insertID())->get()->getRowArray();

            // Update registration with QR reference
            $this->db->table('registrations')
                ->where('id', $registrationId)
                ->update(['qr_code' => $qrHash]);

            return [
                'success' => true,
                'message' => 'QR Code generated successfully',
                'qr_code' => $qrCode,
                'qr_data' => $qrData
            ];

        } catch (\Exception $e) {
            log_message('error', 'QR Code generation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'QR Code generation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user's QR Code
     *
     * @param int $userId
     * @param string $status
     * @return array
     */
    public function getUserQRCode(int $userId, string $status = 'active'): array
    {
        try {
            $qrCode = $this->db->table('qr_codes')
                ->where('user_id', $userId)
                ->where('status', $status)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();

            if (!$qrCode) {
                return [
                    'success' => false,
                    'message' => 'QR Code not found'
                ];
            }

            return [
                'success' => true,
                'qr_code' => $qrCode
            ];

        } catch (\Exception $e) {
            log_message('error', 'Failed to get QR Code: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get QR Code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate QR Code
     *
     * @param string $qrDataJson
     * @return array
     */
    public function validateQRCode(string $qrDataJson): array
    {
        try {
            $qrData = json_decode($qrDataJson, true);

            if (!$qrData || !isset($qrData['hash'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid QR Code format'
                ];
            }

            // Get QR from database
            $qrCode = $this->db->table('qr_codes')
                ->where('qr_hash', $qrData['hash'])
                ->get()
                ->getRowArray();

            if (!$qrCode) {
                return [
                    'success' => false,
                    'message' => 'QR Code not found in database'
                ];
            }

            // Check status
            if ($qrCode['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => 'QR Code is not active',
                    'status' => $qrCode['status']
                ];
            }

            // Check expiration
            if ($qrCode['expires_at'] && strtotime($qrCode['expires_at']) < time()) {
                return [
                    'success' => false,
                    'message' => 'QR Code has expired'
                ];
            }

            // Verify hash - use the stored hash from qr_codes table
            if ($qrData['hash'] !== $qrCode['qr_hash']) {
                return [
                    'success' => false,
                    'message' => 'QR Code hash verification failed'
                ];
            }

            // Get registration info
            $registration = $this->db->table('registrations r')
                ->select('r.*, u.first_name, u.last_name, u.email, e.title as event_title')
                ->join('users u', 'u.id = r.user_id')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.user_id', $qrCode['user_id'])
                ->get()
                ->getRowArray();

            return [
                'success' => true,
                'message' => 'QR Code is valid',
                'qr_code' => $qrCode,
                'qr_data' => $qrData,
                'registration' => $registration
            ];

        } catch (\Exception $e) {
            log_message('error', 'QR Code validation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'QR Code validation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update QR Code data
     *
     * @param int $qrCodeId
     * @param array $data
     * @return array
     */
    public function updateQRCodeData(int $qrCodeId, array $data): array
    {
        try {
            $updateData = [];
            
            // Allow only specific fields to be updated
            $allowedFields = ['status', 'is_verified', 'expires_at', 'last_scanned_at', 'scan_count'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if (empty($updateData)) {
                return [
                    'success' => false,
                    'message' => 'No valid fields to update'
                ];
            }

            $updateData['updated_at'] = date('Y-m-d H:i:s');

            $updated = $this->db->table('qr_codes')
                ->where('id', $qrCodeId)
                ->update($updateData);

            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Failed to update QR Code'
                ];
            }

            return [
                'success' => true,
                'message' => 'QR Code updated successfully'
            ];

        } catch (\Exception $e) {
            log_message('error', 'Failed to update QR Code: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update QR Code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete/Deactivate user's QR Code
     *
     * @param int $userId
     * @param bool $hardDelete
     * @return array
     */
    public function deleteUserQRCode(int $userId, bool $hardDelete = false): array
    {
        try {
            if ($hardDelete) {
                // Hard delete from database
                $deleted = $this->db->table('qr_codes')
                    ->where('user_id', $userId)
                    ->delete();
            } else {
                // Soft delete - mark as inactive
                $deleted = $this->db->table('qr_codes')
                    ->where('user_id', $userId)
                    ->update([
                        'status' => 'inactive',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'QR Code not found or already deleted'
                ];
            }

            // Remove QR reference from registration
            $this->db->table('registrations')
                ->where('user_id', $userId)
                ->update(['qr_code' => null]);

            return [
                'success' => true,
                'message' => 'QR Code deleted successfully'
            ];

        } catch (\Exception $e) {
            log_message('error', 'Failed to delete QR Code: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete QR Code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get QR Code by registration ID
     *
     * @param int $registrationId
     * @return array
     */
    public function getQRCodeByRegistration(int $registrationId): array
    {
        try {
            $registration = $this->db->table('registrations')
                ->where('id', $registrationId)
                ->get()
                ->getRowArray();

            if (!$registration) {
                return [
                    'success' => false,
                    'message' => 'Registration not found'
                ];
            }

            return $this->getUserQRCode($registration['user_id']);

        } catch (\Exception $e) {
            log_message('error', 'Failed to get QR Code by registration: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get QR Code: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Record QR scan
     *
     * @param int $qrCodeId
     * @param int $userId
     * @param string $scanType
     * @param int|null $scannerUserId
     * @param array $additionalData
     * @return array
     */
    public function recordQRScan(int $qrCodeId, int $userId, string $scanType = 'check_in', ?int $scannerUserId = null, array $additionalData = []): array
    {
        try {
            $scanData = [
                'qr_code_id' => $qrCodeId,
                'user_id' => $userId,
                'scan_type' => $scanType,
                'scanner_user_id' => $scannerUserId,
                'location' => $additionalData['location'] ?? null,
                'ip_address' => $additionalData['ip_address'] ?? null,
                'user_agent' => $additionalData['user_agent'] ?? null,
                'scan_result' => 'success',
                'notes' => $additionalData['notes'] ?? null,
                'scanned_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $scanId = $this->db->table('qr_scans')->insert($scanData);

            if (!$scanId) {
                return [
                    'success' => false,
                    'message' => 'Failed to record QR scan'
                ];
            }

            // Update QR code scan count and last scanned time
            $this->db->table('qr_codes')
                ->where('id', $qrCodeId)
                ->set('scan_count', 'scan_count + 1', false)
                ->set('last_scanned_at', date('Y-m-d H:i:s'))
                ->update();

            return [
                'success' => true,
                'message' => 'QR scan recorded successfully',
                'scan_id' => $this->db->insertID()
            ];

        } catch (\Exception $e) {
            log_message('error', 'Failed to record QR scan: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to record QR scan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code image
     * 
     * @param string $data
     * @return string Base64 encoded image
     */
    private function generateQRImage($data)
    {
        try {
            // QR code generation for v6.x using BaconQrCode (underlying library)
            $qrCode = new \Endroid\QrCode\QrCode($data);
            
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            
            // Set options directly on writer
            $result = $writer->write($qrCode);

            return base64_encode($result->getString());

        } catch (\Exception $e) {
            throw new \Exception('Failed to generate QR image: ' . $e->getMessage());
        }
    }

    /**
     * Scan QR Code for attendance/check-in
     *
     * @param string $qrHash
     * @param array $options
     * @return array
     */
    public function scanQRCode(string $qrHash, array $options = []): array
    {
        try {
            // Get QR code details
            $qrCode = $this->db->table('qr_codes')
                ->where('qr_hash', $qrHash)
                ->where('status', 'active')
                ->get()
                ->getRowArray();

            if (!$qrCode) {
                return [
                    'success' => false,
                    'message' => 'QR code not found or inactive'
                ];
            }

            // Check if QR code is expired
            if ($qrCode['expires_at'] && strtotime($qrCode['expires_at']) < time()) {
                return [
                    'success' => false,
                    'message' => 'QR code has expired'
                ];
            }

            // Record the scan
            $scanData = [
                'qr_code_id' => $qrCode['id'],
                'user_id' => $qrCode['user_id'], // QR code owner
                'scanner_user_id' => $options['scanner_user_id'] ?? $qrCode['user_id'],
                'scan_type' => $options['scan_type'] ?? 'attendance',
                'location' => $options['location'] ?? 'Conference Hall',
                'notes' => $options['notes'] ?? null,
                'ip_address' => $options['ip_address'] ?? null,
                'scan_result' => 'success',
                'scanned_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $scanId = $this->db->table('qr_scans')->insert($scanData);

            if (!$scanId) {
                return [
                    'success' => false,
                    'message' => 'Failed to record scan'
                ];
            }

            // Update QR code scan count and last scanned time
            $this->db->table('qr_codes')
                ->where('id', $qrCode['id'])
                ->update([
                    'scan_count' => $qrCode['scan_count'] + 1,
                    'last_scanned_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // If this is attendance scan, update registration attendance status
            if ($options['scan_type'] === 'attendance' || $options['scan_type'] === 'check_in') {
                $qrData = json_decode($qrCode['qr_data'], true);
                if (isset($qrData['registration_id'])) {
                    $this->db->table('registrations')
                        ->where('id', $qrData['registration_id'])
                        ->update([
                            'attended' => true,
                            'attendance_time' => date('Y-m-d H:i:s')
                        ]);
                }
            }

            return [
                'success' => true,
                'message' => 'QR code scanned successfully',
                'scan_data' => [
                    'scan_id' => $this->db->insertID(),
                    'scan_type' => $scanData['scan_type'],
                    'location' => $scanData['location'],
                    'scanned_at' => $scanData['scanned_at'],
                    'qr_owner' => $qrCode['user_id']
                ]
            ];

        } catch (\Exception $e) {
            log_message('error', 'QR Code scan failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'QR Code scan failed: ' . $e->getMessage()
            ];
        }
    }
}