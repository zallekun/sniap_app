<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DeleteUserByEmail extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'delete:user-email';
    protected $description = 'Delete a user by email address';

    public function run(array $params)
    {
        $email = $params[0] ?? null;
        
        if (!$email) {
            CLI::error('Please provide an email address');
            CLI::write('Usage: php spark delete:user-email [email]');
            return;
        }

        $db = \Config\Database::connect();
        
        // Check if user exists
        $user = $db->table('users')->where('email', $email)->get()->getRowArray();
        
        if (!$user) {
            CLI::error("User with email '{$email}' not found");
            return;
        }
        
        CLI::write("Found user: {$user['first_name']} {$user['last_name']} ({$user['email']})", 'yellow');
        CLI::write('Deleting user...', 'yellow');
        
        try {
            $userId = $user['id'];
            
            // Simply delete the user - let foreign key constraints handle related data
            $result = $db->table('users')->where('email', $email)->delete();
            
            if ($result) {
                CLI::write("User '{$email}' deleted successfully!", 'green');
            } else {
                CLI::error('Failed to delete user');
            }
            
        } catch (\Exception $e) {
            CLI::error('Error deleting user: ' . $e->getMessage());
        }
    }
}