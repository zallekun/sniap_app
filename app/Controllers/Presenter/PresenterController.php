<?php

namespace App\Controllers\Presenter;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PresenterController extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is presenter
     */
    protected function checkPresenterAccess()
    {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$userId || $userRole !== 'presenter') {
            return redirect()->to('/login')->with('error', 'Access denied. Presenter privileges required.');
        }
        
        return false;
    }

    /**
     * Presenter Dashboard - Main Overview
     */
    public function dashboard()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Presenter Dashboard - SNIA Conference',
                'user' => $user,
                'stats' => $this->getDashboardStats($userId),
                'abstracts' => $this->getUserAbstracts($userId)
            ];
            
            return view('roles/presenter/dashboard_clean', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter dashboard error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Failed to load presenter dashboard');
        }
    }

    /**
     * Abstract Management
     */
    public function abstracts()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Abstract Management - Presenter',
                'user' => $user,
                'abstracts' => $this->getUserAbstracts($userId)
            ];
            
            return view('roles/presenter/abstracts', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter abstracts error: ' . $e->getMessage());
            return redirect()->to('/presenter/dashboard')->with('error', 'Failed to load abstracts');
        }
    }

    /**
     * Presentation Management
     */
    public function presentations()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Presentation Management - Presenter',
                'user' => $user,
                'presentations' => $this->getUserPresentations($userId)
            ];
            
            return view('roles/presenter/presentations', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter presentations error: ' . $e->getMessage());
            return redirect()->to('/presenter/dashboard')->with('error', 'Failed to load presentations');
        }
    }

    /**
     * Registration Status
     */
    public function registrations()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Registration Status - Presenter',
                'user' => $user,
                'registrations' => $this->getUserRegistrations($userId)
            ];
            
            return view('roles/presenter/registrations', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter registrations error: ' . $e->getMessage());
            return redirect()->to('/presenter/dashboard')->with('error', 'Failed to load registrations');
        }
    }

    /**
     * Schedule & Events
     */
    public function schedule()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Presentation Schedule - Presenter',
                'user' => $user,
                'schedule' => $this->getPresenterSchedule($userId)
            ];
            
            return view('roles/presenter/schedule', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter schedule error: ' . $e->getMessage());
            return redirect()->to('/presenter/dashboard')->with('error', 'Failed to load schedule');
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get presenter dashboard statistics
     */
    private function getDashboardStats($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'total_abstracts' => $db->table('abstracts')->where('user_id', $userId)->countAllResults(),
                'accepted_abstracts' => $db->table('abstracts')->where('user_id', $userId)->where('status', 'accepted')->countAllResults(),
                'pending_abstracts' => $db->table('abstracts')->where('user_id', $userId)->where('status', 'pending')->countAllResults(),
                'rejected_abstracts' => $db->table('abstracts')->where('user_id', $userId)->where('status', 'rejected')->countAllResults(),
                'total_registrations' => $db->table('registrations')->where('user_id', $userId)->countAllResults(),
                'active_registrations' => $db->table('registrations')->where('user_id', $userId)->where('status', 'confirmed')->countAllResults(),
                'pending_payments' => $db->table('registrations r')
                    ->join('payments p', 'p.registration_id = r.id', 'left')
                    ->where('r.user_id', $userId)
                    ->where('p.payment_status', 'pending')
                    ->countAllResults(),
                'completed_presentations' => 0 // Will be calculated based on schedule
            ];
            
            // Calculate upcoming presentations
            $upcomingPresentations = $db->table('registrations r')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.user_id', $userId)
                ->where('r.status', 'confirmed')
                ->where('e.start_date >', date('Y-m-d H:i:s'))
                ->countAllResults();
            
            $stats['upcoming_presentations'] = $upcomingPresentations;
            
            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Presenter dashboard stats error: ' . $e->getMessage());
            return [
                'total_abstracts' => 0,
                'accepted_abstracts' => 0,
                'pending_abstracts' => 0,
                'rejected_abstracts' => 0,
                'total_registrations' => 0,
                'active_registrations' => 0,
                'pending_payments' => 0,
                'upcoming_presentations' => 0
            ];
        }
    }

    /**
     * Get user abstracts with review status
     */
    private function getUserAbstracts($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('abstracts a')
                ->select('a.*, e.title as event_title, ac.name as category_name')
                ->join('events e', 'e.id = a.event_id', 'left')
                ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                ->where('a.user_id', $userId)
                ->orderBy('a.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get user abstracts error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user presentations
     */
    private function getUserPresentations($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.start_date, e.end_date, e.location')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.user_id', $userId)
                ->where('r.registration_type', 'presenter')
                ->orderBy('e.start_date', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get user presentations error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user registrations with payment status
     */
    private function getUserRegistrations($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('registrations r')
                ->select('r.*, e.title as event_title, p.payment_status, p.amount, p.payment_method')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->join('payments p', 'p.registration_id = r.id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('r.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get user registrations error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get presenter schedule
     */
    private function getPresenterSchedule($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get events where user is registered as presenter
            $schedule = $db->table('registrations r')
                ->select('r.*, e.title, e.description, e.start_date, e.end_date, e.location, e.room')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.user_id', $userId)
                ->where('r.registration_type', 'presenter')
                ->where('r.status', 'confirmed')
                ->orderBy('e.start_date', 'ASC')
                ->get()
                ->getResultArray();
            
            return $schedule;
        } catch (\Exception $e) {
            log_message('error', 'Get presenter schedule error: ' . $e->getMessage());
            return [];
        }
    }

    // ==================== API ENDPOINTS ====================

    /**
     * Get presenter stats (API endpoint)
     */
    public function getStatsApi()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $stats = $this->getDashboardStats($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get presenter stats API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load presenter statistics'
            ]);
        }
    }

    /**
     * Get abstracts data (API endpoint)
     */
    public function getAbstractsApi()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $abstracts = $this->getUserAbstracts($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstracts API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load abstracts'
            ]);
        }
    }
}