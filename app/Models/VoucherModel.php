<?php

namespace App\Models;

use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'code', 'discount_type', 'discount_value', 'max_uses', 'used_count',
        'valid_from', 'valid_until', 'event_id', 'description'
    ];

    protected $useTimestamps = false;

    /**
     * Find voucher by code
     */
    public function findByCode($code)
    {
        return $this->where('code', $code)->first();
    }

    /**
     * Find active vouchers
     */
    public function findActive()
    {
        return $this->findAll();
    }

    /**
     * Find vouchers by event
     */
    public function findByEvent($eventId)
    {
        return $this->where('event_id', $eventId)->findAll();
    }
}