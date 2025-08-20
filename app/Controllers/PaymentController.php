<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PaymentController extends BaseController
{
    protected $session;
    
    public function __construct()
    {
        $this->session = session();
    }

    /**
     * Payment gateway page for a registration
     * GET /payment/{registration_id}
     */
    public function gateway($registrationId)
    {
        // Check if user is logged in
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please login to continue with payment');
        }

        try {
            $db = \Config\Database::connect();
            
            // Get registration with payment info
            $registration = $db->query("
                SELECT r.*, e.title as event_title, e.registration_fee, e.event_date,
                       u.first_name, u.last_name, u.email,
                       p.id as payment_id, p.payment_status, p.amount as payment_amount
                FROM registrations r
                JOIN events e ON e.id = r.event_id
                JOIN users u ON u.id = r.user_id
                LEFT JOIN payments p ON p.registration_id = r.id
                WHERE r.id = ? AND r.user_id = ?
            ", [$registrationId, $userId])->getRowArray();

            if (!$registration) {
                return redirect()->to('/audience/registrations')->with('error', 'Registration not found');
            }

            // Check if already paid
            if ($registration['payment_status'] === 'success') {
                return redirect()->to('/audience/payments')->with('info', 'This registration has already been paid');
            }

            $data = [
                'title' => 'Payment Gateway - SNIA Conference',
                'registration' => $registration,
                'user' => [
                    'id' => $userId,
                    'first_name' => $registration['first_name'],
                    'last_name' => $registration['last_name'],
                    'email' => $registration['email']
                ]
            ];

            return view('payment/gateway', $data);

        } catch (\Exception $e) {
            log_message('error', 'Payment gateway error: ' . $e->getMessage());
            return redirect()->to('/audience/registrations')->with('error', 'Payment gateway temporarily unavailable');
        }
    }

    /**
     * Process payment creation
     * POST /payment/process
     */
    public function process()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $registrationId = $this->request->getPost('registration_id');
            $paymentMethod = $this->request->getPost('payment_method') ?? 'midtrans';

            if (!$registrationId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Verify registration belongs to user
            $registration = $db->query("
                SELECT r.*, e.registration_fee 
                FROM registrations r 
                JOIN events e ON e.id = r.event_id 
                WHERE r.id = ? AND r.user_id = ?
            ", [$registrationId, $userId])->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if payment already exists
            $existingPayment = $db->query("
                SELECT * FROM payments WHERE registration_id = ?
            ", [$registrationId])->getRowArray();

            if ($existingPayment && $existingPayment['payment_status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment already completed for this registration'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $amount = $registration['registration_fee'];
            
            // Create or update payment record
            if ($existingPayment) {
                // Update existing payment
                $db->query("
                    UPDATE payments 
                    SET payment_method = ?, payment_gateway = 'midtrans', payment_status = 'pending'
                    WHERE id = ?
                ", [$paymentMethod, $existingPayment['id']]);
                
                $paymentId = $existingPayment['id'];
            } else {
                // Create new payment
                $paymentResult = $db->query("
                    INSERT INTO payments (registration_id, amount, final_amount, payment_method, payment_gateway, payment_status) 
                    VALUES (?, ?, ?, ?, 'midtrans', 'pending') 
                    RETURNING id
                ", [$registrationId, $amount, $amount, $paymentMethod]);
                
                $paymentData = $paymentResult->getRowArray();
                $paymentId = $paymentData['id'];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment initialized successfully',
                'data' => [
                    'payment_id' => $paymentId,
                    'registration_id' => $registrationId,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'redirect_url' => base_url("payment/simulate/{$paymentId}")
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Payment process error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Payment simulation page (for demo purposes)
     * GET /payment/simulate/{payment_id}
     */
    public function simulate($paymentId)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        try {
            $db = \Config\Database::connect();
            
            // Get payment details
            $payment = $db->query("
                SELECT p.*, r.user_id, r.event_id, e.title as event_title
                FROM payments p
                JOIN registrations r ON r.id = p.registration_id
                JOIN events e ON e.id = r.event_id
                WHERE p.id = ? AND r.user_id = ?
            ", [$paymentId, $userId])->getRowArray();

            if (!$payment) {
                return redirect()->to('/audience/payments')->with('error', 'Payment not found');
            }

            $data = [
                'title' => 'Payment Simulation - SNIA Conference',
                'payment' => $payment
            ];

            return view('payment/simulate', $data);

        } catch (\Exception $e) {
            log_message('error', 'Payment simulate error: ' . $e->getMessage());
            return redirect()->to('/audience/payments')->with('error', 'Payment processing error');
        }
    }

    /**
     * Complete payment (for demo purposes)
     * POST /payment/complete/{payment_id}
     */
    public function complete($paymentId)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated'
            ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        try {
            $db = \Config\Database::connect();
            
            // Verify payment belongs to user
            $payment = $db->query("
                SELECT p.*, r.user_id, r.id as registration_id
                FROM payments p
                JOIN registrations r ON r.id = p.registration_id
                WHERE p.id = ? AND r.user_id = ?
            ", [$paymentId, $userId])->getRowArray();

            if (!$payment) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update payment status
            $transactionId = 'SNIA-TXN-' . date('YmdHis') . '-' . $paymentId;
            
            $db->query("
                UPDATE payments 
                SET payment_status = 'success', 
                    transaction_id = ?, 
                    paid_at = NOW()
                WHERE id = ?
            ", [$transactionId, $paymentId]);

            // Update registration status
            $db->query("
                UPDATE registrations 
                SET registration_status = 'confirmed'
                WHERE id = ?
            ", [$payment['registration_id']]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment completed successfully',
                'data' => [
                    'payment_id' => $paymentId,
                    'transaction_id' => $transactionId,
                    'redirect_url' => base_url('/audience/payments?success=1')
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Payment complete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Payment completion failed'
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}