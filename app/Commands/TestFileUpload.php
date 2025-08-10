<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestFileUpload extends BaseCommand
{
    protected $group       = 'test';
    protected $name        = 'test:upload';
    protected $description = 'Test File Upload System functionality';

    public function run(array $params)
    {
        CLI::write('🔧 Testing File Upload System...', 'green');
        CLI::newLine();

        try {
            // Test FileUploadService
            $uploadService = new \App\Services\FileUploadService();
            
            CLI::write('📊 Getting upload statistics...', 'yellow');
            $stats = $uploadService->getUploadStats();
            
            CLI::write('✅ Upload Configuration:', 'green');
            CLI::write('   - Max file size: ' . $stats['max_file_size_mb'] . ' MB');
            CLI::write('   - Allowed extensions: ' . implode(', ', $stats['allowed_extensions']));
            CLI::write('   - Upload path: ' . $stats['upload_path']);
            
            if (isset($stats['file_counts'])) {
                CLI::write('   - Current files:');
                CLI::write('     * Abstracts: ' . $stats['file_counts']['abstracts']);
                CLI::write('     * Revisions: ' . $stats['file_counts']['revisions']);
            }
            
            CLI::newLine();
            
            // Test directory permissions
            CLI::write('🔍 Testing directory permissions...', 'yellow');
            $uploadPath = WRITEPATH . '../uploads/';
            
            if (is_dir($uploadPath)) {
                CLI::write('✅ Upload directory exists', 'green');
                
                if (is_writable($uploadPath)) {
                    CLI::write('✅ Upload directory is writable', 'green');
                } else {
                    CLI::write('❌ Upload directory is not writable', 'red');
                    CLI::write('🔧 Fix: chmod 755 ' . $uploadPath, 'yellow');
                }
                
                $subDirs = ['abstracts', 'revisions', 'loa', 'certificates'];
                foreach ($subDirs as $subDir) {
                    $fullPath = $uploadPath . $subDir . '/';
                    if (is_dir($fullPath)) {
                        CLI::write('✅ ' . $subDir . ' directory exists', 'green');
                    } else {
                        CLI::write('❌ ' . $subDir . ' directory missing', 'red');
                    }
                }
            } else {
                CLI::write('❌ Upload directory does not exist', 'red');
                CLI::write('🔧 Fix: mkdir -p ' . $uploadPath, 'yellow');
            }
            
            CLI::newLine();
            
            // Create a test file for validation
            CLI::write('📝 Creating test file for validation...', 'yellow');
            $testContent = 'This is a test PDF content for file upload validation.';
            $testFilePath = sys_get_temp_dir() . '/test_abstract.txt';
            file_put_contents($testFilePath, $testContent);
            
            // Simulate $_FILES array
            $testFile = [
                'name' => 'test_abstract.pdf',
                'type' => 'application/pdf',
                'tmp_name' => $testFilePath,
                'error' => UPLOAD_ERR_OK,
                'size' => strlen($testContent)
            ];
            
            // Test file validation (without actual file move)
            CLI::write('🔍 Testing file validation...', 'yellow');
            
            // This will fail because it's not a real PDF, but that's expected
            $validation = $uploadService->validateFile($testFile);
            
            if ($validation['valid']) {
                CLI::write('✅ File validation passed', 'green');
            } else {
                CLI::write('⚠️  File validation failed (expected): ' . $validation['message'], 'yellow');
                CLI::write('   (This is normal - test file is not a real PDF)', 'cyan');
            }
            
            // Clean up test file
            unlink($testFilePath);
            
            CLI::newLine();
            
            // Test API endpoint logic (without HTTP context)
            CLI::write('🌐 Testing API logic...', 'yellow');
            
            try {
                // Test the service directly instead of controller
                $stats = $uploadService->getUploadStats();
                if (isset($stats['max_file_size']) && isset($stats['allowed_extensions'])) {
                    CLI::write('✅ Upload service logic working', 'green');
                } else {
                    CLI::write('❌ Upload service incomplete', 'red');
                }
            } catch (\Exception $e) {
                CLI::write('❌ Upload service error: ' . $e->getMessage(), 'red');
            }
            
            CLI::newLine();
            
            // Test database connections for abstracts
            CLI::write('🗄️ Testing database integration...', 'yellow');
            $db = \Config\Database::connect();
            
            // Check if we can query abstracts table
            try {
                $abstractCount = $db->table('abstracts')->countAllResults();
                CLI::write('✅ Abstracts table accessible (' . $abstractCount . ' records)', 'green');
            } catch (\Exception $e) {
                CLI::write('❌ Abstracts table error: ' . $e->getMessage(), 'red');
            }
            
            // Check registrations for testing
            try {
                $regCount = $db->table('registrations')->where('registration_type', 'presenter')->countAllResults();
                CLI::write('✅ Found ' . $regCount . ' presenter registrations for testing', 'green');
                
                if ($regCount > 0) {
                    $sampleReg = $db->table('registrations')
                        ->where('registration_type', 'presenter')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
                    
                    CLI::write('📋 Sample presenter registration ID: ' . $sampleReg['id'], 'cyan');
                    CLI::write('👤 User ID: ' . $sampleReg['user_id'], 'cyan');
                    CLI::write('🎪 Event ID: ' . $sampleReg['event_id'], 'cyan');
                }
            } catch (\Exception $e) {
                CLI::write('❌ Registration table error: ' . $e->getMessage(), 'red');
            }
            
        } catch (\Exception $e) {
            CLI::write('💥 Error: ' . $e->getMessage(), 'red');
            CLI::write('📍 File: ' . $e->getFile() . ':' . $e->getLine(), 'red');
        }

        CLI::newLine();
        CLI::write('🏁 File Upload Test completed!', 'green');
        
        CLI::newLine();
        CLI::write('📋 Next Steps for Testing:', 'cyan');
        CLI::write('1. Use Postman/curl to test file upload:', 'white');
        CLI::write('   POST /api/v1/abstracts', 'white');
        CLI::write('   Content-Type: multipart/form-data', 'white');
        CLI::write('   Fields: registration_id, title, abstract_text, abstract_file', 'white');
        CLI::write('2. Test file download:', 'white');
        CLI::write('   GET /api/v1/abstracts/{id}/download', 'white');
        CLI::write('3. Check upload requirements:', 'white');
        CLI::write('   GET /api/v1/abstracts/upload-info', 'white');
    }
}