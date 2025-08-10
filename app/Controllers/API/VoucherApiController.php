<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class VoucherApiController extends BaseController
{
    /**
     * Apply voucher to registration/payment
     * POST /api/v1/vouchers/apply
     */
    public function apply()
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
            $voucherCode = $this->request->getPost('voucher_code') 
                        ?? $this->request->getJSON(true)['voucher_code'] ?? null;
            $registrationId = $this->request->getPost('registration_id')
                           ?? $this->request->getJSON(true)['registration_id'] ?? null;
            
            if (!$voucherCode) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Voucher code is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            if (!$registrationId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Check if voucher exists and is valid
            $voucher = $db->table('vouchers')
                ->where('code', strtoupper($voucherCode))
                ->where('is_active', true)
                ->where('valid_from <=', date('Y-m-d H:i:s'))
                ->where('valid_until >=', date('Y-m-d H:i:s'))
                ->get()
                ->getRowArray();

            if (!$voucher) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid or expired voucher code'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if voucher usage limit reached
            $usageCount = $db->table('voucher_usage')
                ->where('voucher_id', $voucher['id'])
                ->countAllResults();

            if ($voucher['max_uses'] > 0 && $usageCount >= $voucher['max_uses']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Voucher usage limit reached'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if user already used this voucher
            $userUsage = $db->table('voucher_usage')
                ->where('voucher_id', $voucher['id'])
                ->where('user_id', $user['id'])
                ->get()
                ->getRowArray();

            if ($userUsage) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You have already used this voucher'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            // Get registration details
            $registration = $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.registration_fee')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.id', $registrationId)
                ->where('r.user_id', $user['id'])
                ->get()
                ->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Note: Vouchers are currently global (not event-specific)
            // Future enhancement: Add event_id column to vouchers table if needed

            // Calculate discount
            $originalAmount = $registration['registration_fee'];
            $discountAmount = 0;
            $finalAmount = $originalAmount;

            if ($voucher['discount_type'] === 'percentage') {
                $discountAmount = ($originalAmount * $voucher['discount_value']) / 100;
                $finalAmount = $originalAmount - $discountAmount;
            } elseif ($voucher['discount_type'] === 'fixed') {
                $discountAmount = min($voucher['discount_value'], $originalAmount);
                $finalAmount = $originalAmount - $discountAmount;
            } elseif ($voucher['discount_type'] === 'free') {
                $discountAmount = $originalAmount;
                $finalAmount = 0;
            }

            // Ensure final amount is not negative
            $finalAmount = max(0, $finalAmount);

            // Record voucher usage
            $db->table('voucher_usage')->insert([
                'voucher_id' => $voucher['id'],
                'user_id' => $user['id'],
                'registration_id' => $registrationId,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'used_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update registration with voucher discount
            $db->table('registrations')
                ->where('id', $registrationId)
                ->update([
                    'voucher_id' => $voucher['id'],
                    'original_fee' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'final_fee' => $finalAmount,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Voucher applied successfully',
                'data' => [
                    'voucher_code' => $voucher['code'],
                    'voucher_description' => $voucher['description'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_value' => $voucher['discount_value'],
                    'original_amount' => (float) $originalAmount,
                    'discount_amount' => (float) $discountAmount,
                    'final_amount' => (float) $finalAmount,
                    'savings' => (float) $discountAmount,
                    'is_free' => ($finalAmount == 0)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to apply voucher: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Check voucher validity
     * GET /api/v1/vouchers/check/{code}
     */
    public function check($voucherCode)
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
            
            // Get voucher details
            $voucher = $db->table('vouchers')
                ->where('code', strtoupper($voucherCode))
                ->get()
                ->getRowArray();

            if (!$voucher) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Voucher not found',
                    'is_valid' => false
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check validity conditions
            $now = date('Y-m-d');
            $isActive = $voucher['is_active'];
            $isWithinDateRange = ($voucher['start_date'] <= $now && $voucher['end_date'] >= $now);
            
            // Check usage limit
            $usageCount = $db->table('voucher_usage')
                ->where('voucher_id', $voucher['id'])
                ->countAllResults();
            $hasUsageLeft = ($voucher['max_uses'] == 0 || $usageCount < $voucher['max_uses']);

            // Check if user already used it
            $userUsage = $db->table('voucher_usage')
                ->where('voucher_id', $voucher['id'])
                ->where('user_id', $user['id'])
                ->get()
                ->getRowArray();
            $notUsedByUser = !$userUsage;

            $isValid = $isActive && $isWithinDateRange && $hasUsageLeft && $notUsedByUser;

            $response = [
                'status' => 'success',
                'is_valid' => $isValid,
                'voucher' => [
                    'code' => $voucher['code'],
                    'description' => $voucher['description'],
                    'discount_type' => $voucher['discount_type'],
                    'discount_value' => $voucher['discount_value'],
                    'start_date' => $voucher['start_date'],
                    'end_date' => $voucher['end_date'],
                    'max_uses' => $voucher['max_uses'],
                    'usage_count' => $usageCount,
                    'remaining_usage' => ($voucher['max_uses'] > 0) ? max(0, $voucher['max_uses'] - $usageCount) : 'unlimited'
                ],
                'validation_details' => [
                    'is_active' => $isActive,
                    'is_within_date_range' => $isWithinDateRange,
                    'has_usage_left' => $hasUsageLeft,
                    'not_used_by_user' => $notUsedByUser
                ]
            ];

            if (!$isValid) {
                $response['message'] = 'Voucher is not valid';
                if (!$isActive) $response['reason'] = 'Voucher is inactive';
                elseif (!$isWithinDateRange) $response['reason'] = 'Voucher is expired or not yet active';
                elseif (!$hasUsageLeft) $response['reason'] = 'Voucher usage limit reached';
                elseif (!$notUsedByUser) $response['reason'] = 'You have already used this voucher';
            }

            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to check voucher: ' . $e->getMessage(),
                'is_valid' => false
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get user's voucher usage history
     * GET /api/v1/vouchers/my-usage
     */
    public function myUsage()
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
            
            $usage = $db->table('voucher_usage vu')
                ->select('vu.*, v.code, v.description, v.discount_type, r.registration_type, e.title as event_title')
                ->join('vouchers v', 'v.id = vu.voucher_id')
                ->join('registrations r', 'r.id = vu.registration_id', 'LEFT')
                ->join('events e', 'e.id = r.event_id', 'LEFT')
                ->where('vu.user_id', $user['id'])
                ->orderBy('vu.used_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $usage,
                'total_savings' => array_sum(array_column($usage, 'discount_amount'))
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get voucher usage: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available vouchers (admin only)
     * GET /api/v1/vouchers
     */
    public function index()
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

            $db = \Config\Database::connect();
            
            $vouchers = $db->table('vouchers v')
                ->select('v.*')
                ->orderBy('v.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Add usage statistics
            foreach ($vouchers as &$voucher) {
                $usageCount = $db->table('voucher_usage')
                    ->where('voucher_id', $voucher['id'])
                    ->countAllResults();
                
                $totalSavings = $db->table('voucher_usage')
                    ->selectSum('discount_amount', 'total_savings')
                    ->where('voucher_id', $voucher['id'])
                    ->get()
                    ->getRow()
                    ->total_savings ?? 0;

                $voucher['usage_count'] = $usageCount;
                $voucher['total_savings'] = (float) $totalSavings;
                $voucher['remaining_usage'] = ($voucher['max_uses'] > 0) ? max(0, $voucher['max_uses'] - $usageCount) : 'unlimited';
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $vouchers
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get vouchers: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create voucher (admin only)
     * POST /api/v1/vouchers
     */
    public function create()
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

            // Get data from both POST and JSON
            $jsonData = $this->request->getJSON(true) ?? [];
            
            $data = [
                'code' => strtoupper($this->request->getPost('code') ?? $jsonData['code'] ?? ''),
                'description' => $this->request->getPost('description') ?? $jsonData['description'] ?? '',
                'discount_type' => $this->request->getPost('discount_type') ?? $jsonData['discount_type'] ?? '',
                'discount_value' => $this->request->getPost('discount_value') ?? $jsonData['discount_value'] ?? 0,
                'valid_from' => $this->request->getPost('valid_from') ?? $jsonData['valid_from'] ?? '',
                'valid_until' => $this->request->getPost('valid_until') ?? $jsonData['valid_until'] ?? '',
                'max_uses' => $this->request->getPost('max_uses') ?? $jsonData['max_uses'] ?? 0,
                'is_active' => true,
                'created_by' => $user['id']
            ];

            // Validation
            $requiredFields = ['code', 'description', 'discount_type', 'valid_from', 'valid_until'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
                }
            }

            if ($data['discount_type'] !== 'free' && empty($data['discount_value'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Discount value is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Check if code already exists
            $existing = $db->table('vouchers')->where('code', $data['code'])->get()->getRowArray();
            if ($existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Voucher code already exists'
                ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
            }

            $voucherId = $db->table('vouchers')->insert($data);

            if ($voucherId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Voucher created successfully',
                    'data' => array_merge($data, ['id' => $voucherId])
                ])->setStatusCode(ResponseInterface::HTTP_CREATED);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create voucher'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create voucher: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}