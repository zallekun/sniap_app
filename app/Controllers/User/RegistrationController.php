<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Models\PaymentModel;
use App\Models\VoucherModel;
use CodeIgniter\HTTP\ResponseInterface;

class RegistrationController extends BaseController
{
    protected $eventModel;
    protected $registrationModel;
    protected $userModel;
    protected $paymentModel;
    protected $voucherModel;
    protected $session;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->registrationModel = new RegistrationModel();
        $this->userModel = new UserModel();
        $this->paymentModel = new PaymentModel();
        $this->voucherModel = new VoucherModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display user's registrations
     */
    public function index()
    {
        $userId = $this->session->get('user_id');

        // Get user's registrations with event details
        $registrations = $this->registrationModel->getUserRegistrations($userId);

        // Separate by status
        $upcoming = [];
        $completed = [];
        $cancelled = [];

        foreach ($registrations as $registration) {
            if ($registration['status'] === 'cancelled') {
                $cancelled[] = $registration;
            } elseif (strtotime($registration['end_date']) < time()) {
                $completed[] = $registration;
            } else {
                $upcoming[] = $registration;
            }
        }

        $data = [
            'title' => 'My Registrations - SNIA Conference',
            'upcoming' => $upcoming,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'stats' => [
                'total' => count($registrations),
                'upcoming' => count($upcoming),
                'completed' => count($completed),
                'cancelled' => count($cancelled)
            ]
        ];

        return view('user/registrations/index', $data);
    }

    /**
     * Show registration form for specific event
     */
    public function create($eventId)
    {
        $userId = $this->session->get('user_id');
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        if (!$event['is_active']) {
            return redirect()->to('/events')->with('error', 'This event is not available for registration.');
        }

        // Check if registration deadline has passed
        if ($event['registration_deadline'] && strtotime($event['registration_deadline']) < time()) {
            return redirect()->to('/events')->with('error', 'Registration deadline has passed for this event.');
        }

        // Check if event is full
        $currentRegistrations = $this->registrationModel->where('event_id', $eventId)->where('status', 'confirmed')->countAllResults();
        if ($event['max_participants'] && $currentRegistrations >= $event['max_participants']) {
            return redirect()->to('/events')->with('error', 'This event is fully booked.');
        }

        // Check if user is already registered
        $existingRegistration = $this->registrationModel
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->where('status !=', 'cancelled')
            ->first();

        if ($existingRegistration) {
            return redirect()->to('/registrations')->with('info', 'You are already registered for this event.');
        }

        // Get user details
        $user = $this->userModel->find($userId);

        // Calculate fees
        $fees = $this->calculateEventFees($event);

        $data = [
            'title' => 'Register for ' . $event['title'],
            'event' => $event,
            'user' => $user,
            'fees' => $fees,
            'validation' => \Config\Services::validation()
        ];

        return view('user/registrations/create', $data);
    }

    /**
     * Process event registration
     */
    public function store()
    {
        $userId = $this->session->get('user_id');
        $eventId = $this->request->getPost('event_id');

        // Validate input
        $rules = [
            'event_id' => 'required|integer',
            'dietary_requirements' => 'permit_empty|max_length[500]',
            'special_requirements' => 'permit_empty|max_length[500]',
            'emergency_contact_name' => 'permit_empty|max_length[100]',
            'emergency_contact_phone' => 'permit_empty|max_length[20]',
            'voucher_code' => 'permit_empty|max_length[20]',
            'terms_accepted' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate event
        $event = $this->eventModel->find($eventId);
        if (!$event || !$event['is_active']) {
            return redirect()->back()->with('error', 'Invalid event.');
        }

        // Check registration deadline
        if ($event['registration_deadline'] && strtotime($event['registration_deadline']) < time()) {
            return redirect()->back()->with('error', 'Registration deadline has passed.');
        }

        // Check capacity
        $currentRegistrations = $this->registrationModel->where('event_id', $eventId)->where('status', 'confirmed')->countAllResults();
        if ($event['max_participants'] && $currentRegistrations >= $event['max_participants']) {
            return redirect()->back()->with('error', 'Event is fully booked.');
        }

        // Check existing registration
        $existingRegistration = $this->registrationModel
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->where('status !=', 'cancelled')
            ->first();

        if ($existingRegistration) {
            return redirect()->to('/registrations')->with('info', 'You are already registered for this event.');
        }

        try {
            // Calculate fees and apply voucher if provided
            $fees = $this->calculateEventFees($event);
            $voucherCode = $this->request->getPost('voucher_code');
            $discount = 0;
            $voucherId = null;

            if ($voucherCode) {
                $voucher = $this->voucherModel->validateVoucher($voucherCode);
                if ($voucher) {
                    $discount = $this->calculateVoucherDiscount($fees['total'], $voucher);
                    $voucherId = $voucher['id'];
                }
            }

            $finalAmount = max(0, $fees['total'] - $discount);

            // Generate QR code
            $qrCode = $this->generateQRCode($userId, $eventId);

            // Create registration
            $registrationData = [
                'user_id' => $userId,
                'event_id' => $eventId,
                'registration_fee' => $fees['registration_fee'],
                'early_bird_discount' => $fees['early_bird_discount'],
                'voucher_discount' => $discount,
                'total_amount' => $finalAmount,
                'dietary_requirements' => $this->request->getPost('dietary_requirements'),
                'special_requirements' => $this->request->getPost('special_requirements'),
                'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
                'emergency_contact_phone' => $this->request->getPost('emergency_contact_phone'),
                'qr_code' => $qrCode,
                'status' => $finalAmount > 0 ? 'pending' : 'confirmed',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $registrationId = $this->registrationModel->insert($registrationData);

            if (!$registrationId) {
                throw new \Exception('Failed to create registration');
            }

            // Update voucher usage if used
            if ($voucherId) {
                $this->voucherModel->incrementUsage($voucherId);
            }

            // Create payment record if amount > 0
            if ($finalAmount > 0) {
                $paymentData = [
                    'registration_id' => $registrationId,
                    'amount' => $finalAmount,
                    'currency' => 'IDR', // or get from system settings
                    'status' => 'pending',
                    'payment_method' => 'online',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $paymentId = $this->paymentModel->insert($paymentData);

                // Redirect to payment page
                return redirect()->to("/registrations/payment/{$registrationId}")
                    ->with('success', 'Registration created! Please complete your payment.');
            } else {
                // Free registration - send confirmation email
                $this->sendRegistrationConfirmation($registrationId);

                return redirect()->to('/registrations')
                    ->with('success', 'Registration completed successfully!');
            }

        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }

    /**
     * Show registration details
     */
    public function show($registrationId)
    {
        $userId = $this->session->get('user_id');
        
        $registration = $this->registrationModel
            ->select('registrations.*, events.title as event_title, events.start_date, events.end_date, 
                      events.start_time, events.end_time, events.location, events.format, events.online_link')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.id', $registrationId)
            ->where('registrations.user_id', $userId)
            ->first();

        if (!$registration) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Registration not found');
        }

        // Get payment details if exists
        $payment = $this->paymentModel->where('registration_id', $registrationId)->first();

        $data = [
            'title' => 'Registration Details - ' . $registration['event_title'],
            'registration' => $registration,
            'payment' => $payment
        ];

        return view('user/registrations/show', $data);
    }

    /**
     * Cancel registration
     */
    public function cancel($registrationId)
    {
        $userId = $this->session->get('user_id');
        
        $registration = $this->registrationModel
            ->where('id', $registrationId)
            ->where('user_id', $userId)
            ->first();

        if (!$registration) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registration not found'
            ]);
        }

        if ($registration['status'] === 'cancelled') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registration is already cancelled'
            ]);
        }

        // Check if event has started
        $event = $this->eventModel->find($registration['event_id']);
        if (strtotime($event['start_date']) <= time()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot cancel registration after event has started'
            ]);
        }

        try {
            // Update registration status
            $this->registrationModel->update($registrationId, [
                'status' => 'cancelled',
                'cancelled_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Process refund if payment was made
            $payment = $this->paymentModel->where('registration_id', $registrationId)->first();
            if ($payment && $payment['status'] === 'completed') {
                $this->processRefund($payment);
            }

            // Send cancellation email
            $this->sendCancellationEmail($registrationId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration cancelled successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Registration cancellation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to cancel registration'
            ]);
        }
    }

    /**
     * Download registration certificate/ticket
     */
    public function downloadTicket($registrationId)
    {
        $userId = $this->session->get('user_id');
        
        $registration = $this->registrationModel
            ->select('registrations.*, events.title as event_title, events.start_date, events.end_date, 
                      events.start_time, events.end_time, events.location, events.format, 
                      users.full_name, users.email')
            ->join('events', 'events.id = registrations.event_id')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.id', $registrationId)
            ->where('registrations.user_id', $userId)
            ->first();

        if (!$registration || $registration['status'] !== 'confirmed') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Valid registration not found');
        }

        // Generate PDF ticket
        return $this->generateTicketPDF($registration);
    }

    /**
     * Check voucher validity via AJAX
     */
    public function checkVoucher()
    {
        $voucherCode = $this->request->getPost('voucher_code');
        $eventId = $this->request->getPost('event_id');

        if (!$voucherCode) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Voucher code is required'
            ]);
        }

        $voucher = $this->voucherModel->validateVoucher($voucherCode);

        if (!$voucher) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Invalid or expired voucher code'
            ]);
        }

        // Calculate discount
        $event = $this->eventModel->find($eventId);
        $fees = $this->calculateEventFees($event);
        $discount = $this->calculateVoucherDiscount($fees['total'], $voucher);

        return $this->response->setJSON([
            'valid' => true,
            'message' => 'Voucher applied successfully!',
            'discount_amount' => $discount,
            'discount_type' => $voucher['type'],
            'discount_value' => $voucher['value'],
            'new_total' => max(0, $fees['total'] - $discount)
        ]);
    }

    /**
     * Payment page
     */
    public function payment($registrationId)
    {
        $userId = $this->session->get('user_id');
        
        $registration = $this->registrationModel
            ->select('registrations.*, events.title as event_title')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.id', $registrationId)
            ->where('registrations.user_id', $userId)
            ->first();

        if (!$registration) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Registration not found');
        }

        if ($registration['status'] !== 'pending') {
            return redirect()->to('/registrations')->with('info', 'Payment is not required for this registration.');
        }

        $payment = $this->paymentModel->where('registration_id', $registrationId)->first();

        if (!$payment || $payment['status'] !== 'pending') {
            return redirect()->to('/registrations')->with('info', 'No pending payment found.');
        }

        $data = [
            'title' => 'Payment - ' . $registration['event_title'],
            'registration' => $registration,
            'payment' => $payment
        ];

        return view('user/registrations/payment', $data);
    }

    /**
     * Process payment (integrate with payment gateway)
     */
    public function processPayment()
    {
        $registrationId = $this->request->getPost('registration_id');
        $paymentMethod = $this->request->getPost('payment_method');

        $payment = $this->paymentModel->where('registration_id', $registrationId)->first();

        if (!$payment || $payment['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payment request'
            ]);
        }

        try {
            // Here you would integrate with payment gateway (Midtrans, etc.)
            // For now, we'll simulate a successful payment
            
            $paymentResult = $this->processPaymentGateway($payment, $paymentMethod);

            if ($paymentResult['success']) {
                // Update payment status
                $this->paymentModel->update($payment['id'], [
                    'status' => 'completed',
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $paymentResult['transaction_id'],
                    'paid_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Update registration status
                $this->registrationModel->update($registrationId, [
                    'status' => 'confirmed',
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Send confirmation email
                $this->sendRegistrationConfirmation($registrationId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment successful! Registration confirmed.',
                    'redirect_url' => base_url("registrations/{$registrationId}")
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['message']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Payment processing error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment processing failed'
            ]);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Calculate event fees
     */
    private function calculateEventFees($event)
    {
        $registrationFee = floatval($event['registration_fee']);
        $earlyBirdFee = floatval($event['early_bird_fee']);
        $earlyBirdDiscount = 0;

        // Check if early bird discount applies
        if ($earlyBirdFee && $event['early_bird_deadline'] && strtotime($event['early_bird_deadline']) >= time()) {
            $earlyBirdDiscount = $registrationFee - $earlyBirdFee;
            $total = $earlyBirdFee;
        } else {
            $total = $registrationFee;
        }

        return [
            'registration_fee' => $registrationFee,
            'early_bird_fee' => $earlyBirdFee,
            'early_bird_discount' => $earlyBirdDiscount,
            'total' => $total
        ];
    }

    /**
     * Calculate voucher discount
     */
    private function calculateVoucherDiscount($amount, $voucher)
    {
        if ($voucher['type'] === 'percentage') {
            return ($amount * $voucher['value']) / 100;
        } else {
            return min($voucher['value'], $amount);
        }
    }

    /**
     * Generate QR code for registration
     */
    private function generateQRCode($userId, $eventId)
    {
        return 'REG-' . str_pad($userId, 4, '0', STR_PAD_LEFT) . '-' . str_pad($eventId, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }

    /**
     * Send registration confirmation email
     */
    private function sendRegistrationConfirmation($registrationId)
    {
        $registration = $this->registrationModel
            ->select('registrations.*, events.title as event_title, events.start_date, events.end_date, 
                      events.start_time, events.end_time, events.location, events.format, events.online_link,
                      users.full_name, users.email')
            ->join('events', 'events.id = registrations.event_id')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.id', $registrationId)
            ->first();

        if (!$registration) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>Registration Confirmed!</h2>
            <p>Hello {$registration['full_name']},</p>
            <p>Your registration for <strong>{$registration['event_title']}</strong> has been confirmed!</p>
            
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #28a745;'>
                <h3>Event Details:</h3>
                <p><strong>Event:</strong> {$registration['event_title']}</p>
                <p><strong>Date:</strong> {$registration['start_date']} to {$registration['end_date']}</p>
                <p><strong>Time:</strong> {$registration['start_time']} - {$registration['end_time']}</p>
                <p><strong>Format:</strong> " . ucfirst($registration['format']) . "</p>
                " . ($registration['location'] ? "<p><strong>Location:</strong> {$registration['location']}</p>" : "") . "
                " . ($registration['online_link'] ? "<p><strong>Online Link:</strong> {$registration['online_link']}</p>" : "") . "
            </div>

            <div style='margin: 20px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #007bff;'>
                <h3>Registration Details:</h3>
                <p><strong>Registration ID:</strong> {$registration['id']}</p>
                <p><strong>QR Code:</strong> {$registration['qr_code']}</p>
                <p><strong>Status:</strong> " . ucfirst($registration['status']) . "</p>
            </div>

            <p>Please bring this confirmation email or download your ticket from your dashboard.</p>
            <p><a href='" . base_url("registrations/{$registrationId}") . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Registration</a></p>
            
            <p>If you have any questions, please don't hesitate to contact us.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($registration['email']);
        $emailService->setSubject('Registration Confirmed - ' . $registration['event_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send registration confirmation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send cancellation email
     */
    private function sendCancellationEmail($registrationId)
    {
        $registration = $this->registrationModel
            ->select('registrations.*, events.title as event_title, users.full_name, users.email')
            ->join('events', 'events.id = registrations.event_id')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.id', $registrationId)
            ->first();

        if (!$registration) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>Registration Cancelled</h2>
            <p>Hello {$registration['full_name']},</p>
            <p>Your registration for <strong>{$registration['event_title']}</strong> has been cancelled.</p>
            <p><strong>Cancellation Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p>If you paid for this registration, we will process your refund within 3-5 business days.</p>
            <p>If you have any questions, please contact our support team.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($registration['email']);
        $emailService->setSubject('Registration Cancelled - ' . $registration['event_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send cancellation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process refund
     */
    private function processRefund($payment)
    {
        // Update payment with refund status
        $this->paymentModel->update($payment['id'], [
            'status' => 'refunded',
            'refunded_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Here you would integrate with payment gateway to process actual refund
        // For now, just log the refund request
        log_message('info', "Refund processed for payment ID: {$payment['id']}, Amount: {$payment['amount']}");
    }

    /**
     * Generate ticket PDF
     */
    private function generateTicketPDF($registration)
    {
        // This is a placeholder for PDF generation
        // You would use a library like TCPDF or mPDF to generate actual PDF
        
        $ticketContent = "
            SNIA CONFERENCE TICKET
            
            Event: {$registration['event_title']}
            Attendee: {$registration['full_name']}
            Email: {$registration['email']}
            
            Date: {$registration['start_date']} to {$registration['end_date']}
            Time: {$registration['start_time']} - {$registration['end_time']}
            Location: {$registration['location']}
            Format: " . ucfirst($registration['format']) . "
            
            Registration ID: {$registration['id']}
            QR Code: {$registration['qr_code']}
            
            Please present this ticket at the event venue.
        ";

        // For now, return as text file
        $filename = 'ticket_' . $registration['id'] . '.txt';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo $ticketContent;
        exit;
    }

    /**
     * Process payment via gateway (placeholder)
     */
    private function processPaymentGateway($payment, $paymentMethod)
    {
        // This is a placeholder for actual payment gateway integration
        // You would integrate with Midtrans, Stripe, etc.
        
        // Simulate successful payment
        return [
            'success' => true,
            'transaction_id' => 'TXN_' . time() . '_' . $payment['id'],
            'message' => 'Payment successful'
        ];
    }
}