<?php

namespace App\Models;
use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'registration_id', 'amount', 'discount_amount', 'final_amount',
        'voucher_code', 'payment_method', 'payment_gateway', 'transaction_id',
        'payment_status', 'payment_proof', 'paid_at'
    ];

    protected $useTimestamps = false;

    public function getPaymentWithDetails(int $paymentId)
    {
        return $this->select('payments.*, registrations.registration_type,
                             users.first_name, users.last_name, users.email,
                             events.title as event_title')
                    ->join('registrations', 'registrations.id = payments.registration_id')
                    ->join('users', 'users.id = registrations.user_id')
                    ->join('events', 'events.id = registrations.event_id')
                    ->where('payments.id', $paymentId)
                    ->first();
    }

    public function getPaymentByRegistration(int $registrationId)
    {
        return $this->where('registration_id', $registrationId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    public function markAsPaid(int $paymentId, ?string $transactionId = null): bool
    {
        $updateData = [
            'payment_status' => 'success',
            'paid_at' => date('Y-m-d H:i:s')
        ];

        if ($transactionId) {
            $updateData['transaction_id'] = $transactionId;
        }

        return $this->update($paymentId, $updateData);
    }

    public function calculatePaymentAmount(int $registrationId, ?string $voucherCode = null): array|false
    {
        $registrationModel = new RegistrationModel();
        $registration = $registrationModel->find($registrationId);
        
        if (!$registration) {
            return false;
        }

        $eventModel = new EventModel();
        $event = $eventModel->find($registration['event_id']);
        $baseAmount = $event['registration_fee'];
        
        $discountAmount = 0;
        if ($voucherCode) {
            $voucherModel = new VoucherModel();
            $voucher = $voucherModel->getValidVoucher($voucherCode);
            
            if ($voucher) {
                if ($voucher['discount_type'] === 'percentage') {
                    $discountAmount = ($baseAmount * $voucher['discount_value']) / 100;
                } else {
                    $discountAmount = $voucher['discount_value'];
                }
            }
        }

        $finalAmount = max(0, $baseAmount - $discountAmount);

        return [
            'amount' => $baseAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'voucher_code' => $voucherCode
        ];
    }

    // ==================== PRESENTER PAYMENT METHODS ====================

    /**
     * Create payment for accepted abstract
     */
    public function createPresenterPayment($registrationId, $amount, $voucherCode = null)
    {
        // Calculate final amount with voucher if provided
        $calculation = $this->calculateAmount($amount, $voucherCode);
        
        $data = [
            'registration_id' => $registrationId,
            'amount' => $calculation['amount'],
            'discount_amount' => $calculation['discount_amount'],
            'final_amount' => $calculation['final_amount'],
            'voucher_code' => $calculation['voucher_code'],
            'payment_status' => 'pending',
            'payment_method' => 'online',
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Get payment status for presenter registration
     */
    public function getPresenterPaymentStatus($registrationId)
    {
        return $this->select('payment_status, final_amount, payment_method, paid_at, transaction_id')
                   ->where('registration_id', $registrationId)
                   ->orderBy('created_at', 'DESC')
                   ->first();
    }

    /**
     * Process payment via gateway (simulation for now)
     */
    public function processPaymentGateway($paymentId, $paymentData)
    {
        // Simulate payment gateway processing
        // In real implementation, this would integrate with actual payment gateway
        
        $updateData = [
            'payment_gateway' => $paymentData['gateway'] ?? 'midtrans',
            'transaction_id' => $paymentData['transaction_id'] ?? 'TXN' . time(),
            'payment_status' => $paymentData['status'] ?? 'success',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($paymentData['status'] === 'success') {
            $updateData['paid_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($paymentId, $updateData);
    }

    /**
     * Check if presenter has completed payment for event
     */
    public function hasCompletedPayment($registrationId)
    {
        $payment = $this->where('registration_id', $registrationId)
                       ->where('payment_status', 'success')
                       ->first();
        
        return !empty($payment);
    }

    /**
     * Get payment history for presenter
     */
    public function getPresenterPaymentHistory($userId)
    {
        return $this->db->table('payments p')
                       ->select('p.*, r.registration_type, e.title as event_title, e.start_date')
                       ->join('registrations r', 'r.id = p.registration_id', 'inner')
                       ->join('events e', 'e.id = r.event_id', 'inner')
                       ->where('r.user_id', $userId)
                       ->orderBy('p.created_at', 'DESC')
                       ->get()->getResultArray();
    }
}
