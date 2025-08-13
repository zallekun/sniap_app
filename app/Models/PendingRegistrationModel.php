<?php

namespace App\Models;

use CodeIgniter\Model;

class PendingRegistrationModel extends Model
{
    protected $table = 'pending_registrations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'email', 'password', 'first_name', 'last_name', 'phone', 
        'institution', 'role', 'verification_code', 'verification_code_expires',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[pending_registrations.email,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'permit_empty|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'institution' => 'permit_empty|max_length[255]',
        'role' => 'required|in_list[presenter,audience]',
        'verification_code' => 'required|exact_length[6]'
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please provide a valid email address',
            'is_unique' => 'This email is already registered'
        ],
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters long'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    protected $allowCallbacks = true;

    /**
     * Find pending registration by email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Check if verification code is valid and not expired
     */
    public function isValidCode($email, $code)
    {
        $registration = $this->where('email', $email)
            ->where('verification_code', $code)
            ->where('verification_code_expires >', date('Y-m-d H:i:s'))
            ->first();
            
        return $registration !== null;
    }

    /**
     * Clean up expired registrations (older than 24 hours)
     */
    public function cleanupExpired()
    {
        $expiredTime = date('Y-m-d H:i:s', strtotime('-24 hours'));
        return $this->where('created_at <', $expiredTime)->delete();
    }
}
