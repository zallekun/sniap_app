<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EmailService;

class LoaController extends BaseController
{
    /**
     * Generate LOA for accepted abstract (Presenter)
     * GET /api/v1/loa/generate/{abstract_id}
     */
    public function generateLoa($abstractId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ]);
            }
            
            // Only admin and reviewer can generate LOA
            if (!in_array($user['role'], ['admin', 'reviewer'])) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only admin and reviewers can generate LOA',
                    'user_role' => $user['role']
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get abstract details with presenter info
            $abstractQuery = $db->query("
    SELECT DISTINCT
        a.*,
        e.title as event_title,
        e.description as event_description,
        e.event_date,
        e.format as event_format,
        e.location as event_location,
        r.user_id,
        r.registration_type,
        r.payment_status,
        u.first_name,
        u.last_name,
        u.email,
        u.institution,
        u.phone
    FROM abstracts a
    JOIN registrations r ON a.registration_id = r.id
    JOIN events e ON r.event_id = e.id
    JOIN users u ON r.user_id = u.id
    WHERE a.id = ?
", [$abstractId]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            // Check if abstract is accepted
            if ($abstract['final_status'] !== 'final_accepted') {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'LOA can only be generated for finally accepted abstracts',
                    'current_status' => $abstract['final_status']
                ]);
            }
            
            // Check if payment is completed (relaxed for testing)
            if ($abstract['payment_status'] !== 'paid' && $abstract['payment_status'] !== 'completed') {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Payment must be completed before generating LOA',
                    'payment_status' => $abstract['payment_status']
                ]);
            }
            
            // Check if LOA already exists
            $existingLoaQuery = $db->query("
                SELECT * FROM loa_documents 
                WHERE abstract_id = ?
            ", [$abstractId]);
            
            $existingLoa = $existingLoaQuery->getRowArray();
            
            if ($existingLoa) {
                // Return existing LOA
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'LOA already exists',
                    'data' => [
                        'loa_id' => $existingLoa['id'],
                        'loa_number' => $existingLoa['loa_number'],
                        'generated_at' => $existingLoa['generated_at'],
                        'download_url' => base_url("api/v1/loa/download/{$existingLoa['id']}"),
                        'pdf_path' => $existingLoa['pdf_path'],
                        'status' => 'existing'
                    ]
                ]);
            }
            
            // Generate unique LOA number
            $loaNumber = $this->generateLoaNumber($abstract);
            
            // Create LOA content
            $loaContent = $this->createLoaContent($abstract, $loaNumber);
            
            // Generate PDF path (for now, store as HTML content)
            $pdfPath = $this->generateLoaPdf($loaContent, $loaNumber);
            
            // Save LOA record to database (PostgreSQL compatible)
            $insertLoaQuery = $db->query("
                INSERT INTO loa_documents (
                    abstract_id, loa_number, presenter_name, presenter_email,
                    event_title, event_date, abstract_title, pdf_path,
                    html_content, generated_by, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active'::loa_status_type)
                RETURNING id
            ", [
                $abstractId,
                $loaNumber,
                $abstract['first_name'] . ' ' . $abstract['last_name'],
                $abstract['email'],
                $abstract['event_title'],
                $abstract['event_date'],
                $abstract['title'],
                $pdfPath,
                $loaContent,
                $user['id']
            ]);
            
            if ($insertLoaQuery) {
                $result = $insertLoaQuery->getRowArray();
                $loaId = $result['id'];
                
                // 🚀 EMAIL NOTIFICATION AFTER LOA GENERATION:
                try {
                    $emailService = new EmailService();
                    $presenterName = $abstract['first_name'] . ' ' . $abstract['last_name'];
                    
                    $emailResult = $emailService->sendLOADelivery(
                        $abstract['email'],
                        $presenterName,
                        $abstract['title'],
                        $pdfPath, // LOA file path
                        1 // eventId
                    );
                    
                    if ($emailResult['success']) {
                        log_message('info', "LOA delivery email sent to: " . $abstract['email']);
                    } else {
                        log_message('error', "Failed to send LOA delivery email: " . $emailResult['message']);
                    }
                } catch (\Exception $emailException) {
                    log_message('error', 'LOA email notification error: ' . $emailException->getMessage());
                }
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'LOA generated successfully',
                    'data' => [
                        'loa_id' => (int)$loaId,
                        'loa_number' => $loaNumber,
                        'abstract_id' => (int)$abstractId,
                        'presenter_name' => $abstract['first_name'] . ' ' . $abstract['last_name'],
                        'event_title' => $abstract['event_title'],
                        'event_date' => $abstract['event_date'],
                        'abstract_title' => $abstract['title'],
                        'generated_at' => date('Y-m-d H:i:s'),
                        'download_url' => base_url("api/v1/test-loa/download/$loaId"),
                        'pdf_path' => $pdfPath,
                        'status' => 'generated'
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save LOA record'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate LOA: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Download LOA
     * GET /api/v1/loa/download/{loa_id}
     */
    public function downloadLoa($loaId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get LOA details
            log_message('debug', 'Looking for LOA ID: ' . $loaId);
            
            $loaQuery = $db->query("
                SELECT * FROM loa_documents WHERE id = ?
            ", [$loaId]);
            
            $loa = $loaQuery->getRowArray();
            
            if (!$loa) {
                // Debug: show available LOAs
                $availableLoas = $db->query('SELECT id, abstract_id, loa_number FROM loa_documents')->getResultArray();
                
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'LOA not found',
                    'debug' => [
                        'requested_loa_id' => $loaId,
                        'available_loas' => $availableLoas
                    ]
                ]);
            }
            
            // Return HTML content
            $this->response->setHeader('Content-Type', 'text/html');
            $this->response->setHeader('Content-Disposition', 'inline; filename="LOA_' . $loa['loa_number'] . '.html"');
            
            return $this->response->setBody($loa['html_content']);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to download LOA: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get presenter's LOA list
     * GET /api/v1/loa/my-loas
     */
    public function getMyLoas()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all LOAs (for testing, return all)
            $loasQuery = $db->query("
                SELECT DISTINCT
                    l.*,
                    a.title as abstract_title,
                    a.review_status,
                    a.final_status
                FROM loa_documents l
                JOIN abstracts a ON l.abstract_id = a.id
                ORDER BY l.generated_at DESC
            ");
            
            $loas = $loasQuery->getResultArray();
            
            // Add download URLs
            foreach ($loas as &$loa) {
                $loa['download_url'] = base_url("api/v1/test-loa/download/{$loa['id']}");
                $loa['can_download'] = true;
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'loas' => $loas,
                    'total_count' => count($loas)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get LOA list: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Admin: Get all LOAs
     * GET /api/v1/admin/loas
     */
    public function getAllLoas()
    {
        try {
            $db = \Config\Database::connect();
            
            // Get all LOAs with details
            $loasQuery = $db->query("
                SELECT DISTINCT
                    l.*,
                    a.title as abstract_title
                FROM loa_documents l
                JOIN abstracts a ON l.abstract_id = a.id
                ORDER BY l.generated_at DESC
            ");
            
            $loas = $loasQuery->getResultArray();
            
            // Get statistics
            $statsQuery = $db->query("
                SELECT 
                    COUNT(*) as total_loas,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_loas,
                    COUNT(CASE WHEN status = 'revoked' THEN 1 END) as revoked_loas,
                    COUNT(CASE WHEN DATE(generated_at) = CURRENT_DATE THEN 1 END) as generated_today
                FROM loa_documents
            ");
            
            $stats = $statsQuery->getRowArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'loas' => $loas,
                    'statistics' => [
                        'total_loas' => (int)$stats['total_loas'],
                        'active_loas' => (int)$stats['active_loas'],
                        'revoked_loas' => (int)$stats['revoked_loas'],
                        'generated_today' => (int)$stats['generated_today']
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get LOA list: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate unique LOA number
     */
    private function generateLoaNumber($abstract)
    {
        $year = date('Y');
        $eventCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $abstract['event_title']), 0, 3));
        $sequence = str_pad($abstract['id'], 4, '0', STR_PAD_LEFT);
        
        return "LOA-{$year}-{$eventCode}-{$sequence}";
    }

    /**
     * Create LOA HTML content
     */
    private function createLoaContent($abstract, $loaNumber)
    {
        $eventDate = date('F d, Y', strtotime($abstract['event_date']));
        $generatedDate = date('F d, Y');
        
        // Handle location based on format
        $eventLocation = $abstract['event_format'] === 'online' ? 
            'Online Event' : 
            ($abstract['event_location'] ?: 'To be announced');
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Letter of Acceptance - {$loaNumber}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
                .header { text-align: center; margin-bottom: 40px; }
                .letterhead { font-size: 24px; font-weight: bold; color: #2c3e50; }
                .loa-number { font-size: 14px; color: #7f8c8d; margin-top: 10px; }
                .content { margin: 30px 0; }
                .abstract-details { background: #f8f9fa; padding: 20px; border-left: 4px solid #3498db; margin: 20px 0; }
                .signature { margin-top: 60px; }
                .footer { margin-top: 40px; font-size: 12px; color: #7f8c8d; }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='letterhead'>LETTER OF ACCEPTANCE</div>
                <div class='loa-number'>LOA Number: {$loaNumber}</div>
            </div>
            
            <div class='content'>
                <p><strong>Date:</strong> {$generatedDate}</p>
                
                <p><strong>To:</strong><br>
                {$abstract['first_name']} {$abstract['last_name']}<br>
                {$abstract['institution']}<br>
                {$abstract['email']}</p>
                
                <p>Dear {$abstract['first_name']} {$abstract['last_name']},</p>
                
                <p>We are pleased to inform you that your abstract has been <strong>ACCEPTED</strong> for presentation at <strong>{$abstract['event_title']}</strong>.</p>
                
                <div class='abstract-details'>
                    <h3>Abstract Details:</h3>
                    <p><strong>Title:</strong> {$abstract['title']}</p>
                    <p><strong>Event:</strong> {$abstract['event_title']}</p>
                    <p><strong>Event Date:</strong> {$eventDate}</p>
                    <p><strong>Event Format:</strong> " . ucfirst($abstract['event_format']) . "</p>
                    <p><strong>Location:</strong> {$eventLocation}</p>
                    <p><strong>Presenter:</strong> {$abstract['first_name']} {$abstract['last_name']}</p>
                    <p><strong>Institution:</strong> {$abstract['institution']}</p>
                </div>
                
                <p>Your abstract has successfully completed the peer review process and has been approved by our scientific committee. You are now confirmed as a presenter at this event.</p>
                
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>Prepare your presentation according to the conference guidelines</li>
                    <li>Register for the conference if you haven't already done so</li>
                    <li>Complete your payment if not already done</li>
                    <li>Submit your final presentation materials by the specified deadline</li>
                </ul>
                
                <p>We look forward to your valuable contribution to the conference.</p>
                
                <div class='signature'>
                    <p>Sincerely,</p>
                    <p><strong>Conference Organizing Committee</strong><br>
                    SNIA (Indonesian Computer Science Students Association)<br>
                    {$abstract['event_title']}</p>
                </div>
            </div>
            
            <div class='footer'>
                <p>This Letter of Acceptance is digitally generated and valid without signature.<br>
                LOA Number: {$loaNumber} | Generated on: {$generatedDate}</p>
            </div>
        </body>
        </html>";
        
        return $html;
    }

    /**
     * Generate LOA file path (placeholder)
     */
    private function generateLoaPdf($htmlContent, $loaNumber)
    {
        // For now, just return a placeholder path
        return "loa/{$loaNumber}_" . date('Y-m-d_H-i-s') . '.html';
    }
}