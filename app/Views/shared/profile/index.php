<?= $this->extend('shared/layouts/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/shared/profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 profile-page">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-header">
                <div class="profile-header-content">
                    <div class="profile-avatar">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?= base_url('uploads/' . $user['profile_photo']) ?>" alt="Profile Photo" class="profile-photo">
                        <?php else: ?>
                            <div class="profile-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h1><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                        <p class="profile-role">
                            <i class="fas fa-user-tag me-2"></i>
                            <?= ucfirst(esc($user['role'])) ?>
                        </p>
                        <p class="profile-email">
                            <i class="fas fa-envelope me-2"></i>
                            <?= esc($user['email']) ?>
                        </p>
                        <?php if (!empty($user['institution'])): ?>
                        <p class="profile-institution">
                            <i class="fas fa-building me-2"></i>
                            <?= esc($user['institution']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="profile-actions">
                        <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Statistics -->
    <div class="row mb-4">
        <?php foreach ($stats as $key => $value): ?>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-<?= getStatIcon($key) ?>"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($value) ?></h3>
                    <p><?= ucwords(str_replace('_', ' ', $key)) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="profile-card-body">
                    <?php if (!empty($recent_activity)): ?>
                        <div class="activity-timeline">
                            <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-<?= getActivityIcon($activity['type']) ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <h5><?= esc($activity['title']) ?></h5>
                                    <p class="activity-date"><?= date('M j, Y - H:i', strtotime($activity['date'])) ?></p>
                                    <span class="activity-status status-<?= $activity['status'] ?>">
                                        <?= ucfirst($activity['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h4>No Recent Activity</h4>
                            <p>Your recent activities will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications & Settings -->
        <div class="col-lg-4">
            <!-- Notifications -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Recent Notifications</h3>
                    <a href="<?= base_url('profile/notifications') ?>" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="profile-card-body">
                    <?php if (!empty($notifications)): ?>
                        <div class="notifications-list">
                            <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                            <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>">
                                <div class="notification-content">
                                    <h6><?= esc($notification['title']) ?></h6>
                                    <p><?= esc($notification['message']) ?></p>
                                    <small class="text-muted">
                                        <?= timeAgo($notification['created_at']) ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-bell"></i>
                            <h5>No Notifications</h5>
                            <p>You're all caught up!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Settings -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3>Quick Settings</h3>
                </div>
                <div class="profile-card-body">
                    <div class="quick-settings">
                        <a href="<?= base_url('profile/edit') ?>" class="setting-link">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="<?= base_url('profile/notifications') ?>" class="setting-link">
                            <i class="fas fa-bell"></i>
                            <span>Notification Settings</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="<?= base_url('dashboard') ?>" class="setting-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Back to Dashboard</span>
                            <i class="fas fa-chevron-right"></i>
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
// Profile page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add any profile-specific JavaScript here
    console.log('Profile page loaded');
});
</script>
<?= $this->endSection() ?>

<?php
// Helper functions
function getStatIcon($type) {
    $icons = [
        'total_abstracts' => 'file-alt',
        'accepted_abstracts' => 'check-circle',
        'pending_abstracts' => 'clock',
        'total_registrations' => 'calendar',
        'upcoming_events' => 'calendar-alt',
        'total_reviews' => 'star',
        'completed_reviews' => 'check-square',
        'pending_reviews' => 'hourglass-half',
        'attended_events' => 'check-circle'
    ];
    return $icons[$type] ?? 'chart-bar';
}

function getActivityIcon($type) {
    $icons = [
        'registration' => 'calendar-plus',
        'abstract' => 'file-upload',
        'review' => 'star',
        'payment' => 'credit-card'
    ];
    return $icons[$type] ?? 'activity';
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . 'm ago';
    if ($time < 86400) return floor($time/3600) . 'h ago';
    if ($time < 2592000) return floor($time/86400) . 'd ago';
    return date('M j, Y', strtotime($datetime));
}
?>