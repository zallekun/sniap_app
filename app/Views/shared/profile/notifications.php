<?= $this->extend('shared/layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/shared/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 notifications-page">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('profile') ?>">Profile</a></li>
                    <li class="breadcrumb-item active">Notification Settings</li>
                </ol>
            </nav>
            
            <div class="page-header">
                <h1><i class="fas fa-bell me-3"></i>Notification Settings</h1>
                <p class="text-muted">Manage how you receive notifications from SNIA Conference</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notification Settings Form -->
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Email Notifications</h3>
                    <p class="text-muted mb-0">Choose what notifications you want to receive</p>
                </div>
                <div class="profile-card-body">
                    <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('profile/update-notifications') ?>">
                        <?= csrf_field() ?>

                        <div class="notification-group">
                            <h5>Conference Updates</h5>
                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_notifications" 
                                           id="emailNotifications" <?= $settings['email_notifications'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="emailNotifications">
                                        <strong>General Email Notifications</strong>
                                        <small class="d-block text-muted">Receive important updates about your account and registrations</small>
                                    </label>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="event_reminders" 
                                           id="eventReminders" <?= $settings['event_reminders'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="eventReminders">
                                        <strong>Event Reminders</strong>
                                        <small class="d-block text-muted">Get reminders before your registered events start</small>
                                    </label>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="abstract_updates" 
                                           id="abstractUpdates" <?= $settings['abstract_updates'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="abstractUpdates">
                                        <strong>Abstract Status Updates</strong>
                                        <small class="d-block text-muted">Notifications about abstract review results and status changes</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="notification-group">
                            <h5>Marketing & Newsletter</h5>
                            <div class="notification-item">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="newsletter" 
                                           id="newsletter" <?= $settings['newsletter'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="newsletter">
                                        <strong>Newsletter & Announcements</strong>
                                        <small class="d-block text-muted">Receive our newsletter with conference updates and industry news</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                            <a href="<?= base_url('profile') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification History -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Recent Notifications</h3>
                    <p class="text-muted mb-0">Your latest notifications from the system</p>
                </div>
                <div class="profile-card-body">
                    <div class="notification-history">
                        <!-- Sample notifications - these would come from database -->
                        <div class="notification-history-item read">
                            <div class="notification-icon">
                                <i class="fas fa-calendar text-primary"></i>
                            </div>
                            <div class="notification-content">
                                <h6>Event Reminder</h6>
                                <p>Your registered event "SNIA Conference 2025" starts in 2 hours</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                            <div class="notification-status">
                                <span class="badge bg-light">Read</span>
                            </div>
                        </div>

                        <div class="notification-history-item unread">
                            <div class="notification-icon">
                                <i class="fas fa-file-alt text-success"></i>
                            </div>
                            <div class="notification-content">
                                <h6>Abstract Status Update</h6>
                                <p>Your abstract "AI in Healthcare" has been accepted for presentation</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                            <div class="notification-status">
                                <span class="badge bg-primary">New</span>
                            </div>
                        </div>

                        <div class="notification-history-item read">
                            <div class="notification-icon">
                                <i class="fas fa-credit-card text-warning"></i>
                            </div>
                            <div class="notification-content">
                                <h6>Payment Confirmation</h6>
                                <p>Your payment for SNIA Conference 2025 registration has been processed</p>
                                <small class="text-muted">3 days ago</small>
                            </div>
                            <div class="notification-status">
                                <span class="badge bg-light">Read</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" onclick="loadMoreNotifications()">
                            <i class="fas fa-chevron-down me-2"></i>Load More
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notification Statistics -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Notification Summary</h3>
                </div>
                <div class="profile-card-body">
                    <div class="notification-stats">
                        <div class="stat-item">
                            <div class="stat-value">24</div>
                            <div class="stat-label">Total Notifications</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">3</div>
                            <div class="stat-label">Unread</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">7</div>
                            <div class="stat-label">This Week</div>
                        </div>
                    </div>

                    <div class="notification-actions mt-3">
                        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="markAllAsRead()">
                            <i class="fas fa-check-double me-2"></i>Mark All as Read
                        </button>
                        <button class="btn btn-outline-danger btn-sm w-100" onclick="clearAllNotifications()">
                            <i class="fas fa-trash me-2"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Quick Links</h3>
                </div>
                <div class="profile-card-body">
                    <div class="quick-links">
                        <a href="<?= base_url('profile/edit') ?>" class="quick-link">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                        <a href="<?= base_url('dashboard') ?>" class="quick-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?= base_url('dashboard/event-schedule-page') ?>" class="quick-link">
                            <i class="fas fa-calendar"></i>
                            <span>Event Schedule</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Notification settings functionality
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save notification preferences
    const switches = document.querySelectorAll('.form-check-input');
    switches.forEach(switch => {
        switch.addEventListener('change', function() {
            // Optional: Add auto-save functionality here
        });
    });
});

function markAllAsRead() {
    // Implementation for marking all notifications as read
    console.log('Marking all notifications as read...');
    // AJAX call to mark all as read
}

function clearAllNotifications() {
    if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
        console.log('Clearing all notifications...');
        // AJAX call to clear all notifications
    }
}

function loadMoreNotifications() {
    console.log('Loading more notifications...');
    // AJAX call to load more notifications
}
</script>
<?= $this->endSection() ?>