<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CountUsers extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';
    protected $name = 'count:users';
    protected $description = 'Count users by role in the database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== USER COUNT SUMMARY ===', 'yellow');
        CLI::newLine();
        
        // Total users
        $totalUsers = $db->table('users')->countAllResults();
        CLI::write("Total Users: {$totalUsers}", 'white');
        
        // Count by role
        $roles = ['admin', 'presenter', 'audience', 'reviewer'];
        foreach ($roles as $role) {
            $count = $db->table('users')->where('role', $role)->countAllResults();
            CLI::write("  {$role}: {$count}", 'light_gray');
        }
        
        CLI::newLine();
        
        // Recent users (last 5)
        $recentUsers = $db->table('users')
                         ->select('email, first_name, last_name, role, created_at')
                         ->orderBy('created_at', 'DESC')
                         ->limit(5)
                         ->get()
                         ->getResultArray();
        
        CLI::write('Recent Users (Last 5):', 'cyan');
        foreach ($recentUsers as $user) {
            $name = trim($user['first_name'] . ' ' . $user['last_name']);
            $date = $user['created_at'] ? date('Y-m-d H:i', strtotime($user['created_at'])) : 'Unknown';
            CLI::write("  {$user['email']} ({$name}) - {$user['role']} - {$date}", 'light_gray');
        }
        
        CLI::newLine();
        CLI::write('=== COUNT COMPLETE ===', 'yellow');
    }
}
