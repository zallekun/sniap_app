<?= $this->extend('shared/layouts/presenter_layout') ?>

<?= $this->section('title') ?>Presenter Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-microphone me-2"></i>
                Presenter Dashboard
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/presenter/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Overview</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-outline-secondary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
    </div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon abstracts">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= number_format($stats['total_abstracts'] ?? 0) ?></h3>
                    <p>Total Abstracts</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon presentations">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= number_format($stats['accepted_abstracts'] ?? 0) ?></h3>
                    <p>Accepted Abstracts</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon events">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= number_format($stats['pending_abstracts'] ?? 0) ?></h3>
                    <p>Pending Review</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon certificates">
                    <i class="fas fa-presentation"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= number_format($stats['upcoming_presentations'] ?? 0) ?></h3>
                    <p>Upcoming Presentations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </h5>
        </div>
        <div class="card-body">
            <div class="presenter-quick-actions-grid">
                <a href="/presenter/abstracts" class="presenter-action-card">
                    <div class="presenter-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="presenter-action-title">Submit Abstract</div>
                    <div class="presenter-action-description">Create new</div>
                </a>
                
                <a href="/presenter/presentations" class="presenter-action-card">
                    <div class="presenter-action-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="presenter-action-title">My Presentations</div>
                    <div class="presenter-action-description">View all</div>
                </a>
                
                <a href="/presenter/registrations" class="presenter-action-card">
                    <div class="presenter-action-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="presenter-action-title">My Events</div>
                    <div class="presenter-action-description">Registrations</div>
                </a>
                
                <a href="/events" class="presenter-action-card">
                    <div class="presenter-action-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="presenter-action-title">Event Schedule</div>
                    <div class="presenter-action-description">Browse events</div>
                </a>
            </div>
        </div>
    </div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Recent Abstracts</h5>
            </div>
            <div class="card-body">
                <?php if (empty($abstracts ?? [])): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">No abstracts submitted yet</h6>
                        <p class="text-muted">Submit your first abstract to get started</p>
                        <a href="/presenter/abstracts" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Submit Abstract
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($abstracts ?? [], 0, 5) as $abstract): ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?= esc($abstract['title']) ?></div>
                                        <small class="text-muted"><?= esc($abstract['event_title'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = match($abstract['review_status'] ?? 'pending') {
                                            'accepted' => 'bg-success',
                                            'rejected' => 'bg-danger', 
                                            'under_review' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($abstract['review_status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('M j, Y', strtotime($abstract['submitted_at'] ?? '')) ?></small>
                                    </td>
                                    <td>
                                        <a href="/presenter/abstracts/<?= $abstract['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/presenter/abstracts" class="btn btn-outline-primary">
                            View All Abstracts
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Upcoming Events</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>SNIA 2024</h6>
                    <p class="mb-2">Seminar Nasional Informatika</p>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>December 15, 2024<br>
                        <i class="fas fa-map-marker-alt me-1"></i>Gedung Serbaguna
                    </small>
                </div>
                
                <div class="d-grid">
                    <a href="/event-schedule" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>View Full Schedule
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Help & Support</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-question-circle me-2"></i>How to submit abstract
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-file-pdf me-2"></i>Submission guidelines
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-envelope me-2"></i>Contact support
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>