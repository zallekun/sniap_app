<?= $this->extend('shared/layouts/reviewer_layout') ?>

<?= $this->section('title') ?>Reviewer Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Statistics Cards -->
    <div class="reviewer-stats-grid">
        <div class="reviewer-stat-card">
            <div class="reviewer-stat-icon assigned">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="reviewer-stat-content">
                <h3><?= number_format($stats['total_assigned'] ?? 0) ?></h3>
                <p>Total Assigned</p>
            </div>
        </div>
    
        <div class="reviewer-stat-card">
            <div class="reviewer-stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="reviewer-stat-content">
                <h3><?= number_format($stats['completed_reviews'] ?? 0) ?></h3>
                <p>Completed</p>
            </div>
        </div>
        
        <div class="reviewer-stat-card">
            <div class="reviewer-stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="reviewer-stat-content">
                <h3><?= number_format($stats['pending_reviews'] ?? 0) ?></h3>
                <p>Pending Reviews</p>
            </div>
        </div>
        
        <div class="reviewer-stat-card">
            <div class="reviewer-stat-icon assigned">
                <i class="fas fa-star"></i>
            </div>
            <div class="reviewer-stat-content">
                <h3><?= number_format($stats['average_score'] ?? 0, 1) ?></h3>
                <p>Average Score</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="reviewer-quick-actions mb-4">
        <h5 class="mb-3">Quick Actions</h5>
        <div class="reviewer-quick-actions-grid">
            <a href="/reviewer/assigned" class="reviewer-action-card">
                <div class="reviewer-action-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="reviewer-action-title">View Assigned</div>
                <div class="reviewer-action-description">Review abstracts</div>
            </a>
            <a href="/reviewer/reviews" class="reviewer-action-card">
                <div class="reviewer-action-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="reviewer-action-title">My Reviews</div>
                <div class="reviewer-action-description">View completed</div>
            </a>
            <a href="/events" class="reviewer-action-card">
                <div class="reviewer-action-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="reviewer-action-title">Event Schedule</div>
                <div class="reviewer-action-description">Conference timeline</div>
            </a>
            <a href="/profile/edit" class="reviewer-action-card">
                <div class="reviewer-action-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="reviewer-action-title">Update Profile</div>
                <div class="reviewer-action-description">Edit information</div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Assignments</h5>
                </div>
                <div class="card-body">
                <?php if (empty($assigned_abstracts ?? [])): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">No assignments yet</h6>
                        <p class="text-muted">You will receive abstracts to review here</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Abstract Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Assigned</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($assigned_abstracts ?? [], 0, 5) as $abstract): ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?= esc($abstract['title']) ?></div>
                                        <small class="text-muted"><?= esc($abstract['event_title'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <div><?= esc($abstract['first_name']) ?> <?= esc($abstract['last_name']) ?></div>
                                        <small class="text-muted"><?= esc($abstract['email']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($abstract['reviewed_at']): ?>
                                            <span class="badge bg-success">Reviewed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('M j, Y', strtotime($abstract['assigned_at'] ?? '')) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($abstract['reviewed_at']): ?>
                                            <a href="/reviewer/review/<?= $abstract['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                                View Review
                                            </a>
                                        <?php else: ?>
                                            <a href="/reviewer/review/<?= $abstract['id'] ?>" class="btn btn-sm btn-primary">
                                                Start Review
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/reviewer/assigned" class="btn btn-outline-primary">
                            View All Assignments
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Review Performance</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Completion Rate</span>
                        <span class="small"><?= number_format($stats['completion_rate'] ?? 0, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= $stats['completion_rate'] ?? 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="small">This Month</span>
                        <span class="small fw-semibold"><?= $stats['monthly_reviews'] ?? 0 ?> reviews</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="small">Average Score Given</span>
                        <span class="small fw-semibold"><?= number_format($stats['average_score'] ?? 0, 1) ?>/100</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Review Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Check methodology & approach
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Verify originality & contribution
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Assess clarity & presentation
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Provide constructive feedback
                    </li>
                </ul>
                
                <div class="d-grid mt-3">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-pdf me-2"></i>Review Guidelines
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>