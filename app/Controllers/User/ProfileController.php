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
     * Display edit profile page
     */
    public function edit()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        // Get user statistics for sidebar
        $stats = [
            'total_registrations' => $this->getUserRegistrationCount($userId),
            'upcoming_events' => $this->getUserUpcomingEventsCount($userId)
        ];

        $data = [
            'title' => 'Edit Profile - SNIA Conference',
            'user' => $user,
            'stats' => $stats,
            'validation' => \Config\Services::validation()
        ];

        return view('shared/edit_profile', $data);
    }

    /**
     * Update user profile - Dashboard AJAX version
     */
    public function update()
    {
        // Check if this is an AJAX request from dashboard
        if ($this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            return $this->updateProfileAjax();
        }

        // Regular form submission from dedicated edit profile page
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'institution' => 'permit_empty|max_length[255]'
        ];

        // Add password validation rules if password change is requested
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        
        if (!empty($currentPassword) || !empty($newPassword)) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min_length[6]';
            $rules['confirm_password'] = 'required|matches[new_password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify current password if password change is requested
        if (!empty($currentPassword)) {
            if (!password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
            }
        }

        $updateData = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name' => trim($this->request->getPost('last_name')),
            'phone' => $this->request->getPost('phone'),
            'institution' => $this->request->getPost('institution'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Add password to update data if changing password
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Handle profile photo upload
        $profilePhotoFile = $this->request->getFile('profilePhotoInput');
        $photoChanged = $this->request->getPost('photo_changed');
        
        if ($profilePhotoFile && $profilePhotoFile->isValid() && !$profilePhotoFile->hasMoved() && $photoChanged == '1') {
            $profilePhotoUrl = $this->handleProfilePhotoUploadNew($userId, $profilePhotoFile);
            if ($profilePhotoUrl) {
                // Delete old profile photo if exists
                if (!empty($user['profile_photo'])) {
                    $oldPhotoPath = FCPATH . 'uploads/' . $user['profile_photo'];
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                        log_message('info', 'Deleted old profile photo: ' . $user['profile_photo']);
                    }
                }
                $updateData['profile_photo'] = $profilePhotoUrl;
                log_message('info', 'Profile photo will be updated to: ' . $profilePhotoUrl);
            } else {
                log_message('error', 'Profile photo upload failed for user: ' . $userId);
                return redirect()->back()->withInput()->with('error', 'Failed to upload profile photo. Please try again.');
            }
        } elseif ($photoChanged == '1' && (!$profilePhotoFile || !$profilePhotoFile->isValid())) {
            log_message('error', 'Profile photo upload requested but file is invalid for user: ' . $userId);
            return redirect()->back()->withInput()->with('error', 'Invalid photo file. Please select a valid image.');
        }

        try {
            $this->userModel->update($userId, $updateData);

            // Update session data
            $fullName = trim($updateData['first_name'] . ' ' . $updateData['last_name']);
            $this->session->set('user_name', $fullName);

            $successMessage = 'Profile updated successfully!';
            if (isset($updateData['profile_photo'])) {
                $successMessage = 'Profile updated successfully! Photo uploaded and saved.';
            }
            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update profile via AJAX from dashboard
     */
    private function updateProfileAjax()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not authenticated'
            ]);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        try {
            // Validate input
            $firstName = trim($this->request->getPost('first_name'));
            $lastName = trim($this->request->getPost('last_name'));
            $institution = trim($this->request->getPost('institution'));
            $phone = trim($this->request->getPost('phone'));
            $currentPassword = $this->request->getPost('current_password');
            $newPassword = $this->request->getPost('new_password');

            if (empty($firstName) || empty($lastName)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'First name and last name are required'
                ]);
            }

            // Prepare update data
            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'institution' => $institution,
                'phone' => $phone,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle password change if provided
            if (!empty($currentPassword) && !empty($newPassword)) {
                // Verify current password
                if (!password_verify($currentPassword, $user['password'])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Current password is incorrect'
                    ]);
                }

                // Validate new password
                if (strlen($newPassword) < 6) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'New password must be at least 6 characters'
                    ]);
                }

                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // Handle profile photo upload
            $profilePhotoUrl = null;
            if ($this->request->getFile('profile_photo') && $this->request->getFile('profile_photo')->isValid()) {
                $profilePhotoUrl = $this->handleProfilePhotoUpload($userId);
                if ($profilePhotoUrl) {
                    $updateData['profile_photo'] = $profilePhotoUrl;
                }
            }

            // Update user
            $this->userModel->update($userId, $updateData);

            // Update session data
            $this->session->set([
                'first_name' => $firstName,
                'user_name' => trim($firstName . ' ' . $lastName)
            ]);

            // Return success response with updated user data
            $updatedUser = $this->userModel->find($userId);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => [
                    'first_name' => $updatedUser['first_name'],
                    'last_name' => $updatedUser['last_name'],
                    'email' => $updatedUser['email'],
                    'institution' => $updatedUser['institution'],
                    'phone' => $updatedUser['phone'],
                    'profile_photo_url' => $profilePhotoUrl ? base_url('uploads/' . $profilePhotoUrl) : null
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile. Please try again.'
            ]);
        }
    }

    /**
     * Handle profile photo upload for dashboard
     */
    private function handleProfilePhotoUpload($userId)
    {
        $file = $this->request->getFile('profile_photo');

        if (!$file || !$file->isValid()) {
            return null;
        }

        // Validate file
        if ($file->getSize() > 2 * 1024 * 1024) { // 2MB limit
            return null;
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
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
     * Handle profile photo upload for dedicated edit profile page
     */
    private function handleProfilePhotoUploadNew($userId, $file)
    {
        // Validate file
        if ($file->getSize() > 2 * 1024 * 1024) { // 2MB limit
            log_message('error', 'Profile photo upload failed: File size too large (' . $file->getSize() . ' bytes)');
            return null;
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            log_message('error', 'Profile photo upload failed: Invalid mime type (' . $file->getMimeType() . ')');
            return null;
        }

        // Create upload directory if it doesn't exist
        $uploadPath = FCPATH . 'uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $fileName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

        // Move file
        try {
            if ($file->move($uploadPath, $fileName)) {
                log_message('info', 'Profile photo uploaded successfully: ' . $fileName);
                return $fileName;
            } else {
                log_message('error', 'Profile photo upload failed: Could not move file');
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'Profile photo upload error: ' . $e->getMessage());
            return null;
        }
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

    /**
     * Get user registration count
     */
    private function getUserRegistrationCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('registrations')
                  ->where('user_id', $userId)
                  ->countAllResults();
    }

    /**
     * Get upcoming events count for user
     */
    private function getUserUpcomingEventsCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('registrations r')
                  ->join('events e', 'e.id = r.event_id')
                  ->where('r.user_id', $userId)
                  ->where('e.event_date >=', date('Y-m-d'))
                  ->countAllResults();
    }
}