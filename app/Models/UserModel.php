<?php
// ============================================================================
// USER MODEL - CLEAN VERSION
// ============================================================================

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'email', 'password', 'first_name', 'last_name', 'phone', 
        'institution', 'role', 'is_verified', 'verification_token',
        'reset_password_token', 'reset_password_expires'
    ];

    // SIMPLE CASTS - No datetime drama!
    protected array $casts = [
        'is_verified' => 'boolean'
    ];

    // NO TIMESTAMPS - Let PostgreSQL triggers handle it
    protected $useTimestamps = false;

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'role' => 'required|in_list[presenter,audience,reviewer,admin]'
    ];

    // Custom Methods
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findByRole(string $role)
    {
        return $this->where('role', $role)->findAll();
    }

    public function getActiveReviewers()
    {
        return $this->where('role', 'reviewer')
                    ->where('is_verified', true)
                    ->findAll();
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}