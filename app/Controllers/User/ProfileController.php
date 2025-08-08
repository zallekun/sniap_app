<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RegistrationModel;
use App\Models\AbstractModel;
use App\Models\ReviewModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $registrationModel;
    protected $abstractModel;
    protected $reviewModel;
    protected $notificationModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->registrationModel = new RegistrationModel();
        $this->abstractModel = new AbstractModel();
        $this->reviewModel = new ReviewModel();
        $this->notificationModel = new NotificationModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display user profile
     */
    public function index()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        // Get user statistics based on role
        $stats = $this->getUserStats($userId, $user['role']);

        // Get recent activity
        $recentActivity = $this->getRecentActivity($userId, $user['role']);

        // Get notifications
        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->find();

        $data = [
            'title' => 'My Profile - SNIA Conference',
            'user' => $user,
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'notifications' => $notifications,
            'validation' => \Config\Services::validation()
        ];

        return view('user/profile/index', $data);
    }

    /**
     * Update user profile
     */
    public function update()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'institution' => 'permit_empty|max_length[200]',
            'bio' => 'permit_empty|max_length[1000]',
            'profile_picture' => 'permit_empty|uploaded[profile_picture]|is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        $messages = [
            'full_name' => [
                'required' => 'Full name is required',
                'min_length' => 'Full name must be at least 3 characters'
            ],
            'email' => [
                'required' => 'Email is required',
                'valid_email' => 'Please enter a valid email address',
                'is_unique' => 'This email is already taken'
            ],
            'phone' => [
                'min_length' => 'Phone number must be at least 10 digits',
                'max_length' => 'Phone number cannot exceed 15 digits'
            ],
            'profile_picture' => [
                'uploaded' => 'Please select a valid image file',
                'is_image' => 'Profile picture must be an image',
                'max_size' => 'Profile picture must be less than 2MB'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle profile picture upload
        $profilePicture = $this->handleProfilePictureUpload($userId);

        $updateData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => strtolower(trim($this->request->getPost('email'))),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'bio' => $this->request->getPost('bio'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Only update profile picture if new one is uploaded
        if ($profilePicture) {
            // Delete old profile picture
            if ($user['profile_picture']) {
                $oldPicturePath = WRITEPATH . 'uploads/' . $user['profile_picture'];
                if (file_exists($oldPicturePath)) {
                    unlink($oldPicturePath);
                }
            }
            $updateData['profile_picture'] = $profilePicture;
        }

        try {
            $this->userModel->update($userId, $updateData);

            // Update session data if name or email changed
            if ($updateData['full_name'] !== $user['full_name']) {
                $this->session->set('user_name', $updateData['full_name']);
            }
            if ($updateData['email'] !== $user['email']) {
                $this->session->set('user_email', $updateData['email']);
            }

            return redirect()->back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        $messages = [
            'current_password' => [
                'required' => 'Current password is required'
            ],
            'new_password' => [
                'required' => 'New password is required',
                'min_length' => 'New password must be at least 6 characters'
            ],
            'confirm_password' => [
                'required' => 'Password confirmation is required',
                'matches' => 'Password confirmation does not match'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Check if new password is different
        if (password_verify($newPassword, $user['password'])) {
            return redirect()->back()->with('error', 'New password must be different from current password.');
        }

        try {
            // Update password
            $this->userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Send notification email
            $this->sendPasswordChangeNotification($user['email'], $user['full_name']);

            return redirect()->back()->with('success', 'Password changed successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to change password. Please try again.');
        }
    }

    /**
     * Upload new profile picture via AJAX
     */
    public function uploadProfilePicture()
    {
        $userId = $this->session->get('user_id');
        
        $rules = [
            'profile_picture' => 'uploaded[profile_picture]|is_image[profile_picture]|max_size[profile_picture,2048]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid image file. Please select a valid image under 2MB.'
            ]);
        }

        try {
            $user = $this->userModel->find($userId);
            $profilePicture = $this->handleProfilePictureUpload($userId);

            if ($profilePicture) {
                // Delete old profile picture
                if ($user['profile_picture']) {
                    $oldPicturePath = WRITEPATH . 'uploads/' . $user['profile_picture'];
                    if (file_exists($oldPicturePath)) {
                        unlink($oldPicturePath);
                    }
                }

                // Update database
                $this->userModel->update($userId, [
                    'profile_picture' => $profilePicture,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Profile picture updated successfully!',
                    'image_url' => base_url('uploads/' . $profilePicture)
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to upload profile picture.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Profile picture upload error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload profile picture. Please try again.'
            ]);
        }
    }

    /**
     * Delete profile picture
     */
    public function deleteProfilePicture()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['profile_picture']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No profile picture to delete.'
            ]);
        }

        try {
            // Delete file
            $picturePath = WRITEPATH . 'uploads/' . $user['profile_picture'];
            if (file_exists($picturePath)) {
                unlink($picturePath);
            }

            // Update database
            $this->userModel->update($userId, [
                'profile_picture' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile picture deleted successfully!'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Profile picture deletion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete profile picture.'
            ]);
        }
    }

    /**
     * Get notification settings
     */
    public function notificationSettings()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        $settings = [
            'email_notifications' => $user['email_notifications'] ?? true,
            'event_reminders' => $user['event_reminders'] ?? true,
            'abstract_updates' => $user['abstract_updates'] ?? true,
            'newsletter' => $user['newsletter'] ?? false,
        ];

        $data = [
            'title' => 'Notification Settings - SNIA Conference',
            'user' => $user,
            'settings' => $settings
        ];

        return view('user/profile/notifications', $data);
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings()
    {
        $userId = $this->session->get('user_id');

        $settings = [
            'email_notifications' => $this->request->getPost('email_notifications') ? true : false,
            'event_reminders' => $this->request->getPost('event_reminders') ? true : false,
            'abstract_updates' => $this->request->getPost('abstract_updates') ? true : false,
            'newsletter' => $this->request->getPost('newsletter') ? true : false,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->userModel->update($userId, $settings);
            return redirect()->back()->with('success', 'Notification settings updated successfully!');

        } catch (\Exception $e) {
            log_message('error', 'Notification settings update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update settings. Please try again.');
        }
    }

    /**
     * Delete account (deactivate)
     */
    public function deleteAccount()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $password = $this->request->getPost('password');

        if (!$password || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Please enter your correct password to delete account.');
        }

        try {
            // Soft delete - deactivate account instead of hard delete
            $this->userModel->update($userId, [
                'is_active' => false,
                'deactivated_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Send deactivation email
            $this->sendAccountDeactivationEmail($user['email'], $user['full_name']);

            // Destroy session
            $this->session->destroy();

            return redirect()->to('/')->with('success', 'Your account has been deactivated successfully.');

        } catch (\Exception $e) {
            log_message('error', 'Account deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete account. Please try again.');
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get user statistics based on role
     */
    private function getUserStats($userId, $role)
    {
        $stats = [];

        switch ($role) {
            case 'presenter':
                $stats = [
                    'total_abstracts' => $this->abstractModel->where('user_id', $userId)->countAllResults(),
                    'accepted_abstracts' => $this->abstractModel->where('user_id', $userId)->where('status', 'accepted')->countAllResults(),
                    'pending_abstracts' => $this->abstractModel->where('user_id', $userId)->where('status', 'pending')->countAllResults(),
                    'total_registrations' => $this->registrationModel->where('user_id', $userId)->countAllResults(),
                    'upcoming_events' => $this->registrationModel->getUserUpcomingEvents($userId)->countAllResults(),
                ];
                break;

            case 'reviewer':
                $stats = [
                    'total_reviews' => $this->reviewModel->where('reviewer_id', $userId)->countAllResults(),
                    'completed_reviews' => $this->reviewModel->where('reviewer_id', $userId)->where('status', 'completed')->countAllResults(),
                    'pending_reviews' => $this->reviewModel->where('reviewer_id', $userId)->where('status', 'assigned')->countAllResults(),
                    'total_registrations' => $this->registrationModel->where('user_id', $userId)->countAllResults(),
                ];
                break;

            default:
                $stats = [
                    'total_registrations' => $this->registrationModel->where('user_id', $userId)->countAllResults(),
                    'attended_events' => $this->registrationModel->where('user_id', $userId)->where('attendance_status', 'attended')->countAllResults(),
                    'upcoming_events' => $this->registrationModel->getUserUpcomingEvents($userId)->countAllResults(),
                ];
        }

        return $stats;
    }

    /**
     * Get recent user activity
     */
    private function getRecentActivity($userId, $role)
    {
        $activities = [];

        // Get recent registrations
        $recentRegistrations = $this->registrationModel
            ->select('registrations.*, events.title as event_title')
            ->join('events', 'events.id = registrations.event_id')
            ->where('registrations.user_id', $userId)
            ->orderBy('registrations.created_at', 'DESC')
            ->limit(5)
            ->find();

        foreach ($recentRegistrations as $reg) {
            $activities[] = [
                'type' => 'registration',
                'title' => 'Registered for ' . $reg['event_title'],
                'date' => $reg['created_at'],
                'status' => $reg['status']
            ];
        }

        // Add role-specific activities
        if ($role === 'presenter') {
            $recentAbstracts = $this->abstractModel
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->find();

            foreach ($recentAbstracts as $abstract) {
                $activities[] = [
                    'type' => 'abstract',
                    'title' => 'Submitted abstract: ' . $abstract['title'],
                    'date' => $abstract['created_at'],
                    'status' => $abstract['status']
                ];
            }
        } elseif ($role === 'reviewer') {
            $recentReviews = $this->reviewModel
                ->select('reviews.*, abstracts.title as abstract_title')
                ->join('abstracts', 'abstracts.id = reviews.abstract_id')
                ->where('reviews.reviewer_id', $userId)
                ->orderBy('reviews.created_at', 'DESC')
                ->limit(5)
                ->find();

            foreach ($recentReviews as $review) {
                $activities[] = [
                    'type' => 'review',
                    'title' => 'Reviewed: ' . $review['abstract_title'],
                    'date' => $review['created_at'],
                    'status' => $review['status']
                ];
            }
        }

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Handle profile picture upload
     */
    private function handleProfilePictureUpload($userId)
    {
        $file = $this->request->getFile('profile_picture');

        if (!$file || !$file->isValid()) {
            return null;
        }

        // Create upload directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/profiles/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

        // Move file
        if ($file->move($uploadPath, $fileName)) {
            return 'profiles/' . $fileName;
        }

        return null;
    }

    /**
     * Send password change notification
     */
    private function sendPasswordChangeNotification($email, $name)
    {
        $emailService = \Config\Services::email();

        $message = "
            <h2>Password Changed</h2>
            <p>Hello {$name},</p>
            <p>Your password has been successfully changed.</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p>If you did not make this change, please contact us immediately.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject('Password Changed - SNIA Conference');
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password change notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send account deactivation email
     */
    private function sendAccountDeactivationEmail($email, $name)
    {
        $emailService = \Config\Services::email();

        $message = "
            <h2>Account Deactivated</h2>
            <p>Hello {$name},</p>
            <p>Your SNIA Conference account has been deactivated as requested.</p>
            <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p>If you wish to reactivate your account in the future, please contact our support team.</p>
            <p>Thank you for being part of the SNIA Conference community.</p>
            <br>
            <p>Best regards,<br>SNIA Conference Team</p>
        ";

        $emailService->setTo($email);
        $emailService->setSubject('Account Deactivated - SNIA Conference');
        $emailService->setMessage($message);

        try {
            return $emailService->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send deactivation email: ' . $e->getMessage());
            return false;
        }
    }
}