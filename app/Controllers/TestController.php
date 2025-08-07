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
}