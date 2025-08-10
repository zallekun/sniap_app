<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\EmailService;

class ReviewApiController extends BaseController
{
    /**
     * Get abstracts assigned to reviewer
     * GET /api/v1/reviewer/abstracts
     */
    public function getAssignedAbstracts()
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
            
            // Only reviewers can access this
            if ($user['role'] !== 'reviewer') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only reviewers can access assigned abstracts',
                    'user_role' => $user['role']
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get abstracts assigned to this reviewer - FIXED QUERY
            $abstractsQuery = $db->query("
                SELECT DISTINCT
                    a.id,
                    a.title,
                    a.abstract_text,
                    a.keywords,
                    a.first_name,
                    a.last_name,
                    a.email,
                    a.affiliation,
                    a.file_path,
                    a.review_status,
                    a.final_status,
                    a.submitted_at,
                    a.updated_at,
                    a.registration_id,
                    e.title as event_title,
                    e.event_date,
                    ac.name as category_name
                FROM abstracts a
                JOIN registrations r ON a.registration_id = r.id
                JOIN events e ON r.event_id = e.id
                LEFT JOIN abstract_categories ac ON a.category_id = ac.id
                WHERE a.assigned_reviewer_id = ?
                ORDER BY a.submitted_at ASC
            ", [$user['id']]);
            
            $abstracts = $abstractsQuery->getResultArray();
            
            // Get review status for each abstract - SIMPLIFIED VERSION
            foreach ($abstracts as &$abstract) {
                // First check if reviews table exists and what columns it has
                try {
                    $reviewQuery = $db->query("
                        SELECT * FROM reviews 
                        WHERE abstract_id = ? 
                        ORDER BY id DESC
                        LIMIT 1
                    ", [$abstract['id']]);
                    
                    $review = $reviewQuery->getRowArray();
                    $abstract['my_review'] = $review;
                    $abstract['review_completed'] = !empty($review);
                } catch (\Exception $e) {
                    // If reviews table doesn't exist or has issues, set defaults
                    $abstract['my_review'] = null;
                    $abstract['review_completed'] = false;
                }
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts,
                'meta' => [
                    'total_assigned' => count($abstracts),
                    'completed_reviews' => count(array_filter($abstracts, fn($a) => $a['review_completed'])),
                    'pending_reviews' => count(array_filter($abstracts, fn($a) => !$a['review_completed']))
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get assigned abstracts: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Submit a review for an abstract
     * POST /api/v1/reviews
     */
    public function submitReview()
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
            
            // Only reviewers can submit reviews
            if ($user['role'] !== 'reviewer') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only reviewers can submit reviews'
                ]);
            }
            
            $data = $this->request->getJSON(true) ?? [];
            
            // Validate required fields
            if (empty($data['abstract_id'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract ID is required'
                ]);
            }
            
            if (empty($data['decision'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Review decision is required'
                ]);
            }
            
            // Validate decision values (must match enum)
            $validDecisions = ['accepted', 'accepted_with_revision', 'rejected'];
            if (!in_array($data['decision'], $validDecisions)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid decision. Allowed: ' . implode(', ', $validDecisions)
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Verify abstract is assigned to this reviewer
            $abstractQuery = $db->query("
                SELECT id, title, assigned_reviewer_id, review_status
                FROM abstracts 
                WHERE id = ? AND assigned_reviewer_id = ?
            ", [$data['abstract_id'], $user['id']]);
            
            $abstract = $abstractQuery->getRowArray();
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found or not assigned to you'
                ]);
            }
            
            // Check if review already exists
            $existingReviewQuery = $db->query("
                SELECT id FROM reviews 
                WHERE abstract_id = ? AND reviewer_id = ?
            ", [$data['abstract_id'], $user['id']]);
            
            if ($existingReviewQuery->getNumRows() > 0) {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => 'error',
                    'message' => 'Review already submitted for this abstract. Use update instead.'
                ]);
            }
            
            // Insert review with ONLY EXISTING COLUMNS
            $reviewData = [
                'abstract_id' => (int)$data['abstract_id'],
                'reviewer_id' => (int)$user['id'],
                'review_status' => $data['decision'],
                'comments' => $data['comments'] ?? '',
                'reviewed_at' => date('Y-m-d H:i:s')
            ];
            
            $insertReviewQuery = $db->query("
                INSERT INTO reviews (
                    abstract_id, reviewer_id, review_status, comments, reviewed_at
                ) VALUES (?, ?, ?, ?, ?)
                RETURNING id
            ", [
                $reviewData['abstract_id'],
                $reviewData['reviewer_id'],
                $reviewData['review_status'],
                $reviewData['comments'],
                $reviewData['reviewed_at']
            ]);
            
            $reviewResult = $insertReviewQuery->getRowArray();
            $reviewId = $reviewResult['id'];
            
            // Update abstract status based on review decision
            $newReviewStatus = $data['decision']; // accepted, accepted_with_revision, rejected
            $newFinalStatus = ($data['decision'] === 'accepted') ? 'final_accepted' : 'pending';
            
            $updateAbstractQuery = $db->query("
                UPDATE abstracts 
                SET review_status = ?, final_status = ?, updated_at = NOW()
                WHERE id = ?
            ", [$newReviewStatus, $newFinalStatus, $data['abstract_id']]);
            
            if (!$updateAbstractQuery) {
    return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'Review submitted but failed to update abstract status'
    ]);
}
// 🚀 ADD EMAIL NOTIFICATION HERE:
try {
    // Get presenter info untuk email
    $presenterQuery = $db->query("
        SELECT u.first_name, u.last_name, u.email, a.title 
        FROM users u
        JOIN registrations r ON u.id = r.user_id  
        JOIN abstracts a ON r.id = a.registration_id
        WHERE a.id = ?
    ", [$data['abstract_id']]);
    
    $presenter = $presenterQuery->getRowArray();
    
    if ($presenter) {
        $emailService = new EmailService();
        $fullName = $presenter['first_name'] . ' ' . $presenter['last_name'];
        $abstractTitle = $presenter['title'];
        $status = strtoupper($data['decision']); // ACCEPTED, ACCEPTED_WITH_REVISION, REJECTED
        $comments = $data['comments'] ?? '';
        
        $emailResult = $emailService->sendReviewStatusNotification(
            $presenter['email'],
            $fullName,
            $abstractTitle,
            $status,
            $comments
        );
        
        // Log email result tapi jangan fail request kalau email gagal
        if ($emailResult['success']) {
            log_message('info', "Review notification email sent to: " . $presenter['email']);
        } else {
            log_message('error', "Failed to send review notification email: " . $emailResult['message']);
        }
    }
} catch (\Exception $emailException) {
    // Log error tapi jangan fail request
    log_message('error', 'Email notification error: ' . $emailException->getMessage());
}

            
            // Determine next steps for presenter
            $nextSteps = '';
            switch ($data['decision']) {
                case 'accepted':
                    $nextSteps = 'Your abstract has been accepted! You can now proceed to payment.';
                    break;
                case 'accepted_with_revision':
                    $nextSteps = 'Your abstract needs revision. Please check reviewer comments and submit a revised version.';
                    break;
                case 'rejected':
                    $nextSteps = 'Your abstract was not accepted. You may resubmit with significant changes if allowed.';
                    break;
            }
            
            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success',
                'message' => 'Review submitted successfully',
                'data' => [
                    'review_id' => (int)$reviewId,
                    'abstract_id' => (int)$data['abstract_id'],
                    'abstract_title' => $abstract['title'],
                    'decision' => $data['decision'],
                    'rating' => $reviewData['rating'],
                    'reviewed_at' => $reviewData['reviewed_at'],
                    'next_steps' => $nextSteps
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get review details
     * GET /api/v1/reviews/{id}
     */
    public function getReview($reviewId)
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
            
            // Get review with abstract details
            $reviewQuery = $db->query("
                SELECT DISTINCT
                    rv.*,
                    a.title as abstract_title,
                    a.first_name as author_first_name,
                    a.last_name as author_last_name,
                    a.affiliation as author_affiliation,
                    e.title as event_title,
                    u.first_name as reviewer_first_name,
                    u.last_name as reviewer_last_name
                FROM reviews rv
                JOIN abstracts a ON rv.abstract_id = a.id
                JOIN registrations r ON a.registration_id = r.id
                JOIN events e ON r.event_id = e.id
                JOIN users u ON rv.reviewer_id = u.id
                WHERE rv.id = ?
            ", [$reviewId]);
            
            $review = $reviewQuery->getRowArray();
            
            if (!$review) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Review not found'
                ]);
            }
            
            // Check authorization
            $canView = false;
            if ($user['role'] === 'reviewer' && $review['reviewer_id'] == $user['id']) {
                $canView = true; // Reviewer can see own reviews
            } elseif ($user['role'] === 'admin') {
                $canView = true; // Admin can see all reviews
            } elseif ($user['role'] === 'presenter') {
                // Presenter can see reviews of their abstracts
                $presenterCheck = $db->query("
                    SELECT 1 FROM registrations reg
                    JOIN abstracts abs ON reg.id = abs.registration_id
                    WHERE abs.id = ? AND reg.user_id = ?
                ", [$review['abstract_id'], $user['id']]);
                $canView = $presenterCheck->getNumRows() > 0;
            }
            
            if (!$canView) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized to view this review'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $review
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get review: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update a review
     * PUT /api/v1/reviews/{id}
     */
    public function updateReview($reviewId)
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
            
            // Only reviewers can update reviews
            if ($user['role'] !== 'reviewer') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only reviewers can update reviews'
                ]);
            }
            
            $data = $this->request->getJSON(true) ?? [];
            
            $db = \Config\Database::connect();
            
            // Verify review exists and belongs to this reviewer
            $reviewQuery = $db->query("
                SELECT rv.*, a.title as abstract_title
                FROM reviews rv
                JOIN abstracts a ON rv.abstract_id = a.id
                WHERE rv.id = ? AND rv.reviewer_id = ?
            ", [$reviewId, $user['id']]);
            
            $review = $reviewQuery->getRowArray();
            
            if (!$review) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Review not found or unauthorized'
                ]);
            }
            
            // Build update fields
            $updateFields = [];
            $updateValues = [];
            
            if (isset($data['decision'])) {
    $newFinalStatus = ($data['decision'] === 'accepted') ? 'final_accepted' : 'pending';
    $db->query("
        UPDATE abstracts 
        SET review_status = ?, final_status = ?, updated_at = NOW()
        WHERE id = ?
    ", [$data['decision'], $newFinalStatus, $review['abstract_id']]);
    
    // 🚀 ADD EMAIL NOTIFICATION FOR UPDATED REVIEW:
    try {
        // Get presenter info
        $presenterQuery = $db->query("
            SELECT u.first_name, u.last_name, u.email, a.title 
            FROM users u
            JOIN registrations r ON u.id = r.user_id  
            JOIN abstracts a ON r.id = a.registration_id
            WHERE a.id = ?
        ", [$review['abstract_id']]);
        
        $presenter = $presenterQuery->getRowArray();
        
        if ($presenter) {
            $emailService = new EmailService();
            $fullName = $presenter['first_name'] . ' ' . $presenter['last_name'];
            $abstractTitle = $presenter['title'];
            $status = strtoupper($data['decision']);
            $comments = $data['comments'] ?? '';
            
            $emailResult = $emailService->sendReviewStatusNotification(
                $presenter['email'],
                $fullName,
                $abstractTitle,
                $status,
                $comments
            );
            
            if ($emailResult['success']) {
                log_message('info', "Updated review notification email sent to: " . $presenter['email']);
            } else {
                log_message('error', "Failed to send updated review notification email: " . $emailResult['message']);
            }
        }
    } catch (\Exception $emailException) {
        log_message('error', 'Email notification error in update: ' . $emailException->getMessage());
    }
}
            
            if (isset($data['comments'])) {
                $updateFields[] = 'comments = ?';
                $updateValues[] = $data['comments'];
            }
            
            // Remove rating update since column doesn't exist
            
            if (empty($updateFields)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'No valid fields to update'
                ]);
            }
            
            $updateFields[] = 'reviewed_at = NOW()';
            $updateValues[] = $reviewId;
            
            // Update review
            $updateQuery = $db->query("
                UPDATE reviews 
                SET " . implode(', ', $updateFields) . "
                WHERE id = ?
            ", $updateValues);
            
            if ($updateQuery) {
                // If decision changed, update abstract status
                if (isset($data['decision'])) {
                    $newFinalStatus = ($data['decision'] === 'accepted') ? 'final_accepted' : 'pending';
                    $db->query("
                        UPDATE abstracts 
                        SET review_status = ?, final_status = ?, updated_at = NOW()
                        WHERE id = ?
                    ", [$data['decision'], $newFinalStatus, $review['abstract_id']]);
                }
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Review updated successfully',
                    'data' => [
                        'review_id' => (int)$reviewId,
                        'abstract_title' => $review['abstract_title'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to update review'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to update review: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get reviewer dashboard statistics
     * GET /api/v1/reviewer/dashboard
     */
    public function reviewerDashboard()
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
            
            // Only reviewers can access dashboard
            if ($user['role'] !== 'reviewer') {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Only reviewers can access dashboard'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Get reviewer statistics - REMOVE RATING COLUMN
            $statsQuery = $db->query("
                SELECT 
                    COUNT(DISTINCT a.id) as total_assigned,
                    COUNT(DISTINCT r.id) as total_reviewed,
                    COUNT(DISTINCT CASE WHEN r.review_status = 'accepted' THEN r.id END) as accepted_count,
                    COUNT(DISTINCT CASE WHEN r.review_status = 'accepted_with_revision' THEN r.id END) as revision_count,
                    COUNT(DISTINCT CASE WHEN r.review_status = 'rejected' THEN r.id END) as rejected_count
                FROM abstracts a
                LEFT JOIN reviews r ON a.id = r.abstract_id AND r.reviewer_id = ?
                WHERE a.assigned_reviewer_id = ?
            ", [$user['id'], $user['id']]);
            
            $stats = $statsQuery->getRowArray();
            
            // Get recent reviews - REMOVE RATING COLUMN  
            $recentReviewsQuery = $db->query("
                SELECT DISTINCT
                    r.id,
                    r.review_status,
                    r.reviewed_at,
                    a.title as abstract_title,
                    a.first_name,
                    a.last_name
                FROM reviews r
                JOIN abstracts a ON r.abstract_id = a.id
                WHERE r.reviewer_id = ?
                ORDER BY r.reviewed_at DESC
                LIMIT 5
            ", [$user['id']]);
            
            $recentReviews = $recentReviewsQuery->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'statistics' => [
                        'total_assigned' => (int)$stats['total_assigned'],
                        'total_reviewed' => (int)$stats['total_reviewed'],
                        'pending_reviews' => (int)$stats['total_assigned'] - (int)$stats['total_reviewed'],
                        'accepted_count' => (int)$stats['accepted_count'],
                        'revision_count' => (int)$stats['revision_count'],
                        'rejected_count' => (int)$stats['rejected_count']
                    ],
                    'recent_reviews' => $recentReviews
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to get dashboard: ' . $e->getMessage()
            ]);
        }
    }
}