<?php

namespace App\Controllers\Presenter;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AbstractModel;
use App\Models\PaymentModel;
use App\Models\LoaModel;
use App\Models\CertificateModel;
use App\Services\QRCodeService;
use CodeIgniter\HTTP\ResponseInterface;

class PresenterController extends BaseController
{
    protected $userModel;
    protected $abstractModel;
    protected $paymentModel;
    protected $loaModel;
    protected $certificateModel;
    protected $qrCodeService;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->abstractModel = new AbstractModel();
        $this->paymentModel = new PaymentModel();
        $this->loaModel = new LoaModel();
        $this->certificateModel = new CertificateModel();
        $this->qrCodeService = new QRCodeService();
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is presenter
     */
    protected function checkPresenterAccess()
    {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        log_message('info', "checkPresenterAccess - UserID: {$userId}, Role: {$userRole}");
        
        if (!$userId || $userRole !== 'presenter') {
            log_message('warning', "Access denied - UserID: {$userId}, Role: {$userRole}");
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
            $userRole = $this->session->get('user_role');
            
            log_message('info', "Presenter dashboard access - UserID: {$userId}, Role: {$userRole}");
            
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                log_message('error', "User not found for ID: {$userId}");
                $this->session->destroy();
                return redirect()->to('/login')->with('error', 'User account not found');
            }
            
            log_message('info', "Loading presenter dashboard for user: {$user['email']}");
            
            // Get stats with error handling
            $stats = [];
            try {
                $stats = $this->abstractModel->getUserStats($userId);
            } catch (\Exception $e) {
                log_message('error', 'Failed to get user stats: ' . $e->getMessage());
                $stats = [
                    'total_abstracts' => 0,
                    'accepted_abstracts' => 0,
                    'pending_abstracts' => 0,
                    'rejected_abstracts' => 0,
                    'upcoming_presentations' => 0
                ];
            }
            
            // Get abstracts with error handling
            $abstracts = [];
            try {
                $abstracts = $this->abstractModel->getUserAbstracts($userId, 5);
            } catch (\Exception $e) {
                log_message('error', 'Failed to get user abstracts: ' . $e->getMessage());
                $abstracts = [];
            }
            
            $data = [
                'title' => 'Presenter Dashboard - SNIA Conference',
                'user' => $user,
                'stats' => $stats,
                'abstracts' => $abstracts
            ];
            
            return view('roles/presenter/dashboard_clean', $data);
        } catch (\Exception $e) {
            log_message('error', 'Presenter dashboard critical error: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return redirect()->to('/login')->with('error', 'Failed to load presenter dashboard');
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
                'abstracts' => $this->abstractModel->getUserAbstracts($userId),
                'registered_events' => $this->getUserRegisteredEvents($userId)
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

    /**
     * Check if user is registered for a specific event
     */
    private function isUserRegisteredForEvent($userId, $eventId)
    {
        try {
            $db = \Config\Database::connect();
            
            $count = $db->table('registrations')
                ->where('user_id', $userId)
                ->where('event_id', $eventId)
                ->where('status', 'confirmed')
                ->countAllResults();
                
            return $count > 0;
        } catch (\Exception $e) {
            log_message('error', 'Check user registration error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get events user is registered for
     */
    private function getUserRegisteredEvents($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('registrations r')
                ->select('e.id, e.title, e.description, e.start_date, e.end_date')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.user_id', $userId)
                ->where('r.status', 'confirmed')
                ->orderBy('e.start_date', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get user registered events error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get registration ID for user and event
     */
    private function getUserRegistrationId($userId, $eventId)
    {
        try {
            $db = \Config\Database::connect();
            
            $result = $db->table('registrations')
                ->select('id')
                ->where('user_id', $userId)
                ->where('event_id', $eventId)
                ->where('status', 'confirmed')
                ->get()
                ->getFirstRow('array');
                
            return $result ? $result['id'] : null;
        } catch (\Exception $e) {
            log_message('error', 'Get user registration ID error: ' . $e->getMessage());
            return null;
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
            $abstracts = $this->abstractModel->getUserAbstracts($userId);
            
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

    /**
     * Submit new abstract (API endpoint)
     */
    public function submitAbstract()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/presenter/abstracts');
        }
        
        try {
            $userId = $this->session->get('user_id');
            
            // Validation rules
            $validationRules = [
                'title' => 'required|max_length[200]',
                'event_id' => 'required|integer',
                'abstract_text' => 'required|max_length[2000]',
                'category_id' => 'permit_empty|integer',
                'keywords' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Check if user is registered for the event and get registration_id
            $eventId = $this->request->getPost('event_id');
            $registrationId = $this->getUserRegistrationId($userId, $eventId);
            
            if (!$registrationId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You must register for this event before submitting an abstract.'
                ]);
            }

            // Get user data for the abstract
            $user = $this->userModel->find($userId);

            // Prepare data
            $data = [
                'registration_id' => $registrationId,
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'affiliation' => $user['affiliation'] ?? '',
                'title' => $this->request->getPost('title'),
                'category_id' => $this->request->getPost('category_id'),
                'abstract_text' => $this->request->getPost('abstract_text'),
                'keywords' => $this->request->getPost('keywords'),
                'file_path' => ''
            ];

            // Handle file upload
            $file = $this->request->getFile('abstract_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/abstracts/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                if ($file->move($uploadPath, $newName)) {
                    $data['file_path'] = 'uploads/abstracts/' . $newName;
                    $data['file_name'] = $file->getClientName();
                }
            }

            // Submit abstract
            $abstractId = $this->abstractModel->submitAbstract($data);
            
            if ($abstractId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Abstract submitted successfully!',
                    'data' => ['id' => $abstractId]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to submit abstract'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Submit abstract error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit abstract: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get abstract details (API endpoint)
     */
    public function getAbstractDetails($abstractId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $abstract = $this->abstractModel->getAbstractDetailsForUser($abstractId, $userId);
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstract
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstract details error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load abstract details'
            ]);
        }
    }

    /**
     * Update abstract (API endpoint)
     */
    public function updateAbstract($abstractId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/presenter/abstracts');
        }
        
        try {
            $userId = $this->session->get('user_id');
            
            // Check if user can edit this abstract
            if (!$this->abstractModel->canEdit($abstractId, $userId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot edit this abstract. It may have already been reviewed.'
                ]);
            }

            // Validation rules
            $validationRules = [
                'title' => 'required|max_length[200]',
                'abstract_text' => 'required|max_length[2000]',
                'keywords' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Prepare data
            $data = [
                'title' => $this->request->getPost('title'),
                'abstract_text' => $this->request->getPost('abstract_text'),
                'keywords' => $this->request->getPost('keywords')
            ];

            // Update abstract
            $result = $this->abstractModel->updateAbstract($abstractId, $userId, $data);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Abstract updated successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update abstract'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Update abstract error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update abstract'
            ]);
        }
    }

    /**
     * Download abstract file
     */
    public function downloadAbstract($abstractId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $abstract = $this->abstractModel->getAbstractDetailsForUser($abstractId, $userId);
            
            if (!$abstract || !$abstract['file_path']) {
                return redirect()->to('/presenter/abstracts')->with('error', 'File not found');
            }
            
            $filePath = WRITEPATH . $abstract['file_path'];
            
            if (!file_exists($filePath)) {
                return redirect()->to('/presenter/abstracts')->with('error', 'File not found on server');
            }
            
            return $this->response->download($filePath, null)->setFileName($abstract['file_name']);
            
        } catch (\Exception $e) {
            log_message('error', 'Download abstract error: ' . $e->getMessage());
            return redirect()->to('/presenter/abstracts')->with('error', 'Failed to download file');
        }
    }

    // ==================== REVISION WORKFLOW ENDPOINTS ====================

    /**
     * Submit revision for abstract (API endpoint)
     */
    public function submitRevision($abstractId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/presenter/abstracts');
        }
        
        try {
            $userId = $this->session->get('user_id');
            
            // Check if abstract can be revised
            if (!$this->abstractModel->canRevise($abstractId, $userId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract cannot be revised at this time.'
                ]);
            }
            
            // Validation rules
            $validationRules = [
                'title' => 'required|max_length[200]',
                'abstract_text' => 'required|max_length[2000]',
                'keywords' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($validationRules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Prepare revision data
            $data = [
                'title' => $this->request->getPost('title'),
                'abstract_text' => $this->request->getPost('abstract_text'),
                'keywords' => $this->request->getPost('keywords')
            ];

            // Handle file upload for revision
            $file = $this->request->getFile('abstract_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/abstracts/';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                if ($file->move($uploadPath, $newName)) {
                    $data['file_path'] = 'uploads/abstracts/' . $newName;
                    $data['file_name'] = $file->getClientName();
                }
            }

            // Submit revision
            $result = $this->abstractModel->submitRevision($abstractId, $data, $userId);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Revision submitted successfully! Your abstract is now under review again.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to submit revision'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Submit revision error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Get abstracts needing revision (API endpoint)
     */
    public function getRevisionRequired()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $abstracts = $this->abstractModel->getAbstractsNeedingRevision($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get revision required error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load revision requirements'
            ]);
        }
    }

    /**
     * Check revision status for abstract (API endpoint)
     */
    public function checkRevisionStatus($abstractId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $abstract = $this->abstractModel->getAbstractDetailsForUser($abstractId, $userId);
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            $canRevise = $this->abstractModel->canRevise($abstractId, $userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'can_revise' => $canRevise,
                    'review_status' => $abstract['review_status'],
                    'revision_notes' => $abstract['revision_notes'],
                    'revision_deadline' => $abstract['revision_deadline'],
                    'revision_count' => $abstract['revision_count'],
                    'max_revisions' => $abstract['max_revisions'],
                    'can_upload_again' => $abstract['can_upload_again']
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Check revision status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to check revision status'
            ]);
        }
    }

    // ==================== PAYMENT SYSTEM ENDPOINTS ====================

    /**
     * Create payment for accepted abstract (API endpoint)
     */
    public function createPayment()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/presenter/registrations');
        }
        
        try {
            $userId = $this->session->get('user_id');
            $registrationId = $this->request->getPost('registration_id');
            $voucherCode = $this->request->getPost('voucher_code');
            
            // Verify user owns this registration
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found or not owned by user'
                ]);
            }
            
            // Check if payment already exists
            $existingPayment = $this->paymentModel->getPaymentByRegistration($registrationId);
            if ($existingPayment && $existingPayment['payment_status'] === 'success') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment already completed for this registration'
                ]);
            }
            
            // Get event pricing (for now use fixed amount, later get from event settings)
            $amount = 500000; // IDR 500k default presenter fee
            
            // Create payment
            $paymentId = $this->paymentModel->createPresenterPayment($registrationId, $amount, $voucherCode);
            
            if ($paymentId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment created successfully',
                    'data' => [
                        'payment_id' => $paymentId,
                        'registration_id' => $registrationId
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create payment'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Create payment error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Process payment via gateway (API endpoint)
     */
    public function processPayment($paymentId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/presenter/registrations');
        }
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify payment ownership through registration
            $payment = $this->paymentModel->getPaymentWithDetails($paymentId);
            if (!$payment || $payment['user_id'] != $userId) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ]);
            }
            
            // Simulate payment gateway processing
            $paymentData = [
                'gateway' => $this->request->getPost('gateway') ?? 'midtrans',
                'transaction_id' => 'TXN' . time() . rand(1000, 9999),
                'status' => 'success' // In real implementation, this comes from gateway callback
            ];
            
            $result = $this->paymentModel->processPaymentGateway($paymentId, $paymentData);
            
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment processed successfully!',
                    'data' => [
                        'transaction_id' => $paymentData['transaction_id'],
                        'payment_status' => $paymentData['status']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to process payment'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Process payment error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Get payment status (API endpoint)
     */
    public function getPaymentStatus($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            $paymentStatus = $this->paymentModel->getPresenterPaymentStatus($registrationId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $paymentStatus ?: [
                    'payment_status' => 'not_created',
                    'final_amount' => 0,
                    'payment_method' => null,
                    'paid_at' => null,
                    'transaction_id' => null
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get payment status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get payment status'
            ]);
        }
    }

    /**
     * Get payment history (API endpoint)
     */
    public function getPaymentHistory()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $paymentHistory = $this->paymentModel->getPresenterPaymentHistory($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $paymentHistory
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get payment history error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load payment history'
            ]);
        }
    }

    /**
     * Helper method to get user registration by ID
     */
    private function getUserRegistrationById($userId, $registrationId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('registrations r')
                ->select('r.*, e.title as event_title')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.id', $registrationId)
                ->where('r.user_id', $userId)
                ->get()
                ->getFirstRow('array');
        } catch (\Exception $e) {
            log_message('error', 'Get user registration by ID error: ' . $e->getMessage());
            return null;
        }
    }

    // ==================== LOA GENERATION ENDPOINTS ====================

    /**
     * Generate LOA after payment completion (API endpoint)
     */
    public function generateLoa($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            // Check if presenter can download LOA (payment completed + abstract accepted)
            if (!$this->loaModel->canDownloadLoa($registrationId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'LOA can only be generated after payment completion and abstract acceptance'
                ]);
            }
            
            // Generate or get existing LOA
            $loa = $this->loaModel->generatePresenterLoa($registrationId, $userId);
            
            if ($loa) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'LOA generated successfully',
                    'data' => [
                        'loa_number' => $loa['loa_number'],
                        'file_path' => $loa['file_path'],
                        'generated_at' => $loa['generated_at']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to generate LOA'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Generate LOA error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Download LOA document (API endpoint)
     */
    public function downloadLoa($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return redirect()->to('/presenter/registrations')->with('error', 'Registration not found');
            }
            
            // Check if LOA exists
            $loa = $this->loaModel->getLoaByRegistration($registrationId);
            if (!$loa) {
                return redirect()->to('/presenter/registrations')->with('error', 'LOA not found. Please generate LOA first.');
            }
            
            // Check if file exists
            $filePath = WRITEPATH . $loa['file_path'];
            if (!file_exists($filePath)) {
                return redirect()->to('/presenter/registrations')->with('error', 'LOA file not found on server');
            }
            
            // Download file
            return $this->response->download($filePath, null)->setFileName('LOA_' . $loa['loa_number'] . '.html');
            
        } catch (\Exception $e) {
            log_message('error', 'Download LOA error: ' . $e->getMessage());
            return redirect()->to('/presenter/registrations')->with('error', 'Failed to download LOA');
        }
    }

    /**
     * Get LOA status (API endpoint)
     */
    public function getLoaStatus($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            $loa = $this->loaModel->getLoaByRegistration($registrationId);
            $canDownload = $this->loaModel->canDownloadLoa($registrationId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'has_loa' => !empty($loa),
                    'can_download' => $canDownload,
                    'loa_number' => $loa['loa_number'] ?? null,
                    'generated_at' => $loa['generated_at'] ?? null
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get LOA status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get LOA status'
            ]);
        }
    }

    // ==================== QR CODE GENERATION ENDPOINTS ====================

    /**
     * Generate QR code for attendance (API endpoint)
     */
    public function generateQRCode($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            // Check if payment is completed (requirement for QR code)
            if (!$this->paymentModel->hasCompletedPayment($registrationId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'QR code can only be generated after payment completion'
                ]);
            }
            
            // Generate QR code
            $qrResult = $this->qrCodeService->generateUserQRCode($registrationId);
            
            if ($qrResult['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $qrResult['message'],
                    'data' => [
                        'qr_hash' => $qrResult['qr_code']['qr_hash'],
                        'qr_image' => $qrResult['qr_code']['qr_image'],
                        'expires_at' => $qrResult['qr_code']['expires_at'],
                        'is_verified' => $qrResult['qr_code']['is_verified']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $qrResult['message']
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Generate QR code error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Get QR code status (API endpoint)
     */
    public function getQRCodeStatus($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            // Get QR code from database
            $db = \Config\Database::connect();
            $qrCode = $db->table('qr_codes')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->orderBy('created_at', 'DESC')
                ->get()->getFirstRow('array');
            
            $hasPayment = $this->paymentModel->hasCompletedPayment($registrationId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'has_qr_code' => !empty($qrCode),
                    'can_generate' => $hasPayment,
                    'qr_hash' => $qrCode['qr_hash'] ?? null,
                    'is_verified' => $qrCode['is_verified'] ?? false,
                    'expires_at' => $qrCode['expires_at'] ?? null,
                    'qr_image' => $qrCode['qr_image'] ?? null
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get QR code status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get QR code status'
            ]);
        }
    }

    /**
     * Download QR code image (API endpoint)
     */
    public function downloadQRCode($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return redirect()->to('/presenter/registrations')->with('error', 'Registration not found');
            }
            
            // Get QR code
            $db = \Config\Database::connect();
            $qrCode = $db->table('qr_codes')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->orderBy('created_at', 'DESC')
                ->get()->getFirstRow('array');
            
            if (!$qrCode) {
                return redirect()->to('/presenter/registrations')->with('error', 'QR code not found. Please generate QR code first.');
            }
            
            // Create QR code image file for download
            $qrImageData = base64_decode($qrCode['qr_image']);
            $fileName = 'QR_Code_' . $qrCode['qr_hash'] . '.png';
            
            return $this->response->download($qrImageData, null)
                   ->setFileName($fileName)
                   ->setContentType('image/png');
            
        } catch (\Exception $e) {
            log_message('error', 'Download QR code error: ' . $e->getMessage());
            return redirect()->to('/presenter/registrations')->with('error', 'Failed to download QR code');
        }
    }

    // ==================== CERTIFICATE SYSTEM ENDPOINTS ====================

    /**
     * Generate presenter certificate after presentation completion (API endpoint)
     */
    public function generateCertificate($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            // Check if presenter can access certificate (presentation completed)
            if (!$this->certificateModel->canAccessCertificate($registrationId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Certificate can only be accessed after the event/presentation has been completed'
                ]);
            }
            
            // Generate or get existing certificate
            $certificate = $this->certificateModel->generatePresenterCertificate($registrationId, $userId);
            
            if ($certificate) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Certificate generated successfully',
                    'data' => [
                        'certificate_number' => $certificate['certificate_number'],
                        'file_path' => $certificate['file_path'],
                        'created_at' => $certificate['created_at']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to generate certificate'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Generate certificate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    /**
     * Download certificate document (API endpoint)
     */
    public function downloadCertificate($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return redirect()->to('/presenter/presentations')->with('error', 'Registration not found');
            }
            
            // Check if certificate exists
            $certificate = $this->certificateModel->getCertificateByRegistration($registrationId);
            if (!$certificate) {
                return redirect()->to('/presenter/presentations')->with('error', 'Certificate not found. Please generate certificate first.');
            }
            
            // Check if file exists
            $filePath = WRITEPATH . $certificate['file_path'];
            if (!file_exists($filePath)) {
                return redirect()->to('/presenter/presentations')->with('error', 'Certificate file not found on server');
            }
            
            // Download file
            return $this->response->download($filePath, null)->setFileName('Certificate_' . $certificate['certificate_number'] . '.html');
            
        } catch (\Exception $e) {
            log_message('error', 'Download certificate error: ' . $e->getMessage());
            return redirect()->to('/presenter/presentations')->with('error', 'Failed to download certificate');
        }
    }

    /**
     * Get certificate status (API endpoint)
     */
    public function getCertificateStatus($registrationId)
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Verify registration ownership
            $registration = $this->getUserRegistrationById($userId, $registrationId);
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }
            
            $certificate = $this->certificateModel->getCertificateByRegistration($registrationId);
            $canAccess = $this->certificateModel->canAccessCertificate($registrationId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'has_certificate' => !empty($certificate),
                    'can_access' => $canAccess,
                    'certificate_number' => $certificate['certificate_number'] ?? null,
                    'created_at' => $certificate['created_at'] ?? null
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get certificate status error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get certificate status'
            ]);
        }
    }

    /**
     * Get presenter certificates list (API endpoint)
     */
    public function getCertificatesApi()
    {
        $redirect = $this->checkPresenterAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Get all presenter registrations with certificate status
            $db = \Config\Database::connect();
            $certificates = $db->table('registrations r')
                ->select('r.id as registration_id, r.registration_type, e.title as event_title, e.start_date, e.end_date, 
                         c.certificate_number, c.file_path, c.created_at as certificate_created_at')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->join('certificates c', 'c.registration_id = r.id', 'left')
                ->where('r.user_id', $userId)
                ->where('r.registration_type', 'presenter')
                ->orderBy('e.start_date', 'DESC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $certificates
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get certificates API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load certificates'
            ]);
        }
    }
}