<?php

/**
 * Development Routes - Only loaded in development environment
 * These routes are for testing and development purposes only
 */

// ==========================================
// EMAIL TESTING ROUTES
// ==========================================
$routes->get('test-email/verification', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendVerificationEmail(
        'sniaevents@gmail.com',
        'John Doe',
        'abc123token456'
    );
    
    return $result['success'] ? 'Verification email sent successfully!' : 'Failed: ' . $result['message'];
});

$routes->get('test-email/review-accepted', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendReviewStatusNotification(
        'sniaevents@gmail.com',
        'Jane Smith',
        'Machine Learning Applications in Healthcare',
        'ACCEPTED',
        'Excellent research work! The methodology is solid and results are impressive.'
    );
    
    return $result['success'] ? 'Review ACCEPTED email sent!' : 'Failed: ' . $result['message'];
});

$routes->get('test-email/review-revision', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendReviewStatusNotification(
        'sniaevents@gmail.com',
        'Mike Johnson', 
        'AI Ethics in Modern Society',
        'REVISION',
        'Please address: 1) Add more recent references 2) Clarify methodology section'
    );
    
    return $result['success'] ? 'Review REVISION email sent!' : 'Failed: ' . $result['message'];
});

$routes->get('test-email/review-rejected', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendReviewStatusNotification(
        'sniaevents@gmail.com',
        'Sarah Wilson',
        'Blockchain Technology Overview', 
        'REJECTED',
        'While the topic is interesting, the research lacks originality and depth.'
    );
    
    return $result['success'] ? 'Review REJECTED email sent!' : 'Failed: ' . $result['message'];
});

$routes->get('test-email/payment', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendPaymentConfirmation(
        'sniaevents@gmail.com',
        'Alex Chen',
        500000,
        'PAY-SNIA-' . date('Ymd') . '-001'
    );
    
    return $result['success'] ? 'Payment confirmation email sent!' : 'Failed: ' . $result['message'];
});

$routes->get('test-email/loa-simple', function() {
    $emailService = new \App\Services\EmailService();
    
    $result = $emailService->sendLOADelivery(
        'sniaevents@gmail.com',
        'Dr. Maria Garcia',
        'Quantum Computing in Pharmaceutical Research',
        '/fake/path/loa-document.pdf'
    );
    
    return $result['success'] ? 'LOA email sent!' : 'Failed: ' . $result['message'];
});

// ==========================================
// REGISTRATION TESTING ROUTES
// ==========================================
$routes->get('test-register-email-fixed', function() {
    try {
        $userData = [
            'first_name' => 'Backend Test',
            'last_name' => 'User',
            'email' => 'test-backend@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'phone' => '081234567890',
            'institution' => 'Test University',
            'role' => 'presenter',
            'is_verified' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userModel = new \App\Models\UserModel();
        $userId = $userModel->insert($userData);
        
        if ($userId) {
            $emailService = new \App\Services\EmailService();
            $token = base64_encode($userId . '|' . time() . '|test');
            $fullName = $userData['first_name'] . ' ' . $userData['last_name'];
            
            $result = $emailService->sendVerificationEmail(
                'sniaevents@gmail.com',
                $fullName,
                $token
            );
            
            return $result['success'] ? 
                "✅ BACKEND TEST SUCCESS!<br>User ID: {$userId}<br>Email sent!" :
                "❌ Email failed: " . $result['message'];
        } else {
            $errors = $userModel->errors();
            return "❌ User creation failed: " . json_encode($errors);
        }
    } catch(\Exception $e) {
        return "❌ Exception: " . $e->getMessage();
    }
});

// ==========================================
// DEBUG ROUTES
// ==========================================
$routes->get('debug-user-creation', function() {
    try {
        $db = \Config\Database::connect();
        
        $result1 = $db->query("SELECT 1 as test")->getRow();
        echo "✅ DB Connection: OK<br>";
        
        $result2 = $db->query("SELECT COUNT(*) as count FROM users")->getRow();
        echo "✅ Users table exists, current count: " . $result2->count . "<br>";
        
        return "Debug complete!";
    } catch(\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

$routes->get('debug-simple-insert', function() {
    try {
        $userModel = new \App\Models\UserModel();
        
        $testData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'debug-test@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'role' => 'presenter',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $userModel->insert($testData);
        
        return $userId ? 
            "✅ Simple insert success! User ID: {$userId}" :
            "❌ Insert failed. Errors: " . json_encode($userModel->errors());
            
    } catch(\Exception $e) {
        return "❌ Exception: " . $e->getMessage();
    }
});

// ==========================================
// REVIEW TESTING ROUTES
// ==========================================
$routes->get('test-review-email', function() {
    try {
        $emailService = new \App\Services\EmailService();
        
        $result = $emailService->sendReviewStatusNotification(
            'sniaevents@gmail.com',
            'Test Presenter',
            'Machine Learning Applications in Healthcare Research',
            'ACCEPTED',
            'Excellent research work! The methodology is solid and results are impressive.'
        );
        
        return $result['success'] ? 
            "✅ Review Email Integration Test SUCCESS!" :
            "❌ Email failed: " . $result['message'];
            
    } catch (\Exception $e) {
        return "❌ Test exception: " . $e->getMessage();
    }
});

// ==========================================
// PAYMENT TESTING ROUTES  
// ==========================================
$routes->get('test-payment-email', function() {
    try {
        $emailService = new \App\Services\EmailService();
        
        $result = $emailService->sendPaymentConfirmation(
            'sniaevents@gmail.com',
            'Test Payment User',
            500000,
            'DEMO-TXN-' . time()
        );
        
        return $result['success'] ? 
            "✅ Payment Email Integration Test SUCCESS!" :
            "❌ Email failed: " . $result['message'];
            
    } catch (\Exception $e) {
        return "❌ Test exception: " . $e->getMessage();
    }
});

// ==========================================
// LOA TESTING ROUTES
// ==========================================
$routes->get('test-loa-email', function() {
    try {
        $emailService = new \App\Services\EmailService();
        
        $result = $emailService->sendLOADelivery(
            'sniaevents@gmail.com',
            'Dr. Test Presenter',
            'Advanced Machine Learning Applications in Scientific Research',
            '/fake/path/loa-document.pdf'
        );
        
        return $result['success'] ? 
            "✅ LOA Email Integration Test SUCCESS!" :
            "❌ Email failed: " . $result['message'];
            
    } catch (\Exception $e) {
        return "❌ Test exception: " . $e->getMessage();
    }
});

// ==========================================
// QR CODE TESTING ROUTES
// ==========================================
$routes->get('test-qr-setup', function() {
    try {
        if (class_exists('Endroid\QrCode\QrCode')) {
            return "✅ QR Code library installed successfully!<br>Ready to generate QR codes.";
        } else {
            return "❌ QR Code library not found. Please run: composer require endroid/qr-code";
        }
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

$routes->get('test-qr-basic', 'TestController::qrBasic');
$routes->get('test-qr-custom/(:any)', 'TestController::qrCustom/$1');

$routes->get('test/debug-database', 'TestController::debugDatabase');
$routes->get('debug-database', 'TestController::debugDatabase');

$routes->get('check-table-structures', function() {
    try {
        $db = \Config\Database::connect();
        
        $tables = ['users', 'events', 'registrations', 'loa_documents', 'payments', 'abstracts'];
        $tableStructures = [];
        
        foreach ($tables as $table) {
            // Get column information
            $query = $db->query("
                SELECT 
                    column_name, 
                    data_type, 
                    is_nullable, 
                    column_default,
                    character_maximum_length
                FROM information_schema.columns 
                WHERE table_name = '$table' 
                ORDER BY ordinal_position
            ");
            
            $tableStructures[$table] = $query->getResultArray();
        }
        
        // Check foreign key relationships
        $fkQuery = $db->query("
            SELECT 
                tc.table_name, 
                kcu.column_name,
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name 
            FROM information_schema.table_constraints AS tc 
            JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema = kcu.table_schema
            JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
                AND ccu.table_schema = tc.table_schema
            WHERE tc.constraint_type = 'FOREIGN KEY'
            ORDER BY tc.table_name, kcu.column_name
        ");
        
        $foreignKeys = $fkQuery->getResultArray();
        
        // Check existing enums
        $enumQuery = $db->query("
            SELECT t.typname as enum_name, 
                   array_agg(e.enumlabel ORDER BY e.enumsortorder) as enum_values
            FROM pg_type t 
            JOIN pg_enum e ON t.oid = e.enumtypid  
            GROUP BY t.typname
            ORDER BY t.typname
        ");
        
        $existingEnums = $enumQuery->getResultArray();
        
        return json_encode([
            'table_structures' => $tableStructures,
            'foreign_keys' => $foreignKeys,
            'existing_enums' => $existingEnums,
            'analysis_timestamp' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return "❌ Structure check error: " . $e->getMessage();
    }

    // Add to Development.php or Routes.php
$routes->get('test/qr-generate/(:num)', 'TestController::testQRGenerate/$1');
$routes->get('test/qr-generate', 'TestController::testQRGenerate');
$routes->get('test/qr-validate/(:any)', 'TestController::testQRValidate/$1');
$routes->get('test/qr-scan/(:any)', 'TestController::testQRScan/$1');
$routes->get('test/qr-data/(:num)', 'TestController::testQRData/$1');
$routes->get('test/qr-data', 'TestController::testQRData');
});