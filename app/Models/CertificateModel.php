<?php

namespace App\Models;
use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table = 'certificates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'registration_id', 'certificate_number', 'certificate_type', 
        'file_path', 'generated_by'
    ];

    protected $useTimestamps = false;

    public function generateCertificateNumber(string $type, ?int $year = null): string
    {
        $year = $year ?: date('Y');
        $typeCode = $type === 'presenter' ? 'PRES' : 'PART';
        
        $lastCert = $this->select('certificate_number')
                        ->like('certificate_number', "SNIA-{$year}-{$typeCode}-", 'after')
                        ->orderBy('id', 'DESC')
                        ->first();

        $nextNumber = 1;
        if ($lastCert) {
            $parts = explode('-', $lastCert['certificate_number']);
            $nextNumber = intval(end($parts)) + 1;
        }

        return sprintf('SNIA-%s-%s-%03d', $year, $typeCode, $nextNumber);
    }

    public function getCertificateByRegistration(int $registrationId)
    {
        return $this->where('registration_id', $registrationId)->first();
    }

    public function createCertificate(array $data): int|false
    {
        if (!isset($data['certificate_number'])) {
            $data['certificate_number'] = $this->generateCertificateNumber($data['certificate_type']);
        }

        return $this->insert($data);
    }

    // ==================== PRESENTER CERTIFICATE METHODS ====================

    /**
     * Generate certificate for presenter after successful presentation
     */
    public function generatePresenterCertificate($registrationId, $generatedBy)
    {
        // Check if certificate already exists
        $existingCert = $this->getCertificateByRegistration($registrationId);
        if ($existingCert) {
            return $existingCert;
        }

        // Get registration details
        $db = \Config\Database::connect();
        $registration = $db->table('registrations r')
            ->select('r.*, u.first_name, u.last_name, u.email, e.title as event_title, e.start_date, e.end_date')
            ->join('users u', 'u.id = r.user_id', 'inner')
            ->join('events e', 'e.id = r.event_id', 'inner')
            ->where('r.id', $registrationId)
            ->get()->getFirstRow('array');

        if (!$registration) {
            return false;
        }

        // Generate certificate number
        $certNumber = $this->generateCertificateNumber('presenter');

        // Create certificate record
        $data = [
            'registration_id' => $registrationId,
            'certificate_number' => $certNumber,
            'certificate_type' => 'presenter',
            'file_path' => '', // Will be updated after PDF generation
            'generated_by' => $generatedBy,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $certId = $this->insert($data);
        
        if ($certId) {
            // Generate PDF and update file path
            $filePath = $this->generateCertificatePdf($certId, $registration, $certNumber);
            if ($filePath) {
                $this->update($certId, ['file_path' => $filePath]);
                return $this->find($certId);
            }
        }

        return false;
    }

    /**
     * Check if presenter can access certificate (presentation completed)
     */
    public function canAccessCertificate($registrationId)
    {
        $db = \Config\Database::connect();
        
        // Check if event has ended (presentation completed)
        $event = $db->table('registrations r')
            ->select('e.end_date')
            ->join('events e', 'e.id = r.event_id', 'inner')
            ->where('r.id', $registrationId)
            ->get()->getFirstRow('array');
            
        if (!$event) return false;
        
        $eventEndDate = strtotime($event['end_date']);
        $now = time();
        
        // Certificate available after event ends
        return $eventEndDate < $now;
    }

    /**
     * Generate certificate PDF document
     */
    private function generateCertificatePdf($certId, $registration, $certNumber)
    {
        try {
            // Create uploads directory if not exists
            $uploadPath = WRITEPATH . 'uploads/certificates/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate PDF file name
            $fileName = 'CERT_' . $certNumber . '_' . time() . '.pdf';
            $filePath = $uploadPath . $fileName;

            // Generate PDF content (HTML for now)
            $htmlContent = $this->generateCertificateHtml($registration, $certNumber);
            
            // Save as HTML (in production, use PDF library)
            file_put_contents($filePath . '.html', $htmlContent);
            
            return 'uploads/certificates/' . $fileName . '.html';

        } catch (\Exception $e) {
            log_message('error', 'Generate certificate PDF error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate certificate HTML content
     */
    private function generateCertificateHtml($registration, $certNumber)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Certificate of Presentation - ' . $certNumber . '</title>
            <style>
                body { font-family: Georgia, serif; margin: 0; padding: 40px; text-align: center; }
                .certificate { border: 10px solid #2c3e50; padding: 60px; background: #f8f9fa; }
                .header { margin-bottom: 40px; }
                .logo { font-size: 32px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
                .title { font-size: 28px; color: #e74c3c; margin: 20px 0; }
                .subtitle { font-size: 18px; color: #34495e; margin-bottom: 40px; }
                .recipient { font-size: 36px; color: #2c3e50; margin: 30px 0; font-weight: bold; }
                .content { font-size: 18px; line-height: 1.8; margin: 30px 0; color: #2c3e50; }
                .event-info { font-size: 16px; color: #7f8c8d; margin: 20px 0; }
                .signature { margin-top: 60px; display: flex; justify-content: space-between; }
                .signature div { text-align: center; }
                .signature-line { border-top: 2px solid #2c3e50; width: 200px; margin-bottom: 10px; }
                .footer { margin-top: 40px; font-size: 12px; color: #95a5a6; }
            </style>
        </head>
        <body>
            <div class="certificate">
                <div class="header">
                    <div class="logo">SNIA CONFERENCE</div>
                    <div class="title">CERTIFICATE OF PRESENTATION</div>
                    <div class="subtitle">This certificate is awarded to</div>
                </div>
                
                <div class="recipient">' . $registration['first_name'] . ' ' . $registration['last_name'] . '</div>
                
                <div class="content">
                    For successfully presenting at<br>
                    <strong>' . $registration['event_title'] . '</strong><br>
                    demonstrating excellence in research and knowledge sharing
                </div>
                
                <div class="event-info">
                    Event Date: ' . date('F d, Y', strtotime($registration['start_date'])) . ' - ' . date('F d, Y', strtotime($registration['end_date'])) . '<br>
                    Certificate Number: ' . $certNumber . '
                </div>
                
                <div class="signature">
                    <div>
                        <div class="signature-line"></div>
                        <strong>Conference Chair</strong><br>
                        SNIA Conference Committee
                    </div>
                    <div>
                        <div class="signature-line"></div>
                        <strong>Program Director</strong><br>
                        SNIA Conference Committee
                    </div>
                </div>
                
                <div class="footer">
                    Certificate issued on ' . date('F d, Y') . ' | Certificate ID: ' . $certNumber . '
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
