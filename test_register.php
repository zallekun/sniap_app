<?php

// Simple test script for registration
include 'vendor/autoload.php';

// Test data
$testData = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'confirm_password' => 'password123',
    'phone' => '081234567890',
    'institution' => 'Test Institution',
    'role' => 'audience',
    'terms' => 'accepted'
];

// Send POST request to register
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Redirect URL: $redirectUrl\n";
echo "Response Headers:\n$response\n";