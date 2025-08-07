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
}
