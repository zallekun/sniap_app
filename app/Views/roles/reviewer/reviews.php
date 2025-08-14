<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Review History - Reviewer';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="/css/reviewer/dashboard.css">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
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
                    <a href="/reviewer/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Review Management</div>
                <div class="nav-item">
                    <a href="/reviewer/assigned" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        Assigned Abstracts
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/reviewer/reviews" class="nav-link active">
                        <i class="fas fa-check-circle"></i>
                        Review History
                        <?php if (isset($completed_reviews) && count($completed_reviews) > 0): ?>
                            <span class="nav-badge"><?= count($completed_reviews) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">System</div>
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
    <main class="reviewer-main">
        <!-- Header -->
        <header class="reviewer-header">
            <h1 class="header-title">Review History</h1>
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
                            Reviewer
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="reviewer-content">
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid mb-4">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Total Reviews</div>
                        <div class="stat-value"><?= count($completed_reviews ?? []) ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Average Score</div>
                        <div class="stat-value">
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
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">
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
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Accepted</div>
                        <div class="stat-value">
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
                        </div>
                    </div>
                    <div class="stat-icon accepted">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Completed Reviews</h3>
                    <div class="card-actions">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterReviews('all')">All Reviews</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterReviews('accept')">Accepted</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterReviews('minor_revision')">Minor Revision</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterReviews('major_revision')">Major Revision</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterReviews('reject')">Rejected</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshReviews()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($completed_reviews)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-history fa-3x"></i>
                            </div>
                            <h4>No Reviews Completed</h4>
                            <p>You haven't completed any reviews yet. Check your <a href="/reviewer/assigned">assigned abstracts</a> to get started.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="reviewsTable">
                                <thead>
                                    <tr>
                                        <th>Abstract Title</th>
                                        <th>Author</th>
                                        <th>Event</th>
                                        <th>Score</th>
                                        <th>Recommendation</th>
                                        <th>Reviewed Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completed_reviews as $review): ?>
                                        <tr data-recommendation="<?= esc($review['recommendation'] ?? '') ?>">
                                            <td>
                                                <div class="abstract-title">
                                                    <?= esc($review['title']) ?>
                                                </div>
                                                <small class="text-muted">
                                                    Review ID: #<?= $review['id'] ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="author-info">
                                                    <strong><?= esc($review['first_name'] ?? '') ?> <?= esc($review['last_name'] ?? '') ?></strong>
                                                </div>
                                            </td>
                                            <td><?= esc($review['event_title'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="score-badge score-<?= floor(($review['score'] ?? 0) / 2) ?>">
                                                    <?= $review['score'] ?? 'N/A' ?>/10
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $recommendation = $review['recommendation'] ?? '';
                                                $badgeClass = '';
                                                $icon = '';
                                                $label = '';
                                                
                                                switch ($recommendation) {
                                                    case 'accept':
                                                        $badgeClass = 'badge-success';
                                                        $icon = 'fa-check';
                                                        $label = 'Accept';
                                                        break;
                                                    case 'minor_revision':
                                                        $badgeClass = 'badge-warning';
                                                        $icon = 'fa-edit';
                                                        $label = 'Minor Revision';
                                                        break;
                                                    case 'major_revision':
                                                        $badgeClass = 'badge-info';
                                                        $icon = 'fa-tools';
                                                        $label = 'Major Revision';
                                                        break;
                                                    case 'reject':
                                                        $badgeClass = 'badge-danger';
                                                        $icon = 'fa-times';
                                                        $label = 'Reject';
                                                        break;
                                                    default:
                                                        $badgeClass = 'badge-secondary';
                                                        $icon = 'fa-question';
                                                        $label = 'Unknown';
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <i class="fas <?= $icon ?>"></i> <?= $label ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($review['reviewed_at'])): ?>
                                                    <?= date('M d, Y', strtotime($review['reviewed_at'])) ?>
                                                    <br><small class="text-muted">
                                                        <?= date('H:i', strtotime($review['reviewed_at'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" 
                                                            onclick="viewReviewDetails(<?= $review['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm" 
                                                            onclick="exportReview(<?= $review['id'] ?>)">
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
    </main>
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
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<style>
.score-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 1rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.score-0, .score-1 { background-color: #fee2e2; color: #dc2626; }
.score-2, .score-3 { background-color: #fef3c7; color: #d97706; }
.score-4, .score-5 { background-color: #dbeafe; color: #2563eb; }

.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-icon {
    color: #9ca3af;
    margin-bottom: 1rem;
}

.abstract-title {
    font-weight: 600;
    color: #1f2937;
}

.author-info strong {
    color: #374151;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Review history page loaded');
});

function refreshReviews() {
    location.reload();
}

function filterReviews(recommendation) {
    const table = document.getElementById('reviewsTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (recommendation === 'all' || row.dataset.recommendation === recommendation) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update filter button text
    const filterBtn = document.querySelector('.dropdown-toggle');
    const filterText = recommendation === 'all' ? 'All Reviews' : 
                      recommendation.charAt(0).toUpperCase() + recommendation.slice(1).replace('_', ' ');
    filterBtn.innerHTML = `<i class="fas fa-filter"></i> ${filterText}`;
}

function viewReviewDetails(reviewId) {
    const modal = new bootstrap.Modal(document.getElementById('reviewDetailsModal'));
    const content = document.getElementById('reviewDetailsContent');
    
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    modal.show();
    
    fetch(`/api/reviews/${reviewId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const review = data.data;
                content.innerHTML = `
                    <div class="review-details">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><strong>Abstract Title:</strong></h6>
                                <p>${review.title}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Author:</strong></h6>
                                <p>${review.first_name} ${review.last_name}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h6><strong>Score:</strong></h6>
                                <p><span class="badge badge-info">${review.score}/10</span></p>
                            </div>
                            <div class="col-md-4">
                                <h6><strong>Recommendation:</strong></h6>
                                <p><span class="badge badge-${getRecommendationClass(review.recommendation)}">${review.recommendation}</span></p>
                            </div>
                            <div class="col-md-4">
                                <h6><strong>Reviewed:</strong></h6>
                                <p>${new Date(review.reviewed_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        
                        <h6><strong>Comments:</strong></h6>
                        <div class="p-3 bg-light rounded">
                            ${review.comments.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Failed to load review details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading review details.</div>';
        });
}

function exportReview(reviewId) {
    window.open(`/reviewer/export-review/${reviewId}`, '_blank');
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
</script>
<?php $this->endSection(); ?>