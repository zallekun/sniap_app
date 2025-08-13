<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PendingRegistrationModel;

class CleanupExpiredRegistrations extends BaseCommand
{
    protected $group = 'maintenance';
    protected $name = 'cleanup:expired-registrations';
    protected $description = 'Clean up expired pending registrations (older than 24 hours)';
    protected $usage = 'cleanup:expired-registrations [options]';
    protected $options = [
        '--dry-run' => 'Show what would be deleted without actually deleting',
    ];

    public function run(array $params)
    {
        $pendingModel = new PendingRegistrationModel();
        $isDryRun = array_key_exists('dry-run', $params) || CLI::getOption('dry-run');
        
        // Find expired registrations
        $expiredTime = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $expired = $pendingModel->where('created_at <', $expiredTime)->findAll();
        
        if (empty($expired)) {
            CLI::write('No expired pending registrations found.', 'green');
            return;
        }
        
        CLI::write(sprintf('Found %d expired pending registration(s):', count($expired)), 'yellow');
        
        foreach ($expired as $registration) {
            $age = date('Y-m-d H:i:s', strtotime($registration['created_at']));
            CLI::write("  - {$registration['email']} (created: {$age})");
        }
        
        if ($isDryRun) {
            CLI::write("\nDry run mode - nothing was deleted.", 'blue');
            return;
        }
        
        // Ask for confirmation
        if (!CLI::prompt('Do you want to delete these expired registrations?', ['y', 'n']) === 'y') {
            CLI::write('Operation cancelled.', 'yellow');
            return;
        }
        
        // Delete expired registrations
        $deleted = $pendingModel->where('created_at <', $expiredTime)->delete();
        
        if ($deleted) {
            CLI::write(sprintf('Successfully deleted %d expired registration(s).', count($expired)), 'green');
        } else {
            CLI::write('Failed to delete expired registrations.', 'red');
        }
    }
}
