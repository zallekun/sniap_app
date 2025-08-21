<?php

namespace App\Models;
use CodeIgniter\Model;

class AbstractModel extends Model
{
    protected $table = 'abstracts';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'registration_id', 'first_name', 'last_name', 'email', 'affiliation',
        'category_id', 'title', 'abstract_text', 'keywords', 'file_path',
        'submission_version', 'review_status', 'final_status', 'can_resubmit',
        'assigned_reviewer_id', 'user_id', 'event_id', 'submitted_at', 'file_name',
        'revision_notes', 'revision_deadline', 'can_upload_again', 'revision_count',
        'max_revisions', 'revised_at', 'reviewer_notes'
    ];

    protected array $casts = [
        'can_resubmit' => 'boolean'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAbstractWithDetails(int $abstractId)
    {
        return $this->select('abstracts.*, abstract_categories.name as category_name,
                             registrations.event_id, events.title as event_title,
                             users.first_name as reviewer_first_name, users.last_name as reviewer_last_name')
                    ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id')
                    ->join('registrations', 'registrations.id = abstracts.registration_id')
                    ->join('events', 'events.id = registrations.event_id')
                    ->join('users', 'users.id = abstracts.assigned_reviewer_id', 'left')
                    ->where('abstracts.id', $abstractId)
                    ->first();
    }

    public function getAbstractsByReviewer(int $reviewerId)
    {
        return $this->select('abstracts.*, abstract_categories.name as category_name')
                    ->join('abstract_categories', 'abstract_categories.id = abstracts.category_id')
                    ->where('abstracts.assigned_reviewer_id', $reviewerId)
                    ->orderBy('abstracts.submitted_at', 'DESC')
                    ->findAll();
    }

    public function getAbstractsByStatus(string $status)
    {
        return $this->where('review_status', $status)
                    ->orderBy('submitted_at', 'ASC')
                    ->findAll();
    }

    public function assignReviewer(int $abstractId, int $reviewerId): bool
    {
        return $this->update($abstractId, ['assigned_reviewer_id' => $reviewerId]);
    }

    public function updateReviewStatus(int $abstractId, string $status): bool
    {
        return $this->update($abstractId, ['review_status' => $status]);
    }

    // ==================== PRESENTER METHODS ====================

    /**
     * Get abstracts by user ID for presenter
     */
    public function getUserAbstracts($userId, $limit = null)
    {
        $builder = $this->db->table('abstracts a')
                           ->select('a.*, e.title as event_title, ac.name as category_name')
                           ->join('registrations r', 'r.id = a.registration_id', 'inner')
                           ->join('events e', 'e.id = r.event_id', 'left')
                           ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                           ->where('r.user_id', $userId)
                           ->orderBy('a.submitted_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get abstract details for presenter
     */
    public function getAbstractDetailsForUser($abstractId, $userId)
    {
        return $this->db->table('abstracts a')
                       ->select('a.*, e.title as event_title, ac.name as category_name')
                       ->join('registrations r', 'r.id = a.registration_id', 'inner')
                       ->join('events e', 'e.id = r.event_id', 'left')
                       ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                       ->where('a.id', $abstractId)
                       ->where('r.user_id', $userId)
                       ->get()->getFirstRow('array');
    }

    /**
     * Get user abstract statistics for presenter dashboard
     */
    public function getUserStats($userId)
    {
        $builder = $this->db->table('abstracts a')
                           ->join('registrations r', 'r.id = a.registration_id', 'inner')
                           ->where('r.user_id', $userId);
        
        $total = $builder->countAllResults(false);
        
        $builder = $this->db->table('abstracts a')
                           ->join('registrations r', 'r.id = a.registration_id', 'inner')
                           ->where('r.user_id', $userId)
                           ->where('a.review_status', 'accepted');
        $accepted = $builder->countAllResults(false);
        
        $builder = $this->db->table('abstracts a')
                           ->join('registrations r', 'r.id = a.registration_id', 'inner')
                           ->where('r.user_id', $userId)
                           ->whereIn('a.review_status', ['pending']);
        $pending = $builder->countAllResults(false);
        
        $builder = $this->db->table('abstracts a')
                           ->join('registrations r', 'r.id = a.registration_id', 'inner')
                           ->where('r.user_id', $userId)
                           ->where('a.review_status', 'rejected');
        $rejected = $builder->countAllResults(false);

        return [
            'total_abstracts' => $total,
            'accepted_abstracts' => $accepted,
            'pending_abstracts' => $pending,
            'rejected_abstracts' => $rejected,
            'upcoming_presentations' => $accepted
        ];
    }

    /**
     * Submit new abstract
     */
    public function submitAbstract($data)
    {
        // Set defaults
        $data['submitted_at'] = date('Y-m-d H:i:s');
        $data['review_status'] = 'pending';
        
        return $this->insert($data);
    }

    /**
     * Check if user can edit abstract
     */
    public function canEdit($abstractId, $userId)
    {
        $abstract = $this->where('id', $abstractId)
                        ->where('user_id', $userId)
                        ->first();

        if (!$abstract) {
            return false;
        }

        // Can only edit if status is pending
        return $abstract['review_status'] === 'pending';
    }

    /**
     * Update abstract (only if pending)
     */
    public function updateAbstract($abstractId, $userId, $data)
    {
        if (!$this->canEdit($abstractId, $userId)) {
            return false;
        }

        return $this->where('id', $abstractId)
                   ->where('user_id', $userId)
                   ->set($data)
                   ->update();
    }

    // ==================== REVISION WORKFLOW METHODS ====================

    /**
     * Submit revision for abstract
     */
    public function submitRevision($abstractId, $data, $userId)
    {
        // Check if user can submit revision
        $abstract = $this->getAbstractDetailsForUser($abstractId, $userId);
        
        if (!$abstract || $abstract['review_status'] !== 'accepted_with_revision') {
            return false;
        }

        if (!$abstract['can_upload_again'] || $abstract['revision_count'] >= $abstract['max_revisions']) {
            return false;
        }

        // Update with revision data
        $updateData = [
            'title' => $data['title'] ?? $abstract['title'],
            'abstract_text' => $data['abstract_text'] ?? $abstract['abstract_text'],
            'keywords' => $data['keywords'] ?? $abstract['keywords'],
            'file_path' => $data['file_path'] ?? $abstract['file_path'],
            'file_name' => $data['file_name'] ?? $abstract['file_name'],
            'revision_count' => $abstract['revision_count'] + 1,
            'revised_at' => date('Y-m-d H:i:s'),
            'review_status' => 'pending' // Reset to pending for re-review
        ];

        return $this->update($abstractId, $updateData);
    }

    /**
     * Request revision from reviewer
     */
    public function requestRevision($abstractId, $revisionNotes, $deadline = null)
    {
        $data = [
            'review_status' => 'accepted_with_revision',
            'revision_notes' => $revisionNotes,
            'revision_deadline' => $deadline,
            'can_upload_again' => true
        ];

        return $this->update($abstractId, $data);
    }

    /**
     * Accept abstract final
     */
    public function acceptAbstract($abstractId, $reviewerNotes = null)
    {
        $data = [
            'review_status' => 'accepted',
            'final_status' => 'final_accepted',
            'can_upload_again' => false
        ];

        if ($reviewerNotes) {
            $data['reviewer_notes'] = $reviewerNotes;
        }

        return $this->update($abstractId, $data);
    }

    /**
     * Reject abstract final
     */
    public function rejectAbstract($abstractId, $reviewerNotes = null)
    {
        $data = [
            'review_status' => 'rejected',
            'final_status' => 'final_rejected',
            'can_upload_again' => false
        ];

        if ($reviewerNotes) {
            $data['reviewer_notes'] = $reviewerNotes;
        }

        return $this->update($abstractId, $data);
    }

    /**
     * Check if abstract can be revised
     */
    public function canRevise($abstractId, $userId)
    {
        $abstract = $this->getAbstractDetailsForUser($abstractId, $userId);
        
        if (!$abstract) return false;

        return $abstract['review_status'] === 'accepted_with_revision' 
               && $abstract['can_upload_again'] 
               && $abstract['revision_count'] < $abstract['max_revisions'];
    }

    /**
     * Get abstracts needing revision
     */
    public function getAbstractsNeedingRevision($userId)
    {
        return $this->db->table('abstracts a')
                       ->select('a.*, e.title as event_title, ac.name as category_name')
                       ->join('registrations r', 'r.id = a.registration_id', 'inner')
                       ->join('events e', 'e.id = r.event_id', 'left')
                       ->join('abstract_categories ac', 'ac.id = a.category_id', 'left')
                       ->where('r.user_id', $userId)
                       ->where('a.review_status', 'accepted_with_revision')
                       ->where('a.can_upload_again', true)
                       ->get()->getResultArray();
    }
}
