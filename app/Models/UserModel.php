<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // FIXED: Remove is_active and is_verified since columns don't exist
    protected $allowedFields = [
        'email', 'password', 'first_name', 'last_name', 'phone', 
        'institution', 'role', 'profile_picture'
        // Removed: 'is_verified', 'is_active', 'verification_token', 'reset_token', 'reset_expires_at', 'last_login_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'permit_empty|max_length[100]',
        'phone' => 'permit_empty|max_length[20]',
        'institution' => 'permit_empty|max_length[255]',
        'role' => 'required|in_list[admin,presenter,audience,reviewer]'
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
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find all users (removed is_active filter)
     */
    public function findAllUsers()
    {
        return $this->findAll();
    }

    /**
     * Find users by role (removed is_active filter) 
     */
    public function findByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }

    /**
     * Custom insert method to handle full_name
     */
    public function insertUser($data)
    {
        if (isset($data['full_name']) && !empty($data['full_name'])) {
            $fullName = trim($data['full_name']);
            $nameParts = explode(' ', $fullName, 2);
            
            $data['first_name'] = $nameParts[0];
            $data['last_name'] = isset($nameParts[1]) ? $nameParts[1] : '';
            
            unset($data['full_name']);
        }
        
        return $this->insert($data);
    }

    /**
     * Get user with full_name virtual field
     */
    public function getUserWithFullName($userId)
    {
        $user = $this->find($userId);
        if ($user) {
            $user['full_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        }
        return $user;
    }

    /**
     * Override find to add full_name
     */
    public function find($id = null)
    {
        $result = parent::find($id);
        
        if ($result) {
            if (is_array($result) && isset($result[0])) {
                // Multiple records
                foreach ($result as &$record) {
                    $record['full_name'] = trim(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? ''));
                }
            } elseif (is_array($result)) {
                // Single record
                $result['full_name'] = trim(($result['first_name'] ?? '') . ' ' . ($result['last_name'] ?? ''));
            }
        }
        
        return $result;
    }

    /**
     * Override findAll to add full_name
     */
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $result = parent::findAll($limit, $offset);
        
        if ($result) {
            foreach ($result as &$record) {
                $record['full_name'] = trim(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? ''));
            }
        }
        
        return $result;
    }
}