<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display main dashboard
     */
    public function index()
    {
        // Try to get user from session first
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if ($userId) {
            // User has session, get user data
            $user = $this->userModel->find($userId);
            
            if (!$user) {
                $this->session->destroy();
                return redirect()->to('/login')->with('error', 'User account not found.');
            }

            // Redirect to role-specific dashboard if not audience
            switch ($user['role']) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'presenter':
                    return redirect()->to('/presenter/dashboard');
                case 'reviewer':
                    return redirect()->to('/reviewer/dashboard');
                default:
                    // For audience/default users, show audience dashboard
                    break;
            }

            $data = [
                'title' => 'Dashboard - SNIA Conference',
                'user' => $user,
                'userRole' => $userRole,
                'userName' => trim($user['first_name'] . ' ' . $user['last_name']),
                'stats' => $this->getAudienceStats($userId),
                'registrations' => $this->getUserRegistrations($userId)
            ];
        } else {
            // No session, but allow access - JWT token will be handled by JavaScript
            $data = [
                'title' => 'Dashboard - SNIA Conference',
                'user' => null,
                'userRole' => null,
                'userName' => null
            ];
        }

        return view('roles/audience/dashboard_clean', $data);
    }

    /**
     * API endpoint to get user profile data
     */
    public function profile()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        // Remove sensitive data
        unset($user['password']);
        unset($user['remember_token']);
        unset($user['reset_token']);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $updateData = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->userModel->update($userId, $updateData);

            // Update session data
            $fullName = $updateData['first_name'] . ' ' . $updateData['last_name'];
            $this->session->set('user_name', $fullName);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $user = $this->userModel->find($userId);
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ]);
        }

        try {
            $this->userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to change password'
            ]);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');

        try {
            $stats = [];

            // Get role-specific statistics
            if ($userRole === 'presenter') {
                $stats = [
                    'registrations' => $this->getRegistrationCount($userId),
                    'abstracts' => $this->getAbstractCount($userId),
                    'certificates' => $this->getCertificateCount($userId),
                    'payments' => $this->getPaymentCount($userId)
                ];
            } else {
                $stats = [
                    'registrations' => $this->getRegistrationCount($userId),
                    'qr_codes' => $this->getQRCodeCount($userId),
                    'certificates' => $this->getCertificateCount($userId),
                    'attendances' => $this->getAttendanceCount($userId)
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard stats error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load statistics'
            ]);
        }
    }

    /**
     * Get user registrations
     */
    public function registrations()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');

        try {
            $db = \Config\Database::connect();
            $registrations = $db->table('registrations r')
                ->select('r.*, e.title as event_name, e.description as event_description')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('r.created_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $registrations
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Load registrations error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load registrations'
            ]);
        }
    }

    /**
     * Get available events
     */
    public function events()
    {
        try {
            $db = \Config\Database::connect();
            $events = $db->table('events')
                ->where('is_active', true)
                ->orderBy('event_date', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();


            return $this->response->setJSON([
                'status' => 'success',
                'data' => $events,
                'count' => count($events)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Load events error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load events'
            ]);
        }
    }

    /**
     * Get event schedule data for calendar view
     */
    public function eventSchedule()
    {
        try {
            // Check if user is logged in
            $userId = $this->session->get('user_id');
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ]);
            }
            
            $db = \Config\Database::connect();
            log_message('debug', 'Event schedule - User ID: ' . $userId);
            
            // First, check if we have any events at all
            $totalEventsCount = $db->table('events')->countAllResults();
            log_message('debug', 'Total events in database: ' . $totalEventsCount);
            
            // Check active events
            $activeEventsCount = $db->table('events')->where('is_active', true)->countAllResults();
            log_message('debug', 'Active events in database: ' . $activeEventsCount);
            
            // Get all active events first
            $allEvents = $db->table('events')
                ->where('is_active', true)
                ->orderBy('event_date', 'ASC')
                ->orderBy('event_time', 'ASC')
                ->get()
                ->getResultArray();
            
            // Get user registrations
            $userRegistrations = $db->table('registrations')
                ->where('user_id', $userId)
                ->get()
                ->getResultArray();
            
            // Create a map of registered event IDs
            $registeredEventIds = [];
            $registrationData = [];
            foreach ($userRegistrations as $reg) {
                $registeredEventIds[] = $reg['event_id'];
                $registrationData[$reg['event_id']] = $reg;
            }
            
            // Combine events with registration status
            $events = [];
            foreach ($allEvents as $event) {
                $isRegistered = in_array($event['id'], $registeredEventIds);
                $regData = $registrationData[$event['id']] ?? null;
                
                $events[] = array_merge($event, [
                    'is_registered' => $isRegistered ? 1 : 0,
                    'registration_status' => $regData['registration_status'] ?? null,
                    'payment_status' => $regData['payment_status'] ?? null
                ]);
            }
            
            log_message('debug', 'Event schedule data count: ' . count($events));
            
            if (count($events) > 0) {
                log_message('debug', 'First event sample: ' . json_encode($events[0]));
            } else {
                log_message('debug', 'No events found - creating sample data for testing');
                // Add some sample events for testing
                $sampleEvents = [
                    [
                        'id' => '1',
                        'title' => 'Sample Conference 2024',
                        'description' => 'This is a sample event for testing the calendar display.',
                        'start' => '2025-08-20T09:00:00',
                        'date' => '2025-08-20',
                        'time' => '09:00',
                        'format' => 'hybrid',
                        'location' => 'Convention Center',
                        'zoom_link' => null,
                        'registration_fee' => 150000,
                        'max_participants' => 100,
                        'registration_deadline' => '2025-08-18',
                        'abstract_deadline' => '2025-08-15',
                        'is_registered' => false,
                        'registration_status' => null,
                        'payment_status' => null,
                        'className' => 'event-available'
                    ],
                    [
                        'id' => '2',
                        'title' => 'Workshop on AI Technology',
                        'description' => 'Learn about the latest developments in AI and machine learning.',
                        'start' => '2025-08-25T14:00:00',
                        'date' => '2025-08-25',
                        'time' => '14:00',
                        'format' => 'online',
                        'location' => 'Online Event',
                        'zoom_link' => 'https://zoom.us/j/example',
                        'registration_fee' => 75000,
                        'max_participants' => 50,
                        'registration_deadline' => '2025-08-23',
                        'abstract_deadline' => null,
                        'is_registered' => true,
                        'registration_status' => 'confirmed',
                        'payment_status' => 'paid',
                        'className' => 'event-registered'
                    ]
                ];
                // Removed sample events - use only database data
                // $events = array_merge($events, $sampleEvents);
            }

            // Transform events for calendar format
            $calendarEvents = [];
            foreach ($events as $event) {
                $calendarEvents[] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'start' => $event['event_date'] . 'T' . $event['event_time'],
                    'date' => $event['event_date'],
                    'time' => date('H:i', strtotime($event['event_time'])),
                    'format' => $event['format'],
                    'location' => $event['location'],
                    'zoom_link' => $event['zoom_link'],
                    'registration_fee' => $event['registration_fee'],
                    'max_participants' => $event['max_participants'],
                    'registration_deadline' => $event['registration_deadline'],
                    'abstract_deadline' => $event['abstract_deadline'],
                    'is_registered' => (bool)$event['is_registered'],
                    'registration_status' => $event['registration_status'],
                    'payment_status' => $event['payment_status'],
                    'className' => $event['is_registered'] ? 'event-registered' : 'event-available'
                ];
            }

            log_message('debug', 'Calendar events count: ' . count($calendarEvents));

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $calendarEvents,
                'count' => count($calendarEvents),
                'debug' => [
                    'user_id' => $userId,
                    'total_events' => $totalEventsCount,
                    'active_events' => $activeEventsCount,
                    'registered_events' => count($userRegistrations)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Load event schedule error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load event schedule: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display event schedule page
     */
    public function eventSchedulePage()
    {
        try {
            $userId = $this->session->get('user_id');
            
            if (!$userId) {
                return redirect()->to('/login')->with('error', 'Please login first');
            }
            
            $user = $this->userModel->find($userId);

            if (!$user) {
                return redirect()->to('/login')->with('error', 'User not found');
            }

            $data = [
                'title' => 'Jadwal Acara - SNIA Conference',
                'user' => $user,
                'validation' => \Config\Services::validation()
            ];

            return view('roles/audience/event_schedule', $data);
        } catch (\Exception $e) {
            log_message('error', 'Event schedule page error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Failed to load event schedule page');
        }
    }

    /**
     * Register for event
     */
    public function registerEvent()
    {
        if (!$this->session->get('user_id')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $userId = $this->session->get('user_id');
        $eventId = $this->request->getPost('event_id');
        $registrationType = $this->request->getPost('registration_type') ?? 'audience';

        if (!$eventId) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Event ID is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if already registered
            $existing = $db->table('registrations')
                ->where('user_id', $userId)
                ->where('event_id', $eventId)
                ->get()
                ->getRowArray();

            if ($existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You are already registered for this event'
                ]);
            }

            // Create registration
            $data = [
                'user_id' => $userId,
                'event_id' => $eventId,
                'registration_type' => $registrationType,
                'registration_status' => 'pending',
                'payment_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->table('registrations')->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => ['registration_id' => $db->insertID()]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Registration failed'
            ]);
        }
    }

    // ==================== PRIVATE METHODS ====================

    private function getRegistrationCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('registrations')
                  ->where('user_id', $userId)
                  ->countAllResults();
    }

    private function getAbstractCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('abstracts a')
                  ->join('registrations r', 'r.id = a.registration_id')
                  ->where('r.user_id', $userId)
                  ->countAllResults();
    }

    private function getCertificateCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('certificates c')
                  ->join('registrations r', 'r.id = c.registration_id')
                  ->where('r.user_id', $userId)
                  ->countAllResults();
    }

    private function getPaymentCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('payments p')
                  ->join('registrations r', 'r.id = p.registration_id')
                  ->where('r.user_id', $userId)
                  ->where('p.payment_status', 'success')
                  ->countAllResults();
    }

    private function getQRCodeCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('qr_codes')
                  ->where('user_id', $userId)
                  ->countAllResults();
    }

    private function getAttendanceCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('qr_scans qs')
                  ->join('qr_codes qc', 'qc.id = qs.qr_code_id')
                  ->where('qc.user_id', $userId)
                  ->where('qs.scan_result', 'success')
                  ->countAllResults();
    }

    /**
     * Get audience-specific statistics
     */
    private function getAudienceStats($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'total_registrations' => $db->table('registrations')->where('user_id', $userId)->countAllResults(),
                'upcoming_events' => $db->table('registrations r')
                    ->join('events e', 'e.id = r.event_id', 'inner')
                    ->where('r.user_id', $userId)
                    ->where('e.event_date >=', date('Y-m-d'))
                    ->countAllResults(),
                'completed_events' => $db->table('registrations r')
                    ->join('events e', 'e.id = r.event_id', 'inner')
                    ->where('r.user_id', $userId)
                    ->where('e.event_date <', date('Y-m-d'))
                    ->countAllResults(),
                'certificates_earned' => $db->table('certificates c')
                    ->join('registrations r', 'r.id = c.registration_id')
                    ->where('r.user_id', $userId)
                    ->countAllResults()
            ];
            
            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Get audience stats error: ' . $e->getMessage());
            return [
                'total_registrations' => 0,
                'upcoming_events' => 0,
                'completed_events' => 0,
                'certificates_earned' => 0
            ];
        }
    }

    /**
     * Get user registrations with event details
     */
    private function getUserRegistrations($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.event_date, e.event_time, e.location')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('e.event_date', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get user registrations error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Audience registrations page
     */
    public function audienceRegistrations()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);
        
        $data = [
            'title' => 'My Registrations - SNIA Conference',
            'user' => $user,
            'userRole' => $user['role'],
            'userName' => trim($user['first_name'] . ' ' . $user['last_name'])
        ];

        return view('roles/audience/registrations', $data);
    }

    /**
     * Payment history page
     */
    public function paymentHistory()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);
        
        $data = [
            'title' => 'Payment History - SNIA Conference',
            'user' => $user,
            'userRole' => $user['role'],
            'userName' => trim($user['first_name'] . ' ' . $user['last_name'])
        ];

        return view('roles/audience/payment_history', $data);
    }

    /**
     * API: Get audience registrations
     */
    public function getAudienceRegistrationsApi()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $db = \Config\Database::connect();
            
            $registrations = $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.event_date, e.event_time, e.location, e.description, e.registration_fee as price,
                         p.payment_status, p.amount as payment_amount, p.payment_method')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->join('payments p', 'p.registration_id = r.id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('r.created_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $registrations,
                'total' => count($registrations)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get audience registrations API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load registrations'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Get audience statistics
     */
    public function getAudienceStatsApi()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $stats = $this->getAudienceStats($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get audience stats API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load statistics'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Get upcoming events
     */
    public function getUpcomingEventsApi()
    {
        try {
            $db = \Config\Database::connect();
            
            $events = $db->table('events')
                ->where('event_date >=', date('Y-m-d'))
                ->where('is_active', true)
                ->orderBy('event_date', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $events,
                'total' => count($events)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get upcoming events API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load events'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Get user certificates
     */
    public function getCertificatesApi()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $db = \Config\Database::connect();
            
            $certificates = $db->table('certificates c')
                ->select('c.*, r.registration_type, e.title as event_title, e.event_date')
                ->join('registrations r', 'r.id = c.registration_id', 'inner')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->where('r.user_id', $userId)
                ->orderBy('c.issued_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $certificates,
                'total' => count($certificates)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get certificates API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load certificates'
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Get payment history
     */
    public function getPaymentHistoryApi()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $db = \Config\Database::connect();
            
            // Get all registrations with payment info (including unpaid ones)
            $payments = $db->table('registrations r')
                ->select('r.id as registration_id, r.registration_type, r.created_at as registration_date,
                         e.title as event_title, e.event_date, e.registration_fee as event_price,
                         p.id as payment_id, p.amount as payment_amount, p.payment_status, 
                         p.payment_method, p.transaction_id, p.created_at as payment_date,
                         COALESCE(p.payment_status, \'pending\') as final_status')
                ->join('events e', 'e.id = r.event_id', 'inner')
                ->join('payments p', 'p.registration_id = r.id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('r.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Format the data for better display
            $formattedPayments = [];
            foreach ($payments as $payment) {
                $formattedPayments[] = [
                    'id' => $payment['payment_id'] ?? $payment['registration_id'],
                    'registration_id' => $payment['registration_id'],
                    'event_title' => $payment['event_title'],
                    'event_date' => $payment['event_date'],
                    'registration_type' => $payment['registration_type'],
                    'amount' => $payment['payment_amount'] ?? $payment['event_price'] ?? 0,
                    'status' => $payment['final_status'],
                    'payment_method' => $payment['payment_method'] ?? 'N/A',
                    'external_id' => $payment['external_id'] ?? null,
                    'invoice_url' => $payment['invoice_url'] ?? null,
                    'created_at' => $payment['payment_date'] ?? $payment['registration_date'],
                    'registration_date' => $payment['registration_date']
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedPayments,
                'total' => count($formattedPayments)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get payment history API error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load payment history'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Get my registrations (session-based, compatible with Postman)
     */
    public function getMyRegistrations()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $db = \Config\Database::connect();
            
            $registrations = $db->table('registrations r')
                ->select('r.*, e.title as event_title, e.event_date, e.event_time, e.location, e.description, e.registration_fee as price,
                         p.payment_status, p.amount as payment_amount, p.payment_method')
                ->join('events e', 'e.id = r.event_id', 'left')
                ->join('payments p', 'p.registration_id = r.id', 'left')
                ->where('r.user_id', $userId)
                ->orderBy('r.created_at', 'DESC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $registrations,
                'total' => count($registrations)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get my registrations error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load registrations'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * Cancel user registration (both POST and DELETE support)
     */
    public function cancelRegistration($registrationId = null)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Support both URL parameter and POST body
        if (!$registrationId) {
            $registrationId = $this->request->getPost('registration_id');
        }
        if (!$registrationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Registration ID is required'
            ]);
        }

        try {
            $db = \Config\Database::connect();
            
            // Verify the registration belongs to the user and is cancelable
            $registration = $db->table('registrations')
                ->where('id', $registrationId)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            if (!$registration) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found'
                ]);
            }

            // Check if registration can be cancelled (only pending registrations)
            if ($registration['registration_status'] !== 'pending') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Only pending registrations can be cancelled'
                ]);
            }

            // Update registration status to cancelled
            $updated = $db->table('registrations')
                ->where('id', $registrationId)
                ->update([
                    'registration_status' => 'cancelled',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($updated) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Registration cancelled successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to cancel registration'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Cancel registration error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to cancel registration'
            ])->setStatusCode(500);
        }
    }
}