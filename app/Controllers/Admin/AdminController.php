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
            
            return view('roles/admin/dashboard_simple', $data);
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
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $db = \Config\Database::connect();
            $limit = $this->request->getGet('limit') ?? 10;
            $page = $this->request->getGet('page') ?? 1;
            $search = $this->request->getGet('search') ?? '';
            $offset = ($page - 1) * $limit;
            
            $query = $db->table('users u');
            
            if ($search) {
                $query->groupStart()
                    ->like('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->groupEnd();
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
                'status' => 'success',
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
                'status' => 'error',
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
                'status' => 'success',
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
                'status' => 'error',
                'message' => 'Failed to load registrations data'
            ]);
        }
    }

    /**
     * Get dashboard statistics (API endpoint)
     */
    public function getDashboardStatsApi()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $stats = $this->getDashboardStats();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get dashboard stats API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load dashboard statistics'
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
                'status' => 'success',
                'data' => $formattedAbstracts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstracts data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
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
                'status' => 'success',
                'data' => $reviewers
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get reviewers data error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
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
                'status' => 'success',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstract stats API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
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
                    'status' => 'error',
                    'message' => 'Abstract ID and Reviewer ID are required'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Check if abstract exists
            $abstract = $db->table('abstracts')->where('id', $abstractId)->get()->getRowArray();
            if (!$abstract) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            // Check if reviewer exists
            $reviewer = $db->table('users')->where('id', $reviewerId)->where('role', 'reviewer')->get()->getRowArray();
            if (!$reviewer) {
                return $this->response->setJSON([
                    'status' => 'error',
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
                    'status' => 'success',
                    'message' => 'Reviewer assigned successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to assign reviewer'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Assign reviewer error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
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
                    'status' => 'error',
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
                'status' => 'success',
                'message' => "Successfully assigned reviewers to {$assignedCount} abstracts"
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Bulk assign reviewers error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to bulk assign reviewers'
            ]);
        }
    }
}