<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\AbstractCategoryModel;
use App\Models\VoucherModel;
use CodeIgniter\HTTP\ResponseInterface;

class EventController extends BaseController
{
    protected $eventModel;
    protected $registrationModel;
    protected $abstractModel;
    protected $categoryModel;
    protected $voucherModel;
    protected $session;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->registrationModel = new RegistrationModel();
        $this->abstractModel = new AbstractModel();
        $this->categoryModel = new AbstractCategoryModel();
        $this->voucherModel = new VoucherModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display events list
     */
    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $format = $this->request->getGet('format');
        $sort = $this->request->getGet('sort') ?? 'created_at';
        $order = $this->request->getGet('order') ?? 'DESC';

        $builder = $this->eventModel;

        // Apply filters
        if ($search) {
            $builder = $builder->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('location', $search)
                ->groupEnd();
        }

        if ($status && $status !== 'all') {
            switch ($status) {
                case 'active':
                    $builder = $builder->where('is_active', true);
                    break;
                case 'inactive':
                    $builder = $builder->where('is_active', false);
                    break;
                case 'upcoming':
                    $builder = $builder->where('start_date >', date('Y-m-d'));
                    break;
                case 'ongoing':
                    $builder = $builder->where('start_date <=', date('Y-m-d'))
                        ->where('end_date >=', date('Y-m-d'));
                    break;
                case 'completed':
                    $builder = $builder->where('end_date <', date('Y-m-d'));
                    break;
            }
        }

        if ($format && $format !== 'all') {
            $builder = $builder->where('format', $format);
        }

        // Get events with registration counts
        $events = $builder->select('events.*, 
            (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.id) as registration_count')
            ->orderBy($sort, $order)
            ->paginate($perPage);
        
        $pager = $this->eventModel->pager;

        // Get statistics
        $stats = [
            'total' => $this->eventModel->countAll(),
            'active' => $this->eventModel->where('is_active', true)->countAllResults(),
            'upcoming' => $this->eventModel->where('start_date >', date('Y-m-d'))->countAllResults(),
            'ongoing' => $this->eventModel->where('start_date <=', date('Y-m-d'))->where('end_date >=', date('Y-m-d'))->countAllResults(),
            'completed' => $this->eventModel->where('end_date <', date('Y-m-d'))->countAllResults(),
        ];

        $data = [
            'title' => 'Event Management - Admin',
            'events' => $events,
            'pager' => $pager,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'format' => $format,
                'sort' => $sort,
                'order' => $order,
                'per_page' => $perPage
            ]
        ];

        return view('admin/events/index', $data);
    }

    /**
     * Display event details
     */
    public function show($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        // Get event statistics
        $stats = [
            'registrations' => $this->registrationModel->where('event_id', $id)->countAllResults(),
            'confirmed' => $this->registrationModel->where('event_id', $id)->where('status', 'confirmed')->countAllResults(),
            'pending' => $this->registrationModel->where('event_id', $id)->where('status', 'pending')->countAllResults(),
            'abstracts' => $this->abstractModel->where('event_id', $id)->countAllResults(),
            'accepted_abstracts' => $this->abstractModel->where('event_id', $id)->where('status', 'accepted')->countAllResults(),
        ];

        // Get recent registrations
        $recentRegistrations = $this->registrationModel
            ->select('registrations.*, users.full_name, users.email')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.event_id', $id)
            ->orderBy('registrations.created_at', 'DESC')
            ->limit(10)
            ->find();

        // Get event revenue
        $revenue = $this->getEventRevenue($id);

        $data = [
            'title' => 'Event Details - ' . $event['title'],
            'event' => $event,
            'stats' => $stats,
            'registrations' => $recentRegistrations,
            'revenue' => $revenue,
        ];

        return view('admin/events/show', $data);
    }

    /**
     * Show create event form
     */
    public function create()
    {
        $categories = $this->categoryModel->where('is_active', true)->findAll();

        $data = [
            'title' => 'Create New Event - Admin',
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/events/create', $data);
    }

    /**
     * Store new event
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[200]',
            'description' => 'required|min_length[10]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'format' => 'required|in_list[online,offline,hybrid]',
            'max_participants' => 'permit_empty|integer|greater_than[0]',
            'registration_fee' => 'permit_empty|decimal',
            'early_bird_fee' => 'permit_empty|decimal',
            'early_bird_deadline' => 'permit_empty|valid_date',
            'registration_deadline' => 'permit_empty|valid_date',
            'location' => 'permit_empty|max_length[500]',
            'online_link' => 'permit_empty|valid_url',
            'contact_email' => 'permit_empty|valid_email',
            'contact_phone' => 'permit_empty|max_length[20]',
            'is_active' => 'permit_empty|in_list[0,1]',
            'allow_abstract_submission' => 'permit_empty|in_list[0,1]',
            'abstract_deadline' => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file uploads
        $bannerImage = $this->handleFileUpload('banner_image', 'events/banners');
        $brochureFile = $this->handleFileUpload('brochure_file', 'events/brochures');

        $eventData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'format' => $this->request->getPost('format'),
            'max_participants' => $this->request->getPost('max_participants') ?: null,
            'registration_fee' => $this->request->getPost('registration_fee') ?: 0,
            'early_bird_fee' => $this->request->getPost('early_bird_fee') ?: null,
            'early_bird_deadline' => $this->request->getPost('early_bird_deadline') ?: null,
            'registration_deadline' => $this->request->getPost('registration_deadline') ?: null,
            'location' => $this->request->getPost('location'),
            'online_link' => $this->request->getPost('online_link'),
            'contact_email' => $this->request->getPost('contact_email'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'banner_image' => $bannerImage,
            'brochure_file' => $brochureFile,
            'is_active' => $this->request->getPost('is_active') ? true : false,
            'allow_abstract_submission' => $this->request->getPost('allow_abstract_submission') ? true : false,
            'abstract_deadline' => $this->request->getPost('abstract_deadline') ?: null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $eventId = $this->eventModel->insert($eventData);

            if (!$eventId) {
                throw new \Exception('Failed to create event');
            }

            // Create default abstract categories for this event if abstract submission is allowed
            if ($eventData['allow_abstract_submission']) {
                $this->createDefaultCategories($eventId);
            }

            // Log admin action
            $this->logAdminAction('create_event', $eventId, "Created event: {$eventData['title']}");

            return redirect()->to('/admin/events')->with('success', 'Event created successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Admin event creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create event. Please try again.');
        }
    }

    /**
     * Show edit event form
     */
    public function edit($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        $categories = $this->categoryModel->where('is_active', true)->findAll();

        $data = [
            'title' => 'Edit Event - ' . $event['title'],
            'event' => $event,
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/events/edit', $data);
    }

    /**
     * Update event
     */
    public function update($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        $rules = [
            'title' => 'required|min_length[5]|max_length[200]',
            'description' => 'required|min_length[10]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'format' => 'required|in_list[online,offline,hybrid]',
            'max_participants' => 'permit_empty|integer|greater_than[0]',
            'registration_fee' => 'permit_empty|decimal',
            'early_bird_fee' => 'permit_empty|decimal',
            'early_bird_deadline' => 'permit_empty|valid_date',
            'registration_deadline' => 'permit_empty|valid_date',
            'location' => 'permit_empty|max_length[500]',
            'online_link' => 'permit_empty|valid_url',
            'contact_email' => 'permit_empty|valid_email',
            'contact_phone' => 'permit_empty|max_length[20]',
            'is_active' => 'permit_empty|in_list[0,1]',
            'allow_abstract_submission' => 'permit_empty|in_list[0,1]',
            'abstract_deadline' => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file uploads
        $bannerImage = $this->handleFileUpload('banner_image', 'events/banners') ?: $event['banner_image'];
        $brochureFile = $this->handleFileUpload('brochure_file', 'events/brochures') ?: $event['brochure_file'];

        $updateData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'format' => $this->request->getPost('format'),
            'max_participants' => $this->request->getPost('max_participants') ?: null,
            'registration_fee' => $this->request->getPost('registration_fee') ?: 0,
            'early_bird_fee' => $this->request->getPost('early_bird_fee') ?: null,
            'early_bird_deadline' => $this->request->getPost('early_bird_deadline') ?: null,
            'registration_deadline' => $this->request->getPost('registration_deadline') ?: null,
            'location' => $this->request->getPost('location'),
            'online_link' => $this->request->getPost('online_link'),
            'contact_email' => $this->request->getPost('contact_email'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'banner_image' => $bannerImage,
            'brochure_file' => $brochureFile,
            'is_active' => $this->request->getPost('is_active') ? true : false,
            'allow_abstract_submission' => $this->request->getPost('allow_abstract_submission') ? true : false,
            'abstract_deadline' => $this->request->getPost('abstract_deadline') ?: null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->eventModel->update($id, $updateData);

            // Log admin action
            $this->logAdminAction('update_event', $id, "Updated event: {$updateData['title']}");

            return redirect()->to('/admin/events')->with('success', 'Event updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Admin event update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update event. Please try again.');
        }
    }

    /**
     * Delete event
     */
    public function delete($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return $this->response->setJSON(['success' => false, 'message' => 'Event not found']);
        }

        // Check if event has registrations
        $hasRegistrations = $this->registrationModel->where('event_id', $id)->countAllResults() > 0;

        if ($hasRegistrations) {
            // Soft delete - deactivate event
            $this->eventModel->update($id, ['is_active' => false]);
            $message = 'Event deactivated (has existing registrations)';
            $action = 'deactivated';
        } else {
            // Hard delete
            $this->eventModel->delete($id);
            $message = 'Event deleted successfully';
            $action = 'deleted';
        }

        // Log admin action
        $this->logAdminAction('delete_event', $id, "Event {$action}: {$event['title']}");

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    /**
     * Duplicate event
     */
    public function duplicate($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return $this->response->setJSON(['success' => false, 'message' => 'Event not found']);
        }

        // Remove unique fields and update title
        unset($event['id']);
        $event['title'] = 'Copy of ' . $event['title'];
        $event['is_active'] = false; // New event starts as inactive
        $event['created_at'] = date('Y-m-d H:i:s');
        $event['updated_at'] = null;

        try {
            $newEventId = $this->eventModel->insert($event);

            if (!$newEventId) {
                throw new \Exception('Failed to duplicate event');
            }

            // Log admin action
            $this->logAdminAction('duplicate_event', $newEventId, "Duplicated event: {$event['title']}");

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Event duplicated successfully',
                'new_event_id' => $newEventId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Event duplication error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to duplicate event']);
        }
    }

    /**
     * Toggle event status
     */
    public function toggleStatus($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return $this->response->setJSON(['success' => false, 'message' => 'Event not found']);
        }

        $newStatus = !$event['is_active'];
        $this->eventModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activated' : 'deactivated';

        // Log admin action
        $this->logAdminAction('toggle_event_status', $id, "Event {$statusText}: {$event['title']}");

        return $this->response->setJSON([
            'success' => true, 
            'message' => "Event {$statusText} successfully",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Get event registrations
     */
    public function registrations($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->registrationModel
            ->select('registrations.*, users.full_name, users.email, users.phone, users.institution')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.event_id', $id);

        // Apply filters
        if ($search) {
            $builder = $builder->groupStart()
                ->like('users.full_name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.institution', $search)
                ->groupEnd();
        }

        if ($status && $status !== 'all') {
            $builder = $builder->where('registrations.status', $status);
        }

        $registrations = $builder->orderBy('registrations.created_at', 'DESC')->paginate($perPage);
        $pager = $this->registrationModel->pager;

        $data = [
            'title' => 'Event Registrations - ' . $event['title'],
            'event' => $event,
            'registrations' => $registrations,
            'pager' => $pager,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'per_page' => $perPage
            ]
        ];

        return view('admin/events/registrations', $data);
    }

    /**
     * Export event registrations
     */
    public function exportRegistrations($id, $format = 'csv')
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        $registrations = $this->registrationModel
            ->select('registrations.*, users.full_name, users.email, users.phone, users.institution, users.role')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.event_id', $id)
            ->orderBy('registrations.created_at', 'DESC')
            ->findAll();

        if ($format === 'csv') {
            return $this->exportRegistrationsCSV($registrations, $event['title']);
        } else {
            return $this->exportRegistrationsExcel($registrations, $event['title']);
        }
    }

    /**
     * Send event notifications
     */
    public function sendNotification($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return $this->response->setJSON(['success' => false, 'message' => 'Event not found']);
        }

        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');
        $recipients = $this->request->getPost('recipients'); // 'all', 'confirmed', 'pending'

        if (!$subject || !$message) {
            return $this->response->setJSON(['success' => false, 'message' => 'Subject and message are required']);
        }

        // Get recipients
        $builder = $this->registrationModel
            ->select('users.email, users.full_name')
            ->join('users', 'users.id = registrations.user_id')
            ->where('registrations.event_id', $id);

        if ($recipients !== 'all') {
            $builder = $builder->where('registrations.status', $recipients);
        }

        $users = $builder->findAll();

        // Send emails
        $sent = 0;
        $failed = 0;

        foreach ($users as $user) {
            if ($this->sendEventEmail($user['email'], $user['full_name'], $subject, $message, $event)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        // Log admin action
        $this->logAdminAction('send_event_notification', $id, "Sent notification to {$sent} users for event: {$event['title']}");

        return $this->response->setJSON([
            'success' => true,
            'message' => "Notification sent to {$sent} users" . ($failed > 0 ? " ({$failed} failed)" : "")
        ]);
    }

    /**
     * Get event analytics
     */
    public function analytics($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Event not found');
        }

        $analytics = [
            'registration_timeline' => $this->getRegistrationTimeline($id),
            'registration_by_role' => $this->getRegistrationsByRole($id),
            'registration_by_status' => $this->getRegistrationsByStatus($id),
            'daily_registrations' => $this->getDailyRegistrations($id),
            'institution_breakdown' => $this->getInstitutionBreakdown($id),
            'revenue_data' => $this->getEventRevenue($id)
        ];

        return $this->response->setJSON($analytics);
    }

    /**
     * Bulk actions for events
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $eventIds = $this->request->getPost('event_ids');

        if (!$action || !$eventIds || !is_array($eventIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $processed = 0;

        foreach ($eventIds as $id) {
            switch ($action) {
                case 'activate':
                    $this->eventModel->update($id, ['is_active' => true]);
                    $processed++;
                    break;

                case 'deactivate':
                    $this->eventModel->update($id, ['is_active' => false]);
                    $processed++;
                    break;

                case 'delete':
                    // Check if event has registrations before deleting
                    $hasRegistrations = $this->registrationModel->where('event_id', $id)->countAllResults() > 0;
                    if (!$hasRegistrations) {
                        $this->eventModel->delete($id);
                        $processed++;
                    }
                    break;
            }
        }

        // Log admin action
        $this->logAdminAction('bulk_event_action', null, "Bulk action '{$action}' performed on {$processed} events");

        return $this->response->setJSON([
            'success' => true, 
            'message' => "Action completed successfully for {$processed} events"
        ]);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Handle file upload
     */
    private function handleFileUpload($fieldName, $uploadPath)
    {
        $file = $this->request->getFile($fieldName);

        if (!$file || !$file->isValid()) {
            return null;
        }

        // Create upload directory if it doesn't exist
        $fullPath = WRITEPATH . 'uploads/' . $uploadPath;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Generate unique filename
        $fileName = $file->getRandomName();

        // Move file to upload directory
        if ($file->move($fullPath, $fileName)) {
            return $uploadPath . '/' . $fileName;
        }

        return null;
    }

    /**
     * Create default abstract categories for event
     */
    private function createDefaultCategories($eventId)
    {
        $defaultCategories = [
            ['name' => 'Research Paper', 'description' => 'Original research presentations'],
            ['name' => 'Case Study', 'description' => 'Real-world application cases'],
            ['name' => 'Workshop', 'description' => 'Interactive workshop sessions'],
            ['name' => 'Poster', 'description' => 'Poster presentations'],
        ];

        foreach ($defaultCategories as $category) {
            $this->categoryModel->insert([
                'name' => $category['name'],
                'description' => $category['description'],
                'event_id' => $eventId,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get event revenue data
     */
    private function getEventRevenue($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                SUM(CASE WHEN p.status = 'completed' THEN p.amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN p.status = 'pending' THEN p.amount ELSE 0 END) as pending_revenue,
                COUNT(CASE WHEN p.status = 'completed' THEN 1 END) as paid_registrations,
                COUNT(CASE WHEN p.status = 'pending' THEN 1 END) as pending_payments
            FROM registrations r
            LEFT JOIN payments p ON p.registration_id = r.id
            WHERE r.event_id = ?
        ", [$eventId]);

        return $query->getRowArray();
    }

    /**
     * Get registration timeline
     */
    private function getRegistrationTimeline($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM registrations
            WHERE event_id = ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$eventId]);

        return $query->getResultArray();
    }

    /**
     * Get registrations by role
     */
    private function getRegistrationsByRole($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT u.role, COUNT(*) as count
            FROM registrations r
            JOIN users u ON u.id = r.user_id
            WHERE r.event_id = ?
            GROUP BY u.role
        ", [$eventId]);

        return $query->getResultArray();
    }

    /**
     * Get registrations by status
     */
    private function getRegistrationsByStatus($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT status, COUNT(*) as count
            FROM registrations
            WHERE event_id = ?
            GROUP BY status
        ", [$eventId]);

        return $query->getResultArray();
    }

    /**
     * Get daily registrations
     */
    private function getDailyRegistrations($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM registrations
            WHERE event_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$eventId]);

        return $query->getResultArray();
    }

    /**
     * Get institution breakdown
     */
    private function getInstitutionBreakdown($eventId)
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                COALESCE(u.institution, 'Not Specified') as institution, 
                COUNT(*) as count
            FROM registrations r
            JOIN users u ON u.id = r.user_id
            WHERE r.event_id = ?
            GROUP BY u.institution
            ORDER BY count DESC
            LIMIT 10
        ", [$eventId]);

        return $query->getResultArray();
    }

    /**
     * Send event email
     */
    private function sendEventEmail($email, $name, $subject, $message, $event)
    {
        $emailService = \Config\Services::email();

        $fullMessage = "
            <h2>{$subject}</h2>
            <p>Hello {$name},</p>
            <p>This is a message regarding the event: <strong>{$event['title']}</strong></p>
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;'>
                {$message}
            </div>
            <p>Event Details:</p>
            <ul>
                <li><strong>Date:</strong> {$event['start_date']} to {$event['end_date']}</li>
                <li><strong>Time:</strong> {$event['start_time']} - {$event['end_time']}</li>
                <li><strong>Format:</strong> " . ucfirst($event['format']) . "</li>
                " . ($event['location'] ? "<li><strong>Location:</strong> {$event['location']}</li>" : "") . "
            </ul>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject($subject . ' - ' . $event['title']);
        $emailService->setMessage($fullMessage);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send event email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export registrations as CSV
     */
    private function exportRegistrationsCSV($registrations, $eventTitle)
    {
        $filename = 'registrations_' . preg_replace('/[^A-Za-z0-9]/', '_', $eventTitle) . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, [
            'Registration ID', 'Full Name', 'Email', 'Phone', 'Institution', 'Role', 
            'Status', 'QR Code', 'Registration Date', 'Payment Status'
        ]);
        
        // Write data
        foreach ($registrations as $reg) {
            fputcsv($output, [
                $reg['id'],
                $reg['full_name'],
                $reg['email'],
                $reg['phone'],
                $reg['institution'],
                $reg['role'],
                $reg['status'],
                $reg['qr_code'],
                $reg['created_at'],
                $reg['payment_status'] ?? 'N/A'
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export registrations as Excel (placeholder)
     */
    private function exportRegistrationsExcel($registrations, $eventTitle)
    {
        // For now, fallback to CSV
        return $this->exportRegistrationsCSV($registrations, $eventTitle);
    }

    /**
     * Log admin actions
     */
    private function logAdminAction($action, $targetId, $description)
    {
        $adminId = $this->session->get('user_id');
        $adminName = $this->session->get('user_name');
        
        log_message('info', "Admin Action - Admin: {$adminName} (ID: {$adminId}), Action: {$action}, Target: {$targetId}, Description: {$description}");
    }
}