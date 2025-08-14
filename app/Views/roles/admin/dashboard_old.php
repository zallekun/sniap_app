<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Admin Dashboard';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="/css/admin/dashboard.css">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    --admin-secondary: #3b82f6;
    --admin-success: #10b981;
    --admin-warning: #f59e0b;
    --admin-danger: #ef4444;
    --admin-info: #06b6d4;
    --sidebar-bg: #1f2937;
    --sidebar-text: #d1d5db;
    --sidebar-active: #3b82f6;
}

/* Layout Structure */
.admin-layout {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
}

/* Sidebar */
.admin-sidebar {
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid #374151;
    text-align: center;
}

.sidebar-logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
}

.sidebar-subtitle {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-section-title {
    padding: 0.5rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
    margin-bottom: 0.5rem;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: var(--sidebar-text);
    text-decoration: none;
    transition: all 0.2s ease;
    position: relative;
}

.nav-link:hover {
    background: rgba(59, 130, 246, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(59, 130, 246, 0.2);
    color: white;
    border-right: 3px solid var(--sidebar-active);
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.nav-badge {
    background: var(--admin-danger);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    margin-left: auto;
}

/* Main Content Area */
.admin-main {
    flex: 1;
    margin-left: var(--sidebar-width);
    min-height: 100vh;
}

.admin-header {
    background: white;
    height: var(--header-height);
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.admin-content {
    padding: 2rem;
}

/* Dashboard Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: between;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-right: 1rem;
}

.stat-icon.users { background: var(--admin-info); }
.stat-icon.events { background: var(--admin-primary); }
.stat-icon.registrations { background: var(--admin-success); }
.stat-icon.abstracts { background: var(--admin-warning); }
.stat-icon.reviews { background: var(--admin-danger); }
.stat-icon.payments { background: #8b5cf6; }
.stat-icon.revenue { background: #10b981; }

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-change {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.stat-change.positive {
    color: var(--admin-success);
}

.stat-change.negative {
    color: var(--admin-danger);
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }
    
    .admin-main {
        margin-left: 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-header {
        padding: 0 1rem;
    }
    
    .admin-content {
        padding: 1rem;
    }
}
</style>
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