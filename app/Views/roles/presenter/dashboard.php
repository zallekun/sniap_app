<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Presenter Dashboard';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="/css/presenter/dashboard.css">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
/* Presenter Dashboard Specific Styles */
:root {
    --sidebar-width: 280px;
    --header-height: 70px;
    --presenter-primary: #059669;
    --presenter-secondary: #10b981;
    --presenter-accent: #34d399;
    --presenter-warning: #f59e0b;
    --presenter-danger: #ef4444;
    --presenter-info: #06b6d4;
    --sidebar-bg: #064e3b;
    --sidebar-text: #d1fae5;
    --sidebar-active: #10b981;
}

/* Layout Structure */
.presenter-layout {
    display: flex;
    min-height: 100vh;
    background: #f0fdfa;
}

/* Sidebar */
.presenter-sidebar {
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
    border-bottom: 1px solid #047857;
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
    color: #a7f3d0;
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
    color: #a7f3d0;
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
    background: rgba(16, 185, 129, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(16, 185, 129, 0.2);
    color: white;
    border-right: 3px solid var(--sidebar-active);
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.nav-badge {
    background: var(--presenter-warning);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    margin-left: auto;
}

/* Main Content Area */
.presenter-main {
    flex: 1;
    margin-left: var(--sidebar-width);
    min-height: 100vh;
}

.presenter-header {
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
    background: var(--presenter-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.presenter-content {
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

.stat-icon.abstracts { background: var(--presenter-primary); }
.stat-icon.accepted { background: var(--presenter-secondary); }
.stat-icon.pending { background: var(--presenter-warning); }
.stat-icon.rejected { background: var(--presenter-danger); }
.stat-icon.registrations { background: var(--presenter-info); }
.stat-icon.presentations { background: #8b5cf6; }
.stat-icon.payments { background: #f59e0b; }

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
    color: var(--presenter-secondary);
}

.stat-change.negative {
    color: var(--presenter-danger);
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

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.accepted {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge.confirmed {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.draft {
    background: #f3f4f6;
    color: #374151;
}

/* Progress Bar */
.progress-container {
    margin-bottom: 1rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--presenter-primary);
    transition: width 0.3s ease;
}

/* Timeline */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
    margin-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: var(--presenter-primary);
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 3px var(--presenter-primary);
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 1.5rem;
    width: 2px;
    height: calc(100% + 0.5rem);
    background: #e5e7eb;
}

.timeline-content {
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
}

.timeline-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.timeline-description {
    font-size: 0.875rem;
    color: #4b5563;
}

/* Responsive Design */
@media (max-width: 768px) {
    .presenter-sidebar {
        transform: translateX(-100%);
    }
    
    .presenter-main {
        margin-left: 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .presenter-header {
        padding: 0 1rem;
    }
    
    .presenter-content {
        padding: 1rem;
    }
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="presenter-layout">
    <!-- Sidebar -->
    <aside class="presenter-sidebar">
        <div class="sidebar-header">
            <a href="/presenter/dashboard" class="sidebar-logo">SNIA Presenter</a>
            <div class="sidebar-subtitle">Conference Management</div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <div class="nav-item">
                    <a href="/presenter/dashboard" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">My Content</div>
                <div class="nav-item">
                    <a href="/presenter/abstracts" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        My Abstracts
                        <?php if (isset($stats['pending_abstracts']) && $stats['pending_abstracts'] > 0): ?>
                            <span class="nav-badge"><?= $stats['pending_abstracts'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/presenter/presentations" class="nav-link">
                        <i class="fas fa-presentation"></i>
                        Presentations
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Events</div>
                <div class="nav-item">
                    <a href="/presenter/registrations" class="nav-link">
                        <i class="fas fa-user-check"></i>
                        My Registrations
                        <?php if (isset($stats['pending_payments']) && $stats['pending_payments'] > 0): ?>
                            <span class="nav-badge"><?= $stats['pending_payments'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/presenter/schedule" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        Presentation Schedule
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Account</div>
                <div class="nav-item">
                    <a href="/profile/edit" class="nav-link">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/dashboard" class="nav-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Main Dashboard
                    </a>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="presenter-main">
        <!-- Header -->
        <header class="presenter-header">
            <h1 class="header-title">Presenter Dashboard</h1>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'P', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">
                            <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                        </div>
                        <div style="font-size: 0.875rem; color: #6b7280;">
                            Conference Presenter
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="presenter-content">
            <!-- Statistics Overview -->
            <div class="stats-grid">
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
                        <div class="stat-icon accepted">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Accepted Abstracts</div>
                            <div class="stat-value"><?= number_format($stats['accepted_abstracts'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Pending Review</div>
                            <div class="stat-value"><?= number_format($stats['pending_abstracts'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon registrations">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Active Registrations</div>
                            <div class="stat-value"><?= number_format($stats['active_registrations'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon presentations">
                            <i class="fas fa-presentation"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Upcoming Presentations</div>
                            <div class="stat-value"><?= number_format($stats['upcoming_presentations'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon payments">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Pending Payments</div>
                            <div class="stat-value"><?= number_format($stats['pending_payments'] ?? 0) ?></div>
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
                        <a href="/presenter/abstracts" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i>Submit New Abstract
                        </a>
                        <a href="/presenter/presentations" class="btn btn-primary btn-sm">
                            <i class="fas fa-presentation me-2"></i>Manage Presentations
                        </a>
                        <a href="/presenter/registrations" class="btn btn-warning btn-sm">
                            <i class="fas fa-credit-card me-2"></i>Check Payment Status
                        </a>
                        <a href="/presenter/schedule" class="btn btn-info btn-sm">
                            <i class="fas fa-calendar-check me-2"></i>View Schedule
                        </a>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Abstract Progress -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Abstract Submission Progress</h3>
                    </div>
                    <div class="card-body">
                        <?php 
                        $totalAbstracts = $stats['total_abstracts'] ?? 0;
                        $acceptedAbstracts = $stats['accepted_abstracts'] ?? 0;
                        $acceptanceRate = $totalAbstracts > 0 ? ($acceptedAbstracts / $totalAbstracts) * 100 : 0;
                        ?>
                        
                        <div class="progress-container">
                            <div class="progress-label">
                                <span>Acceptance Rate</span>
                                <span><?= number_format($acceptanceRate, 1) ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $acceptanceRate ?>%"></div>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                            <div style="text-align: center;">
                                <div class="stat-value" style="font-size: 1.5rem; color: var(--presenter-secondary);">
                                    <?= $stats['accepted_abstracts'] ?? 0 ?>
                                </div>
                                <div class="stat-label">Accepted</div>
                            </div>
                            <div style="text-align: center;">
                                <div class="stat-value" style="font-size: 1.5rem; color: var(--presenter-warning);">
                                    <?= $stats['pending_abstracts'] ?? 0 ?>
                                </div>
                                <div class="stat-label">Pending</div>
                            </div>
                            <div style="text-align: center;">
                                <div class="stat-value" style="font-size: 1.5rem; color: var(--presenter-danger);">
                                    <?= $stats['rejected_abstracts'] ?? 0 ?>
                                </div>
                                <div class="stat-label">Rejected</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="timeline-title">Welcome to SNIA Presenter Dashboard</div>
                                    <div class="timeline-date">Today</div>
                                    <div class="timeline-description">
                                        You can now manage your abstracts, presentations, and registrations from this dashboard.
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($stats['pending_payments']) && $stats['pending_payments'] > 0): ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="timeline-title">Payment Required</div>
                                    <div class="timeline-date">Action Required</div>
                                    <div class="timeline-description">
                                        You have <?= $stats['pending_payments'] ?> registration(s) with pending payments. 
                                        <a href="/presenter/registrations" class="text-decoration-none">Complete payment</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($stats['upcoming_presentations']) && $stats['upcoming_presentations'] > 0): ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="timeline-title">Upcoming Presentations</div>
                                    <div class="timeline-date">Schedule</div>
                                    <div class="timeline-description">
                                        You have <?= $stats['upcoming_presentations'] ?> upcoming presentation(s). 
                                        <a href="/presenter/schedule" class="text-decoration-none">View schedule</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<script>
// Presenter Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Presenter Dashboard loaded');
    
    // Add any presenter-specific JavaScript here
    // Example: Auto-refresh stats every 30 seconds
    // setInterval(updatePresenterStats, 30000);
});

function updatePresenterStats() {
    // Fetch updated stats via AJAX
    fetch('/presenter/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update stat values
                Object.keys(data.data).forEach(key => {
                    const elements = document.querySelectorAll(`[data-stat="${key}"]`);
                    elements.forEach(element => {
                        element.textContent = data.data[key];
                    });
                });
            }
        })
        .catch(error => console.error('Error updating presenter stats:', error));
}
</script>
<?php $this->endSection(); ?>