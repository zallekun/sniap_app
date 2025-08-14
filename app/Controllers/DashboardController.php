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

            $data = [
                'title' => 'Dashboard - SNIA Conference',
                'user' => $user,
                'userRole' => $userRole,
                'userName' => trim($user['first_name'] . ' ' . $user['last_name'])
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

        return view('dashboard/index', $data);
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

            log_message('debug', 'Events loaded: ' . count($events) . ' events found');
            log_message('debug', 'Events data: ' . json_encode($events));

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

            return view('user/event_schedule', $data);
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
}