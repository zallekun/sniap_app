<?php

namespace App\Models;
use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'code', 'description', 'discount_type', 'discount_value',
        'max_uses', 'used_count', 'valid_from', 'valid_until',
        'is_active', 'created_by'
    ];

    protected array $casts = [
        'is_active' => 'boolean'
    ];

    protected $useTimestamps = false;

    public function getActiveVouchers()
    {
        return $this->where('is_active', true)
                    ->where('valid_until >=', date('Y-m-d H:i:s'))
                    ->orderBy('valid_until', 'ASC')
                    ->findAll();
    }

    public function getValidVoucher(string $code)
    {
        return $this->where('code', $code)
                    ->where('is_active', true)
                    ->where('valid_from <=', date('Y-m-d H:i:s'))
                    ->where('valid_until >=', date('Y-m-d H:i:s'))
                    ->where('(max_uses IS NULL OR used_count < max_uses)')
                    ->first();
    }

    public function useVoucher(string $code): bool
    {
        $voucher = $this->getValidVoucher($code);
        
        if (!$voucher) {
            return false;
        }

        return $this->where('id', $voucher['id'])
                    ->set('used_count', 'used_count + 1', false)
                    ->update();
    }
}
