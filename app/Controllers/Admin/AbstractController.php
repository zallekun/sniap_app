<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbstractModel;
use App\Models\EventModel;
use App\Models\UserModel;
use App\Models\ReviewModel;
use App\Models\AbstractCategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class AbstractController extends BaseController
{
    protected $abstractModel;
    protected $eventModel;
    protected $userModel;
    protected $reviewModel;
    protected $categoryModel;
    protected $session;

    public function __construct()
    {
        $this->abstractModel = new AbstractModel();
        $this->eventModel = new EventModel();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->categoryModel = new AbstractCategoryModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display abstracts list
     */
    public function index()
    {
        $perPage = $this->request->getGet('per_page') ?? 20;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $event = $this->request->getGet('event');
        $category = $this->request->getGet('category');
        $sort = $this->request->getGet('sort') ?? 'created_at';
        $order = $this->request->getGet('order') ?? 'DESC';

        $builder = $this->abstractModel
            ->select('abstracts.*, users.full_name, users.email, events.title as event_title, abstract_categories.name as category_name')
            ->join('users', 'users.id = abstracts.user_id')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id', 'left');

        // Apply filters
        if ($search) {
            $builder = $builder->groupStart()
                ->like('abstracts.title', $search)
                ->orLike('abstracts.abstract_text', $search)
                ->orLike('users.full_name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        if ($status && $status !== 'all') {
            $builder = $builder->where('abstracts.status', $status);
        }

        if ($event && $event !== 'all') {
            $builder = $builder->where('abstracts.event_id', $event);
        }

        if ($category && $category !== 'all') {
            $builder = $builder->where('abstracts.category_id', $category);
        }

        $abstracts = $builder->orderBy($sort, $order)->paginate($perPage);
        $pager = $this->abstractModel->pager;

        // Get filter options
        $events = $this->eventModel->where('allow_abstract_submission', true)->findAll();
        $categories = $this->categoryModel->where('is_active', true)->findAll();

        // Get statistics
        $stats = [
            'total' => $this->abstractModel->countAll(),
            'pending' => $this->abstractModel->where('status', 'pending')->countAllResults(),
            'under_review' => $this->abstractModel->where('status', 'under_review')->countAllResults(),
            'accepted' => $this->abstractModel->where('status', 'accepted')->countAllResults(),
            'rejected' => $this->abstractModel->where('status', 'rejected')->countAllResults(),
            'needs_revision' => $this->abstractModel->where('status', 'needs_revision')->countAllResults(),
        ];

        $data = [
            'title' => 'Abstract Management - Admin',
            'abstracts' => $abstracts,
            'pager' => $pager,
            'events' => $events,
            'categories' => $categories,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'event' => $event,
                'category' => $category,
                'sort' => $sort,
                'order' => $order,
                'per_page' => $perPage
            ]
        ];

        return view('admin/abstracts/index', $data);
    }

    /**
     * Display abstract details
     */
    public function show($id)
    {
        $abstract = $this->abstractModel
            ->select('abstracts.*, users.full_name, users.email, users.phone, users.institution, 
                      events.title as event_title, abstract_categories.name as category_name')
            ->join('users', 'users.id = abstracts.user_id')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id', 'left')
            ->where('abstracts.id', $id)
            ->first();

        if (!$abstract) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Abstract not found');
        }

        // Get reviews for this abstract
        $reviews = $this->reviewModel
            ->select('reviews.*, users.full_name as reviewer_name')
            ->join('users', 'users.id = reviews.reviewer_id')
            ->where('reviews.abstract_id', $id)
            ->findAll();

        // Get available reviewers
        $availableReviewers = $this->userModel
            ->select('id, full_name, email')
            ->where('role', 'reviewer')
            ->where('is_active', true)
            ->whereNotIn('id', array_column($reviews, 'reviewer_id'))
            ->findAll();

        $data = [
            'title' => 'Abstract Details - ' . $abstract['title'],
            'abstract' => $abstract,
            'reviews' => $reviews,
            'available_reviewers' => $availableReviewers,
        ];

        return view('admin/abstracts/show', $data);
    }

    /**
     * Assign reviewer to abstract
     */
    public function assignReviewer()
    {
        $abstractId = $this->request->getPost('abstract_id');
        $reviewerId = $this->request->getPost('reviewer_id');

        if (!$abstractId || !$reviewerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Check if abstract exists
        $abstract = $this->abstractModel->find($abstractId);
        if (!$abstract) {
            return $this->response->setJSON(['success' => false, 'message' => 'Abstract not found']);
        }

        // Check if reviewer is valid
        $reviewer = $this->userModel->where('id', $reviewerId)->where('role', 'reviewer')->first();
        if (!$reviewer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid reviewer']);
        }

        // Check if reviewer is already assigned
        $existingReview = $this->reviewModel
            ->where('abstract_id', $abstractId)
            ->where('reviewer_id', $reviewerId)
            ->first();

        if ($existingReview) {
            return $this->response->setJSON(['success' => false, 'message' => 'Reviewer already assigned']);
        }

        try {
            // Create review assignment
            $this->reviewModel->insert([
                'abstract_id' => $abstractId,
                'reviewer_id' => $reviewerId,
                'status' => 'assigned',
                'assigned_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update abstract status
            $this->abstractModel->update($abstractId, ['status' => 'under_review']);

            // Send notification to reviewer
            $this->sendReviewerNotification($reviewer, $abstract);

            // Log admin action
            $this->logAdminAction('assign_reviewer', $abstractId, "Assigned reviewer {$reviewer['full_name']} to abstract: {$abstract['title']}");

            return $this->response->setJSON(['success' => true, 'message' => 'Reviewer assigned successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Reviewer assignment error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to assign reviewer']);
        }
    }

    /**
     * Remove reviewer from abstract
     */
    public function removeReviewer($reviewId)
    {
        $review = $this->reviewModel->find($reviewId);

        if (!$review) {
            return $this->response->setJSON(['success' => false, 'message' => 'Review not found']);
        }

        try {
            $this->reviewModel->delete($reviewId);

            // Check if abstract has other reviewers
            $remainingReviews = $this->reviewModel->where('abstract_id', $review['abstract_id'])->countAllResults();
            
            if ($remainingReviews == 0) {
                // No more reviewers, set status back to pending
                $this->abstractModel->update($review['abstract_id'], ['status' => 'pending']);
            }

            // Log admin action
            $this->logAdminAction('remove_reviewer', $review['abstract_id'], "Removed reviewer from abstract");

            return $this->response->setJSON(['success' => true, 'message' => 'Reviewer removed successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Reviewer removal error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove reviewer']);
        }
    }

    /**
     * Update abstract status
     */
    public function updateStatus($id)
    {
        $abstract = $this->abstractModel->find($id);

        if (!$abstract) {
            return $this->response->setJSON(['success' => false, 'message' => 'Abstract not found']);
        }

        $newStatus = $this->request->getPost('status');
        $adminComments = $this->request->getPost('admin_comments');

        $validStatuses = ['pending', 'under_review', 'accepted', 'rejected', 'needs_revision'];

        if (!in_array($newStatus, $validStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        try {
            $updateData = [
                'status' => $newStatus,
                'admin_comments' => $adminComments,
                'reviewed_at' => date('Y-m-d H:i:s')
            ];

            $this->abstractModel->update($id, $updateData);

            // Send notification to author
            $author = $this->userModel->find($abstract['user_id']);
            $this->sendStatusUpdateNotification($author, $abstract, $newStatus, $adminComments);

            // Log admin action
            $this->logAdminAction('update_abstract_status', $id, "Updated abstract status to: {$newStatus}");

            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Abstract status update error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Bulk assign reviewers
     */
    public function bulkAssignReviewers()
    {
        $abstractIds = $this->request->getPost('abstract_ids');
        $reviewerIds = $this->request->getPost('reviewer_ids');

        if (!$abstractIds || !$reviewerIds || !is_array($abstractIds) || !is_array($reviewerIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $assigned = 0;
        $failed = 0;

        foreach ($abstractIds as $abstractId) {
            foreach ($reviewerIds as $reviewerId) {
                // Check if reviewer is already assigned to this abstract
                $existing = $this->reviewModel
                    ->where('abstract_id', $abstractId)
                    ->where('reviewer_id', $reviewerId)
                    ->first();

                if (!$existing) {
                    try {
                        $this->reviewModel->insert([
                            'abstract_id' => $abstractId,
                            'reviewer_id' => $reviewerId,
                            'status' => 'assigned',
                            'assigned_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        // Update abstract status
                        $this->abstractModel->update($abstractId, ['status' => 'under_review']);
                        $assigned++;

                    } catch (\Exception $e) {
                        $failed++;
                        log_message('error', 'Bulk reviewer assignment error: ' . $e->getMessage());
                    }
                }
            }
        }

        // Log admin action
        $this->logAdminAction('bulk_assign_reviewers', null, "Bulk assigned reviewers: {$assigned} assignments, {$failed} failed");

        return $this->response->setJSON([
            'success' => true,
            'message' => "Assigned {$assigned} reviewers" . ($failed > 0 ? " ({$failed} failed)" : "")
        ]);
    }

    /**
     * Bulk status update
     */
    public function bulkUpdateStatus()
    {
        $abstractIds = $this->request->getPost('abstract_ids');
        $status = $this->request->getPost('status');
        $comments = $this->request->getPost('comments');

        if (!$abstractIds || !$status || !is_array($abstractIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $validStatuses = ['pending', 'under_review', 'accepted', 'rejected', 'needs_revision'];

        if (!in_array($status, $validStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        $updated = 0;

        foreach ($abstractIds as $id) {
            try {
                $this->abstractModel->update($id, [
                    'status' => $status,
                    'admin_comments' => $comments,
                    'reviewed_at' => date('Y-m-d H:i:s')
                ]);

                // Send notification to author
                $abstract = $this->abstractModel->find($id);
                $author = $this->userModel->find($abstract['user_id']);
                $this->sendStatusUpdateNotification($author, $abstract, $status, $comments);

                $updated++;

            } catch (\Exception $e) {
                log_message('error', 'Bulk status update error: ' . $e->getMessage());
            }
        }

        // Log admin action
        $this->logAdminAction('bulk_update_status', null, "Bulk updated {$updated} abstracts to status: {$status}");

        return $this->response->setJSON([
            'success' => true,
            'message' => "Updated {$updated} abstracts successfully"
        ]);
    }

    /**
     * Export abstracts
     */
    public function export($format = 'csv')
    {
        $abstracts = $this->abstractModel
            ->select('abstracts.*, users.full_name, users.email, users.institution, 
                      events.title as event_title, abstract_categories.name as category_name')
            ->join('users', 'users.id = abstracts.user_id')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id', 'left')
            ->orderBy('abstracts.created_at', 'DESC')
            ->findAll();

        if ($format === 'csv') {
            return $this->exportCSV($abstracts);
        } else {
            return $this->exportExcel($abstracts);
        }
    }

    /**
     * Get review statistics
     */
    public function getReviewStats()
    {
        $stats = [
            'total_abstracts' => $this->abstractModel->countAll(),
            'pending_review' => $this->abstractModel->where('status', 'pending')->countAllResults(),
            'under_review' => $this->abstractModel->where('status', 'under_review')->countAllResults(),
            'reviewed' => $this->abstractModel->whereIn('status', ['accepted', 'rejected', 'needs_revision'])->countAllResults(),
            'total_reviews' => $this->reviewModel->countAll(),
            'completed_reviews' => $this->reviewModel->where('status', 'completed')->countAllResults(),
            'pending_reviews' => $this->reviewModel->where('status', 'assigned')->countAllResults(),
        ];

        return $this->response->setJSON($stats);
    }

    /**
     * Get reviewer workload
     */
    public function getReviewerWorkload()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                u.id,
                u.full_name,
                u.email,
                COUNT(r.id) as total_assignments,
                COUNT(CASE WHEN r.status = 'completed' THEN 1 END) as completed_reviews,
                COUNT(CASE WHEN r.status = 'assigned' THEN 1 END) as pending_reviews,
                AVG(CASE WHEN r.status = 'completed' THEN 
                    DATEDIFF(r.reviewed_at, r.assigned_at) END) as avg_review_days
            FROM users u
            LEFT JOIN reviews r ON u.id = r.reviewer_id
            WHERE u.role = 'reviewer' AND u.is_active = true
            GROUP BY u.id, u.full_name, u.email
            ORDER BY total_assignments DESC
        ");

        return $this->response->setJSON($query->getResultArray());
    }

    /**
     * Send reminder to reviewers
     */
    public function sendReviewerReminder($reviewId)
    {
        $review = $this->reviewModel
            ->select('reviews.*, abstracts.title as abstract_title, users.full_name, users.email')
            ->join('abstracts', 'abstracts.id = reviews.abstract_id')
            ->join('users', 'users.id = reviews.reviewer_id')
            ->where('reviews.id', $reviewId)
            ->first();

        if (!$review) {
            return $this->response->setJSON(['success' => false, 'message' => 'Review not found']);
        }

        if ($review['status'] === 'completed') {
            return $this->response->setJSON(['success' => false, 'message' => 'Review already completed']);
        }

        // Send reminder email
        $sent = $this->sendReviewReminderEmail($review);

        if ($sent) {
            // Update review with reminder sent timestamp
            $this->reviewModel->update($reviewId, ['reminder_sent_at' => date('Y-m-d H:i:s')]);

            // Log admin action
            $this->logAdminAction('send_reviewer_reminder', $reviewId, "Sent reminder to reviewer: {$review['full_name']}");

            return $this->response->setJSON(['success' => true, 'message' => 'Reminder sent successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to send reminder']);
        }
    }

    /**
     * Generate review report
     */
    public function generateReviewReport($eventId = null)
    {
        $builder = $this->abstractModel
            ->select('abstracts.*, users.full_name as author_name, events.title as event_title,
                      COUNT(reviews.id) as review_count,
                      AVG(CASE WHEN reviews.recommendation = "accept" THEN 1 
                               WHEN reviews.recommendation = "reject" THEN 0 
                               ELSE 0.5 END) as acceptance_score')
            ->join('users', 'users.id = abstracts.user_id')
            ->join('events', 'events.id = abstracts.event_id')
            ->join('reviews', 'reviews.abstract_id = abstracts.id', 'left')
            ->groupBy('abstracts.id');

        if ($eventId) {
            $builder = $builder->where('abstracts.event_id', $eventId);
        }

        $report = $builder->findAll();

        return $this->response->setJSON($report);
    }

    /**
     * Delete abstract (admin only)
     */
    public function delete($id)
    {
        $abstract = $this->abstractModel->find($id);

        if (!$abstract) {
            return $this->response->setJSON(['success' => false, 'message' => 'Abstract not found']);
        }

        try {
            // Delete associated reviews first
            $this->reviewModel->where('abstract_id', $id)->delete();

            // Delete the abstract
            $this->abstractModel->delete($id);

            // Delete associated files
            if ($abstract['file_path']) {
                $fullPath = WRITEPATH . 'uploads/' . $abstract['file_path'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            // Log admin action
            $this->logAdminAction('delete_abstract', $id, "Deleted abstract: {$abstract['title']}");

            return $this->response->setJSON(['success' => true, 'message' => 'Abstract deleted successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Abstract deletion error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete abstract']);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Send notification to reviewer
     */
    private function sendReviewerNotification($reviewer, $abstract)
    {
        $emailService = \Config\Services::email();

        $message = "
            <h2>New Abstract Review Assignment</h2>
            <p>Hello {$reviewer['full_name']},</p>
            <p>You have been assigned to review the following abstract:</p>
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;'>
                <strong>Title:</strong> {$abstract['title']}<br>
                <strong>Submitted by:</strong> {$abstract['full_name']}<br>
                <strong>Submission Date:</strong> {$abstract['created_at']}
            </div>
            <p>Please log in to the system to access the full abstract and submit your review.</p>
            <p><a href='" . base_url('reviewer/dashboard') . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Access Review System</a></p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($reviewer['email']);
        $emailService->setSubject('New Abstract Review Assignment - SNIA Conference');
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send reviewer notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send status update notification to author
     */
    private function sendStatusUpdateNotification($author, $abstract, $status, $comments)
    {
        $emailService = \Config\Services::email();

        $statusMessages = [
            'accepted' => 'Congratulations! Your abstract has been accepted.',
            'rejected' => 'We regret to inform you that your abstract has not been accepted.',
            'needs_revision' => 'Your abstract requires revisions before final acceptance.',
            'under_review' => 'Your abstract is currently under review.',
            'pending' => 'Your abstract is pending review assignment.'
        ];

        $statusMessage = $statusMessages[$status] ?? 'Your abstract status has been updated.';

        $message = "
            <h2>Abstract Status Update</h2>
            <p>Hello {$author['full_name']},</p>
            <p>{$statusMessage}</p>
            <div style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;'>
                <strong>Abstract:</strong> {$abstract['title']}<br>
                <strong>New Status:</strong> " . ucwords(str_replace('_', ' ', $status)) . "<br>
                <strong>Updated:</strong> " . date('Y-m-d H:i:s') . "
            </div>";

        if ($comments) {
            $message .= "
            <div style='margin: 20px 0; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>
                <strong>Comments:</strong><br>
                {$comments}
            </div>";
        }

        $message .= "
            <p>You can view your abstract status and details by logging into your account.</p>
            <p><a href='" . base_url('presenter/abstracts') . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View My Abstracts</a></p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($author['email']);
        $emailService->setSubject('Abstract Status Update - ' . $abstract['title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send status update notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send review reminder email
     */
    private function sendReviewReminderEmail($review)
    {
        $emailService = \Config\Services::email();

        $daysAssigned = floor((time() - strtotime($review['assigned_at'])) / (60 * 60 * 24));

        $message = "
            <h2>Review Reminder</h2>
            <p>Hello {$review['full_name']},</p>
            <p>This is a friendly reminder that you have a pending abstract review.</p>
            <div style='margin: 20px 0; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>
                <strong>Abstract:</strong> {$review['abstract_title']}<br>
                <strong>Assigned:</strong> {$review['assigned_at']} ({$daysAssigned} days ago)<br>
                <strong>Status:</strong> Pending Review
            </div>
            <p>Please complete your review at your earliest convenience.</p>
            <p><a href='" . base_url('reviewer/reviews') . "' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Complete Review</a></p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($review['email']);
        $emailService->setSubject('Review Reminder - ' . $review['abstract_title']);
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send review reminder: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export abstracts as CSV
     */
    private function exportCSV($abstracts)
    {
        $filename = 'abstracts_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, [
            'ID', 'Title', 'Author', 'Email', 'Institution', 'Event', 'Category', 
            'Status', 'Submission Date', 'Review Count', 'File'
        ]);
        
        // Write data
        foreach ($abstracts as $abstract) {
            // Get review count
            $reviewCount = $this->reviewModel->where('abstract_id', $abstract['id'])->countAllResults();
            
            fputcsv($output, [
                $abstract['id'],
                $abstract['title'],
                $abstract['full_name'],
                $abstract['email'],
                $abstract['institution'],
                $abstract['event_title'],
                $abstract['category_name'],
                $abstract['status'],
                $abstract['created_at'],
                $reviewCount,
                $abstract['file_path'] ? 'Yes' : 'No'
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export abstracts as Excel (placeholder)
     */
    private function exportExcel($abstracts)
    {
        // For now, fallback to CSV
        return $this->exportCSV($abstracts);
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