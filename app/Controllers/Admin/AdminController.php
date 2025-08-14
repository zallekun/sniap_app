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
            
            return view('admin/dashboard', $data);
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
            
            return view('admin/users', $data);
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
            
            return view('admin/events', $data);
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
            
            return view('admin/registrations', $data);
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
            
            return view('admin/abstracts', $data);
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
            
            return view('admin/settings', $data);
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
            
            return view('admin/analytics', $data);
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
                'pending_reviews' => $db->table('abstracts')->where('status', 'pending')->countAllResults(),
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
}