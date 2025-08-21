<?php

namespace App\Controllers\Reviewer;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class ReviewerController extends BaseController
{
    protected $userModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is reviewer
     */
    protected function checkReviewerAccess()
    {
        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('user_role');
        
        if (!$userId || $userRole !== 'reviewer') {
            return redirect()->to('/login')->with('error', 'Access denied. Reviewer privileges required.');
        }
        
        return false;
    }

    /**
     * Reviewer Dashboard - Main Overview
     */
    public function dashboard()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Reviewer Dashboard - SNIA Conference',
                'user' => $user,
                'stats' => $this->getDashboardStats($userId),
                'assigned_abstracts' => $this->getAssignedAbstracts($userId)
            ];
            
            return view('roles/reviewer/dashboard_clean', $data);
        } catch (\Exception $e) {
            log_message('error', 'Reviewer dashboard error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Failed to load reviewer dashboard');
        }
    }

    /**
     * Assigned Abstracts - Abstracts assigned for review
     */
    public function assigned()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Assigned Abstracts - Reviewer',
                'user' => $user,
                'assigned_abstracts' => $this->getAssignedAbstracts($userId)
            ];
            
            return view('roles/reviewer/assigned', $data);
        } catch (\Exception $e) {
            log_message('error', 'Reviewer assigned abstracts error: ' . $e->getMessage());
            return redirect()->to('/reviewer/dashboard')->with('error', 'Failed to load assigned abstracts');
        }
    }

    /**
     * Review History - All completed reviews
     */
    public function reviews()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $redirect;
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            $data = [
                'title' => 'Review History - Reviewer',
                'user' => $user,
                'completed_reviews' => $this->getCompletedReviews($userId)
            ];
            
            return view('roles/reviewer/reviews', $data);
        } catch (\Exception $e) {
            log_message('error', 'Reviewer reviews error: ' . $e->getMessage());
            return redirect()->to('/reviewer/dashboard')->with('error', 'Failed to load review history');
        }
    }

    /**
     * Review Form - Form to submit a review
     */
    public function review($abstractId = null)
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $redirect;
        
        if (!$abstractId) {
            return redirect()->to('/reviewer/assigned')->with('error', 'Abstract ID required');
        }
        
        try {
            $userId = $this->session->get('user_id');
            $user = $this->userModel->find($userId);
            
            // Check if reviewer is assigned to this abstract
            $assignment = $this->checkAbstractAssignment($abstractId, $userId);
            if (!$assignment) {
                return redirect()->to('/reviewer/assigned')->with('error', 'You are not assigned to review this abstract');
            }
            
            $data = [
                'title' => 'Review Abstract - Reviewer',
                'user' => $user,
                'abstract' => $this->getAbstractDetails($abstractId),
                'existing_review' => $this->getExistingReview($abstractId, $userId)
            ];
            
            return view('roles/reviewer/review_form', $data);
        } catch (\Exception $e) {
            log_message('error', 'Reviewer review form error: ' . $e->getMessage());
            return redirect()->to('/reviewer/assigned')->with('error', 'Failed to load review form');
        }
    }

    /**
     * Submit Review - Handle review form submission
     */
    public function submitReview()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $redirect;
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/reviewer/assigned');
        }
        
        try {
            $userId = $this->session->get('user_id');
            $abstractId = $this->request->getPost('abstract_id');
            
            // Validate assignment
            if (!$this->checkAbstractAssignment($abstractId, $userId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You are not assigned to review this abstract'
                ]);
            }
            
            $reviewData = [
                'abstract_id' => $abstractId,
                'reviewer_id' => $userId,
                'score' => $this->request->getPost('score'),
                'comments' => $this->request->getPost('comments'),
                'recommendation' => $this->request->getPost('recommendation'),
                'reviewed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Check if review already exists
            $existingReview = $this->getExistingReview($abstractId, $userId);
            
            $db = \Config\Database::connect();
            
            if ($existingReview) {
                // Update existing review
                $result = $db->table('reviews')
                    ->where('abstract_id', $abstractId)
                    ->where('reviewer_id', $userId)
                    ->update($reviewData);
                $message = 'Review updated successfully';
            } else {
                // Create new review
                $reviewData['created_at'] = date('Y-m-d H:i:s');
                $result = $db->table('reviews')->insert($reviewData);
                $message = 'Review submitted successfully';
            }
            
            if ($result) {
                // Update abstract status if needed
                $this->updateAbstractStatus($abstractId);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $message
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to submit review'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Submit review error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to submit review'
            ]);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get reviewer dashboard statistics
     */
    private function getDashboardStats($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get assigned abstracts
            $assignedAbstracts = $db->table('abstract_reviewers ar')
                ->join('abstracts a', 'a.id = ar.abstract_id', 'inner')
                ->where('ar.reviewer_id', $userId)
                ->countAllResults();
            
            // Get completed reviews
            $completedReviews = $db->table('reviews')
                ->where('reviewer_id', $userId)
                ->countAllResults();
            
            // Get pending reviews (assigned but not reviewed)
            $pendingReviews = $db->table('abstract_reviewers ar')
                ->join('abstracts a', 'a.id = ar.abstract_id', 'inner')
                ->where('ar.reviewer_id', $userId)
                ->where('ar.abstract_id NOT IN (SELECT abstract_id FROM reviews WHERE reviewer_id = ' . $userId . ')', null, false)
                ->countAllResults();
            
            // Get reviews this month
            $monthlyReviews = $db->table('reviews')
                ->where('reviewer_id', $userId)
                ->where('reviewed_at >=', date('Y-m-01'))
                ->countAllResults();
            
            // Average score given
            $avgScoreResult = $db->table('reviews')
                ->where('reviewer_id', $userId)
                ->selectAvg('score', 'avg_score')
                ->get()
                ->getRow();
            
            $avgScore = $avgScoreResult ? round($avgScoreResult->avg_score, 1) : 0;
            
            $stats = [
                'total_assigned' => $assignedAbstracts,
                'completed_reviews' => $completedReviews,
                'pending_reviews' => $pendingReviews,
                'monthly_reviews' => $monthlyReviews,
                'average_score' => $avgScore,
                'completion_rate' => $assignedAbstracts > 0 ? round(($completedReviews / $assignedAbstracts) * 100, 1) : 0
            ];
            
            return $stats;
        } catch (\Exception $e) {
            log_message('error', 'Reviewer dashboard stats error: ' . $e->getMessage());
            return [
                'total_assigned' => 0,
                'completed_reviews' => 0,
                'pending_reviews' => 0,
                'monthly_reviews' => 0,
                'average_score' => 0,
                'completion_rate' => 0
            ];
        }
    }

    /**
     * Get abstracts assigned to reviewer
     */
    private function getAssignedAbstracts($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('abstract_reviewers ar')
                ->select('a.*, u.first_name, u.last_name, u.email, e.title as event_title, ac.name as category_name, 
                         r.score, r.recommendation, r.reviewed_at')
                ->join('abstracts a', 'a.id = ar.abstract_id', 'inner')
                ->join('users u', 'u.id = a.user_id', 'left')
                ->join('events e', 'e.id = a.event_id', 'left')
                ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                ->join('reviews r', 'r.abstract_id = a.id AND r.reviewer_id = ' . $userId, 'left')
                ->where('ar.reviewer_id', $userId)
                ->orderBy('ar.assigned_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get assigned abstracts error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get completed reviews by reviewer
     */
    private function getCompletedReviews($userId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('reviews r')
                ->select('r.*, a.title, a.abstract_text, u.first_name, u.last_name, e.title as event_title')
                ->join('abstracts a', 'a.id = r.abstract_id', 'inner')
                ->join('users u', 'u.id = a.user_id', 'left')
                ->join('events e', 'e.id = a.event_id', 'left')
                ->where('r.reviewer_id', $userId)
                ->orderBy('r.reviewed_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Get completed reviews error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if reviewer is assigned to abstract
     */
    private function checkAbstractAssignment($abstractId, $reviewerId)
    {
        try {
            $db = \Config\Database::connect();
            
            $assignment = $db->table('abstract_reviewers')
                ->where('abstract_id', $abstractId)
                ->where('reviewer_id', $reviewerId)
                ->get()
                ->getRowArray();
                
            return $assignment !== null;
        } catch (\Exception $e) {
            log_message('error', 'Check abstract assignment error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get abstract details for review
     */
    private function getAbstractDetails($abstractId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('abstracts a')
                ->select('a.*, u.first_name, u.last_name, u.email, u.institution, 
                         e.title as event_title, ac.name as category_name')
                ->join('users u', 'u.id = a.user_id', 'left')
                ->join('events e', 'e.id = a.event_id', 'left')
                ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                ->where('a.id', $abstractId)
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Get abstract details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get existing review if any
     */
    private function getExistingReview($abstractId, $reviewerId)
    {
        try {
            $db = \Config\Database::connect();
            
            return $db->table('reviews')
                ->where('abstract_id', $abstractId)
                ->where('reviewer_id', $reviewerId)
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', 'Get existing review error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update abstract status based on reviews
     */
    private function updateAbstractStatus($abstractId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Count total assigned reviewers for this abstract
            $totalReviewers = $db->table('abstract_reviewers')
                ->where('abstract_id', $abstractId)
                ->countAllResults();
            
            // Count completed reviews
            $completedReviews = $db->table('reviews')
                ->where('abstract_id', $abstractId)
                ->countAllResults();
            
            // If all reviews are completed, update abstract status
            if ($completedReviews >= $totalReviewers && $totalReviewers > 0) {
                // Get average recommendation
                $recommendations = $db->table('reviews')
                    ->select('recommendation')
                    ->where('abstract_id', $abstractId)
                    ->get()
                    ->getResultArray();
                
                $acceptCount = 0;
                $rejectCount = 0;
                
                foreach ($recommendations as $rec) {
                    if ($rec['recommendation'] === 'accept') {
                        $acceptCount++;
                    } else {
                        $rejectCount++;
                    }
                }
                
                // Determine final status
                $finalStatus = $acceptCount > $rejectCount ? 'accepted' : 'rejected';
                
                // Update abstract status
                $db->table('abstracts')
                    ->where('id', $abstractId)
                    ->update([
                        'status' => $finalStatus,
                        'reviewed_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Update abstract status error: ' . $e->getMessage());
        }
    }

    // ==================== API ENDPOINTS ====================

    /**
     * Get reviewer stats (API endpoint)
     */
    public function getStatsApi()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $stats = $this->getDashboardStats($userId);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get reviewer stats API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load reviewer statistics'
            ]);
        }
    }

    /**
     * Get assigned abstracts (API endpoint)
     */
    public function getAssignedApi()
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $status = $this->request->getGet('status'); // Filter by status if provided
            $abstracts = $this->getAssignedAbstracts($userId);
            
            // Filter by status if requested
            if ($status === 'pending') {
                $abstracts = array_filter($abstracts, function($abstract) {
                    return empty($abstract['reviewed_at']);
                });
            } elseif ($status === 'completed') {
                $abstracts = array_filter($abstracts, function($abstract) {
                    return !empty($abstract['reviewed_at']);
                });
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => array_values($abstracts) // Reindex array after filtering
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get assigned abstracts API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load assigned abstracts'
            ]);
        }
    }

    /**
     * Get abstract details (API endpoint)
     */
    public function getAbstractDetailsApi($abstractId)
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            
            // Check if reviewer is assigned to this abstract
            if (!$this->checkAbstractAssignment($abstractId, $userId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'You are not assigned to review this abstract'
                ]);
            }
            
            $abstract = $this->getAbstractDetails($abstractId);
            
            if (!$abstract) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'abstract' => $abstract
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get abstract details API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load abstract details'
            ]);
        }
    }

    /**
     * Get review details (API endpoint)
     */
    public function getReviewDetailsApi($reviewId)
    {
        $redirect = $this->checkReviewerAccess();
        if ($redirect) return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        
        try {
            $userId = $this->session->get('user_id');
            $db = \Config\Database::connect();
            
            // Get review with abstract details, ensuring the reviewer owns this review
            $review = $db->table('reviews r')
                ->select('r.*, a.title, a.abstract_text, u.first_name, u.last_name, e.title as event_title')
                ->join('abstracts a', 'a.id = r.abstract_id', 'inner')
                ->join('users u', 'u.id = a.user_id', 'left')
                ->join('events e', 'e.id = a.event_id', 'left')
                ->where('r.id', $reviewId)
                ->where('r.reviewer_id', $userId)
                ->get()
                ->getRowArray();
                
            if (!$review) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Review not found or access denied'
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'review' => $review
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get review details API error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to load review details'
            ]);
        }
    }
}