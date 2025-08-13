<?php

// Direct database test
$db = new PDO('pgsql:host=localhost;port=5432;dbname=snia_db', 'postgres', 'root');

// Test create user
echo "Creating test user...\n";
$sql = "INSERT INTO users (first_name, last_name, email, password, phone, institution, role, is_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$result = $stmt->execute([
    'Test',
    'User', 
    'test' . time() . '@example.com',
    password_hash('test123', PASSWORD_DEFAULT),
    '+628123456789',
    'Test Institution',
    'audience',
    'f',
    date('Y-m-d H:i:s')
]);

if ($result) {
    $userId = $db->lastInsertId();
    echo "User created with ID: $userId\n";
    
    // Test update
    echo "Testing update...\n";
    $updateSql = "UPDATE users SET verification_code = ?, verification_code_expires = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateSql);
    $updateResult = $updateStmt->execute([
        '123456',
        date('Y-m-d H:i:s', strtotime('+15 minutes')),
        $userId
    ]);
    
    if ($updateResult) {
        echo "Update successful!\n";
        echo "Rows affected: " . $updateStmt->rowCount() . "\n";
    } else {
        echo "Update failed!\n";
        print_r($updateStmt->errorInfo());
    }
    
    // Clean up
    $db->exec("DELETE FROM users WHERE id = $userId");
    echo "Test user deleted\n";
} else {
    echo "Failed to create user\n";
    print_r($stmt->errorInfo());
}