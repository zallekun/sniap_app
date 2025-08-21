<?= $this->extend('shared/layouts/reviewer_layout') ?>

<?= $this->section('title') ?>My Reviews<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Review History</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/reviewer/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Review History</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="filterReviews('all')">All Reviews</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterReviews('accept')">Accepted</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterReviews('minor_revision')">Minor Revision</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterReviews('major_revision')">Major Revision</a></li>
                    <li><a class="dropdown-item" href="#" onclick="filterReviews('reject')">Rejected</a></li>
                </ul>
            </div>
            <button class="btn btn-outline-secondary" onclick="refreshReviews()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3><?= count($completed_reviews ?? []) ?></h3>
                    <p>Total Reviews</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon assigned">
                    <i class="fas fa-star"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3>
                        <?php 
                        $totalScore = 0;
                        $reviewCount = count($completed_reviews ?? []);
                        if ($reviewCount > 0) {
                            foreach ($completed_reviews as $review) {
                                $totalScore += $review['score'] ?? 0;
                            }
                            echo number_format($totalScore / $reviewCount, 1);
                        } else {
                            echo '0.0';
                        }
                        ?>
                    </h3>
                    <p>Average Score</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon pending">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3>
                        <?php 
                        $thisMonth = 0;
                        $currentMonth = date('Y-m');
                        if (isset($completed_reviews)) {
                            foreach ($completed_reviews as $review) {
                                if (isset($review['reviewed_at']) && strpos($review['reviewed_at'], $currentMonth) === 0) {
                                    $thisMonth++;
                                }
                            }
                        }
                        echo $thisMonth;
                        ?>
                    </h3>
                    <p>This Month</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon completed" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3>
                        <?php 
                        $accepted = 0;
                        if (isset($completed_reviews)) {
                            foreach ($completed_reviews as $review) {
                                if (($review['recommendation'] ?? '') === 'accept') {
                                    $accepted++;
                                }
                            }
                        }
                        echo $accepted;
                        ?>
                    </h3>
                    <p>Accepted</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Completed Reviews
            </h5>
            <span class="badge bg-success"><?= count($completed_reviews ?? []) ?> reviews</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($completed_reviews)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-history text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Reviews Completed</h4>
                    <p class="text-muted">You haven't completed any reviews yet. Check your <a href="/reviewer/assigned" class="text-decoration-none">assigned abstracts</a> to get started.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="reviewsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Abstract Title</th>
                                <th class="border-0">Author</th>
                                <th class="border-0">Event</th>
                                <th class="border-0">Score</th>
                                <th class="border-0">Recommendation</th>
                                <th class="border-0">Reviewed Date</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completed_reviews as $review): ?>
                                <tr data-recommendation="<?= esc($review['recommendation'] ?? '') ?>">
                                    <td class="align-middle">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= esc($review['title']) ?></h6>
                                            <small class="text-muted">Review ID: #<?= $review['id'] ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-semibold"><?= esc($review['first_name'] ?? '') ?> <?= esc($review['last_name'] ?? '') ?></div>
                                        <?php if (!empty($review['email'])): ?>
                                            <small class="text-muted"><?= esc($review['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-light text-dark"><?= esc($review['event_title'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $score = $review['score'] ?? 0;
                                        $scoreClass = '';
                                        if ($score >= 8) $scoreClass = 'bg-success';
                                        elseif ($score >= 6) $scoreClass = 'bg-primary';
                                        elseif ($score >= 4) $scoreClass = 'bg-warning';
                                        else $scoreClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $scoreClass ?> px-3 py-2">
                                            <i class="fas fa-star me-1"></i><?= $score ?>/10
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $recommendation = $review['recommendation'] ?? '';
                                        $badgeClass = '';
                                        $icon = '';
                                        $label = '';
                                        
                                        switch ($recommendation) {
                                            case 'accept':
                                                $badgeClass = 'bg-success';
                                                $icon = 'fa-check';
                                                $label = 'Accept';
                                                break;
                                            case 'minor_revision':
                                                $badgeClass = 'bg-warning';
                                                $icon = 'fa-edit';
                                                $label = 'Minor Revision';
                                                break;
                                            case 'major_revision':
                                                $badgeClass = 'bg-info';
                                                $icon = 'fa-tools';
                                                $label = 'Major Revision';
                                                break;
                                            case 'reject':
                                                $badgeClass = 'bg-danger';
                                                $icon = 'fa-times';
                                                $label = 'Reject';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                $icon = 'fa-question';
                                                $label = 'Unknown';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <i class="fas <?= $icon ?> me-1"></i><?= $label ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!empty($review['reviewed_at'])): ?>
                                            <div class="text-sm">
                                                <?= date('M d, Y', strtotime($review['reviewed_at'])) ?>
                                            </div>
                                            <small class="text-muted"><?= date('H:i', strtotime($review['reviewed_at'])) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewReviewDetails(<?= $review['id'] ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    onclick="exportReview(<?= $review['id'] ?>)"
                                                    title="Export Review">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Review Details Modal -->
<div class="modal fade" id="reviewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('reviewer_scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Review history page loaded');
    
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});

function refreshReviews() {
    // Show loading state
    const refreshBtn = document.querySelector('button[onclick="refreshReviews()"]');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload after a brief delay to show loading state
    setTimeout(() => {
        location.reload();
    }, 500);
}

function filterReviews(recommendation) {
    const table = document.getElementById('reviewsTable');
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (recommendation === 'all' || row.dataset.recommendation === recommendation) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update filter button text
    const filterBtn = document.querySelector('.dropdown-toggle');
    const filterText = recommendation === 'all' ? 'All Reviews' : 
                      recommendation.charAt(0).toUpperCase() + recommendation.slice(1).replace('_', ' ');
    filterBtn.innerHTML = `<i class="fas fa-filter me-1"></i> ${filterText}`;
    
    // Update badge count
    const badge = document.querySelector('.card-header .badge');
    badge.textContent = `${visibleCount} reviews`;
    
    // Show/hide empty state if no results
    const tbody = table.querySelector('tbody');
    if (visibleCount === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No reviews found</h5>
                    <p class="text-muted">No reviews match the selected filter criteria.</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="filterReviews('all')">Show All Reviews</button>
                </td>
            </tr>
        `;
    }
}

function viewReviewDetails(reviewId) {
    const modal = new bootstrap.Modal(document.getElementById('reviewDetailsModal'));
    const content = document.getElementById('reviewDetailsContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading review details...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch review details
    fetch(`/reviewer/api/review-details/${reviewId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const review = data.review;
            const scoreClass = review.score >= 8 ? 'bg-success' : 
                             review.score >= 6 ? 'bg-primary' :
                             review.score >= 4 ? 'bg-warning' : 'bg-danger';
            const recClass = getRecommendationClass(review.recommendation);
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            ${review.title}
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <div class="h2 mb-1">
                                    <span class="badge ${scoreClass} px-3 py-2">
                                        <i class="fas fa-star me-1"></i>${review.score}/10
                                    </span>
                                </div>
                                <small class="text-muted">Review Score</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <div class="h5 mb-1">
                                    <span class="badge bg-${recClass}">
                                        ${getRecommendationIcon(review.recommendation)} ${getRecommendationLabel(review.recommendation)}
                                    </span>
                                </div>
                                <small class="text-muted">Recommendation</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <div class="h6 mb-1">${new Date(review.reviewed_at).toLocaleDateString()}</div>
                                <small class="text-muted">Review Date</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Abstract Information</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div><strong>${review.title}</strong></div>
                                <div class="text-muted">by ${review.first_name} ${review.last_name}</div>
                                <div class="text-muted">${review.event_title}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Review Summary</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div>Score: <strong>${review.score}/10</strong></div>
                                <div>Recommendation: <strong>${getRecommendationLabel(review.recommendation)}</strong></div>
                                <div>Reviewed: ${new Date(review.reviewed_at).toLocaleDateString()}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Review Comments</h6>
                    <div class="card">
                        <div class="card-body">
                            <div style="line-height: 1.6; white-space: pre-line;">${review.comments}</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <small class="text-muted">Review ID: #${review.id}</small>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${data.message || 'Failed to load review details.'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading review details:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading review details. Please try again.
            </div>
        `;
    });
}

function exportReview(reviewId) {
    // Show loading notification
    const exportBtn = document.querySelector(`button[onclick="exportReview(${reviewId})"]`);
    const originalContent = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    exportBtn.disabled = true;
    
    // Open export URL in new tab
    window.open(`/reviewer/export-review/${reviewId}`, '_blank');
    
    // Reset button after delay
    setTimeout(() => {
        exportBtn.innerHTML = originalContent;
        exportBtn.disabled = false;
    }, 2000);
}

function getRecommendationClass(recommendation) {
    switch (recommendation) {
        case 'accept': return 'success';
        case 'minor_revision': return 'warning';
        case 'major_revision': return 'info';
        case 'reject': return 'danger';
        default: return 'secondary';
    }
}

function getRecommendationIcon(recommendation) {
    switch (recommendation) {
        case 'accept': return '<i class="fas fa-check me-1"></i>';
        case 'minor_revision': return '<i class="fas fa-edit me-1"></i>';
        case 'major_revision': return '<i class="fas fa-tools me-1"></i>';
        case 'reject': return '<i class="fas fa-times me-1"></i>';
        default: return '<i class="fas fa-question me-1"></i>';
    }
}

function getRecommendationLabel(recommendation) {
    switch (recommendation) {
        case 'accept': return 'Accept';
        case 'minor_revision': return 'Minor Revision';
        case 'major_revision': return 'Major Revision';
        case 'reject': return 'Reject';
        default: return 'Unknown';
    }
}
</script>
<?= $this->endSection() ?>