<?php

require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Check user
$db = \Config\Database::connect();
$user = $db->table('users')->where('email', 'lyzhamid@gmail.com')->get()->getRowArray();

if ($user) {
    echo "User found:\n";
    print_r($user);
} else {
    echo "User not found with email lyzhamid@gmail.com\n";
}

// Check latest user
$latestUser = $db->table('users')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
echo "\nLatest user:\n";
print_r($latestUser);