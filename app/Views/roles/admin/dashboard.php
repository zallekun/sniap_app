<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Admin Dashboard';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="<?= base_url('css/admin/dashboard.css') ?>">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/admin/dashboard" class="sidebar-logo">SNIA Admin</a>
            <div class="sidebar-subtitle">Conference Management</div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <div class="nav-item">
                    <a href="/admin/dashboard" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <div class="nav-item">
                    <a href="/admin/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        User Management
                        <?php if (isset($stats['total_users']) && $stats['total_users'] > 0): ?>
                            <span class="nav-badge"><?= number_format($stats['total_users']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/events" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        Event Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/registrations" class="nav-link">
                        <i class="fas fa-user-check"></i>
                        Registrations
                        <?php if (isset($stats['total_registrations']) && $stats['total_registrations'] > 0): ?>
                            <span class="nav-badge"><?= number_format($stats['total_registrations']) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/abstracts" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        Abstract & Reviews
                        <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                            <span class="nav-badge"><?= $stats['pending_reviews'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Reports & Analytics</div>
                <div class="nav-item">
                    <a href="/admin/analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <div class="nav-item">
                    <a href="/admin/settings" class="nav-link">
                        <i class="fas fa-cog"></i>
                        System Settings
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/dashboard" class="nav-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <h1 class="header-title">Admin Dashboard</h1>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">
                            <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                        </div>
                        <div style="font-size: 0.875rem; color: #6b7280;">
                            Administrator
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="admin-content">
            <!-- Statistics Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Users</div>
                            <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon events">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Events</div>
                            <div class="stat-value"><?= number_format($stats['total_events'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon registrations">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Registrations</div>
                            <div class="stat-value"><?= number_format($stats['total_registrations'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon abstracts">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Abstracts</div>
                            <div class="stat-value"><?= number_format($stats['total_abstracts'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon reviews">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Pending Reviews</div>
                            <div class="stat-value"><?= number_format($stats['pending_reviews'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon payments">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Successful Payments</div>
                            <div class="stat-value"><?= number_format($stats['total_payments'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Revenue</div>
                            <div class="stat-value">Rp <?= number_format($stats['revenue'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <a href="/admin/users" class="btn btn-primary btn-sm">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                        <a href="/admin/events" class="btn btn-success btn-sm">
                            <i class="fas fa-calendar-plus me-2"></i>Add Event
                        </a>
                        <a href="/admin/abstracts" class="btn btn-warning btn-sm">
                            <i class="fas fa-file-alt me-2"></i>Review Abstracts
                        </a>
                        <a href="/admin/analytics" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar me-2"></i>View Analytics
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">System Overview</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Admin Dashboard</h5>
                        <p class="mb-0">Welcome to the SNIA Conference Admin Dashboard. From here you can manage users, events, registrations, and review system analytics. Use the sidebar navigation to access different management areas.</p>
                    </div>
                    
                    <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Action Required</h6>
                        <p class="mb-0">There are <strong><?= $stats['pending_reviews'] ?></strong> abstracts pending review. <a href="/admin/abstracts" class="alert-link">Review now</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<script>
// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Add any dashboard specific JavaScript here
    console.log('Admin Dashboard loaded');
    
    // Example: Update stats every 30 seconds
    // setInterval(updateStats, 30000);
});

function updateStats() {
    // Fetch updated stats via AJAX
    fetch('/admin/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update stat values
                Object.keys(data.data).forEach(key => {
                    const element = document.querySelector(`[data-stat="${key}"]`);
                    if (element) {
                        element.textContent = data.data[key];
                    }
                });
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}
</script>
<?php $this->endSection(); ?>