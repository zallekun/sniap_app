<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\PendingRegistrationModel;

class DeletePendingRegistration extends BaseCommand
{
    protected $group = 'maintenance';
    protected $name = 'delete:pending';
    protected $description = 'Delete pending registration and user account by email';
    protected $usage = 'delete:pending <email>';
    protected $arguments = [
        'email' => 'Email address to delete'
    ];

    public function run(array $params)
    {
        $email = $params[0] ?? CLI::prompt('Enter email to delete');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::write('Please provide a valid email address.', 'red');
            return;
        }

        $userModel = new UserModel();
        $pendingModel = new PendingRegistrationModel();
        
        $foundUser = false;
        $foundPending = false;
        
        // Check and delete from users table
        $user = $userModel->findByEmail($email);
        if ($user) {
            $foundUser = true;
            CLI::write("Found user: {$user['first_name']} {$user['last_name']} ({$email})", 'yellow');
        }
        
        // Check and delete from pending registrations
        $pending = $pendingModel->findByEmail($email);
        if ($pending) {
            $foundPending = true;
            CLI::write("Found pending registration: {$pending['first_name']} {$pending['last_name']} ({$email})", 'yellow');
        }
        
        if (!$foundUser && !$foundPending) {
            CLI::write("No user or pending registration found with email: {$email}", 'red');
            return;
        }
        
        // Ask for confirmation
        $confirm = CLI::prompt("Delete all records for {$email}? (y/n)", ['y', 'n']);
        if ($confirm !== 'y') {
            CLI::write('Operation cancelled.', 'yellow');
            return;
        }
        
        $deleted = 0;
        
        // Delete from users table
        if ($foundUser) {
            if ($userModel->where('email', $email)->delete()) {
                CLI::write("✓ Deleted user account", 'green');
                $deleted++;
            } else {
                CLI::write("✗ Failed to delete user account", 'red');
            }
        }
        
        // Delete from pending registrations
        if ($foundPending) {
            if ($pendingModel->where('email', $email)->delete()) {
                CLI::write("✓ Deleted pending registration", 'green');
                $deleted++;
            } else {
                CLI::write("✗ Failed to delete pending registration", 'red');
            }
        }
        
        if ($deleted > 0) {
            CLI::write("Successfully deleted {$deleted} record(s) for {$email}", 'green');
        } else {
            CLI::write("Failed to delete any records", 'red');
        }
    }
}
