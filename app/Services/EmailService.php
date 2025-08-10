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
     * Send verification email to new user
     */
    public function sendVerificationEmail($userEmail, $userName, $verificationToken)
    {
        $subject = 'Verify Your SNIA Account';
        $template = 'emails/verification';
        $data = [
            'userName' => $userName,
            'verificationToken' => $verificationToken,
            'verificationUrl' => base_url("verify-email/{$verificationToken}")
        ];

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send review status notification to presenter
     */
    public function sendReviewStatusNotification($userEmail, $userName, $abstractTitle, $status, $comments = '')
    {
        $subject = "Abstract Review Update - {$abstractTitle}";
        $template = 'emails/review_status';
        $data = [
            'userName' => $userName,
            'abstractTitle' => $abstractTitle,
            'status' => $status,
            'comments' => $comments
        ];

        return $this->sendEmail($userEmail, $subject, $template, $data);
    }

    /**
     * Send LOA delivery email
     */
    public function sendLOADelivery($userEmail, $userName, $abstractTitle, $loaPath)
    {
        $subject = 'Letter of Acceptance - SNIA Conference';
        $template = 'emails/loa_delivery';
        $data = [
            'userName' => $userName,
            'abstractTitle' => $abstractTitle
        ];

        $attachments = [$loaPath];
        return $this->sendEmail($userEmail, $subject, $template, $data, $attachments);
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation($userEmail, $userName, $amount, $paymentId)
    {
        $subject = 'Payment Confirmation - SNIA Conference';
        $template = 'emails/payment_confirmation';
        $data = [
            'userName' => $userName,
            'amount' => $amount,
            'paymentId' => $paymentId,
            'paymentDate' => date('Y-m-d H:i:s')
        ];

        return $this->sendEmail($userEmail, $subject, $template, $data);
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
}