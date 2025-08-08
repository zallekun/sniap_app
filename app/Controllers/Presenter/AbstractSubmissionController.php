<?php

namespace App\Controllers\Presenter;

use App\Controllers\BaseController;
use App\Models\AbstractModel;
use App\Models\EventModel;
use App\Models\AbstractCategoryModel;
use App\Models\UserModel;
use App\Models\ReviewModel;
use CodeIgniter\HTTP\ResponseInterface;

class AbstractSubmissionController extends BaseController
{
    protected $abstractModel;
    protected $eventModel;
    protected $categoryModel;
    protected $userModel;
    protected $reviewModel;
    protected $session;

    public function __construct()
    {
        $this->abstractModel = new AbstractModel();
        $this->eventModel = new EventModel();
        $this->categoryModel = new AbstractCategoryModel();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display presenter's abstracts
     */
    public function index()
    {
        $userId = $this->session->get('user_id');

        // Get presenter's abstracts with event and category details
        $abstracts = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, abstract_categories.name as category_name')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id', 'left')
            ->where('abstracts.user_id', $userId)
            ->orderBy('abstracts.created_at', 'DESC')
            ->findAll();

        // Get statistics
        $stats = [
            'total' => count($abstracts),
            'pending' => count(array_filter($abstracts, fn($a) => $a['status'] === 'pending')),
            'under_review' => count(array_filter($abstracts, fn($a) => $a['status'] === 'under_review')),
            'accepted' => count(array_filter($abstracts, fn($a) => $a['status'] === 'accepted')),
            'rejected' => count(array_filter($abstracts, fn($a) => $a['status'] === 'rejected')),
            'needs_revision' => count(array_filter($abstracts, fn($a) => $a['status'] === 'needs_revision')),
        ];

        $data = [
            'title' => 'My Abstracts - SNIA Conference',
            'abstracts' => $abstracts,
            'stats' => $stats
        ];

        return view('presenter/abstracts/index', $data);
    }

    /**
     * Show abstract submission form
     */
    public function create($eventId = null)
    {
        // Get events that allow abstract submission
        $events = $this->eventModel
            ->where('allow_abstract_submission', true)
            ->where('is_active', true)
            ->where('abstract_deadline >=', date('Y-m-d'))
            ->orderBy('abstract_deadline', 'ASC')
            ->findAll();

        if (empty($events)) {
            return redirect()->to('/presenter/abstracts')->with('info', 'No events are currently accepting abstract submissions.');
        }

        // If eventId is provided, validate it
        $selectedEvent = null;
        if ($eventId) {
            $selectedEvent = $this->eventModel->find($eventId);
            if (!$selectedEvent || !$selectedEvent['allow_abstract_submission'] || !$selectedEvent['is_active']) {
                return redirect()->to('/presenter/abstracts/create')->with('error', 'Invalid event selected.');
            }
        }

        // Get categories for selected event or all active categories
        $categories = [];
        if ($selectedEvent) {
            $categories = $this->categoryModel
                ->where('event_id', $eventId)
                ->where('is_active', true)
                ->orderBy('name', 'ASC')
                ->findAll();
        } else {
            $categories = $this->categoryModel
                ->where('is_active', true)
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        $data = [
            'title' => 'Submit Abstract - SNIA Conference',
            'events' => $events,
            'categories' => $categories,
            'selected_event' => $selectedEvent,
            'validation' => \Config\Services::validation()
        ];

        return view('presenter/abstracts/create', $data);
    }

    /**
     * Store new abstract submission
     */
    public function store()
    {
        $userId = $this->session->get('user_id');

        $rules = [
            'event_id' => 'required|integer',
            'category_id' => 'required|integer',
            'title' => 'required|min_length[5]|max_length[200]',
            'abstract_text' => 'required|min_length[100]|max_length[5000]',
            'keywords' => 'required|max_length[500]',
            'authors' => 'required|max_length[500]',
            'author_affiliations' => 'required|max_length[1000]',
            'abstract_file' => 'permit_empty|uploaded[abstract_file]|ext_in[abstract_file,pdf,doc,docx]|max_size[abstract_file,5120]',
            'presentation_type' => 'required|in_list[oral,poster,workshop]',
            'is_presenting_author' => 'permit_empty|in_list[0,1]',
            'terms_accepted' => 'required'
        ];

        $messages = [
            'event_id' => [
                'required' => 'Please select an event',
                'integer' => 'Invalid event selected'
            ],
            'category_id' => [
                'required' => 'Please select a category',
                'integer' => 'Invalid category selected'
            ],
            'title' => [
                'required' => 'Abstract title is required',
                'min_length' => 'Title must be at least 5 characters',
                'max_length' => 'Title cannot exceed 200 characters'
            ],
            'abstract_text' => [
                'required' => 'Abstract content is required',
                'min_length' => 'Abstract must be at least 100 characters',
                'max_length' => 'Abstract cannot exceed 5000 characters'
            ],
            'keywords' => [
                'required' => 'Keywords are required',
                'max_length' => 'Keywords cannot exceed 500 characters'
            ],
            'authors' => [
                'required' => 'Author information is required',
                'max_length' => 'Author information cannot exceed 500 characters'
            ],
            'author_affiliations' => [
                'required' => 'Author affiliations are required',
                'max_length' => 'Affiliations cannot exceed 1000 characters'
            ],
            'abstract_file' => [
                'uploaded' => 'Please upload a valid abstract file',
                'ext_in' => 'Abstract file must be PDF, DOC, or DOCX format',
                'max_size' => 'Abstract file must be less than 5MB'
            ],
            'presentation_type' => [
                'required' => 'Please select presentation type',
                'in_list' => 'Invalid presentation type'
            ],
            'terms_accepted' => [
                'required' => 'You must accept the terms and conditions'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate event
        $eventId = $this->request->getPost('event_id');
        $event = $this->eventModel->find($eventId);

        if (!$event || !$event['allow_abstract_submission'] || !$event['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Invalid event selected.');
        }

        // Check abstract submission deadline
        if ($event['abstract_deadline'] && strtotime($event['abstract_deadline']) < time()) {
            return redirect()->back()->withInput()->with('error', 'Abstract submission deadline has passed for this event.');
        }

        // Validate category
        $categoryId = $this->request->getPost('category_id');
        $category = $this->categoryModel->find($categoryId);

        if (!$category || !$category['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Invalid category selected.');
        }

        // Check if user already submitted abstract for this event (optional limit)
        $existingAbstracts = $this->abstractModel
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->countAllResults();

        // Set limit (e.g., max 3 abstracts per event per user)
        $maxAbstractsPerEvent = 3;
        if ($existingAbstracts >= $maxAbstractsPerEvent) {
            return redirect()->back()->withInput()->with('error', "You can only submit maximum {$maxAbstractsPerEvent} abstracts per event.");
        }

        try {
            // Handle file upload
            $filePath = $this->handleAbstractFileUpload($userId);

            // Prepare abstract data
            $abstractData = [
                'user_id' => $userId,
                'event_id' => $eventId,
                'category_id' => $categoryId,
                'title' => $this->request->getPost('title'),
                'abstract_text' => $this->request->getPost('abstract_text'),
                'keywords' => $this->request->getPost('keywords'),
                'authors' => $this->request->getPost('authors'),
                'author_affiliations' => $this->request->getPost('author_affiliations'),
                'presentation_type' => $this->request->getPost('presentation_type'),
                'is_presenting_author' => $this->request->getPost('is_presenting_author') ? true : false,
                'file_path' => $filePath,
                'status' => 'pending',
                'submitted_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $abstractId = $this->abstractModel->insert($abstractData);

            if (!$abstractId) {
                throw new \Exception('Failed to submit abstract');
            }

            // Send confirmation email
            $this->sendSubmissionConfirmation($abstractId);

            // Send notification to admin
            $this->notifyAdminNewSubmission($abstractId);

            return redirect()->to('/presenter/abstracts')->with('success', 'Abstract submitted successfully! You will be notified about the review status.');

        } catch (\Exception $e) {
            log_message('error', 'Abstract submission error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to submit abstract. Please try again.');
        }
    }

    /**
     * Show abstract details
     */
    public function show($abstractId)
    {
        $userId = $this->session->get('user_id');
        
        $abstract = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, abstract_categories.name as category_name')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id', 'left')
            ->where('abstracts.id', $abstractId)
            ->where('abstracts.user_id', $userId)
            ->first();

        if (!$abstract) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Abstract not found');
        }

        // Get reviews for this abstract
        $reviews = $this->reviewModel
            ->select('reviews.*, users.full_name as reviewer_name')
            ->join('users', 'users.id = reviews.reviewer_id')
            ->where('reviews.abstract_id', $abstractId)
            ->where('reviews.status', 'completed')
            ->findAll();

        $data = [
            'title' => 'Abstract Details - ' . $abstract['title'],
            'abstract' => $abstract,
            'reviews' => $reviews,
            'can_edit' => in_array($abstract['status'], ['pending', 'needs_revision'])
        ];

        return view('presenter/abstracts/show', $data);
    }

    /**
     * Edit abstract (only if pending or needs revision)
     */
    public function edit($abstractId)
    {
        $userId = $this->session->get('user_id');
        
        $abstract = $this->abstractModel
            ->where('id', $abstractId)
            ->where('user_id', $userId)
            ->first();

        if (!$abstract) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Abstract not found');
        }

        // Check if abstract can be edited
        if (!in_array($abstract['status'], ['pending', 'needs_revision'])) {
            return redirect()->to("/presenter/abstracts/{$abstractId}")
                ->with('error', 'This abstract cannot be edited in its current status.');
        }

        // Get event details
        $event = $this->eventModel->find($abstract['event_id']);
        
        // Check if still within deadline
        if ($event['abstract_deadline'] && strtotime($event['abstract_deadline']) < time()) {
            return redirect()->to("/presenter/abstracts/{$abstractId}")
                ->with('error', 'Abstract editing deadline has passed.');
        }

        // Get categories for this event
        $categories = $this->categoryModel
            ->where('event_id', $abstract['event_id'])
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Abstract - ' . $abstract['title'],
            'abstract' => $abstract,
            'event' => $event,
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return view('presenter/abstracts/edit', $data);
    }

    /**
     * Update abstract
     */
    public function update($abstractId)
    {
        $userId = $this->session->get('user_id');
        
        $abstract = $this->abstractModel
            ->where('id', $abstractId)
            ->where('user_id', $userId)
            ->first();

        if (!$abstract || !in_array($abstract['status'], ['pending', 'needs_revision'])) {
            return redirect()->to('/presenter/abstracts')->with('error', 'Abstract cannot be updated.');
        }

        $rules = [
            'category_id' => 'required|integer',
            'title' => 'required|min_length[5]|max_length[200]',
            'abstract_text' => 'required|min_length[100]|max_length[5000]',
            'keywords' => 'required|max_length[500]',
            'authors' => 'required|max_length[500]',
            'author_affiliations' => 'required|max_length[1000]',
            'abstract_file' => 'permit_empty|uploaded[abstract_file]|ext_in[abstract_file,pdf,doc,docx]|max_size[abstract_file,5120]',
            'presentation_type' => 'required|in_list[oral,poster,workshop]',
            'is_presenting_author' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Handle file upload (only if new file is provided)
            $filePath = $abstract['file_path'];
            $newFile = $this->handleAbstractFileUpload($userId);
            if ($newFile) {
                // Delete old file
                if ($filePath) {
                    $oldFilePath = WRITEPATH . 'uploads/' . $filePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $filePath = $newFile;
            }

            // Update abstract data
            $updateData = [
                'category_id' => $this->request->getPost('category_id'),
                'title' => $this->request->getPost('title'),
                'abstract_text' => $this->request->getPost('abstract_text'),
                'keywords' => $this->request->getPost('keywords'),
                'authors' => $this->request->getPost('authors'),
                'author_affiliations' => $this->request->getPost('author_affiliations'),
                'presentation_type' => $this->request->getPost('presentation_type'),
                'is_presenting_author' => $this->request->getPost('is_presenting_author') ? true : false,
                'file_path' => $filePath,
                'status' => 'pending', // Reset to pending after revision
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->abstractModel->update($abstractId, $updateData);

            // Send update notification
            $this->sendUpdateNotification($abstractId);

            return redirect()->to("/presenter/abstracts/{$abstractId}")
                ->with('success', 'Abstract updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Abstract update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update abstract. Please try again.');
        }
    }

    /**
     * Withdraw abstract submission
     */
    public function withdraw($abstractId)
    {
        $userId = $this->session->get('user_id');
        
        $abstract = $this->abstractModel
            ->where('id', $abstractId)
            ->where('user_id', $userId)
            ->first();

        if (!$abstract) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Abstract not found'
            ]);
        }

        // Check if abstract can be withdrawn
        if (in_array($abstract['status'], ['accepted', 'rejected'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot withdraw abstract that has been ' . $abstract['status']
            ]);
        }

        try {
            // Update status to withdrawn
            $this->abstractModel->update($abstractId, [
                'status' => 'withdrawn',
                'withdrawn_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Send withdrawal notification
            $this->sendWithdrawalNotification($abstractId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Abstract withdrawn successfully'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Abstract withdrawal error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to withdraw abstract'
            ]);
        }
    }

    /**
     * Download abstract file
     */
    public function downloadFile($abstractId)
    {
        $userId = $this->session->get('user_id');
        
        $abstract = $this->abstractModel
            ->where('id', $abstractId)
            ->where('user_id', $userId)
            ->first();

        if (!$abstract || !$abstract['file_path']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $abstract['file_path'];
        
        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        // Force download
        return $this->response->download($filePath, null);
    }

    /**
     * Get categories for event via AJAX
     */
    public function getEventCategories($eventId)
    {
        $categories = $this->categoryModel
            ->where('event_id', $eventId)
            ->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->response->setJSON($categories);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Handle abstract file upload
     */
    private function handleAbstractFileUpload($userId)
    {
        $file = $this->request->getFile('abstract_file');

        if (!$file || !$file->isValid()) {
            return null;
        }

        // Create upload directory
        $uploadPath = WRITEPATH . 'uploads/abstracts/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $fileName = 'abstract_' . $userId . '_' . time() . '.' . $file->getExtension();

        // Move file
        if ($file->move($uploadPath, $fileName)) {
            return 'abstracts/' . $fileName;
        }

        return null;
    }

    /**
     * Send submission confirmation email
     */
    private function sendSubmissionConfirmation($abstractId)
    {
        $abstract = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, users.full_name, users.email')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('users', 'users.id = abstracts.user_id')
            ->where('abstracts.id', $abstractId)
            ->first();

        if (!$abstract) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>Abstract Submission Confirmed</h2>
            <p>Hello {$abstract['full_name']},</p>
            <p>Your abstract submission has been received successfully!</p>
            
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #28a745;'>
                <h3>Submission Details:</h3>
                <p><strong>Title:</strong> {$abstract['title']}</p>
                <p><strong>Event:</strong> {$abstract['event_title']}</p>
                <p><strong>Submission ID:</strong> {$abstract['id']}</p>
                <p><strong>Status:</strong> Pending Review</p>
                <p><strong>Submitted:</strong> {$abstract['created_at']}</p>
            </div>

            <p>Your abstract is now under review. You will be notified via email about the review status.</p>
            <p>You can track the status of your submission in your dashboard.</p>
            
            <p><a href='" . base_url("presenter/abstracts/{$abstractId}") . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Submission</a></p>
            
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($abstract['email']);
        $emailService->setSubject('Abstract Submission Confirmed - ' . $abstract['event_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send submission confirmation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send update notification email
     */
    private function sendUpdateNotification($abstractId)
    {
        $abstract = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, users.full_name, users.email')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('users', 'users.id = abstracts.user_id')
            ->where('abstracts.id', $abstractId)
            ->first();

        if (!$abstract) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>Abstract Updated</h2>
            <p>Hello {$abstract['full_name']},</p>
            <p>Your abstract has been updated successfully and is now pending review again.</p>
            
            <div style='margin: 20px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #007bff;'>
                <p><strong>Title:</strong> {$abstract['title']}</p>
                <p><strong>Event:</strong> {$abstract['event_title']}</p>
                <p><strong>Updated:</strong> " . date('Y-m-d H:i:s') . "</p>
            </div>

            <p><a href='" . base_url("presenter/abstracts/{$abstractId}") . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Abstract</a></p>
            
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($abstract['email']);
        $emailService->setSubject('Abstract Updated - ' . $abstract['event_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send update notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send withdrawal notification
     */
    private function sendWithdrawalNotification($abstractId)
    {
        $abstract = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, users.full_name, users.email')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('users', 'users.id = abstracts.user_id')
            ->where('abstracts.id', $abstractId)
            ->first();

        if (!$abstract) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>Abstract Withdrawn</h2>
            <p>Hello {$abstract['full_name']},</p>
            <p>Your abstract submission has been withdrawn as requested.</p>
            
            <div style='margin: 20px 0; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>
                <p><strong>Title:</strong> {$abstract['title']}</p>
                <p><strong>Event:</strong> {$abstract['event_title']}</p>
                <p><strong>Withdrawn:</strong> " . date('Y-m-d H:i:s') . "</p>
            </div>

            <p>If you wish to submit a new abstract for this event, please check if the submission deadline has not passed.</p>
            
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($abstract['email']);
        $emailService->setSubject('Abstract Withdrawn - ' . $abstract['event_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send withdrawal notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify admin about new submission
     */
    private function notifyAdminNewSubmission($abstractId)
    {
        // Get admin users
        $admins = $this->userModel
            ->where('role', 'admin')
            ->where('is_active', true)
            ->findAll();

        if (empty($admins)) {
            return false;
        }

        $abstract = $this->abstractModel
            ->select('abstracts.*, events.title as event_title, users.full_name as author_name')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('users', 'users.id = abstracts.user_id')
            ->where('abstracts.id', $abstractId)
            ->first();

        if (!$abstract) {
            return false;
        }

        $emailService = \Config\Services::email();

        $message = "
            <h2>New Abstract Submission</h2>
            <p>A new abstract has been submitted and is awaiting review.</p>
            
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;'>
                <h3>Submission Details:</h3>
                <p><strong>Title:</strong> {$abstract['title']}</p>
                <p><strong>Author:</strong> {$abstract['author_name']}</p>
                <p><strong>Event:</strong> {$abstract['event_title']}</p>
                <p><strong>Submitted:</strong> {$abstract['created_at']}</p>
            </div>

            <p><a href='" . base_url("admin/abstracts/{$abstractId}") . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Review Abstract</a></p>
            
            <br>
            <p>SNIA Conference System</p>
        ";

        foreach ($admins as $admin) {
            $emailService->setTo($admin['email']);
            $emailService->setSubject('New Abstract Submission - ' . $abstract['event_title']);
            $emailService->setMessage($message);

            try {
                $emailService->send();
            } catch (\Exception $e) {
                log_message('error', 'Failed to send admin notification: ' . $e->getMessage());
            }
        }

        return true;
    }
}