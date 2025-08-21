<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\EventModel;
use App\Models\AbstractCategoryModel;
use App\Models\VoucherModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\SystemSettingModel;
use App\Models\PaymentModel;
use App\Models\ReviewModel;
use App\Models\CertificateModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder;

class TestController extends BaseController
{
    /**
     * Simple test to check if controller works
     */
    public function simpleTest()
    {
        $data = [
            'status' => 'success',
            'message' => 'TestController is working!',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => ENVIRONMENT,
            'php_version' => PHP_VERSION,
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION
        ];

        return $this->response->setJSON($data);
    }

    /**
     * Test profile edit page styling
     */
    public function testProfileEdit()
    {
        try {
            // Create mock user data
            $user = [
                'id' => 999,
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test.user@example.com',
                'role' => 'audience',
                'phone' => '+6281234567890',
                'institution' => 'Test University',
                'profile_photo' => null,
                'created_at' => '2024-01-15 10:30:00'
            ];

            // Create mock stats
            $stats = [
                'total_registrations' => 5,
                'upcoming_events' => 2
            ];

            $data = [
                'title' => 'Edit Profile - SNIA Conference',
                'user' => $user,
                'stats' => $stats,
                'validation' => \Config\Services::validation()
            ];

            return view('shared/edit_profile', $data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to load profile edit test',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Test reviewer assigned page styling
     */
    public function testReviewerAssigned()
    {
        try {
            // Create mock user data
            $user = [
                'id' => 999,
                'first_name' => 'Test',
                'last_name' => 'Reviewer',
                'email' => 'test.reviewer@example.com',
                'role' => 'reviewer'
            ];

            // Create mock assigned abstracts
            $assigned_abstracts = [
                [
                    'id' => 1,
                    'title' => 'AI Applications in Medical Diagnostics: A Comprehensive Review',
                    'first_name' => 'Dr. Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@university.edu',
                    'institution' => 'Stanford Medical School',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'category_name' => 'Artificial Intelligence',
                    'keywords' => 'AI, Machine Learning, Medical Diagnostics, Healthcare',
                    'abstract_text' => 'This paper presents a comprehensive review of artificial intelligence applications in medical diagnostics. We analyze various ML algorithms and their effectiveness in diagnosing different medical conditions.',
                    'reviewed_at' => null,
                    'assigned_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ],
                [
                    'id' => 2,
                    'title' => 'Machine Learning in Data Storage Optimization',
                    'first_name' => 'Prof. Michael',
                    'last_name' => 'Chen',
                    'email' => 'michael.chen@tech.edu',
                    'institution' => 'MIT Computer Science',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'category_name' => 'Storage Technology',
                    'keywords' => 'Machine Learning, Data Storage, Optimization, Performance',
                    'abstract_text' => 'This research explores the application of machine learning techniques to optimize data storage systems. We propose novel algorithms that can predict storage patterns and improve system performance.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'score' => 8,
                    'assigned_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
                ],
                [
                    'id' => 3,
                    'title' => 'Blockchain Technology in Healthcare Data Management',
                    'first_name' => 'Dr. Emily',
                    'last_name' => 'Rodriguez',
                    'email' => 'emily.r@healthcare.org',
                    'institution' => 'Johns Hopkins University',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'category_name' => 'Blockchain & Security',
                    'keywords' => 'Blockchain, Healthcare, Data Security, Privacy',
                    'abstract_text' => 'This paper investigates the potential of blockchain technology for secure healthcare data management. We present a framework for implementing blockchain-based solutions in medical institutions.',
                    'reviewed_at' => null,
                    'assigned_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ]
            ];

            $data = [
                'title' => 'Assigned Abstracts - SNIA Conference',
                'user' => $user,
                'assigned_abstracts' => $assigned_abstracts
            ];

            return view('roles/reviewer/assigned', $data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to load reviewer assigned test',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Test reviewer reviews page styling
     */
    public function testReviewerReviews()
    {
        try {
            // Create mock user data
            $user = [
                'id' => 999,
                'first_name' => 'Test',
                'last_name' => 'Reviewer',
                'email' => 'test.reviewer@example.com',
                'role' => 'reviewer'
            ];

            // Create mock completed reviews
            $completed_reviews = [
                [
                    'id' => 1,
                    'title' => 'AI Applications in Medical Diagnostics: A Comprehensive Review',
                    'first_name' => 'Dr. Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@university.edu',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'score' => 9,
                    'recommendation' => 'accept',
                    'comments' => 'This is an excellent paper with comprehensive research methodology and clear presentation of results. The AI applications in medical diagnostics are well-documented and the conclusions are supported by strong evidence.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                ],
                [
                    'id' => 2,
                    'title' => 'Machine Learning in Data Storage Optimization',
                    'first_name' => 'Prof. Michael',
                    'last_name' => 'Chen',
                    'email' => 'michael.chen@tech.edu',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'score' => 7,
                    'recommendation' => 'minor_revision',
                    'comments' => 'Good research with solid methodology. However, some minor issues need to be addressed: 1) The literature review could be more comprehensive, 2) Statistical analysis needs more detail, 3) Some figures need better labeling.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
                ],
                [
                    'id' => 3,
                    'title' => 'Blockchain Technology in Healthcare Data Management',
                    'first_name' => 'Dr. Emily',
                    'last_name' => 'Rodriguez',
                    'email' => 'emily.r@healthcare.org',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'score' => 5,
                    'recommendation' => 'major_revision',
                    'comments' => 'The topic is relevant and interesting, but the paper requires major revisions: 1) Methodology section is unclear, 2) Results presentation needs improvement, 3) Discussion lacks depth, 4) Several references are outdated.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                ],
                [
                    'id' => 4,
                    'title' => 'Legacy System Integration Challenges',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'email' => 'j.smith@enterprise.com',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'score' => 3,
                    'recommendation' => 'reject',
                    'comments' => 'Unfortunately, this paper does not meet the standards for publication. Major issues include: 1) Lack of novelty, 2) Insufficient literature review, 3) Weak methodology, 4) Poor writing quality, 5) Conclusions not supported by evidence.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
                ],
                [
                    'id' => 5,
                    'title' => 'Cloud Security Best Practices for Enterprise',
                    'first_name' => 'Maria',
                    'last_name' => 'Garcia',
                    'email' => 'maria.garcia@cloudsec.com',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'score' => 8,
                    'recommendation' => 'accept',
                    'comments' => 'Very good paper with practical insights into cloud security. The best practices are well-researched and applicable to real-world scenarios. Minor suggestion: add more case studies to strengthen the recommendations.',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                ]
            ];

            $data = [
                'title' => 'Review History - SNIA Conference',
                'user' => $user,
                'completed_reviews' => $completed_reviews
            ];

            return view('roles/reviewer/reviews', $data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to load reviewer reviews test',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Test reviewer dashboard without authentication
     */
    public function testReviewerDashboard()
    {
        try {
            // Create mock user data
            $user = [
                'id' => 999,
                'first_name' => 'Test',
                'last_name' => 'Reviewer',
                'email' => 'test.reviewer@example.com',
                'role' => 'reviewer'
            ];

            // Create mock stats
            $stats = [
                'total_assigned' => 8,
                'completed_reviews' => 5,
                'pending_reviews' => 3,
                'monthly_reviews' => 2,
                'average_score' => 85.5,
                'completion_rate' => 62.5
            ];

            // Create mock assigned abstracts
            $assigned_abstracts = [
                [
                    'id' => 1,
                    'title' => 'AI Applications in Medical Diagnostics',
                    'first_name' => 'Dr. Sarah',
                    'last_name' => 'Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'reviewed_at' => null,
                    'assigned_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'id' => 2,
                    'title' => 'Machine Learning in Data Storage Optimization',
                    'first_name' => 'Prof. Michael',
                    'last_name' => 'Chen',
                    'email' => 'michael.chen@example.com',
                    'event_title' => 'SNIA Annual Conference 2025',
                    'reviewed_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'assigned_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ]
            ];

            $data = [
                'title' => 'Reviewer Dashboard - SNIA Conference',
                'user' => $user,
                'stats' => $stats,
                'assigned_abstracts' => $assigned_abstracts
            ];

            return view('roles/reviewer/dashboard_clean', $data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Failed to load reviewer dashboard test',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Test all models
     */
    public function testAllModels()
    {
        $results = [];
        $totalErrors = 0;

        // Test UserModel
        try {
            $userModel = new UserModel();
            $userCount = $userModel->countAll();
            $results['UserModel'] = [
                'status' => 'success',
                'count' => $userCount,
                'message' => "UserModel working - {$userCount} records"
            ];
        } catch (\Exception $e) {
            $results['UserModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test EventModel
        try {
            $eventModel = new EventModel();
            $eventCount = $eventModel->countAll();
            $results['EventModel'] = [
                'status' => 'success',
                'count' => $eventCount,
                'message' => "EventModel working - {$eventCount} records"
            ];
        } catch (\Exception $e) {
            $results['EventModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test AbstractCategoryModel
        try {
            $categoryModel = new AbstractCategoryModel();
            $categoryCount = $categoryModel->countAll();
            $results['AbstractCategoryModel'] = [
                'status' => 'success',
                'count' => $categoryCount,
                'message' => "AbstractCategoryModel working - {$categoryCount} records"
            ];
        } catch (\Exception $e) {
            $results['AbstractCategoryModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test VoucherModel
        try {
            $voucherModel = new VoucherModel();
            $voucherCount = $voucherModel->countAll();
            $results['VoucherModel'] = [
                'status' => 'success',
                'count' => $voucherCount,
                'message' => "VoucherModel working - {$voucherCount} records"
            ];
        } catch (\Exception $e) {
            $results['VoucherModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test RegistrationModel
        try {
            $registrationModel = new RegistrationModel();
            $registrationCount = $registrationModel->countAll();
            $results['RegistrationModel'] = [
                'status' => 'success',
                'count' => $registrationCount,
                'message' => "RegistrationModel working - {$registrationCount} records"
            ];
        } catch (\Exception $e) {
            $results['RegistrationModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test AbstractModel
        try {
            $abstractModel = new AbstractModel();
            $abstractCount = $abstractModel->countAll();
            $results['AbstractModel'] = [
                'status' => 'success',
                'count' => $abstractCount,
                'message' => "AbstractModel working - {$abstractCount} records"
            ];
        } catch (\Exception $e) {
            $results['AbstractModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test SystemSettingModel
        try {
            $systemModel = new SystemSettingModel();
            $systemCount = $systemModel->countAll();
            $results['SystemSettingModel'] = [
                'status' => 'success',
                'count' => $systemCount,
                'message' => "SystemSettingModel working - {$systemCount} records"
            ];
        } catch (\Exception $e) {
            $results['SystemSettingModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test PaymentModel
        try {
            $paymentModel = new PaymentModel();
            $paymentCount = $paymentModel->countAll();
            $results['PaymentModel'] = [
                'status' => 'success',
                'count' => $paymentCount,
                'message' => "PaymentModel working - {$paymentCount} records"
            ];
        } catch (\Exception $e) {
            $results['PaymentModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test ReviewModel
        try {
            $reviewModel = new ReviewModel();
            $reviewCount = $reviewModel->countAll();
            $results['ReviewModel'] = [
                'status' => 'success',
                'count' => $reviewCount,
                'message' => "ReviewModel working - {$reviewCount} records"
            ];
        } catch (\Exception $e) {
            $results['ReviewModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test CertificateModel
        try {
            $certificateModel = new CertificateModel();
            $certificateCount = $certificateModel->countAll();
            $results['CertificateModel'] = [
                'status' => 'success',
                'count' => $certificateCount,
                'message' => "CertificateModel working - {$certificateCount} records"
            ];
        } catch (\Exception $e) {
            $results['CertificateModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Test NotificationModel
        try {
            $notificationModel = new NotificationModel();
            $notificationCount = $notificationModel->countAll();
            $results['NotificationModel'] = [
                'status' => 'success',
                'count' => $notificationCount,
                'message' => "NotificationModel working - {$notificationCount} records"
            ];
        } catch (\Exception $e) {
            $results['NotificationModel'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            $totalErrors++;
        }

        // Summary
        $summary = [
            'total_models' => 11,
            'successful' => 11 - $totalErrors,
            'errors' => $totalErrors,
            'overall_status' => $totalErrors === 0 ? 'ALL MODELS WORKING' : "SOME MODELS HAVE ERRORS",
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $response = [
            'summary' => $summary,
            'results' => $results
        ];

        return $this->response->setJSON($response);
    }

    /**
     * Debug database connection
     */
    public function debugDatabase()
    {
        $results = [];

        // Test database connection
        try {
            $db = \Config\Database::connect();
            
            // Test basic connection
            $query = $db->query('SELECT 1 as test');
            $result = $query->getRow();
            
            if ($result && $result->test == 1) {
                $results['connection'] = [
                    'status' => 'success',
                    'message' => 'Database connection successful'
                ];
            } else {
                $results['connection'] = [
                    'status' => 'error',
                    'message' => 'Database connection failed'
                ];
            }

            // Get database info
            $versionQuery = $db->query('SELECT version() as version');
            $versionResult = $versionQuery->getRow();
            
            $results['database_info'] = [
                'version' => $versionResult ? $versionResult->version : 'Unknown',
                'database' => $db->getDatabase(),
                'platform' => $db->getPlatform()
            ];

            // List all tables
            $tables = $db->listTables();
            $results['tables'] = [
                'count' => count($tables),
                'list' => $tables
            ];

            // Check each table
            $tableStatus = [];
            foreach ($tables as $table) {
                try {
                    $query = $db->query("SELECT COUNT(*) as count FROM {$table}");
                    $result = $query->getRow();
                    $tableStatus[$table] = [
                        'status' => 'accessible',
                        'row_count' => $result->count
                    ];
                } catch (\Exception $e) {
                    $tableStatus[$table] = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }
            $results['table_status'] = $tableStatus;

        } catch (\Exception $e) {
            $results['connection'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return $this->response->setJSON($results);
    }

    /**
     * Test specific model
     */
    public function testModel($modelName = null)
    {
        if (!$modelName) {
            return $this->response->setJSON([
                'error' => 'Model name is required',
                'usage' => '/test/model/ModelName'
            ]);
        }

        $modelClass = "App\\Models\\{$modelName}";
        
        if (!class_exists($modelClass)) {
            return $this->response->setJSON([
                'error' => "Model {$modelName} not found",
                'model_class' => $modelClass
            ]);
        }

        try {
            $model = new $modelClass();
            $count = $model->countAll();
            
            // Get sample data
            $sampleData = $model->limit(5)->find();
            
            return $this->response->setJSON([
                'status' => 'success',
                'model' => $modelName,
                'total_records' => $count,
                'sample_data' => $sampleData,
                'table_name' => $model->getTable() ?? 'Unknown'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'model' => $modelName,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Test system requirements
     */
    public function systemRequirements()
    {
        $requirements = [
            'php_version' => [
                'current' => PHP_VERSION,
                'required' => '7.4.0',
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'OK' : 'FAIL'
            ],
            'extensions' => [
                'intl' => extension_loaded('intl'),
                'mbstring' => extension_loaded('mbstring'),
                'pdo' => extension_loaded('pdo'),
                'pdo_pgsql' => extension_loaded('pdo_pgsql'),
                'json' => extension_loaded('json'),
                'curl' => extension_loaded('curl'),
                'fileinfo' => extension_loaded('fileinfo')
            ],
            'directories' => [
                'writable' => is_writable(WRITEPATH),
                'writable_logs' => is_writable(WRITEPATH . 'logs'),
                'writable_cache' => is_writable(WRITEPATH . 'cache'),
                'writable_session' => is_writable(WRITEPATH . 'session'),
                'writable_uploads' => is_writable(WRITEPATH . 'uploads')
            ],
            'configuration' => [
                'environment' => ENVIRONMENT,
                'base_url' => base_url(),
                'timezone' => date_default_timezone_get(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ]
        ];

        return $this->response->setJSON($requirements);
    }

    
/**
 * Test QR Code generation (WORKING for v6.x)
 */
public function qrBasic()
{
    try {
        // Simple approach that should work
        $qrCode = new \Endroid\QrCode\QrCode('Hello SNIA QR Code Test!');
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        
        $result = $writer->write($qrCode);
        
        $this->response->setHeader('Content-Type', $result->getMimeType());
        return $this->response->setBody($result->getString());
        
    } catch (\Exception $e) {
        return "❌ QR Generation Error: " . $e->getMessage();
    }
}

/**
 * Test QR Code with custom data (WORKING for v6.x)  
 */
public function qrCustom($data = null)
{
    try {
        $qrData = $data ? urldecode($data) : 'Default SNIA QR Code';
        
        $qrCode = new \Endroid\QrCode\QrCode($qrData);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        
        $result = $writer->write($qrCode);
        
        $this->response->setHeader('Content-Type', $result->getMimeType());
        return $this->response->setBody($result->getString());
        
    } catch (\Exception $e) {
        return "❌ QR Generation Error: " . $e->getMessage();
    }
}

/**
 * Test QR Code Service - Generate QR
 */
public function testQRGenerate($registrationId = null)
{
    try {
        // Use registration ID from URL or default to 1
        $regId = $registrationId ?? 1;
        
        $qrService = new \App\Services\QRCodeService();
        $result = $qrService->generateRegistrationQR($regId);
        
        if ($result['success']) {
            $response = [
                'status' => 'success',
                'registration_id' => $regId,
                'qr_hash' => $result['qr_hash'],
                'qr_data' => is_string($result['qr_data']) ? json_decode($result['qr_data'], true) : $result['qr_data'],
                'expires_at' => $result['expires_at'] ?? 'Not provided',
                'message' => $result['message'],
                'qr_status' => $result['status']
            ];
            
            // If includes image, show size info
            if (isset($result['qr_image'])) {
                $response['qr_image_size'] = strlen($result['qr_image']) . ' bytes (base64)';
            }
            
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Test failed: ' . $e->getMessage()
        ]);
    }
}

/**
 * Test QR Code Service - Validate QR
 */
public function testQRValidate($qrHash = null)
{
    try {
        if (!$qrHash) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'QR hash is required',
                'usage' => '/test/qr-validate/{qr_hash}'
            ]);
        }
        
        $qrService = new \App\Services\QRCodeService();
        $result = $qrService->validateQR($qrHash);
        
        return $this->response->setJSON([
            'status' => $result['valid'] ? 'success' : 'error',
            'valid' => $result['valid'],
            'message' => $result['message'],
            'registration' => $result['registration'] ?? null
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Validation test failed: ' . $e->getMessage()
        ]);
    }
}

/**
 * Test QR Code Service - Scan QR
 */
public function testQRScan($qrHash = null)
{
    try {
        if (!$qrHash) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'QR hash is required',
                'usage' => '/test/qr-scan/{qr_hash}'
            ]);
        }
        
        $qrService = new \App\Services\QRCodeService();
        $result = $qrService->scanQR(
            $qrHash, 
            1, // scanned_by (admin user)
            'test_entrance',
            'Test Scanner'
        );
        
        return $this->response->setJSON([
            'status' => $result['success'] ? 'success' : 'error',
            'scan_success' => $result['success'],
            'scan_result' => $result['scan_result'],
            'message' => $result['message'],
            'participant' => $result['participant'] ?? null,
            'attendance' => $result['attendance'] ?? null
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Scan test failed: ' . $e->getMessage()
        ]);
    }
}

/**
 * Test QR Code Service - Get QR Data
 */
public function testQRData($registrationId = null)
{
    try {
        $regId = $registrationId ?? 1;
        
        $qrService = new \App\Services\QRCodeService();
        $qrData = $qrService->getQRData($regId);
        $scanHistory = $qrService->getScanHistory($regId);
        
        return $this->response->setJSON([
            'status' => 'success',
            'registration_id' => $regId,
            'qr_data' => $qrData,
            'scan_history' => $scanHistory,
            'scan_count' => count($scanHistory)
        ]);
        
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data retrieval test failed: ' . $e->getMessage()
        ]);
    }
}

}