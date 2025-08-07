<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\EventModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\PaymentModel;
use App\Models\ReviewModel;
use App\Models\SystemSettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $eventModel;
    protected $registrationModel;
    protected $abstractModel;
    protected $paymentModel;
    protected $reviewModel;
    protected $systemModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
        $this->registrationModel = new RegistrationModel();
        $this->abstractModel = new AbstractModel();
        $this->paymentModel = new PaymentModel();
        $this->reviewModel = new ReviewModel();
        $this->systemModel = new SystemSettingModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Admin Dashboard Main Page
     */
    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard - SNIA Conference',
            'stats' => $this->getOverallStats(),
            'recent_activities' => $this->getRecentActivities(),
            'charts_data' => $this->getChartsData(),
            'system_alerts' => $this->getSystemAlerts(),
            'quick_stats' => $this->getQuickStats(),
        ];

        return view('admin/dashboard/index', $data);
    }

    /**
     * Get overall statistics for dashboard
     */
    public function getOverallStats()
    {
        $stats = [
            'users' => [
                'total' => $this->userModel->countAll(),
                'active' => $this->userModel->where('is_active', true)->countAllResults(),
                'new_today' => $this->userModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
                'presenters' => $this->userModel->where('role', 'presenter')->countAllResults(),
                'audience' => $this->userModel->where('role', 'audience')->countAllResults(),
                'reviewers' => $this->userModel->where('role', 'reviewer')->countAllResults(),
            ],
            'events' => [
                'total' => $this->eventModel->countAll(),
                'active' => $this->eventModel->where('is_active', true)->countAllResults(),
                'upcoming' => $this->eventModel->where('start_date >', date('Y-m-d'))->countAllResults(),
                'ongoing' => $this->eventModel->where('start_date <=', date('Y-m-d'))->where('end_date >=', date('Y-m-d'))->countAllResults(),
            ],
            'registrations' => [
                'total' => $this->registrationModel->countAll(),
                'today' => $this->registrationModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
                'confirmed' => $this->registrationModel->where('status', 'confirmed')->countAllResults(),
                'pending' => $this->registrationModel->where('status', 'pending')->countAllResults(),
            ],
            'abstracts' => [
                'total' => $this->abstractModel->countAll(),
                'pending' => $this->abstractModel->where('status', 'pending')->countAllResults(),
                'accepted' => $this->abstractModel->where('status', 'accepted')->countAllResults(),
                'rejected' => $this->abstractModel->where('status', 'rejected')->countAllResults(),
                'under_review' => $this->abstractModel->where('status', 'under_review')->countAllResults(),
            ],
            'revenue' => [
                'total' => $this->paymentModel->selectSum('amount')->where('status', 'completed')->get()->getRow()->amount ?? 0,
                'today' => $this->paymentModel->selectSum('amount')->where('status', 'completed')->where('DATE(created_at)', date('Y-m-d'))->get()->getRow()->amount ?? 0,
                'pending' => $this->paymentModel->selectSum('amount')->where('status', 'pending')->get()->getRow()->amount ?? 0,
            ]
        ];

        return $this->response->setJSON($stats);
    }

    /**
     * Get recent activities across the system
     */
    public function getRecentActivities()
    {
        $activities = [];

        // Recent user registrations
        $recentUsers = $this->userModel
            ->select('full_name, email, created_at, role')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registration',
                'message' => "{$user['full_name']} registered as {$user['role']}",
                'timestamp' => $user['created_at'],
                'icon' => 'user-plus',
                'color' => 'success'
            ];
        }

        // Recent event registrations
        $recentRegistrations = $this->registrationModel
            ->select('registrations.*, users.full_name, events.title as event_title')
            ->join('users', 'users.id = registrations.user_id')
            ->join('events', 'events.id = registrations.event_id')
            ->orderBy('registrations.created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($recentRegistrations as $reg) {
            $activities[] = [
                'type' => 'event_registration',
                'message' => "{$reg['full_name']} registered for {$reg['event_title']}",
                'timestamp' => $reg['created_at'],
                'icon' => 'calendar',
                'color' => 'info'
            ];
        }

        // Recent abstract submissions
        $recentAbstracts = $this->abstractModel
            ->select('abstracts.*, users.full_name')
            ->join('users', 'users.id = abstracts.user_id')
            ->orderBy('abstracts.created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($recentAbstracts as $abstract) {
            $activities[] = [
                'type' => 'abstract_submission',
                'message' => "{$abstract['full_name']} submitted abstract: {$abstract['title']}",
                'timestamp' => $abstract['created_at'],
                'icon' => 'file-text',
                'color' => 'warning'
            ];
        }

        // Sort by timestamp
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $this->response->setJSON(array_slice($activities, 0, 10));
    }

    /**
     * Get data for dashboard charts
     */
    public function getChartsData()
    {
        $charts = [
            'user_growth' => $this->getUserGrowthData(),
            'registration_trends' => $this->getRegistrationTrendsData(),
            'abstract_status' => $this->getAbstractStatusData(),
            'revenue_chart' => $this->getRevenueData(),
            'event_popularity' => $this->getEventPopularityData(),
        ];

        return $this->response->setJSON($charts);
    }

    /**
     * Get system alerts and warnings
     */
    public function getSystemAlerts()
    {
        $alerts = [];

        // Check for pending abstract reviews
        $pendingReviews = $this->abstractModel->where('status', 'pending')->countAllResults();
        if ($pendingReviews > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pending Abstract Reviews',
                'message' => "{$pendingReviews} abstracts are waiting for review",
                'action_url' => '/admin/abstracts?status=pending',
                'action_text' => 'Review Now'
            ];
        }

        // Check for pending payments
        $pendingPayments = $this->paymentModel->where('status', 'pending')->countAllResults();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Payments',
                'message' => "{$pendingPayments} payments are pending confirmation",
                'action_url' => '/admin/payments?status=pending',
                'action_text' => 'View Payments'
            ];
        }

        // Check for upcoming events without reviewers
        $eventsNeedReviewers = $this->eventModel
            ->where('start_date >', date('Y-m-d'))
            ->where('is_active', true)
            ->countAllResults();
        
        // You might want to add a more specific check here

        // Check system settings
        $criticalSettings = $this->systemModel->whereIn('key', [
            'smtp_configured', 'payment_gateway_configured', 'ssl_enabled'
        ])->where('value', 'false')->countAllResults();

        if ($criticalSettings > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'System Configuration',
                'message' => 'Some critical system settings need attention',
                'action_url' => '/admin/settings',
                'action_text' => 'Configure'
            ];
        }

        return $this->response->setJSON($alerts);
    }

    /**
     * Get quick statistics for widgets
     */
    public function getQuickStats()
    {
        $stats = [
            'today_registrations' => $this->registrationModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'today_revenue' => $this->paymentModel->selectSum('amount')->where('status', 'completed')->where('DATE(created_at)', date('Y-m-d'))->get()->getRow()->amount ?? 0,
            'active_events' => $this->eventModel->where('start_date <=', date('Y-m-d'))->where('end_date >=', date('Y-m-d'))->countAllResults(),
            'pending_reviews' => $this->abstractModel->where('status', 'pending')->countAllResults(),
        ];

        return $this->response->setJSON($stats);
    }

    /**
     * Export dashboard data to Excel/CSV
     */
    public function exportData($type = 'excel')
    {
        $data = [
            'users' => $this->userModel->findAll(),
            'events' => $this->eventModel->findAll(),
            'registrations' => $this->registrationModel
                ->select('registrations.*, users.full_name, users.email, events.title as event_title')
                ->join('users', 'users.id = registrations.user_id')
                ->join('events', 'events.id = registrations.event_id')
                ->findAll(),
            'abstracts' => $this->abstractModel
                ->select('abstracts.*, users.full_name, users.email')
                ->join('users', 'users.id = abstracts.user_id')
                ->findAll(),
        ];

        if ($type === 'csv') {
            return $this->exportCSV($data);
        } else {
            return $this->exportExcel($data);
        }
    }

    /**
     * Get system performance metrics
     */
    public function getPerformanceMetrics()
    {
        $metrics = [
            'database_size' => $this->getDatabaseSize(),
            'active_sessions' => $this->getActiveSessions(),
            'server_load' => sys_getloadavg(),
            'memory_usage' => [
                'used' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'disk_usage' => disk_free_space('/'),
        ];

        return $this->response->setJSON($metrics);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get user growth data for chart
     */
    private function getUserGrowthData()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");

        return $query->getResultArray();
    }

    /**
     * Get registration trends data
     */
    private function getRegistrationTrendsData()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM registrations 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");

        return $query->getResultArray();
    }

    /**
     * Get abstract status distribution
     */
    private function getAbstractStatusData()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT status, COUNT(*) as count 
            FROM abstracts 
            GROUP BY status
        ");

        return $query->getResultArray();
    }

    /**
     * Get revenue data for chart
     */
    private function getRevenueData()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT DATE(created_at) as date, SUM(amount) as revenue 
            FROM payments 
            WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");

        return $query->getResultArray();
    }

    /**
     * Get event popularity data
     */
    private function getEventPopularityData()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT e.title, COUNT(r.id) as registrations 
            FROM events e 
            LEFT JOIN registrations r ON e.id = r.event_id 
            WHERE e.is_active = true 
            GROUP BY e.id, e.title 
            ORDER BY registrations DESC 
            LIMIT 10
        ");

        return $query->getResultArray();
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT pg_size_pretty(pg_database_size(current_database())) as size");
        $result = $query->getRow();
        return $result ? $result->size : 'Unknown';
    }

    /**
     * Get active sessions count
     */
    private function getActiveSessions()
    {
        // This would depend on your session storage method
        // For file-based sessions, you could count files in session directory
        // For database sessions, count active session records
        return rand(10, 50); // Placeholder
    }

    /**
     * Export data as CSV
     */
    private function exportCSV($data)
    {
        $filename = 'snia_dashboard_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Export each data type
        foreach ($data as $type => $records) {
            fputcsv($output, [strtoupper($type) . ' DATA']);
            
            if (!empty($records)) {
                // Write headers
                fputcsv($output, array_keys($records[0]));
                
                // Write data
                foreach ($records as $record) {
                    fputcsv($output, $record);
                }
            }
            
            fputcsv($output, []); // Empty line
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export data as Excel (requires PhpSpreadsheet)
     */
    private function exportExcel($data)
    {
        // This would require PhpSpreadsheet library
        // For now, fallback to CSV
        return $this->exportCSV($data);
    }
}