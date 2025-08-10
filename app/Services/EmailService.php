<?php

namespace App\Services;

use CodeIgniter\Email\Email;
use Config\Services;

class EmailService
{
    protected $email;
    protected $config;

    public function __construct()
    {
        $this->email = Services::email();
        $this->config = config('Email');
    }

    /**
     * Send verification email to new user with dynamic event data
     */
    public function sendVerificationEmail($userEmail, $userName, $verificationToken, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Verify Your {$eventData['title']} Account";
        $template = 'emails/verification';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'verificationToken' => $verificationToken,
            'verificationUrl' => base_url("verify-email/{$verificationToken}")
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send review status notification to presenter with dynamic event data
     */
    public function sendReviewStatusNotification($userEmail, $userName, $abstractTitle, $status, $comments = '', $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Abstract Review Update - {$abstractTitle} | {$eventData['title']}";
        $template = 'emails/review_status';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'abstractTitle' => $abstractTitle,
            'status' => $status,
            'comments' => $comments
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send LOA delivery email with dynamic event data
     */
    public function sendLOADelivery($userEmail, $userName, $abstractTitle, $loaPath, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Letter of Acceptance - {$abstractTitle} | {$eventData['title']}";
        $template = 'emails/loa_delivery';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'abstractTitle' => $abstractTitle,
            'loaPath' => $loaPath
        ]);

        $attachments = [$loaPath];
        return $this->sendEmail($userEmail, $subject, $template, $data, $attachments);
    }

    /**
     * Send payment confirmation with dynamic event data
     */
    public function sendPaymentConfirmation($userEmail, $userName, $amount, $paymentId, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Payment Confirmation - {$eventData['title']}";
        $template = 'emails/payment_confirmation';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'amount' => $amount,
            'paymentId' => $paymentId,
            'paymentDate' => date('Y-m-d H:i:s')
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Get dynamic event data for email templates
     */
    private function getEventData($eventId = 1)
    {
        $db = \Config\Database::connect();
        
        // Get event details
        $event = $db->table('events')
            ->where('id', $eventId)
            ->get()
            ->getRowArray();
        
        // Default fallback if event not found
        if (!$event) {
            $event = [
                'title' => 'SNIA Scientific Conference',
                'event_date' => date('Y-m-d'),
                'location' => 'Universitas Jenderal Achmad Yani',
                'format' => 'offline',
                'zoom_link' => null,
                'description' => 'Scientific Conference'
            ];
        }
        
        return [
            'eventTitle' => $event['title'],
            'eventDate' => $event['event_date'],
            'eventEndDate' => $event['event_date'], // Use same date as end date
            'eventLocation' => $event['location'],
            'eventMode' => $event['format'],
            'zoomLink' => $event['zoom_link'] ?? null,
            'eventDescription' => $event['description'] ?? 'Scientific Conference',
            'eventDateFormatted' => date('F d, Y', strtotime($event['event_date'])),
            'eventTimeRange' => date('F d, Y', strtotime($event['event_date'])),
            'isOnline' => ($event['format'] === 'online'),
            'isOffline' => ($event['format'] === 'offline'),
            'isHybrid' => ($event['format'] === 'hybrid')
        ];
    }

    /**
     * Core email sending method
     */
    private function sendEmail($to, $subject, $template, $data = [], $attachments = [])
    {
        try {
            // Render email template
            $message = view($template, $data);

            // Setup email
            $this->email->setTo($to);
            $this->email->setSubject($subject);
            $this->email->setMessage($message);

            // Add attachments if any
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $this->email->attach($attachment);
                    }
                }
            }

            // Send email
            if ($this->email->send()) {
                return ['success' => true, 'message' => 'Email sent successfully'];
            } else {
                return ['success' => false, 'message' => $this->email->printDebugger()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send registration confirmation email
     */
    public function sendRegistrationConfirmation($userEmail, $userName, $eventTitle, $registrationType, $registrationFee, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Registration Confirmation - {$eventData['title']}";
        $template = 'emails/registration_confirmation';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'registrationType' => $registrationType,
            'registrationFee' => $registrationFee,
            'requiresPayment' => ($registrationFee > 0)
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send reviewer assignment notification
     */
    public function sendReviewerAssignment($userEmail, $reviewerName, $abstractTitle, $abstractId, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "New Abstract Assignment - {$eventData['title']}";
        $template = 'emails/reviewer_assignment';
        $data = array_merge($eventData, [
            'reviewerName' => $reviewerName,
            'abstractTitle' => $abstractTitle,
            'abstractId' => $abstractId,
            'reviewUrl' => base_url("reviewer/review/{$abstractId}")
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send certificate notification
     */
    public function sendCertificateNotification($userEmail, $userName, $eventTitle, $certificateCode, $eventId = 1)
    {
        // Get event details
        $eventData = $this->getEventData($eventId);
        
        $subject = "Certificate Ready - {$eventData['title']}";
        $template = 'emails/certificate_notification';
        $data = array_merge($eventData, [
            'userName' => $userName,
            'certificateCode' => $certificateCode,
            'downloadUrl' => base_url("certificates/download/{$certificateCode}")
        ]);

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }
}