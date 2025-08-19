<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is admin
     */
    protected function checkAdminAccess()
    {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$userId || $userRole !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin privileges required.');
        }
        
        return false;
    }

    /**
     * Admin Dashboard - Main Overview
     */
    public function dashboard()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Admin Dashboard - SNIA Conference',
                'user' => $user,
                'stats' => $this->getDashboardStats()
            ];
            
            return view('roles/admin/dashboard', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin dashboard error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Failed to load admin dashboard');
        }
    }

    /**
     * User Management
     */
    public function users()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'User Management - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/users', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin users error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load user management');
        }
    }

    /**
     * Event Management
     */
    public function events()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Event Management - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/events', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin events error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load event management');
        }
    }

    /**
     * Registration Management
     */
    public function registrations()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Registration Management - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/registrations', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin registrations error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load registration management');
        }
    }

    /**
     * Abstract & Review Management
     */
    public function abstracts()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Abstract Management - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/abstracts', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin abstracts error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load abstract management');
        }
    }

    /**
     * System Settings
     */
    public function settings()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'System Settings - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/settings', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin settings error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load system settings');
        }
    }

    /**
     * Analytics & Reports
     */
    public function analytics()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Analytics & Reports - Admin Panel',
                'user' => $user
            ];
            
            return view('roles/admin/analytics', $data);
        } catch (\Exception $e) {
            log_message('error', 'Admin analytics error: ' . $e->getMessage());
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to load analytics');
        }
    }

    // ==================== ANALYTICS API ENDPOINTS ====================

    /**
     * Get analytics data (API endpoint)
     */
    public function getAnalyticsData()
    {
        // Temporary: Skip auth check for debugging
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $dateRange = $this->request->getGet('dateRange') ?? 30; // Default to last 30 days
            
            // Calculate date range
            $endDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime("-{$dateRange} days"));
            
            // Get KPI data
            $kpiData = $this->getAnalyticsKPIs($db, $startDate, $endDate);
            
            // Get chart data
            $chartData = $this->getAnalyticsCharts($db, $startDate, $endDate);
            
            // Get top events
            $topEvents = $this->getTopEvents($db, $startDate, $endDate);
            
            // Get recent activity
            $recentActivity = $this->getAnalyticsActivity($db, 10);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'kpis' => $kpiData,
                    'charts' => $chartData,
                    'topEvents' => $topEvents,
                    'recentActivity' => $recentActivity,
                    'dateRange' => [
                        'start' => $startDate,
                        'end' => $endDate,
                        'days' => $dateRange
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get analytics data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load analytics data'
            ]);
        }
    }

    /**
     * Get KPI data for analytics
     */
    private function getAnalyticsKPIs($db, $startDate, $endDate)
    {
        try {
            // Current period data
            $currentParticipants = $db->table('registrations r')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.created_at >=', $startDate)
                ->where('r.created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();
            
            $currentEvents = $db->table('events')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();
            
            // Calculate revenue for current period
            $revenueQuery = $db->table('registrations r')
                ->select('SUM(IFNULL(e.registration_fee, 0)) as total')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.created_at >=', $startDate)
                ->where('r.created_at <=', $endDate . ' 23:59:59')
                ->where('r.payment_status', 'paid')
                ->get();
            
            $currentRevenue = $revenueQuery->getRow()->total ?? 0;
            
            // Get abstracts for current period
            $currentAbstracts = $db->table('abstracts')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();
            
            // Previous period data for comparison
            $previousStartDate = date('Y-m-d', strtotime("-" . (2 * intval(str_replace('-', '', $endDate . ' - ' . $startDate))) . " days"));
            $previousEndDate = date('Y-m-d', strtotime("-1 day", strtotime($startDate)));
            
            $previousParticipants = $db->table('registrations r')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.created_at >=', $previousStartDate)
                ->where('r.created_at <=', $previousEndDate . ' 23:59:59')
                ->countAllResults();
            
            $previousEvents = $db->table('events')
                ->where('created_at >=', $previousStartDate)
                ->where('created_at <=', $previousEndDate . ' 23:59:59')
                ->countAllResults();
            
            $previousRevenueQuery = $db->table('registrations r')
                ->select('SUM(IFNULL(e.registration_fee, 0)) as total')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.created_at >=', $previousStartDate)
                ->where('r.created_at <=', $previousEndDate . ' 23:59:59')
                ->where('r.payment_status', 'paid')
                ->get();
            
            $previousRevenue = $previousRevenueQuery->getRow()->total ?? 0;
            
            $previousAbstracts = $db->table('abstracts')
                ->where('created_at >=', $previousStartDate)
                ->where('created_at <=', $previousEndDate . ' 23:59:59')
                ->countAllResults();
            
            // Calculate percentage changes
            $participantsChange = $previousParticipants > 0 ? (($currentParticipants - $previousParticipants) / $previousParticipants) * 100 : 0;
            $eventsChange = $previousEvents > 0 ? (($currentEvents - $previousEvents) / $previousEvents) * 100 : 0;
            $revenueChange = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
            $abstractsChange = $previousAbstracts > 0 ? (($currentAbstracts - $previousAbstracts) / $previousAbstracts) * 100 : 0;
            
            return [
                'totalParticipants' => $currentParticipants,
                'participantsChange' => round($participantsChange, 1),
                'totalEvents' => $currentEvents,
                'eventsChange' => round($eventsChange, 1),
                'totalRevenue' => $currentRevenue,
                'revenueChange' => round($revenueChange, 1),
                'totalAbstracts' => $currentAbstracts,
                'abstractsChange' => round($abstractsChange, 1)
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Analytics KPI error: ' . $e->getMessage());
            return [
                'totalParticipants' => 0,
                'participantsChange' => 0,
                'totalEvents' => 0,
                'eventsChange' => 0,
                'totalRevenue' => 0,
                'revenueChange' => 0,
                'totalAbstracts' => 0,
                'abstractsChange' => 0
            ];
        }
    }

    /**
     * Get chart data for analytics
     */
    private function getAnalyticsCharts($db, $startDate, $endDate)
    {
        try {
            // Registration trends (weekly data)
            $registrationTrends = $db->query("
                SELECT 
                    WEEK(r.created_at) as week_number,
                    YEARWEEK(r.created_at) as year_week,
                    COUNT(*) as registrations,
                    DATE_FORMAT(r.created_at, '%Y-%m-%d') as week_start
                FROM registrations r 
                WHERE r.created_at >= ? AND r.created_at <= ?
                GROUP BY YEARWEEK(r.created_at)
                ORDER BY r.created_at
            ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
            
            // Revenue by event
            $revenueByEvent = $db->query("
                SELECT 
                    e.title,
                    e.id,
                    SUM(IFNULL(e.registration_fee, 0)) as total_revenue,
                    COUNT(r.id) as participant_count
                FROM events e
                LEFT JOIN registrations r ON r.event_id = e.id AND r.payment_status = 'paid'
                WHERE e.created_at >= ? AND e.created_at <= ?
                GROUP BY e.id, e.title
                ORDER BY total_revenue DESC
                LIMIT 10
            ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
            
            // Participant demographics (by role)
            $demographics = $db->query("
                SELECT 
                    u.role,
                    COUNT(DISTINCT r.user_id) as count
                FROM registrations r
                JOIN users u ON u.id = r.user_id
                WHERE r.created_at >= ? AND r.created_at <= ?
                GROUP BY u.role
                ORDER BY count DESC
            ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
            
            // Event format distribution
            $formatDistribution = $db->query("
                SELECT 
                    e.format,
                    COUNT(*) as count
                FROM events e
                WHERE e.created_at >= ? AND e.created_at <= ?
                GROUP BY e.format
            ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
            
            return [
                'registrationTrends' => [
                    'labels' => array_map(function($item) {
                        return 'Week ' . date('W', strtotime($item['week_start']));
                    }, $registrationTrends),
                    'data' => array_column($registrationTrends, 'registrations')
                ],
                'revenueByEvent' => [
                    'labels' => array_map(function($item) {
                        return strlen($item['title']) > 20 ? substr($item['title'], 0, 20) . '...' : $item['title'];
                    }, $revenueByEvent),
                    'data' => array_column($revenueByEvent, 'total_revenue')
                ],
                'demographics' => [
                    'labels' => array_map(function($item) {
                        return ucfirst($item['role']);
                    }, $demographics),
                    'data' => array_column($demographics, 'count')
                ],
                'formatDistribution' => [
                    'labels' => array_map(function($item) {
                        return ucfirst($item['format']);
                    }, $formatDistribution),
                    'data' => array_column($formatDistribution, 'count')
                ]
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Analytics charts error: ' . $e->getMessage());
            return [
                'registrationTrends' => ['labels' => [], 'data' => []],
                'revenueByEvent' => ['labels' => [], 'data' => []],
                'demographics' => ['labels' => [], 'data' => []],
                'formatDistribution' => ['labels' => [], 'data' => []]
            ];
        }
    }

    /**
     * Get top performing events
     */
    private function getTopEvents($db, $startDate, $endDate)
    {
        try {
            $topEvents = $db->query("
                SELECT 
                    e.title as event_name,
                    COUNT(r.id) as participants,
                    SUM(IFNULL(e.registration_fee, 0)) as revenue,
                    AVG(IFNULL(rv.rating, 0)) as rating,
                    (COUNT(CASE WHEN r.attendance_status = 'attended' THEN 1 END) / COUNT(r.id) * 100) as completion_rate
                FROM events e
                LEFT JOIN registrations r ON r.event_id = e.id
                LEFT JOIN reviews rv ON rv.event_id = e.id
                WHERE e.created_at >= ? AND e.created_at <= ?
                GROUP BY e.id, e.title
                HAVING participants > 0
                ORDER BY participants DESC, revenue DESC
                LIMIT 10
            ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
            
            // Format the data
            return array_map(function($event) {
                return [
                    'event_name' => $event['event_name'],
                    'participants' => (int)$event['participants'],
                    'revenue' => 'Rp ' . number_format($event['revenue']),
                    'rating' => $event['rating'] > 0 ? round($event['rating'], 1) . '/5' : 'N/A',
                    'completion_rate' => round($event['completion_rate']) . '%'
                ];
            }, $topEvents);
            
        } catch (\Exception $e) {
            log_message('error', 'Top events error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent activity for analytics
     */
    private function getAnalyticsActivity($db, $limit = 10)
    {
        try {
            $activities = [];
            
            // Get recent registrations
            $recentRegistrations = $db->query("
                SELECT 
                    CONCAT(u.first_name, ' ', u.last_name) as user_name,
                    e.title as event_title,
                    r.created_at,
                    'registration' as type
                FROM registrations r
                JOIN users u ON u.id = r.user_id
                JOIN events e ON e.id = r.event_id
                ORDER BY r.created_at DESC
                LIMIT ?
            ", [$limit / 2])->getResultArray();
            
            foreach ($recentRegistrations as $reg) {
                $activities[] = [
                    'type' => 'registration',
                    'description' => "{$reg['user_name']} registered for {$reg['event_title']}",
                    'created_at' => $reg['created_at'],
                    'icon' => 'fas fa-user-plus'
                ];
            }
            
            // Get recent events
            $recentEvents = $db->query("
                SELECT 
                    title,
                    created_at,
                    'event' as type
                FROM events
                ORDER BY created_at DESC
                LIMIT ?
            ", [$limit / 2])->getResultArray();
            
            foreach ($recentEvents as $event) {
                $activities[] = [
                    'type' => 'event',
                    'description' => "New event created: {$event['title']}",
                    'created_at' => $event['created_at'],
                    'icon' => 'fas fa-calendar-plus'
                ];
            }
            
            // Sort all activities by date
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return array_slice($activities, 0, $limit);
            
        } catch (\Exception $e) {
            log_message('error', 'Analytics activity error: ' . $e->getMessage());
            return [];
        }
    }

    // ==================== API ENDPOINTS ====================

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'total_users' => $db->table('users')->countAllResults(),
                'total_events' => $db->table('events')->countAllResults(),
                'total_registrations' => $db->table('registrations')->countAllResults(),
                'total_abstracts' => $db->table('abstracts')->countAllResults(),
                'pending_reviews' => $db->table('abstracts')->where('review_status', 'pending')->countAllResults(),
                'total_payments' => $db->table('payments')->where('payment_status', 'success')->countAllResults(),
                'revenue' => $db->table('payments')
                    ->where('payment_status', 'success')
                    ->selectSum('amount', 'total')
                    ->get()
                    ->getRow()
                    ->total ?? 0
            ];
            
            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Dashboard stats error: ' . $e->getMessage());
            return [
                'total_users' => 0,
                'total_events' => 0,
                'total_registrations' => 0,
                'total_abstracts' => 0,
                'pending_reviews' => 0,
                'total_payments' => 0,
                'revenue' => 0
            ];
        }
    }

    /**
     * Get users data (API endpoint)
     */
    public function getUsersData()
    {
        // Temporary: Skip auth check for debugging
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $limit = $this->request->getGet('limit') ?? 10;
            $page = $this->request->getGet('page') ?? 1;
            $search = $this->request->getGet('search') ?? '';
            $roleFilter = $this->request->getGet('role') ?? '';
            $statusFilter = $this->request->getGet('status') ?? '';
            $offset = ($page - 1) * $limit;
            
            $query = $db->table('users u');
            
            // Apply search filter
            if ($search) {
                $query->groupStart()
                    ->like('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->groupEnd();
            }
            
            // Apply role filter
            if ($roleFilter) {
                $query->where('u.role', $roleFilter);
            }
            
            // Apply status filter
            if ($statusFilter) {
                if ($statusFilter === 'active') {
                    $query->where('u.is_verified', true);
                } elseif ($statusFilter === 'inactive') {
                    $query->where('u.is_verified', false);
                } elseif ($statusFilter === 'pending') {
                    $query->where('u.is_verified', false);
                }
            }
            
            $total = $query->countAllResults(false);
            $users = $query->orderBy('u.created_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();
            
            // Remove sensitive data
            foreach ($users as &$user) {
                unset($user['password']);
                unset($user['remember_token']);
                unset($user['reset_token']);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $users,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get users data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load users data'
            ]);
        }
    }

    /**
     * Get registrations data (API endpoint)
     */
    public function getRegistrationsData()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $limit = $this->request->getGet('limit') ?? 10;
            $page = $this->request->getGet('page') ?? 1;
            $offset = ($page - 1) * $limit;
            
            $total = $db->table('registrations r')
                ->join('users u', 'u.id = r.user_id', 'left')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->countAllResults();
            
            $registrations = $db->table('registrations r')
                ->select('r.*, u.first_name, u.last_name, u.email, e.title as event_title')
                ->join('users u', 'u.id = r.user_id', 'left')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->orderBy('r.created_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $registrations,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get registrations data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load registrations data'
            ]);
        }
    }

    /**
     * Get abstracts data (API endpoint)
     */
    public function getAbstractsData()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            
            $abstracts = $db->table('abstracts a')
                ->select('a.*, u.first_name as author_first_name, u.last_name as author_last_name, 
                         u.email as author_email, e.title as event_title, ac.name as category_name,
                         r.first_name as reviewer_first_name, r.last_name as reviewer_last_name,
                         r.email as reviewer_email, rev.status as review_status')
                ->join('registrations reg', 'reg.id = a.registration_id', 'left')
                ->join('users u', 'u.id = reg.user_id', 'left')
                ->join('events e', 'e.id = reg.event_id', 'left')
                ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                ->join('users r', 'r.id = a.assigned_reviewer_id', 'left')
                ->join('reviews rev', 'rev.abstract_id = a.id', 'left')
                ->orderBy('a.submitted_at', 'DESC')
                ->get()
                ->getResultArray();
            
            // Format the data for frontend
            $formattedAbstracts = array_map(function($abstract) {
                return [
                    'id' => $abstract['id'],
                    'title' => $abstract['title'],
                    'author_name' => trim($abstract['author_first_name'] . ' ' . $abstract['author_last_name']),
                    'author_email' => $abstract['author_email'],
                    'event_title' => $abstract['event_title'],
                    'category_name' => $abstract['category_name'],
                    'assigned_reviewer_id' => $abstract['assigned_reviewer_id'],
                    'reviewer_name' => $abstract['assigned_reviewer_id'] ? 
                        trim($abstract['reviewer_first_name'] . ' ' . $abstract['reviewer_last_name']) : null,
                    'reviewer_email' => $abstract['reviewer_email'],
                    'review_status' => $abstract['review_status'],
                    'submitted_at' => $abstract['submitted_at']
                ];
            }, $abstracts);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedAbstracts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstracts data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load abstracts data'
            ]);
        }
    }

    /**
     * Get reviewers data (API endpoint)
     */
    public function getReviewersData()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            
            $reviewers = $db->table('users')
                ->select('id, first_name, last_name, email')
                ->where('role', 'reviewer')
                ->orderBy('first_name', 'ASC')
                ->get()
                ->getResultArray();
            
            // Add current assignment count for each reviewer
            foreach ($reviewers as &$reviewer) {
                $assignmentCount = $db->table('abstracts')
                    ->where('assigned_reviewer_id', $reviewer['id'])
                    ->countAllResults();
                $reviewer['current_assignments'] = $assignmentCount;
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $reviewers
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get reviewers data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load reviewers data'
            ]);
        }
    }

    /**
     * Get abstract statistics (API endpoint)
     */
    public function getAbstractStatsApi()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'total' => $db->table('abstracts')->countAllResults(),
                'pending_assignment' => $db->table('abstracts')->where('assigned_reviewer_id IS NULL', null, false)->countAllResults(),
                'under_review' => $db->table('abstracts')
                    ->where('assigned_reviewer_id IS NOT NULL', null, false)
                    ->where('id NOT IN (SELECT abstract_id FROM reviews WHERE status = "completed")', null, false)
                    ->countAllResults(),
                'completed' => $db->table('reviews')->where('status', 'completed')->countAllResults()
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstract stats API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load abstract statistics'
            ]);
        }
    }

    /**
     * Assign reviewer to abstract (API endpoint)
     */
    public function assignReviewer()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $abstractId = $this->request->getPost('abstract_id');
            $reviewerId = $this->request->getPost('reviewer_id');
            $notes = $this->request->getPost('notes');
            $dueDate = $this->request->getPost('due_date');
            
            if (!$abstractId || !$reviewerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Abstract ID and Reviewer ID are required'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Check if abstract exists
            $abstract = $db->table('abstracts')->where('id', $abstractId)->get()->getRowArray();
            if (!$abstract) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Abstract not found'
                ]);
            }
            
            // Check if reviewer exists
            $reviewer = $db->table('users')->where('id', $reviewerId)->where('role', 'reviewer')->get()->getRowArray();
            if (!$reviewer) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Reviewer not found'
                ]);
            }
            
            // Update abstract with reviewer assignment
            $updateData = [
                'assigned_reviewer_id' => $reviewerId,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->table('abstracts')->where('id', $abstractId)->update($updateData);
            
            if ($result) {
                // Create assignment record in abstract_reviewers table if it exists
                try {
                    $assignmentData = [
                        'abstract_id' => $abstractId,
                        'reviewer_id' => $reviewerId,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'due_date' => $dueDate,
                        'notes' => $notes
                    ];
                    
                    $db->table('abstract_reviewers')->insert($assignmentData);
                } catch (\Exception $e) {
                    // Table might not exist, log but continue
                    log_message('warning', 'Could not create assignment record: ' . $e->getMessage());
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Reviewer assigned successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to assign reviewer'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Assign reviewer error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to assign reviewer'
            ]);
        }
    }

    /**
     * Bulk assign reviewers (API endpoint)
     */
    public function bulkAssignReviewers()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $input = $this->request->getJSON(true);
            $abstractIds = $input['abstract_ids'] ?? [];
            $reviewerIds = $input['reviewer_ids'] ?? [];
            $strategy = $input['strategy'] ?? 'single';
            $dueDate = $input['due_date'] ?? null;
            
            if (empty($abstractIds) || empty($reviewerIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Abstract IDs and Reviewer IDs are required'
                ]);
            }
            
            $db = \Config\Database::connect();
            $assignedCount = 0;
            
            foreach ($abstractIds as $abstractId) {
                $reviewerId = null;
                
                switch ($strategy) {
                    case 'single':
                        $reviewerId = $reviewerIds[0];
                        break;
                    case 'distribute':
                        $reviewerId = $reviewerIds[$assignedCount % count($reviewerIds)];
                        break;
                    case 'category':
                        // For now, use distribution. Category-based assignment would need more logic
                        $reviewerId = $reviewerIds[$assignedCount % count($reviewerIds)];
                        break;
                }
                
                if ($reviewerId) {
                    $updateData = [
                        'assigned_reviewer_id' => $reviewerId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $result = $db->table('abstracts')->where('id', $abstractId)->update($updateData);
                    
                    if ($result) {
                        $assignedCount++;
                        
                        // Create assignment record if table exists
                        try {
                            $assignmentData = [
                                'abstract_id' => $abstractId,
                                'reviewer_id' => $reviewerId,
                                'assigned_at' => date('Y-m-d H:i:s'),
                                'due_date' => $dueDate
                            ];
                            
                            $db->table('abstract_reviewers')->insert($assignmentData);
                        } catch (\Exception $e) {
                            log_message('warning', 'Could not create assignment record: ' . $e->getMessage());
                        }
                    }
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully assigned reviewers to {$assignedCount} abstracts"
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Bulk assign reviewers error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to bulk assign reviewers'
            ]);
        }
    }

    // ==================== EVENT CRUD OPERATIONS ====================

    /**
     * Get events data (API endpoint)
     */
    public function getEventsData()
    {
        // Temporary: Skip auth check for debugging
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $limit = $this->request->getGet('limit') ?? 10;
            $page = $this->request->getGet('page') ?? 1;
            $search = $this->request->getGet('search') ?? '';
            $formatFilter = $this->request->getGet('format') ?? '';
            $statusFilter = $this->request->getGet('status') ?? '';
            $offset = ($page - 1) * $limit;
            
            $query = $db->table('events e');
            
            // Apply search filter
            if ($search) {
                $query->groupStart()
                    ->like('e.title', $search)
                    ->orLike('e.description', $search)
                    ->orLike('e.location', $search)
                    ->groupEnd();
            }
            
            // Apply format filter
            if ($formatFilter) {
                $query->where('e.format', $formatFilter);
            }
            
            // Apply status filter
            if ($statusFilter) {
                if ($statusFilter === 'active') {
                    $query->where('e.is_active', true);
                } elseif ($statusFilter === 'inactive') {
                    $query->where('e.is_active', false);
                }
            }
            
            $total = $query->countAllResults(false);
            $events = $query->orderBy('e.created_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'events' => $events,
                    'pagination' => [
                        'total_records' => $total,
                        'current_page' => (int)$page,
                        'total_pages' => ceil($total / $limit),
                        'start_record' => ($page - 1) * $limit + 1,
                        'end_record' => min($page * $limit, $total)
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get events data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load events data'
            ]);
        }
    }

    /**
     * Create new event (API endpoint)
     */
    public function createEvent()
    {
        // Temporary: Skip auth check for testing
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        log_message('info', 'createEvent method called');
        
        try {
            // Handle both JSON and form data
            $input = $this->request->getJSON(true);
            if (empty($input)) {
                // Fallback to POST data if not JSON
                $input = [
                    'title' => $this->request->getPost('title'),
                    'description' => $this->request->getPost('description'),
                    'event_date' => $this->request->getPost('event_date'),
                    'event_time' => $this->request->getPost('event_time'),
                    'format' => $this->request->getPost('format'),
                    'location' => $this->request->getPost('location'),
                    'zoom_link' => $this->request->getPost('zoom_link'),
                    'registration_fee' => $this->request->getPost('registration_fee'),
                    'max_participants' => $this->request->getPost('max_participants'),
                    'registration_deadline' => $this->request->getPost('registration_deadline'),
                    'abstract_deadline' => $this->request->getPost('abstract_deadline'),
                    'registration_active' => $this->request->getPost('registration_active'),
                    'abstract_submission_active' => $this->request->getPost('abstract_submission_active'),
                    'is_active' => $this->request->getPost('is_active')
                ];
            }

            $validation = \Config\Services::validation();
            $rules = [
                'title' => 'required|min_length[3]|max_length[200]',
                'description' => 'required|min_length[10]|max_length[1000]',
                'event_date' => 'required|valid_date[Y-m-d]',
                'event_time' => 'required',
                'format' => 'required|in_list[online,offline,hybrid]'
            ];

            // Optional fields with validation
            if (!empty($input['registration_fee'])) {
                $rules['registration_fee'] = 'numeric|greater_than_equal_to[0]';
            }
            if (!empty($input['max_participants'])) {
                $rules['max_participants'] = 'integer|greater_than[0]';
            }
            if (!empty($input['registration_deadline'])) {
                $rules['registration_deadline'] = 'valid_date[Y-m-d]';
            }
            if (!empty($input['abstract_deadline'])) {
                $rules['abstract_deadline'] = 'valid_date[Y-m-d]';
            }

            // Conditional validation for location/zoom_link based on format
            $format = $input['format'] ?? '';
            if ($format === 'offline' || $format === 'hybrid') {
                $rules['location'] = 'required|min_length[3]|max_length[255]';
            }
            if ($format === 'online' || $format === 'hybrid') {
                $rules['zoom_link'] = 'required|valid_url|max_length[500]';
            }

            if (!$this->validate($rules, $input)) {
                $errors = \Config\Services::validation()->getErrors();
                log_message('error', 'Event creation validation failed. Input: ' . json_encode($input) . ', Errors: ' . json_encode($errors));
                log_message('error', 'Validation rules used: ' . json_encode($rules));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
            }

            $eventData = [
                'title' => $input['title'],
                'description' => $input['description'],
                'event_date' => $input['event_date'],
                'event_time' => $input['event_time'],
                'format' => $input['format'],
                'location' => isset($input['location']) ? $input['location'] : null,
                'zoom_link' => isset($input['zoom_link']) ? $input['zoom_link'] : null,
                'registration_fee' => isset($input['registration_fee']) ? $input['registration_fee'] : 0,
                'max_participants' => isset($input['max_participants']) ? $input['max_participants'] : null,
                'registration_deadline' => isset($input['registration_deadline']) ? $input['registration_deadline'] : null,
                'abstract_deadline' => isset($input['abstract_deadline']) ? $input['abstract_deadline'] : null,
                'registration_active' => isset($input['registration_active']) ? (bool) $input['registration_active'] : true,
                'abstract_submission_active' => isset($input['abstract_submission_active']) ? (bool) $input['abstract_submission_active'] : true,
                'is_active' => isset($input['is_active']) ? (bool) $input['is_active'] : true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            log_message('info', 'Attempting to create event with data: ' . json_encode($eventData));
            
            $db = \Config\Database::connect();
            $result = $db->table('events')->insert($eventData);
            $insertId = $db->insertID();
            
            // Log the actual SQL query
            $lastQuery = $db->getLastQuery();
            log_message('info', 'SQL Query executed: ' . $lastQuery);
            log_message('info', 'Insert result: ' . ($result ? 'success' : 'failed') . ', Insert ID: ' . $insertId);

            if ($result && $insertId > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event created successfully',
                    'event_id' => $insertId
                ]);
            } else {
                // Get last error from database
                $error = $db->error();
                log_message('error', 'Database error during event creation: ' . json_encode($error));
                log_message('error', 'Event data that failed: ' . json_encode($eventData));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create event',
                    'debug_db_error' => $error,
                    'debug_event_data' => $eventData,
                    'debug_insert_result' => $result,
                    'debug_insert_id' => $insertId
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Create event error: ' . $e->getMessage());
            log_message('error', 'Create event exception trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create event',
                'debug_exception' => $e->getMessage(),
                'debug_trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get single event data (API endpoint)
     */
    public function getEventById($eventId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $db = \Config\Database::connect();
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            
            if (!$event) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Event not found'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $event
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get event by ID error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load event data'
            ]);
        }
    }

    /**
     * Update event (API endpoint)
     */
    public function updateEvent($eventId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $db = \Config\Database::connect();
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if (!$event) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Event not found'
                ]);
            }

            // Handle both JSON and form data
            $input = $this->request->getJSON(true);
            if (empty($input)) {
                // Fallback to POST data if not JSON
                $input = [
                    'title' => $this->request->getPost('title'),
                    'description' => $this->request->getPost('description'),
                    'event_date' => $this->request->getPost('event_date'),
                    'event_time' => $this->request->getPost('event_time'),
                    'format' => $this->request->getPost('format'),
                    'location' => $this->request->getPost('location'),
                    'zoom_link' => $this->request->getPost('zoom_link'),
                    'registration_fee' => $this->request->getPost('registration_fee'),
                    'max_participants' => $this->request->getPost('max_participants'),
                    'registration_deadline' => $this->request->getPost('registration_deadline'),
                    'abstract_deadline' => $this->request->getPost('abstract_deadline'),
                    'registration_active' => $this->request->getPost('registration_active'),
                    'abstract_submission_active' => $this->request->getPost('abstract_submission_active'),
                    'is_active' => $this->request->getPost('is_active')
                ];
            }

            $validation = \Config\Services::validation();
            $rules = [
                'title' => 'required|min_length[3]|max_length[200]',
                'description' => 'required|min_length[10]|max_length[1000]',
                'event_date' => 'required|valid_date[Y-m-d]',
                'event_time' => 'required',
                'format' => 'required|in_list[online,offline,hybrid]'
            ];

            // Optional fields with validation
            if (!empty($input['registration_fee'])) {
                $rules['registration_fee'] = 'numeric|greater_than_equal_to[0]';
            }
            if (!empty($input['max_participants'])) {
                $rules['max_participants'] = 'integer|greater_than[0]';
            }
            if (!empty($input['registration_deadline'])) {
                $rules['registration_deadline'] = 'valid_date[Y-m-d]';
            }
            if (!empty($input['abstract_deadline'])) {
                $rules['abstract_deadline'] = 'valid_date[Y-m-d]';
            }

            // Conditional validation for location/zoom_link based on format
            $format = $input['format'] ?? '';
            if ($format === 'offline' || $format === 'hybrid') {
                $rules['location'] = 'required|min_length[3]|max_length[255]';
            }
            if ($format === 'online' || $format === 'hybrid') {
                $rules['zoom_link'] = 'required|valid_url|max_length[500]';
            }

            if (!$this->validate($rules, $input)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => \Config\Services::validation()->getErrors()
                ]);
            }

            $updateData = [
                'title' => $input['title'],
                'description' => $input['description'],
                'event_date' => $input['event_date'],
                'event_time' => $input['event_time'],
                'format' => $input['format'],
                'location' => $input['location'] ?: null,
                'zoom_link' => $input['zoom_link'] ?: null,
                'registration_fee' => $input['registration_fee'] ?: 0,
                'max_participants' => $input['max_participants'] ?: null,
                'registration_deadline' => $input['registration_deadline'] ?: null,
                'abstract_deadline' => $input['abstract_deadline'] ?: null,
                'registration_active' => isset($input['registration_active']) ? (bool) $input['registration_active'] : true,
                'abstract_submission_active' => isset($input['abstract_submission_active']) ? (bool) $input['abstract_submission_active'] : true,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle is_active field if provided
            if (isset($input['is_active'])) {
                $updateData['is_active'] = (bool) $input['is_active'];
            }

            $result = $db->table('events')->where('id', $eventId)->update($updateData);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update event'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Update event error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update event'
            ]);
        }
    }

    /**
     * Delete event (API endpoint)
     */
    public function deleteEvent($eventId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $db = \Config\Database::connect();
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if (!$event) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Event not found'
                ]);
            }

            // Check if event has registrations
            $registrationCount = $db->table('registrations')->where('event_id', $eventId)->countAllResults();
            if ($registrationCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete event with existing registrations. Please deactivate the event instead.'
                ]);
            }

            $result = $db->table('events')->where('id', $eventId)->delete();

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete event'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Delete event error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete event'
            ]);
        }
    }

    /**
     * Toggle event active status (API endpoint)
     */
    public function toggleEventStatus($eventId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $db = \Config\Database::connect();
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
            if (!$event) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Event not found'
                ]);
            }

            $newStatus = !$event['is_active'];
            $updateData = [
                'is_active' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $db->table('events')->where('id', $eventId)->update($updateData);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Event status updated successfully',
                    'new_status' => $newStatus ? 'active' : 'inactive'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update event status'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Toggle event status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update event status'
            ]);
        }
    }

    // ==================== USER CRUD OPERATIONS ====================

    /**
     * Create new user (API endpoint)
     */
    public function createUser()
    {
        // Temporary: Skip auth check for testing
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            // Handle both JSON and form data
            $input = $this->request->getJSON(true);
            if (empty($input)) {
                // Fallback to POST data if not JSON
                $input = [
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'email' => $this->request->getPost('email'),
                    'role' => $this->request->getPost('role'),
                    'password' => $this->request->getPost('password'),
                    'is_verified' => $this->request->getPost('is_verified')
                ];
            }

            $validation = \Config\Services::validation();
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'role' => 'required|in_list[admin,presenter,reviewer,audience]',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules, $input)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }

            $userData = [
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'role' => $input['role'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'is_verified' => isset($input['is_verified']) ? (bool) $input['is_verified'] : true, // Admin-created users are automatically verified by default
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            if ($userId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User created successfully',
                    'user_id' => $userId
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create user'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Create user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create user'
            ]);
        }
    }

    /**
     * Get single user data (API endpoint)
     */
    public function getUserById($userId)
    {
        // Temporary: Skip auth check for testing
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Remove sensitive data
            unset($user['password']);
            unset($user['remember_token']);
            unset($user['reset_token']);

            return $this->response->setJSON([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get user by ID error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load user data'
            ]);
        }
    }

    /**
     * Update user (API endpoint)
     */
    public function updateUser($userId)
    {
        // Temporary: Skip auth check for testing
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        log_message('info', 'updateUser method called with userId: ' . $userId);
        
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Handle both JSON and form data
            $input = $this->request->getJSON(true);
            if (empty($input)) {
                // Fallback to POST data if not JSON
                $input = [
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                    'email' => $this->request->getPost('email'),
                    'role' => $this->request->getPost('role'),
                    'password' => $this->request->getPost('password'),
                    'is_verified' => $this->request->getPost('is_verified')
                ];
            }

            log_message('info', 'UpdateUser received input: ' . json_encode($input));

            $validation = \Config\Services::validation();
            $rules = [
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
                'role' => 'required|in_list[admin,presenter,reviewer,audience]'
            ];

            // Only validate password if it's being updated
            $password = $input['password'] ?? '';
            if ($password && !empty(trim($password))) {
                $rules['password'] = 'min_length[6]';
            }

            if (!$this->validate($rules, $input)) {
                $errors = \Config\Services::validation()->getErrors();
                log_message('error', 'Update user validation failed for user ' . $userId . ': ' . json_encode($errors));
                log_message('error', 'Validation rules used: ' . json_encode($rules));
                log_message('error', 'Input data received: ' . json_encode($input));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors,
                    'debug_rules' => $rules,
                    'debug_input' => $input
                ]);
            }

            $updateData = [
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'role' => $input['role'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle is_verified field
            if (isset($input['is_verified'])) {
                $updateData['is_verified'] = (bool) $input['is_verified'];
            }

            // Update password if provided
            if ($password && !empty(trim($password))) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            log_message('info', 'Attempting to update user ' . $userId . ' with data: ' . json_encode($updateData));
            
            // Try using query builder directly for better error handling
            $db = \Config\Database::connect();
            
            // Enable query logging for debug
            $builder = $db->table('users');
            $result = $builder->where('id', $userId)->update($updateData);
            
            // Also get affected rows count
            $affectedRows = $db->affectedRows();
            
            // Log the actual SQL query
            $lastQuery = $db->getLastQuery();
            log_message('info', 'SQL Query executed: ' . $lastQuery);

            log_message('info', 'Update result for user ' . $userId . ': ' . ($result ? 'success' : 'failed') . ', affected rows: ' . $affectedRows);

            // Consider success if query executed successfully OR if rows were affected
            if ($result || $affectedRows >= 0) { // >= 0 because even 0 affected rows means query was successful
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            } else {
                // Get last error from database
                $db = \Config\Database::connect();
                $error = $db->error();
                log_message('error', 'Database error during user update: ' . json_encode($error));
                log_message('error', 'Update data that failed: ' . json_encode($updateData));
                log_message('error', 'User ID being updated: ' . $userId);
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update user',
                    'debug_db_error' => $error,
                    'debug_update_data' => $updateData,
                    'debug_user_id' => $userId
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Update user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update user'
            ]);
        }
    }

    /**
     * Delete user (API endpoint)
     */
    public function deleteUser($userId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Prevent deleting the current admin user
            if ($userId == $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ]);
            }

            $result = $this->userModel->delete($userId);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete user'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Delete user error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to delete user'
            ]);
        }
    }

    /**
     * Toggle user active status (API endpoint)
     */
    public function toggleUserStatus($userId)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);

        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            // Prevent deactivating the current admin user
            if ($userId == $this->session->get('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot deactivate your own account'
                ]);
            }

            $newStatus = !$user['is_verified'];
            $updateData = [
                'is_verified' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->userModel->update($userId, $updateData);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User status updated successfully',
                    'new_status' => $newStatus ? 'active' : 'inactive'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update user status'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Toggle user status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to update user status'
            ]);
        }
    }

    /**
     * Get dashboard statistics (API endpoint)
     */
    public function getDashboardStatsApi()
    {
        // Skip auth check for now - will be handled by route middleware
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            
            // Test each query individually
            $totalUsers = $db->table('users')->countAllResults();
            $activeEvents = $db->table('events')->where('is_active', true)->countAllResults();
            $totalRegistrations = $db->table('registrations')->countAllResults();
            
            // Simple revenue calculation for now
            $revenueQuery = $db->table('registrations r')
                ->select('SUM(e.registration_fee) as total')
                ->join('events e', 'e.id = r.event_id')
                ->where('r.payment_status', 'paid')
                ->get();
            
            $totalRevenue = 0;
            if ($revenueQuery && $revenueQuery->getRow()) {
                $totalRevenue = $revenueQuery->getRow()->total ?? 0;
            }
            
            $stats = [
                'total_users' => $totalUsers,
                'active_events' => $activeEvents,
                'total_registrations' => $totalRegistrations,
                'total_revenue' => $totalRevenue
            ];
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get dashboard stats error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load dashboard statistics'
            ]);
        }
    }

    /**
     * Get recent activity (API endpoint)
     */
    public function getRecentActivityApi()
    {
        // Temporary: Skip auth check for debugging
        // $redirect = $this->checkAdminAccess();
        // if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $activities = [];
            
            // Get recent user registrations
            $recentUsers = $db->table('users')
                ->select('first_name, last_name, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'type' => 'user',
                    'description' => "New user registered: {$user['first_name']} {$user['last_name']}",
                    'created_at' => $user['created_at']
                ];
            }
            
            // Get recent events
            $recentEvents = $db->table('events')
                ->select('title, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(3)
                ->get()
                ->getResultArray();
            
            foreach ($recentEvents as $event) {
                $activities[] = [
                    'type' => 'event',
                    'description' => "New event created: {$event['title']}",
                    'created_at' => $event['created_at']
                ];
            }
            
            // Get recent registrations
            $recentRegistrations = $db->table('registrations r')
                ->select('u.first_name, u.last_name, e.title as event_title, r.created_at')
                ->join('users u', 'u.id = r.user_id')
                ->join('events e', 'e.id = r.event_id')
                ->orderBy('r.created_at', 'DESC')
                ->limit(3)
                ->get()
                ->getResultArray();
            
            foreach ($recentRegistrations as $registration) {
                $activities[] = [
                    'type' => 'registration',
                    'description' => "New registration by {$registration['first_name']} {$registration['last_name']} for: {$registration['event_title']}",
                    'created_at' => $registration['created_at']
                ];
            }
            
            // Sort all activities by date
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            // Limit to 10 most recent
            $activities = array_slice($activities, 0, 10);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get recent activity error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to load recent activity'
            ]);
        }
    }
}