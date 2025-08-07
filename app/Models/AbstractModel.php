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
        'assigned_reviewer_id'
    ];

    protected array $casts = [
        'can_resubmit' => 'boolean'
    ];

    protected $useTimestamps = false;

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
}
