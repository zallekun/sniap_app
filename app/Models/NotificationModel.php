<?php

namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'type', 'subject', 'message', 'is_read'
    ];

    protected array $casts = [
        'is_read' => 'boolean'
    ];

    protected $useTimestamps = false;

    public function getUserNotifications(int $userId, bool $unreadOnly = false)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', false);
        }
        
        return $builder->orderBy('sent_at', 'DESC')->findAll();
    }

    public function markAsRead(int $notificationId): bool
    {
        return $this->update($notificationId, ['is_read' => true]);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', false)
                    ->countAllResults();
    }

    public function createNotification(array $data): int|false
    {
        return $this->insert($data);
    }
}
