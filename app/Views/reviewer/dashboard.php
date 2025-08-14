<?php
$this->extend('layouts/main');
$this->section('title');
echo $title ?? 'Reviewer Dashboard';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="/css/colors.css">
<style>
/* Reviewer Dashboard Specific Styles */
:root {
    --sidebar-width: 280px;
    --header-height: 70px;
    --reviewer-primary: #7c3aed;
    --reviewer-secondary: #8b5cf6;
    --reviewer-accent: #a78bfa;
    --reviewer-warning: #f59e0b;
    --reviewer-danger: #ef4444;
    --reviewer-info: #06b6d4;
    --reviewer-success: #10b981;
    --sidebar-bg: #581c87;
    --sidebar-text: #e9d5ff;
    --sidebar-active: #8b5cf6;
}

/* Layout Structure */
.reviewer-layout {
    display: flex;
    min-height: 100vh;
    background: #faf5ff;
}

/* Sidebar */
.reviewer-sidebar {
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
    border-bottom: 1px solid #6b21a8;
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
    color: #c4b5fd;
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
    color: #c4b5fd;
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
    background: rgba(139, 92, 246, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(139, 92, 246, 0.2);
    color: white;
    border-right: 3px solid var(--sidebar-active);
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.nav-badge {
    background: var(--reviewer-warning);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    margin-left: auto;
}

/* Main Content Area */
.reviewer-main {
    flex: 1;
    margin-left: var(--sidebar-width);
    min-height: 100vh;
}

.reviewer-header {
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
    background: var(--reviewer-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.reviewer-content {
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

.stat-icon.assigned { background: var(--reviewer-primary); }
.stat-icon.completed { background: var(--reviewer-success); }
.stat-icon.pending { background: var(--reviewer-warning); }
.stat-icon.monthly { background: var(--reviewer-info); }
.stat-icon.average { background: #f59e0b; }
.stat-icon.completion { background: #10b981; }

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
    color: var(--reviewer-success);
}

.stat-change.negative {
    color: var(--reviewer-danger);
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

/* Performance Metrics */
.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.performance-metric {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--reviewer-primary);
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.875rem;
    color: #64748b;
}

/* Progress Circle */
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
}

.progress-circle svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.progress-circle-bg {
    fill: none;
    stroke: #e5e7eb;
    stroke-width: 8;
}

.progress-circle-fill {
    fill: none;
    stroke: var(--reviewer-primary);
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease-in-out;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-percentage {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--reviewer-primary);
    line-height: 1;
}

.progress-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Review Status Colors */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.pending-review {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.reviewed {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.accepted {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* Priority Indicators */
.priority-high {
    border-left: 4px solid var(--reviewer-danger);
}

.priority-medium {
    border-left: 4px solid var(--reviewer-warning);
}

.priority-low {
    border-left: 4px solid var(--reviewer-success);
}

/* Quick Stats Table */
.quick-stats-table {
    width: 100%;
    margin-top: 1rem;
}

.quick-stats-table td {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.quick-stats-table td:first-child {
    font-weight: 500;
    color: #374151;
}

.quick-stats-table td:last-child {
    text-align: right;
    font-weight: 600;
    color: var(--reviewer-primary);
}

/* Responsive Design */
@media (max-width: 768px) {
    .reviewer-sidebar {
        transform: translateX(-100%);
    }
    
    .reviewer-main {
        margin-left: 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .reviewer-header {
        padding: 0 1rem;
    }
    
    .reviewer-content {
        padding: 1rem;
    }
    
    .performance-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="reviewer-layout">
    <!-- Sidebar -->
    <aside class="reviewer-sidebar">
        <div class="sidebar-header">
            <a href="/reviewer/dashboard" class="sidebar-logo">SNIA Reviewer</a>
            <div class="sidebar-subtitle">Conference Review System</div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <div class="nav-item">
                    <a href="/reviewer/dashboard" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Review Tasks</div>
                <div class="nav-item">
                    <a href="/reviewer/assigned" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        Assigned Abstracts
                        <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                            <span class="nav-badge"><?= $stats['pending_reviews'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/reviewer/reviews" class="nav-link">
                        <i class="fas fa-check-circle"></i>
                        Completed Reviews
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
    <main class="reviewer-main">
        <!-- Header -->
        <header class="reviewer-header">
            <h1 class="header-title">Reviewer Dashboard</h1>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'R', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">
                            <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                        </div>
                        <div style="font-size: 0.875rem; color: #6b7280;">
                            Conference Reviewer
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="reviewer-content">
            <!-- Statistics Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon assigned">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Assigned</div>
                            <div class="stat-value"><?= number_format($stats['total_assigned'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Reviews Completed</div>
                            <div class="stat-value"><?= number_format($stats['completed_reviews'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Pending Reviews</div>
                            <div class="stat-value"><?= number_format($stats['pending_reviews'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon monthly">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">This Month</div>
                            <div class="stat-value"><?= number_format($stats['monthly_reviews'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon average">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Average Score</div>
                            <div class="stat-value"><?= number_format($stats['average_score'] ?? 0, 1) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completion">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Completion Rate</div>
                            <div class="stat-value"><?= number_format($stats['completion_rate'] ?? 0, 1) ?>%</div>
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
                        <a href="/reviewer/assigned" class="btn btn-primary btn-sm">
                            <i class="fas fa-clipboard-list me-2"></i>View Assigned Abstracts
                        </a>
                        <a href="/reviewer/reviews" class="btn btn-success btn-sm">
                            <i class="fas fa-history me-2"></i>Review History
                        </a>
                        <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                        <button class="btn btn-warning btn-sm" onclick="goToNextReview()">
                            <i class="fas fa-play me-2"></i>Start Next Review
                        </button>
                        <?php endif; ?>
                        <a href="/profile/edit" class="btn btn-info btn-sm">
                            <i class="fas fa-user-edit me-2"></i>Update Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <!-- Performance Overview -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Review Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="performance-grid">
                            <div class="performance-metric">
                                <div class="metric-value"><?= $stats['completed_reviews'] ?? 0 ?></div>
                                <div class="metric-label">Reviews Done</div>
                            </div>
                            <div class="performance-metric">
                                <div class="metric-value"><?= $stats['pending_reviews'] ?? 0 ?></div>
                                <div class="metric-label">Reviews Pending</div>
                            </div>
                            <div class="performance-metric">
                                <div class="metric-value"><?= number_format($stats['average_score'] ?? 0, 1) ?></div>
                                <div class="metric-label">Avg Score Given</div>
                            </div>
                            <div class="performance-metric">
                                <div class="metric-value"><?= $stats['monthly_reviews'] ?? 0 ?></div>
                                <div class="metric-label">This Month</div>
                            </div>
                        </div>
                        
                        <table class="quick-stats-table">
                            <tr>
                                <td>Total Abstracts Assigned</td>
                                <td><?= $stats['total_assigned'] ?? 0 ?></td>
                            </tr>
                            <tr>
                                <td>Reviews Completed</td>
                                <td><?= $stats['completed_reviews'] ?? 0 ?></td>
                            </tr>
                            <tr>
                                <td>Completion Rate</td>
                                <td><?= number_format($stats['completion_rate'] ?? 0, 1) ?>%</td>
                            </tr>
                            <tr>
                                <td>Average Review Score</td>
                                <td><?= number_format($stats['average_score'] ?? 0, 1) ?>/5.0</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Review Progress -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Overall Progress</h3>
                    </div>
                    <div class="card-body" style="text-align: center;">
                        <div class="progress-circle">
                            <svg viewBox="0 0 100 100">
                                <circle class="progress-circle-bg" cx="50" cy="50" r="40"></circle>
                                <circle class="progress-circle-fill" cx="50" cy="50" r="40"
                                        stroke-dasharray="<?= 2 * pi() * 40 ?>"
                                        stroke-dashoffset="<?= 2 * pi() * 40 * (1 - ($stats['completion_rate'] ?? 0) / 100) ?>">
                                </circle>
                            </svg>
                            <div class="progress-text">
                                <div class="progress-percentage"><?= number_format($stats['completion_rate'] ?? 0, 0) ?>%</div>
                                <div class="progress-label">Complete</div>
                            </div>
                        </div>
                        
                        <p style="color: #6b7280; font-size: 0.875rem; margin-top: 1rem;">
                            <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                                You have <strong><?= $stats['pending_reviews'] ?></strong> reviews remaining.
                                <br><a href="/reviewer/assigned" class="text-decoration-none">Continue reviewing</a>
                            <?php else: ?>
                                Great job! All assigned reviews are complete.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- System Notice -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Reviewer Guidelines</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Review Instructions</h6>
                        <ul class="mb-0" style="padding-left: 1.5rem;">
                            <li>Review abstracts objectively based on scientific merit, methodology, and clarity</li>
                            <li>Provide constructive feedback to help authors improve their work</li>
                            <li>Rate abstracts on a scale of 1-5 (1=Poor, 2=Fair, 3=Good, 4=Very Good, 5=Excellent)</li>
                            <li>Complete reviews within the assigned timeframe</li>
                            <li>Contact admin if you have conflicts of interest</li>
                        </ul>
                    </div>
                    
                    <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Action Required</h6>
                        <p class="mb-0">You have <strong><?= $stats['pending_reviews'] ?></strong> abstracts awaiting your review. <a href="/reviewer/assigned" class="alert-link">Start reviewing now</a></p>
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
// Reviewer Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reviewer Dashboard loaded');
    
    // Add any reviewer-specific JavaScript here
});

function goToNextReview() {
    // Redirect to the first pending review
    window.location.href = '/reviewer/assigned';
}

function updateReviewerStats() {
    // Fetch updated stats via AJAX
    fetch('/reviewer/api/stats')
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
                
                // Update progress circle
                const completionRate = data.data.completion_rate || 0;
                const circle = document.querySelector('.progress-circle-fill');
                if (circle) {
                    const circumference = 2 * Math.PI * 40;
                    const offset = circumference * (1 - completionRate / 100);
                    circle.style.strokeDashoffset = offset;
                }
            }
        })
        .catch(error => console.error('Error updating reviewer stats:', error));
}
</script>
<?php $this->endSection(); ?>