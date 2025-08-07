<?php

namespace App\Models;
use CodeIgniter\Model;

class AbstractCategoryModel extends Model
{
    protected $table = 'abstract_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'description', 'is_active'];

    protected array $casts = [
        'is_active' => 'boolean'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'description' => 'permit_empty|max_length[500]'
    ];

    public function getActiveCategories()
    {
        return $this->where('is_active', true)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function getCategoryWithAbstractCount(?int $categoryId = null)
    {
        $builder = $this->select('abstract_categories.*, COUNT(abstracts.id) as abstract_count')
                        ->join('abstracts', 'abstracts.category_id = abstract_categories.id', 'left')
                        ->groupBy('abstract_categories.id');
        
        if ($categoryId !== null) {
            $builder->where('abstract_categories.id', $categoryId);
            return $builder->first();
        }
        
        return $builder->findAll();
    }
}
