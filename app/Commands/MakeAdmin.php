<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeAdmin extends BaseCommand
{
    protected $group = 'SNIA';
    protected $name = 'make:admin';
    protected $description = 'Make a user admin by email';
    protected $usage = 'make:admin <email>';
    protected $arguments = [
        'email' => 'Email address of the user to make admin'
    ];

    public function run(array $params)
    {
        $email = $params[0] ?? 'uhuy@example.com';
        
        CLI::write('=== SNIA Make Admin Command ===', 'yellow');
        CLI::write('Email: ' . $email);
        
        $db = \Config\Database::connect();
        
        // Check if user exists
        $user = $db->table('users')->where('email', $email)->get()->getRowArray();
        
        if (!$user) {
            CLI::error('User not found: ' . $email);
            return;
        }
        
        CLI::write('Current user: ' . $user['first_name'] . ' ' . $user['last_name']);
        CLI::write('Current role: ' . $user['role']);
        
        // Update to admin
        $result = $db->table('users')->where('email', $email)->update(['role' => 'admin']);
        
        if ($result) {
            CLI::write('✅ Successfully updated user role to admin!', 'green');
            CLI::write('Login credentials:', 'yellow');
            CLI::write('Email: ' . $email);
            CLI::write('Password: [same as before]');
        } else {
            CLI::error('Failed to update user role');
        }
    }
}