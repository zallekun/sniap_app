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
}
