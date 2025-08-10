<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestQR extends BaseCommand
{
    protected $group       = 'test';
    protected $name        = 'test:qr';
    protected $description = 'Test QR Code generation functionality';

    public function run(array $params)
    {
        CLI::write('🔧 Testing QR Code Service...', 'green');
        CLI::newLine();

        try {
            $qrService = new \App\Services\QRCodeService();
            
            // Test QR generation with first registration
            CLI::write('📝 Testing QR generation for registration ID 1...', 'yellow');
            
            $result = $qrService->generateUserQRCode(1);
            
            if ($result['success']) {
                CLI::write('✅ QR Code generated successfully!', 'green');
                CLI::write('📄 QR Data Sample:', 'cyan');
                
                $qrData = json_decode($result['qr_code']['qr_data'], true);
                CLI::write('   - Registration ID: ' . $qrData['registration_id']);
                CLI::write('   - User ID: ' . $qrData['user_id']);
                CLI::write('   - Event ID: ' . $qrData['event_id']);
                CLI::write('   - Name: ' . $qrData['name']);
                CLI::write('   - Type: ' . $qrData['registration_type']);
                CLI::write('   - Payment: ' . $qrData['payment_status']);
                
                CLI::newLine();
                CLI::write('📊 QR Hash: ' . $result['qr_code']['qr_hash'], 'blue');
                CLI::write('🖼️ QR Image: Base64 encoded (' . number_format(strlen($result['qr_code']['qr_image'])) . ' characters)', 'blue');
                
                CLI::newLine();
                
                // Test validation
                CLI::write('🔍 Testing QR validation...', 'yellow');
                $validateResult = $qrService->validateQRCode($result['qr_code']['qr_data']);
                
                if ($validateResult['success']) {
                    CLI::write('✅ QR Code validation successful!', 'green');
                    CLI::write('👤 Validated User: ' . $validateResult['registration']['first_name'] . ' ' . $validateResult['registration']['last_name']);
                    CLI::write('📧 Email: ' . $validateResult['registration']['email']);
                    CLI::write('🎫 Type: ' . $validateResult['registration']['registration_type']);
                    CLI::write('💳 Payment: ' . $validateResult['registration']['payment_status']);
                } else {
                    CLI::write('❌ QR Code validation failed: ' . $validateResult['message'], 'red');
                }
                
                CLI::newLine();
                
                // Test getting QR by user
                CLI::write('🔄 Testing getUserQRCode...', 'yellow');
                $getUserResult = $qrService->getUserQRCode($qrData['user_id']);
                
                if ($getUserResult['success']) {
                    CLI::write('✅ Successfully retrieved QR by user ID', 'green');
                } else {
                    CLI::write('❌ Failed to get QR by user: ' . $getUserResult['message'], 'red');
                }
                
            } else {
                CLI::write('❌ QR Code generation failed: ' . $result['message'], 'red');
                
                // If no registration exists, let's check database
                CLI::write('📋 Checking registrations in database...', 'yellow');
                
                $db = \Config\Database::connect();
                $count = $db->table('registrations')->countAllResults();
                CLI::write('📊 Total registrations in database: ' . $count);
                
                if ($count > 0) {
                    $registration = $db->table('registrations')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
                    CLI::write('🔍 First registration ID: ' . $registration['id']);
                    CLI::write('👤 User ID: ' . $registration['user_id']);
                    CLI::write('🎪 Event ID: ' . $registration['event_id']);
                    CLI::write('💳 Payment Status: ' . $registration['payment_status']);
                    
                    // Try with the actual registration ID
                    CLI::newLine();
                    CLI::write('🔄 Retrying with registration ID: ' . $registration['id'], 'yellow');
                    $retryResult = $qrService->generateUserQRCode($registration['id']);
                    
                    if ($retryResult['success']) {
                        CLI::write('✅ QR Code generated successfully on retry!', 'green');
                        
                        $qrData = json_decode($retryResult['qr_code']['qr_data'], true);
                        CLI::write('📄 QR Data Sample:', 'cyan');
                        CLI::write('   - Registration ID: ' . $qrData['registration_id']);
                        CLI::write('   - User ID: ' . $qrData['user_id']);
                        CLI::write('   - Event ID: ' . $qrData['event_id']);
                        CLI::write('   - Name: ' . $qrData['name']);
                        CLI::write('   - Type: ' . $qrData['registration_type']);
                        CLI::write('   - Payment: ' . $qrData['payment_status']);
                        
                        CLI::newLine();
                        CLI::write('📊 QR Hash: ' . $retryResult['qr_code']['qr_hash'], 'blue');
                        CLI::write('🖼️ QR Image: Base64 encoded (' . number_format(strlen($retryResult['qr_code']['qr_image'])) . ' characters)', 'blue');
                        CLI::write('🔒 Status: ' . $retryResult['qr_code']['status']);
                        CLI::write('✅ Verified: ' . ($retryResult['qr_code']['is_verified'] ? 'Yes' : 'No'));
                        
                        CLI::newLine();
                        
                        // Test validation
                        CLI::write('🔍 Testing QR validation...', 'yellow');
                        $validateResult = $qrService->validateQRCode($retryResult['qr_code']['qr_data']);
                        
                        if ($validateResult['success']) {
                            CLI::write('✅ QR Code validation successful!', 'green');
                            CLI::write('👤 Validated User: ' . $validateResult['registration']['first_name'] . ' ' . $validateResult['registration']['last_name']);
                            CLI::write('📧 Email: ' . $validateResult['registration']['email']);
                            CLI::write('🎫 Type: ' . $validateResult['registration']['registration_type']);
                            CLI::write('💳 Payment: ' . $validateResult['registration']['payment_status']);
                        } else {
                            CLI::write('❌ QR Code validation failed: ' . $validateResult['message'], 'red');
                        }
                        
                        CLI::newLine();
                        
                        // Test getting QR by user
                        CLI::write('🔄 Testing getUserQRCode...', 'yellow');
                        $getUserResult = $qrService->getUserQRCode($qrData['user_id']);
                        
                        if ($getUserResult['success']) {
                            CLI::write('✅ Successfully retrieved QR by user ID', 'green');
                        } else {
                            CLI::write('❌ Failed to get QR by user: ' . $getUserResult['message'], 'red');
                        }
                        
                        CLI::newLine();
                        
                        // Test record scan
                        CLI::write('📱 Testing QR scan recording...', 'yellow');
                        $scanResult = $qrService->recordQRScan(
                            $retryResult['qr_code']['id'], 
                            $qrData['user_id'], 
                            'check_in', 
                            1, // Admin user ID
                            [
                                'location' => 'Test Location',
                                'ip_address' => '127.0.0.1',
                                'user_agent' => 'CLI Test',
                                'notes' => 'QR Service test scan'
                            ]
                        );
                        
                        if ($scanResult['success']) {
                            CLI::write('✅ QR scan recorded successfully!', 'green');
                            CLI::write('📋 Scan ID: ' . $scanResult['scan_id']);
                        } else {
                            CLI::write('❌ Failed to record QR scan: ' . $scanResult['message'], 'red');
                        }
                        
                    } else {
                        CLI::write('❌ Still failed: ' . $retryResult['message'], 'red');
                    }
                }
            }
            
        } catch (\Exception $e) {
            CLI::write('💥 Error: ' . $e->getMessage(), 'red');
            CLI::write('📍 File: ' . $e->getFile() . ':' . $e->getLine(), 'red');
            CLI::write('📋 Stack trace:', 'red');
            CLI::write($e->getTraceAsString(), 'red');
        }

        CLI::newLine();
        CLI::write('🏁 QR Test completed!', 'green');
    }
}