<?php

require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Check pending registrations
$db = \Config\Database::connect();
$pending = $db->table('pending_registrations')->where('email', 'lyzhamid@gmail.com')->get()->getRowArray();

if ($pending) {
    echo "Pending registration found:\n";
    print_r($pending);
    
    // Delete it
    $deleted = $db->table('pending_registrations')->where('email', 'lyzhamid@gmail.com')->delete();
    if ($deleted) {
        echo "\nPending registration deleted successfully!\n";
    } else {
        echo "\nFailed to delete pending registration.\n";
    }
} else {
    echo "No pending registration found with email lyzhamid@gmail.com\n";
}

// Check users table as well
$user = $db->table('users')->where('email', 'lyzhamid@gmail.com')->get()->getRowArray();
if ($user) {
    echo "\nUser found in users table:\n";
    print_r($user);
} else {
    echo "\nNo user found in users table with email lyzhamid@gmail.com\n";
}