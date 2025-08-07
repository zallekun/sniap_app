<?php

namespace App\Models;
use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'event_id', 'registration_type', 'registration_status',
        'payment_status', 'qr_code', 'attended', 'attendance_time'
    ];

    protected array $casts = [
        'attended' => 'boolean'
    ];

    protected $useTimestamps = false;

    public function getUserRegistrations(int $userId)
    {
        return $this->select('registrations.*, events.title, events.event_date, events.format')
                    ->join('events', 'events.id = registrations.event_id')
                    ->where('registrations.user_id', $userId)
                    ->orderBy('events.event_date', 'DESC')
                    ->findAll();
    }

    public function getEventRegistrations(int $eventId)
    {
        return $this->select('registrations.*, users.first_name, users.last_name, users.email')
                    ->join('users', 'users.id = registrations.user_id')
                    ->where('registrations.event_id', $eventId)
                    ->orderBy('registrations.created_at', 'DESC')
                    ->findAll();
    }

    public function isUserRegistered(int $userId, int $eventId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('event_id', $eventId)
                    ->countAllResults() > 0;
    }

    public function generateQRCode(int $registrationId): string
    {
        $qrCode = 'SNIA-' . date('Y') . '-' . str_pad($registrationId, 6, '0', STR_PAD_LEFT);
        $this->update($registrationId, ['qr_code' => $qrCode]);
        return $qrCode;
    }

    public function markAttendance(string $qrCode): bool
    {
        return $this->where('qr_code', $qrCode)
                    ->set('attended', true)
                    ->set('attendance_time', date('Y-m-d H:i:s'))
                    ->update();
    }
}
