<?php

namespace App\Models;
use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title', 'description', 'event_date', 'event_time', 'format',
        'location', 'zoom_link', 'registration_fee', 'max_participants',
        'registration_deadline', 'abstract_deadline', 'registration_active',
        'abstract_submission_active', 'is_active'
    ];

    protected array $casts = [
        'registration_active' => 'boolean',
        'abstract_submission_active' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $useTimestamps = false;

    public function getActiveEvents()
    {
        return $this->where('is_active', true)
                    ->where('event_date >=', date('Y-m-d'))
                    ->orderBy('event_date', 'ASC')
                    ->findAll();
    }

    public function isRegistrationOpen(int $eventId): bool
    {
        $event = $this->find($eventId);
        if (!$event) return false;

        return $event['registration_active'] && 
               $event['is_active'] &&
               (!$event['registration_deadline'] || strtotime($event['registration_deadline']) > time());
    }

    public function getEventWithRegistrations(int $eventId)
    {
        return $this->select('events.*, COUNT(registrations.id) as total_registrations')
                    ->join('registrations', 'registrations.event_id = events.id', 'left')
                    ->where('events.id', $eventId)
                    ->groupBy('events.id')
                    ->first();
    }
}
