<?= $this->extend('shared/layouts/reviewer_layout') ?>

<?= $this->section('title') ?>Reviewer Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="reviewer-layout">
    <!-- Sidebar -->
    <aside class="reviewer-sidebar">
        <div class="sidebar-header">
            <a href="/reviewer/dashboard" class="sidebar-logo">SNIA Reviewer</a>
            <div class="sidebar-subtitle">Abstract Review System</div>
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
                        <i class="fas fa-tasks"></i>
                        Assigned Abstracts
                        <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                            <span class="nav-badge nav-badge-warning"><?= $stats['pending_reviews'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/reviewer/reviews" class="nav-link">
                        <i class="fas fa-star"></i>
                        My Reviews
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
                    <a href="/logout" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="reviewer-main">
        <!-- Header -->
        <header class="reviewer-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Reviewer Dashboard</h1>
            </div>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'R', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= esc($user['first_name'] ?? 'Reviewer') ?> <?= esc($user['last_name'] ?? '') ?></div>
                        <div class="user-role">Reviewer</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="reviewer-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['total_assignments'] ?? 0 ?></h3>
                        <p>Total Assignments</p>
                    </div>
                    <div class="stat-trend neutral">
                        <i class="fas fa-list"></i>
                    </div>
                </div>

                <div class="stat-card urgent">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['pending_reviews'] ?? 0 ?></h3>
                        <p>Pending Reviews</p>
                    </div>
                    <div class="stat-trend warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['completed_reviews'] ?? 0 ?></h3>
                        <p>Completed Reviews</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($stats['avg_rating'] ?? 0, 1) ?></h3>
                        <p>Average Rating Given</p>
                    </div>
                    <div class="stat-trend neutral">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Pending Reviews -->
                <div class="dashboard-card priority">
                    <div class="card-header">
                        <h3>Urgent Reviews</h3>
                        <span class="priority-badge">High Priority</span>
                    </div>
                    <div class="card-body">
                        <div class="reviews-list">
                            <!-- Sample review items - these would come from database -->
                            <div class="review-item urgent">
                                <div class="review-status status-overdue"></div>
                                <div class="review-content">
                                    <h5>AI Applications in Medical Diagnostics</h5>
                                    <p class="review-meta">
                                        <span><i class="fas fa-user"></i> Dr. Sarah Johnson</span>
                                        <span><i class="fas fa-clock"></i> Due: <?= date('M j, Y', strtotime('-1 day')) ?></span>
                                        <span class="status-badge status-overdue">Overdue</span>
                                    </p>
                                </div>
                                <div class="review-actions">
                                    <a href="/reviewer/review/1" class="btn btn-danger btn-sm">Review Now</a>
                                </div>
                            </div>

                            <div class="review-item">
                                <div class="review-status status-pending"></div>
                                <div class="review-content">
                                    <h5>Machine Learning in Data Storage Optimization</h5>
                                    <p class="review-meta">
                                        <span><i class="fas fa-user"></i> Prof. Michael Chen</span>
                                        <span><i class="fas fa-clock"></i> Due: <?= date('M j, Y', strtotime('+2 days')) ?></span>
                                        <span class="status-badge status-pending">Pending</span>
                                    </p>
                                </div>
                                <div class="review-actions">
                                    <a href="/reviewer/review/2" class="btn btn-warning btn-sm">Review</a>
                                </div>
                            </div>

                            <div class="review-item">
                                <div class="review-status status-pending"></div>
                                <div class="review-content">
                                    <h5>Cloud Security: Advanced Encryption Methods</h5>
                                    <p class="review-meta">
                                        <span><i class="fas fa-user"></i> Dr. Emily Wang</span>
                                        <span><i class="fas fa-clock"></i> Due: <?= date('M j, Y', strtotime('+5 days')) ?></span>
                                        <span class="status-badge status-pending">Pending</span>
                                    </p>
                                </div>
                                <div class="review-actions">
                                    <a href="/reviewer/review/3" class="btn btn-primary btn-sm">Review</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/reviewer/assigned" class="btn btn-outline-primary">View All Assignments</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Completed Reviews</h3>
                        <a href="/reviewer/reviews" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history"></i> View History
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="completed-reviews-list">
                            <div class="completed-review-item">
                                <div class="review-rating">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-value">4.0</span>
                                </div>
                                <div class="review-content">
                                    <h5>Blockchain Technology in Healthcare</h5>
                                    <p class="review-meta">
                                        <span>Reviewed: <?= date('M j, Y', strtotime('-1 day')) ?></span>
                                        <span class="recommendation-badge accepted">Accepted</span>
                                    </p>
                                </div>
                            </div>

                            <div class="completed-review-item">
                                <div class="review-rating">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-value">3.0</span>
                                </div>
                                <div class="review-content">
                                    <h5>IoT Security Framework Analysis</h5>
                                    <p class="review-meta">
                                        <span>Reviewed: <?= date('M j, Y', strtotime('-3 days')) ?></span>
                                        <span class="recommendation-badge revision">Needs Revision</span>
                                    </p>
                                </div>
                            </div>

                            <div class="completed-review-item">
                                <div class="review-rating">
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-value">2.0</span>
                                </div>
                                <div class="review-content">
                                    <h5>Outdated Network Protocol Implementation</h5>
                                    <p class="review-meta">
                                        <span>Reviewed: <?= date('M j, Y', strtotime('-1 week')) ?></span>
                                        <span class="recommendation-badge rejected">Rejected</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Performance -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>My Review Performance</h3>
                    </div>
                    <div class="card-body">
                        <div class="performance-metrics">
                            <div class="metric-item">
                                <div class="metric-label">Reviews This Month</div>
                                <div class="metric-value"><?= $stats['reviews_this_month'] ?? 0 ?></div>
                                <div class="metric-change positive">+3 from last month</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Average Review Time</div>
                                <div class="metric-value"><?= $stats['avg_review_time'] ?? '2.5' ?> days</div>
                                <div class="metric-change positive">-0.5 days from avg</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Review Quality Score</div>
                                <div class="metric-value"><?= number_format($stats['quality_score'] ?? 4.2, 1) ?>/5.0</div>
                                <div class="metric-change positive">Excellent</div>
                            </div>
                        </div>

                        <div class="performance-chart">
                            <canvas id="reviewPerformanceChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="/reviewer/assigned" class="quick-action-btn priority">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Urgent Reviews</span>
                                <?php if (isset($stats['overdue_reviews']) && $stats['overdue_reviews'] > 0): ?>
                                    <span class="action-badge"><?= $stats['overdue_reviews'] ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="/reviewer/reviews" class="quick-action-btn">
                                <i class="fas fa-star"></i>
                                <span>My Reviews</span>
                            </a>
                            <a href="/reviewer/guidelines" class="quick-action-btn">
                                <i class="fas fa-book"></i>
                                <span>Review Guidelines</span>
                            </a>
                            <a href="/profile/edit" class="quick-action-btn">
                                <i class="fas fa-user-edit"></i>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Reviewer dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    loadReviewerStats();
    loadPendingReviews();
    loadRecentReviews();
    initPerformanceChart();
});

function toggleSidebar() {
    document.querySelector('.reviewer-sidebar').classList.toggle('collapsed');
}

function loadReviewerStats() {
    // Load reviewer statistics via AJAX
    fetch('/reviewer/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsCards(data.stats);
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

function loadPendingReviews() {
    // Load pending reviews via AJAX
    fetch('/reviewer/api/assigned?status=pending')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePendingReviewsList(data.reviews);
            }
        })
        .catch(error => console.error('Error loading pending reviews:', error));
}

function loadRecentReviews() {
    // Load recent completed reviews via AJAX
    console.log('Loading recent reviews...');
}

function initPerformanceChart() {
    const ctx = document.getElementById('reviewPerformanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Reviews Completed',
                data: [5, 8, 6, 12, 9, 15],
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateStatsCards(stats) {
    // Update statistics cards with real data
    console.log('Updating reviewer stats cards...', stats);
}

function updatePendingReviewsList(reviews) {
    // Update pending reviews list with real data
    console.log('Updating pending reviews list...', reviews);
}

// Handle responsive sidebar
window.addEventListener('resize', function() {
    if (window.innerWidth <= 768) {
        document.querySelector('.reviewer-sidebar').classList.add('collapsed');
    } else {
        document.querySelector('.reviewer-sidebar').classList.remove('collapsed');
    }
});

// Auto-refresh pending reviews every 5 minutes
setInterval(function() {
    loadPendingReviews();
}, 300000);
</script>
<?= $this->endSection() ?>