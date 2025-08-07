<?php

namespace App\Models;
use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'abstract_id', 'reviewer_id', 'review_status', 'comments', 
        'revision_notes', 'loa_file_path'
    ];

    protected $useTimestamps = false;

    public function getReviewWithDetails(int $reviewId)
    {
        return $this->select('reviews.*, abstracts.title as abstract_title,
                             users.first_name as reviewer_name')
                    ->join('abstracts', 'abstracts.id = reviews.abstract_id')
                    ->join('users', 'users.id = reviews.reviewer_id')
                    ->where('reviews.id', $reviewId)
                    ->first();
    }

    public function getReviewByAbstract(int $abstractId)
    {
        return $this->where('abstract_id', $abstractId)->first();
    }

    public function getReviewsByReviewer(int $reviewerId)
    {
        return $this->select('reviews.*, abstracts.title, abstracts.submitted_at')
                    ->join('abstracts', 'abstracts.id = reviews.abstract_id')
                    ->where('reviews.reviewer_id', $reviewerId)
                    ->orderBy('reviews.reviewed_at', 'DESC')
                    ->findAll();
    }
}
