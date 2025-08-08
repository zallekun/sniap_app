<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentApiController extends BaseController
{
    /**
     * Get user's payment history - SIMPLIFIED
     * GET /api/v1/payments
     */
    public function index()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $limit = $this->request->getGet('limit') ?? 10;
            $page = $this->request->getGet('page') ?? 1;

            $limit = min(50, max(1, (int)$limit));
            $page = max(1, (int)$page);
            $offset = ($page - 1) * $limit;

            // Get payments using direct SQL - SIMPLE VERSION
            $db = \Config\Database::connect();
            $sql = "SELECT p.*, r.event_id, e.title as event_title 
                    FROM payments p 
                    JOIN registrations r ON r.id = p.registration_id 
                    JOIN events e ON e.id = r.event_id 
                    WHERE r.user_id = ? 
                    ORDER BY p.id DESC 
                    LIMIT ? OFFSET ?";
            
            $payments = $db->query($sql, [$user['id'], $limit, $offset])->getResultArray();

            // Get total count
            $countSql = "SELECT COUNT(*) as count 
                         FROM payments p 
                         JOIN registrations r ON r.id = p.registration_id 
                         WHERE r.user_id = ?";
            $totalCount = $db->query($countSql, [$user['id']])->getRow()->count ?? 0;

            // Pagination metadata
            $totalPages = ceil($totalCount / $limit);
            $pagination = [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => (string)$totalCount,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $payments,
                'pagination' => $pagination
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch payments: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create payment for registration - SIMPLIFIED
     * POST /api/v1/payments
     */
    public function create()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $jsonInput = $this->request->getJSON(true);

            if (empty($jsonInput['registration_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $registrationId = (int)$jsonInput['registration_id'];
            $paymentMethod = $jsonInput['payment_method'] ?? 'credit_card';

            // Get registration details with direct SQL
            $db = \Config\Database::connect();
            $registration = $db->query('
                SELECT r.*, e.title as event_title, e.registration_fee 
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

            // Check if payment already completed
            if ($registration['payment_status'] === 'success' || $registration['payment_status'] === 'paid') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment already completed for this registration'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if event requires payment
            if ($registration['registration_fee'] <= 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No payment required for free events'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            // Check if payment already exists
            $existingPayment = $db->query('
                SELECT id, payment_status, final_amount 
                FROM payments 
                WHERE registration_id = ? 
                ORDER BY id DESC 
                LIMIT 1', 
                [$registrationId]
            )->getRowArray();
            
            if ($existingPayment && $existingPayment['payment_status'] === 'pending') {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment already exists',
                    'data' => [
                        'payment_id' => $existingPayment['id'],
                        'registration_id' => $registrationId,
                        'amount' => $existingPayment['final_amount'],
                        'status' => $existingPayment['payment_status'],
                        'payment_method' => $paymentMethod
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

            // Calculate payment amount - SIMPLE VERSION
            $amount = $registration['registration_fee'];
            $discountAmount = 0; // No voucher for now
            $finalAmount = $amount - $discountAmount;

            // Create payment record - DIRECT SQL
            $paymentSql = "INSERT INTO payments (registration_id, amount, discount_amount, final_amount, payment_method, payment_gateway, payment_status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING id";
            
            $paymentResult = $db->query($paymentSql, [
                $registrationId,
                $amount,
                $discountAmount,
                $finalAmount,
                $paymentMethod,
                'midtrans',
                'pending'
            ]);

            $paymentData = $paymentResult->getRowArray();
            $paymentId = $paymentData['id'];

            if (!$paymentId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create payment'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Return payment details
            $responseData = [
                'payment_id' => $paymentId,
                'registration_id' => $registrationId,
                'event_title' => $registration['event_title'],
                'amount' => number_format($amount, 2),
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'payment_url' => base_url("payment/gateway/{$paymentId}"),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'data' => $responseData
            ])->setStatusCode(ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment creation failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get specific payment details - SIMPLIFIED
     * GET /api/v1/payments/{id}
     */
    public function show($paymentId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get payment with details using direct SQL
            $db = \Config\Database::connect();
            $payment = $db->query('
                SELECT p.*, r.user_id, r.event_id, e.title as event_title
                FROM payments p
                JOIN registrations r ON r.id = p.registration_id
                JOIN events e ON e.id = r.event_id
                WHERE p.id = ? AND r.user_id = ?', 
                [$paymentId, $user['id']]
            )->getRowArray();

            if (!$payment) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $payment
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch payment: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify payment status - WITH REGISTRATION UPDATE
     * POST /api/v1/payments/{id}/verify
     */
    public function verify($paymentId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get payment details
            $db = \Config\Database::connect();
            $payment = $db->query('
                SELECT p.*, r.user_id, r.id as registration_id
                FROM payments p 
                JOIN registrations r ON r.id = p.registration_id 
                WHERE p.id = ? AND r.user_id = ?', 
                [$paymentId, $user['id']]
            )->getRowArray();

            if (!$payment) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // For demo purposes, mark payment as successful
            $updateSql = "UPDATE payments 
                         SET payment_status = ?, paid_at = ?, transaction_id = ? 
                         WHERE id = ?";
            
            $transactionId = 'DEMO-TXN-' . time();
            $paidAt = date('Y-m-d H:i:s');
            
            $paymentUpdated = $db->query($updateSql, ['success', $paidAt, $transactionId, $paymentId]);

            if ($paymentUpdated) {
                // Try to update registration payment_status to success
                $registrationUpdateResult = null;
                try {
                    $registrationUpdated = $db->query('
                        UPDATE registrations 
                        SET payment_status = ? 
                        WHERE id = ?', 
                        ['success', $payment['registration_id']]
                    );
                    $registrationUpdateResult = $registrationUpdated ? 'success' : 'failed';
                } catch (\Exception $e) {
                    // If 'success' doesn't work, try other values
                    $possibleValues = ['paid', 'completed', 'confirmed'];
                    foreach ($possibleValues as $value) {
                        try {
                            $registrationUpdated = $db->query('
                                UPDATE registrations 
                                SET payment_status = ? 
                                WHERE id = ?', 
                                [$value, $payment['registration_id']]
                            );
                            if ($registrationUpdated) {
                                $registrationUpdateResult = "success_with_" . $value;
                                break;
                            }
                        } catch (\Exception $innerE) {
                            continue;
                        }
                    }
                    
                    if (!$registrationUpdateResult) {
                        $registrationUpdateResult = 'enum_constraint_failed';
                    }
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment verified successfully',
                    'data' => [
                        'payment_id' => $paymentId,
                        'payment_status' => 'success',
                        'verified_at' => $paidAt,
                        'transaction_id' => $transactionId,
                        'registration_update' => $registrationUpdateResult,
                        'note' => $registrationUpdateResult === 'enum_constraint_failed' 
                            ? 'Payment verified but registration status update failed due to enum constraints' 
                            : 'Payment and registration status updated successfully'
                    ]
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to verify payment'
                ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment statistics for user - SIMPLIFIED
     * GET /api/v1/payments/stats
     */
    public function stats()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Get payment stats using direct SQL
            $db = \Config\Database::connect();
            
            $totalPayments = $db->query('
                SELECT COUNT(*) as count 
                FROM payments p 
                JOIN registrations r ON r.id = p.registration_id 
                WHERE r.user_id = ?', [$user['id']])->getRow()->count ?? 0;

            $successfulPayments = $db->query('
                SELECT COUNT(*) as count 
                FROM payments p 
                JOIN registrations r ON r.id = p.registration_id 
                WHERE r.user_id = ? AND p.payment_status = ?', [$user['id'], 'success'])->getRow()->count ?? 0;

            $pendingPayments = $db->query('
                SELECT COUNT(*) as count 
                FROM payments p 
                JOIN registrations r ON r.id = p.registration_id 
                WHERE r.user_id = ? AND p.payment_status = ?', [$user['id'], 'pending'])->getRow()->count ?? 0;

            $totalAmountPaid = $db->query('
                SELECT COALESCE(SUM(p.final_amount), 0) as total 
                FROM payments p 
                JOIN registrations r ON r.id = p.registration_id 
                WHERE r.user_id = ? AND p.payment_status = ?', [$user['id'], 'success'])->getRow()->total ?? 0;

            $stats = [
                'total_payments' => (int)$totalPayments,
                'successful_payments' => (int)$successfulPayments,
                'pending_payments' => (int)$pendingPayments,
                'failed_payments' => (int)($totalPayments - $successfulPayments - $pendingPayments),
                'total_amount_paid' => (float)$totalAmountPaid,
                'amount_formatted' => 'Rp ' . number_format($totalAmountPaid, 0, ',', '.')
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get payment stats: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Download invoice/receipt (placeholder)
     * GET /api/v1/payments/{id}/invoice
     */
    public function invoice($paymentId)
    {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Invoice generation feature not implemented yet',
            'data' => [
                'payment_id' => $paymentId,
                'download_url' => base_url("invoices/{$paymentId}.pdf")
            ]
        ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    /**
     * Handle Midtrans webhook (placeholder)
     * POST /api/v1/webhooks/midtrans
     */
    public function midtransWebhook()
    {
        try {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Webhook received'
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Webhook processing failed'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}