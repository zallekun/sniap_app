<?php
// Temporary script to update payment status
$db = new PDO('pgsql:host=localhost;port=5432;dbname=snia_db', 'postgres', 'admin123');

// Update payment 9 to success
$stmt = $db->prepare('UPDATE payments SET payment_status = ?, transaction_id = ?, paid_at = ? WHERE id = ?');
$success = $stmt->execute(['success', 'DEMO-TXN-' . time(), date('Y-m-d H:i:s'), 9]);

if ($success) {
    echo "Payment 9 updated to success\n";
    
    // Also update registration payment status
    $stmt2 = $db->prepare('UPDATE registrations SET payment_status = ? WHERE id = ?');
    $success2 = $stmt2->execute(['paid', 16]);
    
    if ($success2) {
        echo "Registration 16 payment status updated to paid\n";
    }
} else {
    echo "Failed to update payment\n";
}
?>