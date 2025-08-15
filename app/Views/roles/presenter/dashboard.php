<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Presenter Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Sidebar -->
    <aside class="presenter-sidebar">
        <div class="sidebar-header">
            <a href="/presenter/dashboard" class="sidebar-logo">SNIA Presenter</a>
            <div class="sidebar-subtitle">Conference Presentation</div>
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
                <div class="nav-section-title">Presentations</div>
                <div class="nav-item">
                    <a href="/presenter/abstracts" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        Abstract Submissions
                        <?php if (isset($stats['pending_abstracts']) && $stats['pending_abstracts'] > 0): ?>
                            <span class="nav-badge"><?= $stats['pending_abstracts'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/presenter/presentations" class="nav-link">
                        <i class="fas fa-presentation-screen"></i>
                        My Presentations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/presenter/schedule" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        Presentation Schedule
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Registration</div>
                <div class="nav-item">
                    <a href="/presenter/registrations" class="nav-link">
                        <i class="fas fa-ticket-alt"></i>
                        My Registrations
                        <?php if (isset($stats['upcoming_events']) && $stats['upcoming_events'] > 0): ?>
                            <span class="nav-badge nav-badge-success"><?= $stats['upcoming_events'] ?></span>
                        <?php endif; ?>
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
    <main class="presenter-main">
        <!-- Header -->
        <header class="presenter-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Presenter Dashboard</h1>
            </div>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'P', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= esc($user['first_name'] ?? 'Presenter') ?> <?= esc($user['last_name'] ?? '') ?></div>
                        <div class="user-role">Presenter</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="presenter-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['total_abstracts'] ?? 0 ?></h3>
                        <p>Total Abstracts</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['accepted_abstracts'] ?? 0 ?></h3>
                        <p>Accepted Abstracts</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['pending_abstracts'] ?? 0 ?></h3>
                        <p>Pending Review</p>
                    </div>
                    <div class="stat-trend neutral">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['upcoming_events'] ?? 0 ?></h3>
                        <p>Upcoming Events</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Content -->
            <div class="dashboard-grid">
                <!-- Recent Abstracts -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Recent Abstract Submissions</h3>
                        <a href="/presenter/abstracts" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Submit New Abstract
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="abstracts-list">
                            <!-- Sample abstract items - these would come from database -->
                            <div class="abstract-item">
                                <div class="abstract-status status-pending"></div>
                                <div class="abstract-content">
                                    <h5>AI in Healthcare: Current Trends and Future Prospects</h5>
                                    <p class="abstract-meta">
                                        <span>Submitted: <?= date('M j, Y') ?></span>
                                        <span class="status-badge status-pending">Under Review</span>
                                    </p>
                                </div>
                                <div class="abstract-actions">
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>

                            <div class="abstract-item">
                                <div class="abstract-status status-accepted"></div>
                                <div class="abstract-content">
                                    <h5>Machine Learning Applications in Data Storage</h5>
                                    <p class="abstract-meta">
                                        <span>Submitted: <?= date('M j, Y', strtotime('-1 week')) ?></span>
                                        <span class="status-badge status-accepted">Accepted</span>
                                    </p>
                                </div>
                                <div class="abstract-actions">
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>

                            <div class="abstract-item">
                                <div class="abstract-status status-revision"></div>
                                <div class="abstract-content">
                                    <h5>Cloud Storage Security: Best Practices</h5>
                                    <p class="abstract-meta">
                                        <span>Submitted: <?= date('M j, Y', strtotime('-2 weeks')) ?></span>
                                        <span class="status-badge status-revision">Needs Revision</span>
                                    </p>
                                </div>
                                <div class="abstract-actions">
                                    <button class="btn btn-warning btn-sm">Revise</button>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/presenter/abstracts" class="btn btn-outline-primary">View All Abstracts</a>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Presentations -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Upcoming Presentations</h3>
                        <a href="/presenter/schedule" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar"></i> View Schedule
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="presentations-list">
                            <!-- Sample presentation items -->
                            <div class="presentation-item">
                                <div class="presentation-date">
                                    <div class="day"><?= date('d') ?></div>
                                    <div class="month"><?= date('M') ?></div>
                                </div>
                                <div class="presentation-content">
                                    <h5>Machine Learning Applications in Data Storage</h5>
                                    <p class="presentation-meta">
                                        <i class="fas fa-clock"></i> 10:30 AM - 11:00 AM
                                        <i class="fas fa-map-marker-alt"></i> Room A-101
                                    </p>
                                </div>
                                <div class="presentation-actions">
                                    <button class="btn btn-success btn-sm">Ready</button>
                                </div>
                            </div>

                            <div class="presentation-item">
                                <div class="presentation-date">
                                    <div class="day"><?= date('d', strtotime('+1 day')) ?></div>
                                    <div class="month"><?= date('M', strtotime('+1 day')) ?></div>
                                </div>
                                <div class="presentation-content">
                                    <h5>Workshop: AI Implementation Strategies</h5>
                                    <p class="presentation-meta">
                                        <i class="fas fa-clock"></i> 2:00 PM - 3:30 PM
                                        <i class="fas fa-map-marker-alt"></i> Workshop Room B
                                    </p>
                                </div>
                                <div class="presentation-actions">
                                    <button class="btn btn-warning btn-sm">Prepare</button>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <a href="/presenter/presentations" class="btn btn-outline-primary">View All Presentations</a>
                        </div>
                    </div>
                </div>

                <!-- Registration Status -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Event Registrations</h3>
                        <a href="/presenter/registrations" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-ticket-alt"></i> View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="registrations-list">
                            <div class="registration-item">
                                <div class="registration-status status-confirmed"></div>
                                <div class="registration-content">
                                    <h5>SNIA Conference 2025</h5>
                                    <p class="registration-meta">
                                        <span>Registration: Confirmed</span>
                                        <span>Payment: Completed</span>
                                    </p>
                                </div>
                                <div class="registration-actions">
                                    <button class="btn btn-outline-primary btn-sm">View Details</button>
                                </div>
                            </div>
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
                            <a href="/presenter/abstracts" class="quick-action-btn">
                                <i class="fas fa-plus"></i>
                                <span>Submit Abstract</span>
                            </a>
                            <a href="/presenter/presentations" class="quick-action-btn">
                                <i class="fas fa-presentation-screen"></i>
                                <span>Manage Presentations</span>
                            </a>
                            <a href="/presenter/schedule" class="quick-action-btn">
                                <i class="fas fa-calendar"></i>
                                <span>View Schedule</span>
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
<script>
// Presenter dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    loadPresenterStats();
    loadRecentAbstracts();
    loadUpcomingPresentations();
});

function toggleSidebar() {
    document.querySelector('.presenter-sidebar').classList.toggle('collapsed');
}

function loadPresenterStats() {
    // Load presenter statistics via AJAX
    fetch('/presenter/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsCards(data.stats);
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

function loadRecentAbstracts() {
    // Load recent abstracts via AJAX
    fetch('/presenter/api/abstracts?recent=true')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateAbstractsList(data.abstracts);
            }
        })
        .catch(error => console.error('Error loading abstracts:', error));
}

function loadUpcomingPresentations() {
    // Load upcoming presentations via AJAX
    console.log('Loading upcoming presentations...');
}

function updateStatsCards(stats) {
    // Update statistics cards with real data
    console.log('Updating stats cards...', stats);
}

function updateAbstractsList(abstracts) {
    // Update abstracts list with real data
    console.log('Updating abstracts list...', abstracts);
}

// Handle responsive sidebar
window.addEventListener('resize', function() {
    if (window.innerWidth <= 768) {
        document.querySelector('.presenter-sidebar').classList.add('collapsed');
    } else {
        document.querySelector('.presenter-sidebar').classList.remove('collapsed');
    }
});
</script>
<?= $this->endSection() ?>