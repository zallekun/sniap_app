<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\EventModel;
use CodeIgniter\HTTP\ResponseInterface;

class EventApiController extends BaseController
{
    protected $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    /**
     * Get all events with filtering and pagination
     * GET /api/v1/events
     */
    public function index()
    {
        try {
            // Get query parameters
            $page = $this->request->getGet('page') ?? 1;
            $limit = $this->request->getGet('limit') ?? 10;
            $format = $this->request->getGet('format'); // online, offline
            $search = $this->request->getGet('search');

            // Validate pagination
            $page = max(1, (int)$page);
            $limit = min(50, max(1, (int)$limit)); // Max 50 per page
            $offset = ($page - 1) * $limit;

            // Build query using direct SQL to avoid model issues
            $db = \Config\Database::connect();
            $sql = "SELECT id, title, description, event_date, event_time, format, location, zoom_link, registration_fee, max_participants, registration_deadline FROM events WHERE 1=1";
            $params = [];

            // Filter by format
            if ($format) {
                $sql .= " AND format = ?";
                $params[] = $format;
            }

            // Search in title and description
            if ($search) {
                $sql .= " AND (title ILIKE ? OR description ILIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            // Get total count
            $countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") as subquery";
            $totalCount = $db->query($countSql, $params)->getRow()->count;

            // Add pagination
            $sql .= " ORDER BY event_date ASC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $events = $db->query($sql, $params)->getResultArray();

            // Add basic stats to each event (without status references)
            foreach ($events as &$event) {
                $event['stats'] = $this->getEventStats($event['id']);
                $event['is_online'] = ($event['format'] === 'online');
                $event['is_free'] = ($event['registration_fee'] == 0);
                $event['price'] = (float)$event['registration_fee'];
                
                // Remove sensitive data
                unset($event['updated_at']);
            }

            // Pagination metadata
            $totalPages = ceil($totalCount / $limit);
            $pagination = [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $totalCount,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $events,
                'pagination' => $pagination
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch events: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get event schedule
     * GET /api/v1/events/{id}/schedule
     */
    public function schedule($eventId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Get event details
            $event = $db->table('events')
                ->where('id', $eventId)
                ->get()
                ->getRowArray();

            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Get event schedule
            $schedule = $db->table('event_schedules')
                ->where('event_id', $eventId)
                ->orderBy('start_time')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'event' => [
                        'id' => $event['id'],
                        'title' => $event['title'],
                        'event_date' => $event['event_date'],
                        'event_time' => $event['event_time'],
                        'location' => $event['location'],
                        'format' => $event['format']
                    ],
                    'schedule' => $schedule,
                    'schedule_count' => count($schedule)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get event schedule: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get specific event details
     * GET /api/v1/events/{id}
     */
    public function show($eventId)
    {
        try {
            // Use direct SQL query to avoid model issues
            $db = \Config\Database::connect();
            $event = $db->query('SELECT * FROM events WHERE id = ?', [$eventId])->getRowArray();

            if (!$event) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Event not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Add stats
            $event['stats'] = $this->getEventStats($eventId);
            $event['is_online'] = ($event['format'] === 'online');
            $event['is_free'] = ($event['registration_fee'] == 0);
            $event['price'] = (float)$event['registration_fee'];
            
            // Remove sensitive data
            unset($event['created_at'], $event['updated_at']);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $event
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch event: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search events
     * GET /api/v1/events/search?q=keyword
     */
    public function search()
    {
        try {
            $query = $this->request->getGet('q');
            $limit = $this->request->getGet('limit') ?? 10;

            if (empty($query)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Search query is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $limit = min(20, max(1, (int)$limit));

            // Direct SQL search
            $db = \Config\Database::connect();
            $sql = "SELECT * FROM events WHERE title ILIKE ? OR description ILIKE ? ORDER BY event_date ASC LIMIT ?";
            $events = $db->query($sql, ["%$query%", "%$query%", $limit])->getResultArray();

            // Format results
            foreach ($events as &$event) {
                $event['stats'] = $this->getEventStats($event['id']);
                $event['is_online'] = ($event['format'] === 'online');
                $event['is_free'] = ($event['registration_fee'] == 0);
                
                unset($event['created_at'], $event['updated_at']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $events,
                'query' => $query,
                'total_found' => count($events)
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get upcoming events
     * GET /api/v1/events/upcoming
     */
    public function upcoming()
    {
        try {
            $now = date('Y-m-d H:i:s');
            
            // Direct SQL query
            $db = \Config\Database::connect();
            $sql = "SELECT * FROM events WHERE event_date > ? ORDER BY event_date ASC LIMIT 10";
            $events = $db->query($sql, [$now])->getResultArray();

            // Add basic stats to each event
            foreach ($events as &$event) {
                $event['stats'] = $this->getEventStats($event['id']);
                $event['is_online'] = ($event['format'] === 'online');
                $event['is_free'] = ($event['registration_fee'] == 0);
                
                unset($event['created_at'], $event['updated_at']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $events
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch upcoming events: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get event categories (placeholder)
     * GET /api/v1/events/categories
     */
    public function categories()
    {
        try {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    ['category' => 'seminar', 'count' => 1],
                    ['category' => 'workshop', 'count' => 1]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch categories: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Helper: Get event statistics (simplified)
     */
    private function getEventStats($eventId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Simple count without status filtering (since status column might not exist)
            $totalRegistrations = $db->query('SELECT COUNT(*) as count FROM registrations WHERE event_id = ?', [$eventId])->getRow()->count ?? 0;
            
            return [
                'total_registrations' => $totalRegistrations,
                'confirmed_registrations' => 0 // Placeholder since we don't have status column
            ];
        } catch (\Exception $e) {
            // Return default stats if registrations table doesn't exist or has issues
            return [
                'total_registrations' => 0,
                'confirmed_registrations' => 0
            ];
        }
    }
}