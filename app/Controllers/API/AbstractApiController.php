<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AbstractApiController extends BaseController
{
    /**
     * Get user's submitted abstracts - FIXED SCHEMA
     * GET /api/v1/abstracts
     */
    public function index()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            // Only presenters can access abstracts
            if ($user['role'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only presenters can access abstracts'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get abstracts with ONLY EXISTING COLUMNS
            $abstractsQuery = $db->query("
                SELECT DISTINCT
                    a.id,
                    a.registration_id,
                    a.first_name,
                    a.last_name,
                    a.email,
                    a.affiliation,
                    a.category_id,
                    a.title,
                    a.abstract_text,
                    a.keywords,
                    a.file_path,
                    a.submission_version,
                    a.review_status,
                    a.final_status,
                    a.can_resubmit,
                    a.assigned_reviewer_id,
                    a.submitted_at,
                    a.updated_at,
                    e.title as event_title,
                    e.event_date,
                    r.registration_status,
                    r.payment_status
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                JOIN events e ON r.event_id = e.id
                WHERE r.user_id = ?
                ORDER BY a.submitted_at DESC
            ", [$user['id']]);
            
            $abstracts = $abstractsQuery->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total_items' => count($abstracts),
                    'total_pages' => 1,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get abstracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Submit a new abstract - FIXED SCHEMA VERSION
     * POST /api/v1/abstracts
     */
    public function create()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            // Only presenters can submit abstracts
            if ($user['role'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only presenters can submit abstracts',
                    'user_role' => $user['role']
                ]);
            }
            
            // Get JSON data
            $data = $this->request->getJSON(true) ?? [];
            
            // Basic validation
            if (empty($data['registration_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ]);
            }
            
            if (empty($data['title'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Title is required'
                ]);
            }
            
            if (empty($data['abstract_text'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract text is required'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Check if registration exists and belongs to user
            $regQuery = $db->query("
                SELECT r.*, e.title as event_title, e.abstract_deadline
                FROM registrations r
                JOIN events e ON r.event_id = e.id
                WHERE r.id = ? AND r.user_id = ?
            ", [$data['registration_id'], $user['id']]);
            
            $registration = $regQuery->getRowArray();
            
            if (!$registration) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Registration not found or unauthorized',
                    'debug' => [
                        'registration_id' => $data['registration_id'],
                        'user_id' => $user['id']
                    ]
                ]);
            }
            
            // Check if user is registered as presenter
            if ($registration['registration_type'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'You must register as presenter to submit abstracts',
                    'current_registration_type' => $registration['registration_type'],
                    'registration_id' => $data['registration_id']
                ]);
            }
            
            // Check if abstract already exists
            $existingQuery = $db->query("
                SELECT id FROM abstracts WHERE registration_id = ?
            ", [$data['registration_id']]);
            
            if ($existingQuery->getNumRows() > 0) {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract already submitted for this registration'
                ]);
            }
            
            // Get user info for abstract
            $userQuery = $db->query("
                SELECT first_name, last_name, email, institution
                FROM users 
                WHERE id = ?
            ", [$user['id']]);
            
            $userInfo = $userQuery->getRowArray();
            
            // Use default category_id = 1 (assume it exists)
            $categoryId = isset($data['category_id']) ? (int)$data['category_id'] : 1;
            
            // Insert abstract with VALID category_id
            $insertQuery = $db->query("
                INSERT INTO abstracts (
                    registration_id, first_name, last_name, email, affiliation,
                    category_id, title, abstract_text, keywords, file_path,
                    submission_version, review_status, final_status, can_resubmit,
                    submitted_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                RETURNING id
            ", [
                (int)$data['registration_id'],
                $userInfo['first_name'],
                $userInfo['last_name'], 
                $userInfo['email'],
                $userInfo['institution'] ?? '', // affiliation
                $categoryId, // valid category_id
                $data['title'],
                $data['abstract_text'],
                $data['keywords'] ?? '',
                'abstracts/placeholder_' . time() . '.txt', // file_path placeholder
                1, // submission_version
                'pending', // review_status
                'pending', // final_status
                true // can_resubmit
            ]);
            
            if ($insertQuery) {
                $result = $insertQuery->getRowArray();
                $abstractId = $result['id'];
                
                return $this->response->setStatusCode(201)->setJSON([
                    'status' => 'success',
                    'message' => 'Abstract submitted successfully',
                    'data' => [
                        'abstract_id' => (int)$abstractId,
                        'registration_id' => (int)$data['registration_id'],
                        'event_title' => $registration['event_title'],
                        'title' => $data['title'],
                        'status' => 'submitted',
                        'submitted_at' => date('Y-m-d H:i:s'),
                        'next_steps' => 'Your abstract will be reviewed. You will receive notification about the review status.'
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to insert abstract'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit abstract: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Get abstract categories - SIMPLIFIED
     * GET /api/v1/abstracts/categories
     */
    public function categories()
    {
        try {
            // Return dummy categories for now
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    ['id' => 1, 'name' => 'Artificial Intelligence', 'description' => 'AI and Machine Learning'],
                    ['id' => 2, 'name' => 'Software Engineering', 'description' => 'Software Development'],
                    ['id' => 3, 'name' => 'Data Science', 'description' => 'Data Analysis and Big Data'],
                    ['id' => 4, 'name' => 'Cybersecurity', 'description' => 'Information Security'],
                    ['id' => 5, 'name' => 'Human-Computer Interaction', 'description' => 'HCI and UX/UI'],
                    ['id' => 6, 'name' => 'Computer Networks', 'description' => 'Networking and Communications']
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get categories: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get abstract details - SIMPLIFIED
     * GET /api/v1/abstracts/{id}
     */
    public function show($abstractId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get abstract with complete details
            $abstractQuery = $db->query("
                SELECT DISTINCT
                    a.*,
                    e.title as event_title,
                    e.event_date,
                    r.registration_status,
                    r.payment_status,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                JOIN events e ON r.event_id = e.id
                JOIN users u ON r.user_id = u.id
                WHERE a.id = ?
            ", [$abstractId]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            // Check authorization (presenter can see own, admin can see all)
            $canView = false;
            
            if ($user['role'] === 'presenter') {
                // Presenters can only see their own abstracts
                $checkOwnerQuery = $db->query("
                    SELECT 1 FROM registrations r 
                    WHERE r.id = ? AND r.user_id = ?
                ", [$abstract['registration_id'], $user['id']]);
                $canView = $checkOwnerQuery->getNumRows() > 0;
            } elseif ($user['role'] === 'admin' || $user['role'] === 'reviewer') {
                // Admins and reviewers can see all abstracts
                $canView = true;
            }
            
            if (!$canView) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized to view this abstract'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstract
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get abstract: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update abstract - SIMPLIFIED
     * PUT /api/v1/abstracts/{id}
     */
    public function update($abstractId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            // Only presenters can update abstracts
            if ($user['role'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only presenters can update abstracts'
                ]);
            }
            
            $data = $this->request->getJSON(true) ?? [];
            
            $db = \Config\Database::connect();
            
            // Verify abstract exists and belongs to user
            $abstractQuery = $db->query("
                SELECT a.*, r.user_id
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                WHERE a.id = ? AND r.user_id = ?
            ", [$abstractId, $user['id']]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found or unauthorized'
                ]);
            }
            
            // Build update data
            $updateFields = [];
            $updateValues = [];
            
            if (isset($data['title'])) {
                $updateFields[] = 'title = ?';
                $updateValues[] = $data['title'];
            }
            
            if (isset($data['abstract_text'])) {
                $updateFields[] = 'abstract_text = ?';
                $updateValues[] = $data['abstract_text'];
            }
            
            if (isset($data['keywords'])) {
                $updateFields[] = 'keywords = ?';
                $updateValues[] = $data['keywords'];
            }
            
            if (isset($data['category_id'])) {
                $updateFields[] = 'category_id = ?';
                $updateValues[] = (int)$data['category_id'];
            }
            
            if (empty($updateFields)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No valid fields to update'
                ]);
            }
            
            $updateFields[] = 'updated_at = NOW()';
            $updateValues[] = $abstractId;
            
            // Update abstract
            $updateQuery = $db->query("
                UPDATE abstracts 
                SET " . implode(', ', $updateFields) . "
                WHERE id = ?
            ", $updateValues);
            
            if ($updateQuery) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Abstract updated successfully',
                    'data' => [
                        'abstract_id' => (int)$abstractId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update abstract'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to update abstract: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get presenter statistics - SIMPLIFIED
     * GET /api/v1/abstracts/stats
     */
    public function stats()
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            // Only presenters can get abstract stats
            if ($user['role'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only presenters can access abstract statistics'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get presenter abstract statistics
            $statsQuery = $db->query("
                SELECT 
                    COUNT(*) as total_abstracts
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                WHERE r.user_id = ?
            ", [$user['id']]);
            
            $stats = $statsQuery->getRowArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'total_abstracts' => (int)$stats['total_abstracts'],
                    'pending_review' => 0, // Placeholder
                    'accepted' => 0,       // Placeholder
                    'rejected' => 0        // Placeholder
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get abstract statistics: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get reviews for an abstract (Presenter can see feedback)
     * GET /api/v1/abstracts/{id}/reviews
     */
    public function getReviews($abstractId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Verify abstract belongs to user (presenter)
            $abstractQuery = $db->query("
                SELECT a.*, r.user_id
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                WHERE a.id = ?
            ", [$abstractId]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            // Check authorization - presenter can see own reviews, admin can see all
            $canView = false;
            if ($user['role'] === 'presenter' && $abstract['user_id'] == $user['id']) {
                $canView = true;
            } elseif ($user['role'] === 'admin') {
                $canView = true;
            }
            
            if (!$canView) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized to view reviews for this abstract'
                ]);
            }
            
            // Get all reviews for this abstract
            $reviewsQuery = $db->query("
                SELECT DISTINCT
                    rv.*,
                    u.first_name as reviewer_first_name,
                    u.last_name as reviewer_last_name
                FROM reviews rv
                LEFT JOIN users u ON rv.reviewer_id = u.id
                WHERE rv.abstract_id = ?
                ORDER BY rv.reviewed_at DESC
            ", [$abstractId]);
            
            $reviews = $reviewsQuery->getResultArray();
            
            // Get abstract current status
            $abstractStatus = [
                'id' => $abstract['id'],
                'title' => $abstract['title'],
                'review_status' => $abstract['review_status'],
                'final_status' => $abstract['final_status'],
                'can_resubmit' => (bool)$abstract['can_resubmit'],
                'submission_version' => $abstract['submission_version']
            ];
            
            // Determine next steps for presenter
            $nextSteps = '';
            switch ($abstract['review_status']) {
                case 'pending':
                    $nextSteps = 'Your abstract is under review. Please wait for reviewer feedback.';
                    break;
                case 'accepted':
                    $nextSteps = 'Congratulations! Your abstract has been accepted. You can now proceed to payment.';
                    break;
                case 'accepted_with_revision':
                    $nextSteps = 'Your abstract needs revision. Please review the feedback below and submit a revised version.';
                    break;
                case 'rejected':
                    if ($abstract['can_resubmit']) {
                        $nextSteps = 'Your abstract was not accepted. You may resubmit with significant changes.';
                    } else {
                        $nextSteps = 'Your abstract was not accepted and resubmission is not allowed.';
                    }
                    break;
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'abstract' => $abstractStatus,
                    'reviews' => $reviews,
                    'review_summary' => [
                        'total_reviews' => count($reviews),
                        'current_status' => $abstract['review_status'],
                        'final_status' => $abstract['final_status'],
                        'can_revise' => $abstract['review_status'] === 'accepted_with_revision',
                        'can_proceed_to_payment' => $abstract['final_status'] === 'final_accepted'
                    ],
                    'next_steps' => $nextSteps
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get reviews: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Submit revision for abstract (after reviewer feedback)
     * POST /api/v1/abstracts/{id}/revision
     */
    public function submitRevision($abstractId)
    {
        try {
            // Get authenticated user
            $request = service('request');
            $user = $request->api_user ?? null;
            
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }
            
            // Only presenters can submit revisions
            if ($user['role'] !== 'presenter') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only presenters can submit revisions'
                ]);
            }
            
            $data = $this->request->getJSON(true) ?? [];
            
            $db = \Config\Database::connect();
            
            // Verify abstract belongs to user and needs revision
            $abstractQuery = $db->query("
                SELECT a.*, r.user_id
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                WHERE a.id = ? AND r.user_id = ?
            ", [$abstractId, $user['id']]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found or unauthorized'
                ]);
            }
            
            // Check if revision is allowed
            if ($abstract['review_status'] !== 'accepted_with_revision') {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract is not in revision status',
                    'current_status' => $abstract['review_status']
                ]);
            }
            
            // Build update data
            $updateFields = [];
            $updateValues = [];
            
            if (isset($data['title'])) {
                $updateFields[] = 'title = ?';
                $updateValues[] = $data['title'];
            }
            
            if (isset($data['abstract_text'])) {
                $updateFields[] = 'abstract_text = ?';
                $updateValues[] = $data['abstract_text'];
            }
            
            if (isset($data['keywords'])) {
                $updateFields[] = 'keywords = ?';
                $updateValues[] = $data['keywords'];
            }
            
            if (empty($updateFields)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No revision data provided'
                ]);
            }
            
            // Update submission version and reset review status
            $updateFields[] = 'submission_version = submission_version + 1';
            $updateFields[] = 'review_status = ?';
            $updateValues[] = 'pending';
            $updateFields[] = 'final_status = ?';
            $updateValues[] = 'pending';
            $updateFields[] = 'updated_at = NOW()';
            $updateValues[] = $abstractId;
            
            // Update abstract
            $updateQuery = $db->query("
                UPDATE abstracts 
                SET " . implode(', ', $updateFields) . "
                WHERE id = ?
            ", $updateValues);
            
            if ($updateQuery) {
                // Get updated abstract info
                $updatedQuery = $db->query("
                    SELECT submission_version, review_status, final_status, updated_at
                    FROM abstracts 
                    WHERE id = ?
                ", [$abstractId]);
                
                $updated = $updatedQuery->getRowArray();
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Revision submitted successfully',
                    'data' => [
                        'abstract_id' => (int)$abstractId,
                        'submission_version' => (int)$updated['submission_version'],
                        'review_status' => $updated['review_status'],
                        'final_status' => $updated['final_status'],
                        'updated_at' => $updated['updated_at'],
                        'next_steps' => 'Your revision has been submitted and is now under review again.'
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to submit revision'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit revision: ' . $e->getMessage()
            ]);
        }
    }
}