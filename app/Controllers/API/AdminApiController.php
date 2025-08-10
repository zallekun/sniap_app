<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminApiController extends BaseController
{
    /**
     * Middleware to check admin access
     */
    private function checkAdminAccess()
    {
        $request = service('request');
        $user = $request->api_user ?? null;
        
        if (!$user || !in_array($user['role'], ['admin', 'reviewer'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Admin access required'
            ])->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }
        
        return $user;
    }

    /**
     * Get all users (admin only)
     * GET /api/v1/admin/users
     */
    public function users()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $limit = min(50, max(1, (int)($this->request->getGet('limit') ?? 20)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            $search = $this->request->getGet('search');
            $role = $this->request->getGet('role');

            $db = \Config\Database::connect();
            $builder = $db->table('users u');
            $builder->select('u.id, u.first_name, u.last_name, u.email, u.role, u.phone, u.institution, u.is_verified, u.created_at');

            if ($search) {
                $builder->groupStart()
                    ->like('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->orLike('u.institution', $search)
                    ->groupEnd();
            }

            if ($role) {
                $builder->where('u.role', $role);
            }

            $totalCount = $builder->countAllResults(false);
            $users = $builder->orderBy('u.created_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            // Get registration counts for each user
            foreach ($users as &$userData) {
                $userData['registrations_count'] = $db->table('registrations')
                    ->where('user_id', $userData['id'])
                    ->countAllResults();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get users: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all abstracts (admin only)
     * GET /api/v1/admin/abstracts
     */
    public function abstracts()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $limit = min(50, max(1, (int)($this->request->getGet('limit') ?? 20)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            $status = $this->request->getGet('status');
            $search = $this->request->getGet('search');

            $db = \Config\Database::connect();
            $builder = $db->table('abstracts a');
            $builder->select('a.*, u.first_name, u.last_name, u.email, r.registration_type, ac.name as category_name, rev.review_status as reviewer_decision');
            $builder->join('registrations r', 'r.id = a.registration_id', 'LEFT');
            $builder->join('users u', 'u.id = r.user_id', 'LEFT');
            $builder->join('abstract_categories ac', 'ac.id = a.category_id', 'LEFT');
            $builder->join('reviews rev', 'rev.abstract_id = a.id', 'LEFT');

            if ($status) {
                $builder->where('a.review_status', $status);
            }

            if ($search) {
                $builder->groupStart()
                    ->like('a.title', $search)
                    ->orLike('u.first_name', $search)
                    ->orLike('u.last_name', $search)
                    ->orLike('u.email', $search)
                    ->groupEnd();
            }

            $totalCount = $builder->countAllResults(false);
            $abstracts = $builder->orderBy('a.submitted_at', 'DESC')
                ->limit($limit, $offset)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $abstracts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get abstracts: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign reviewer to abstract
     * PUT /api/v1/admin/abstracts/{id}/assign
     */
    public function assignReviewer($abstractId)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $reviewerId = $this->request->getPost('reviewer_id');
            
            if (!$reviewerId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Reviewer ID is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            
            // Check if abstract exists
            $abstract = $db->table('abstracts')->where('id', $abstractId)->get()->getRowArray();
            if (!$abstract) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Abstract not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if reviewer exists
            $reviewer = $db->table('users')->where('id', $reviewerId)->where('role', 'reviewer')->get()->getRowArray();
            if (!$reviewer) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Reviewer not found'
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            // Check if already assigned
            $existingReview = $db->table('reviews')->where('abstract_id', $abstractId)->get()->getRowArray();
            
            if ($existingReview) {
                // Update existing assignment
                $db->table('reviews')
                    ->where('abstract_id', $abstractId)
                    ->update([
                        'reviewer_id' => $reviewerId,
                        'assigned_by' => $user['id'],
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'status' => 'assigned',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Create new review assignment
                $db->table('reviews')->insert([
                    'abstract_id' => $abstractId,
                    'reviewer_id' => $reviewerId,
                    'assigned_by' => $user['id'],
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'status' => 'assigned',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update abstract status
            $db->table('abstracts')
                ->where('id', $abstractId)
                ->update([
                    'status' => 'under_review',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Send notification email to reviewer
            $emailService = new \App\Services\EmailService();
            $emailService->sendReviewerAssignment(
                $reviewer['email'],
                $reviewer['first_name'] . ' ' . $reviewer['last_name'],
                $abstract['title'],
                $abstractId
            );

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Reviewer assigned successfully',
                'data' => [
                    'abstract_id' => $abstractId,
                    'reviewer_id' => $reviewerId,
                    'reviewer_name' => $reviewer['first_name'] . ' ' . $reviewer['last_name'],
                    'assigned_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to assign reviewer: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get dashboard statistics
     * GET /api/v1/admin/dashboard
     */
    public function dashboard()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $db = \Config\Database::connect();
            
            $stats = [
                'users' => [
                    'total' => $db->table('users')->countAllResults(),
                    'verified' => $db->table('users')->where('is_verified', true)->countAllResults(),
                    'presenters' => $db->table('users')->where('role', 'presenter')->countAllResults(),
                    'attendees' => $db->table('users')->where('role', 'audience')->countAllResults()
                ],
                'registrations' => [
                    'total' => $db->table('registrations')->countAllResults(),
                    'pending' => $db->table('registrations')->where('registration_status', 'pending')->countAllResults(),
                    'approved' => $db->table('registrations')->where('registration_status', 'approved')->countAllResults(),
                    'rejected' => $db->table('registrations')->where('registration_status', 'rejected')->countAllResults(),
                    'attended' => $db->table('registrations')->where('attended', true)->countAllResults()
                ],
                'abstracts' => [
                    'total' => $db->table('abstracts')->countAllResults(),
                    'pending' => $db->table('abstracts')->where('review_status', 'pending')->countAllResults(),
                    'accepted' => $db->table('abstracts')->where('review_status', 'accepted')->countAllResults(),
                    'accepted_with_revision' => $db->table('abstracts')->where('review_status', 'accepted_with_revision')->countAllResults(),
                    'rejected' => $db->table('abstracts')->where('review_status', 'rejected')->countAllResults()
                ],
                'payments' => [
                    'total' => $db->table('payments')->countAllResults(),
                    'pending' => $db->table('payments')->where('payment_status', 'pending')->countAllResults(),
                    'completed' => $db->table('payments')->where('payment_status', 'success')->countAllResults(),
                    'failed' => $db->table('payments')->where('payment_status', 'failed')->countAllResults(),
                    'total_amount' => $db->query("SELECT SUM(amount) as total FROM payments WHERE payment_status = 'success'")->getRow()->total ?? 0
                ],
                'certificates' => [
                    'requested' => $db->table('certificates')->where('file_path', '')->countAllResults(),
                    'issued' => $db->table('certificates')->where('file_path !=', '')->countAllResults()
                ]
            ];

            // Presenter progress tracking
            $presenterProgress = $db->query("
                SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.institution,
                    r.registration_status,
                    r.payment_status,
                    CASE 
                        WHEN a.id IS NULL THEN 'not_submitted'::TEXT
                        ELSE a.review_status::TEXT
                    END as abstract_status,
                    CASE 
                        WHEN a.id IS NULL THEN 0
                        WHEN a.review_status = 'pending' THEN 25
                        WHEN a.review_status = 'accepted_with_revision' THEN 50
                        WHEN a.review_status = 'accepted' THEN 100
                        WHEN a.review_status = 'rejected' THEN 0
                        ELSE 10
                    END as progress_percentage,
                    a.title as abstract_title,
                    a.submitted_at,
                    rev.review_status as reviewer_decision,
                    rev.reviewed_at,
                    CASE 
                        WHEN loa.id IS NOT NULL THEN 'generated'
                        ELSE 'not_generated'
                    END as loa_status,
                    CASE 
                        WHEN cert.id IS NOT NULL AND cert.file_path != '' THEN 'issued'
                        WHEN cert.id IS NOT NULL THEN 'requested' 
                        ELSE 'not_requested'
                    END as certificate_status
                FROM users u
                JOIN registrations r ON r.user_id = u.id
                LEFT JOIN abstracts a ON a.registration_id = r.id
                LEFT JOIN reviews rev ON rev.abstract_id = a.id
                LEFT JOIN loa_documents loa ON loa.abstract_id = a.id
                LEFT JOIN certificates cert ON cert.registration_id = r.id
                WHERE u.role = 'presenter'
                ORDER BY a.submitted_at DESC NULLS LAST, u.created_at DESC
            ")->getResultArray();
            
            // Progress statistics
            $progressStats = [
                'not_submitted' => 0,
                'pending_review' => 0,
                'need_revision' => 0,
                'accepted' => 0,
                'rejected' => 0,
                'loa_generated' => 0,
                'certificates_issued' => 0
            ];
            
            foreach ($presenterProgress as $presenter) {
                switch ($presenter['abstract_status']) {
                    case 'not_submitted':
                        $progressStats['not_submitted']++;
                        break;
                    case 'pending':
                        $progressStats['pending_review']++;
                        break;
                    case 'accepted_with_revision':
                        $progressStats['need_revision']++;
                        break;
                    case 'accepted':
                        $progressStats['accepted']++;
                        break;
                    case 'rejected':
                        $progressStats['rejected']++;
                        break;
                }
                
                if ($presenter['loa_status'] === 'generated') {
                    $progressStats['loa_generated']++;
                }
                
                if ($presenter['certificate_status'] === 'issued') {
                    $progressStats['certificates_issued']++;
                }
            }

            // Recent activities
            $recentUsers = $db->table('users')
                ->select('first_name, last_name, email, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            $recentAbstracts = $db->table('abstracts a')
                ->select('a.title, a.submitted_at, u.first_name, u.last_name')
                ->join('registrations r', 'r.id = a.registration_id')
                ->join('users u', 'u.id = r.user_id')
                ->orderBy('a.submitted_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();

            // Progress visualization data
            $totalPresenters = count($presenterProgress);
            $progressVisualization = [
                'completion_rate' => $totalPresenters > 0 ? round(($progressStats['accepted'] / $totalPresenters) * 100, 1) : 0,
                'progress_chart' => [
                    'labels' => ['Not Submitted', 'Pending Review', 'Need Revision', 'Accepted', 'Rejected'],
                    'data' => [
                        $progressStats['not_submitted'],
                        $progressStats['pending_review'], 
                        $progressStats['need_revision'],
                        $progressStats['accepted'],
                        $progressStats['rejected']
                    ],
                    'colors' => ['#6b7280', '#f59e0b', '#ef4444', '#10b981', '#dc2626']
                ],
                'workflow_funnel' => [
                    ['stage' => 'Registered', 'count' => $totalPresenters, 'percentage' => 100],
                    ['stage' => 'Abstract Submitted', 'count' => $totalPresenters - $progressStats['not_submitted'], 'percentage' => $totalPresenters > 0 ? round((($totalPresenters - $progressStats['not_submitted']) / $totalPresenters) * 100, 1) : 0],
                    ['stage' => 'Under Review', 'count' => $progressStats['pending_review'], 'percentage' => $totalPresenters > 0 ? round(($progressStats['pending_review'] / $totalPresenters) * 100, 1) : 0],
                    ['stage' => 'Accepted', 'count' => $progressStats['accepted'], 'percentage' => $totalPresenters > 0 ? round(($progressStats['accepted'] / $totalPresenters) * 100, 1) : 0],
                    ['stage' => 'LOA Generated', 'count' => $progressStats['loa_generated'], 'percentage' => $totalPresenters > 0 ? round(($progressStats['loa_generated'] / $totalPresenters) * 100, 1) : 0],
                    ['stage' => 'Certificate Issued', 'count' => $progressStats['certificates_issued'], 'percentage' => $totalPresenters > 0 ? round(($progressStats['certificates_issued'] / $totalPresenters) * 100, 1) : 0]
                ],
                'timeline_trends' => $db->query("
                    SELECT 
                        DATE(a.submitted_at) as date,
                        COUNT(*) as submissions_count,
                        COUNT(CASE WHEN a.review_status = 'accepted' THEN 1 END) as accepted_count,
                        COUNT(CASE WHEN loa.id IS NOT NULL THEN 1 END) as loa_count
                    FROM abstracts a
                    LEFT JOIN loa_documents loa ON loa.abstract_id = a.id
                    WHERE a.submitted_at >= NOW() - INTERVAL '30 days'
                    GROUP BY DATE(a.submitted_at)
                    ORDER BY date DESC
                    LIMIT 10
                ")->getResultArray()
            ];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'statistics' => $stats,
                    'presenter_progress' => [
                        'summary' => $progressStats,
                        'details' => array_slice($presenterProgress, 0, 20), // Limit to 20 recent
                        'total_presenters' => $totalPresenters,
                        'visualization' => $progressVisualization
                    ],
                    'recent_activities' => [
                        'new_users' => $recentUsers,
                        'new_abstracts' => $recentAbstracts
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get dashboard data: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export data
     * GET /api/v1/admin/export/{type}
     */
    public function export($type)
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $format = $this->request->getGet('format') ?? 'csv';
            $db = \Config\Database::connect();
            
            switch ($type) {
                case 'users':
                    $data = $db->table('users')
                        ->select('id, first_name, last_name, email, role, phone, institution, is_verified, created_at')
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'registrations':
                    $data = $db->table('registrations r')
                        ->select('r.id, u.first_name, u.last_name, u.email, e.title as event_title, r.registration_type, r.registration_status, r.payment_status, r.attended, r.created_at')
                        ->join('users u', 'u.id = r.user_id')
                        ->join('events e', 'e.id = r.event_id')
                        ->orderBy('r.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'abstracts':
                    $data = $db->table('abstracts a')
                        ->select('a.id, a.title, u.first_name, u.last_name, u.email, a.status, ac.name as category, a.submitted_at')
                        ->join('registrations r', 'r.id = a.registration_id')
                ->join('users u', 'u.id = r.user_id')
                        ->join('abstract_categories ac', 'ac.id = a.category_id', 'LEFT')
                        ->orderBy('a.submitted_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                case 'payments':
                    $data = $db->table('payments p')
                        ->select('p.id, u.first_name, u.last_name, u.email, p.amount, p.status, p.payment_method, p.transaction_id, p.created_at')
                        ->join('registrations r', 'r.id = p.registration_id')
                        ->join('users u', 'u.id = r.user_id')
                        ->orderBy('p.created_at', 'DESC')
                        ->get()
                        ->getResultArray();
                    break;
                
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid export type'
                    ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            if ($format === 'csv') {
                // Generate CSV
                $filename = "snia_{$type}_export_" . date('Y-m-d_H-i-s') . '.csv';
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                
                if (!empty($data)) {
                    // Write headers
                    fputcsv($output, array_keys($data[0]));
                    
                    // Write data
                    foreach ($data as $row) {
                        fputcsv($output, $row);
                    }
                }
                
                fclose($output);
                exit;
            } else {
                // Return JSON
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'count' => count($data),
                    'export_type' => $type,
                    'exported_at' => date('Y-m-d H:i:s')
                ])->setStatusCode(ResponseInterface::HTTP_OK);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get system settings
     * GET /api/v1/admin/settings
     */
    public function getSettings()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $db = \Config\Database::connect();
            $settings = $db->table('system_settings')
                ->get()
                ->getResultArray();

            $formattedSettings = [];
            foreach ($settings as $setting) {
                $formattedSettings[$setting['setting_key']] = [
                    'value' => $setting['setting_value'],
                    'description' => $setting['description'],
                    'updated_at' => $setting['updated_at']
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedSettings
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get settings: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update system settings
     * PUT /api/v1/admin/settings
     */
    public function updateSettings()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $settings = $this->request->getPost('settings');
            
            if (!$settings || !is_array($settings)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Settings data is required'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }

            $db = \Config\Database::connect();
            $updated = [];
            
            foreach ($settings as $key => $value) {
                // Update or insert setting
                $existing = $db->table('system_settings')
                    ->where('setting_key', $key)
                    ->get()
                    ->getRowArray();
                
                if ($existing) {
                    $db->table('system_settings')
                        ->where('setting_key', $key)
                        ->update([
                            'setting_value' => $value,
                            'updated_by' => $user['id'],
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                } else {
                    $db->table('system_settings')
                        ->insert([
                            'setting_key' => $key,
                            'setting_value' => $value,
                            'created_by' => $user['id'],
                            'updated_by' => $user['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                }
                
                $updated[] = $key;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Settings updated successfully',
                'data' => [
                    'updated_settings' => $updated,
                    'updated_by' => $user['first_name'] . ' ' . $user['last_name'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get detailed presenter progress tracking
     * GET /api/v1/admin/presenter-progress
     */
    public function presenterProgress()
    {
        $user = $this->checkAdminAccess();
        if (!is_array($user)) return $user;

        try {
            $limit = min(100, max(1, (int)($this->request->getGet('limit') ?? 50)));
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $offset = ($page - 1) * $limit;
            $status = $this->request->getGet('status'); // Filter by abstract status
            $search = $this->request->getGet('search');

            $db = \Config\Database::connect();
            
            // Base query for presenter progress
            $whereConditions = ["u.role = 'presenter'"];
            $params = [];
            
            if ($status) {
                if ($status === 'not_submitted') {
                    $whereConditions[] = "a.id IS NULL";
                } else {
                    $whereConditions[] = "a.review_status = ?";
                    $params[] = $status;
                }
            }
            
            if ($search) {
                $whereConditions[] = "(u.first_name ILIKE ? OR u.last_name ILIKE ? OR u.email ILIKE ? OR u.institution ILIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
            
            // Get presenter progress with pagination
            $presenterProgress = $db->query("
                SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.institution,
                    u.phone,
                    u.created_at as registered_at,
                    r.registration_status,
                    r.payment_status,
                    CASE 
                        WHEN a.id IS NULL THEN 'not_submitted'::TEXT
                        ELSE a.review_status::TEXT
                    END as abstract_status,
                    CASE 
                        WHEN a.id IS NULL THEN 0
                        WHEN a.review_status = 'pending' THEN 25
                        WHEN a.review_status = 'accepted_with_revision' THEN 50
                        WHEN a.review_status = 'accepted' THEN 100
                        WHEN a.review_status = 'rejected' THEN 0
                        ELSE 10
                    END as progress_percentage,
                    a.title as abstract_title,
                    a.submitted_at,
                    a.keywords,
                    rev.review_status as reviewer_decision,
                    rev.comments as review_comments,
                    rev.reviewed_at,
                    CASE 
                        WHEN loa.id IS NOT NULL THEN 'generated'
                        ELSE 'not_generated'  
                    END as loa_status,
                    loa.generated_at as loa_generated_at,
                    CASE 
                        WHEN cert.id IS NOT NULL AND cert.file_path != '' THEN 'issued'
                        WHEN cert.id IS NOT NULL THEN 'requested'
                        ELSE 'not_requested'
                    END as certificate_status,
                    cert.generated_at as certificate_requested_at,
                    -- Progress workflow status
                    CASE 
                        WHEN cert.id IS NOT NULL AND cert.file_path != '' THEN 'Certificate Issued'
                        WHEN loa.id IS NOT NULL THEN 'LOA Generated'
                        WHEN a.review_status = 'accepted' THEN 'Abstract Accepted'
                        WHEN a.review_status = 'accepted_with_revision' THEN 'Needs Revision'
                        WHEN a.review_status = 'rejected' THEN 'Abstract Rejected'
                        WHEN a.review_status = 'pending' THEN 'Under Review'
                        WHEN a.id IS NOT NULL THEN 'Abstract Submitted'
                        WHEN r.payment_status = 'paid' THEN 'Payment Completed'
                        WHEN r.registration_status = 'approved' THEN 'Registration Approved'
                        ELSE 'Registered'
                    END as current_stage
                FROM users u
                JOIN registrations r ON r.user_id = u.id
                LEFT JOIN abstracts a ON a.registration_id = r.id
                LEFT JOIN reviews rev ON rev.abstract_id = a.id
                LEFT JOIN loa_documents loa ON loa.abstract_id = a.id
                LEFT JOIN certificates cert ON cert.registration_id = r.id
                {$whereClause}
                ORDER BY a.submitted_at DESC NULLS LAST, u.created_at DESC
                LIMIT ? OFFSET ?
            ", array_merge($params, [$limit, $offset]))->getResultArray();
            
            // Get total count
            $totalCount = $db->query("
                SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                JOIN registrations r ON r.user_id = u.id
                LEFT JOIN abstracts a ON a.registration_id = r.id
                {$whereClause}
            ", $params)->getRow()->total;

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $presenterProgress,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_items' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit),
                    'has_next' => $page < ceil($totalCount / $limit),
                    'has_prev' => $page > 1
                ],
                'summary' => [
                    'total_presenters' => $totalCount,
                    'filters_applied' => [
                        'status' => $status,
                        'search' => $search
                    ]
                ]
            ])->setStatusCode(ResponseInterface::HTTP_OK);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get presenter progress: ' . $e->getMessage()
            ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}