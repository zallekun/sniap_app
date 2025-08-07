<?php

namespace App\Models;
use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'setting_key', 'setting_value', 'setting_category', 'description', 'updated_by'
    ];

    protected $useTimestamps = false;

    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function setSetting(string $key, string $value, ?int $updatedBy = null): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        
        $data = [
            'setting_value' => $value,
            'updated_by' => $updatedBy
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['setting_key'] = $key;
            return $this->insert($data) > 0;
        }
    }

    public function getSettingsByCategory(string $category)
    {
        return $this->where('setting_category', $category)
                    ->orderBy('setting_key', 'ASC')
                    ->findAll();
    }

    public function getEmailSettings(): array
    {
        return [
            'smtp_host' => $this->getSetting('smtp_host'),
            'smtp_port' => $this->getSetting('smtp_port'),
            'smtp_user' => $this->getSetting('smtp_user'),
            'email_from_name' => $this->getSetting('email_from_name')
        ];
    }

    public function getPaymentSettings(): array
    {
        return [
            'payment_gateway' => $this->getSetting('payment_gateway'),
            'midtrans_server_key' => $this->getSetting('midtrans_server_key'),
            'midtrans_client_key' => $this->getSetting('midtrans_client_key'),
            'payment_expiry_hours' => $this->getSetting('payment_expiry_hours', 24)
        ];
    }
}
