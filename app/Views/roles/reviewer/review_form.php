<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Review Abstract<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
.review-form-container {
    max-width: 1200px;
    margin: 0 auto;
}

.abstract-display {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.abstract-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    padding: 0.75rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.meta-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.meta-value {
    color: #212529;
}

.abstract-text {
    line-height: 1.6;
    color: #495057;
    background: white;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.review-form {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.score-selector {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.score-option {
    padding: 0.75rem;
    text-align: center;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 600;
}

.score-option:hover {
    border-color: #7c3aed;
    background: #f3f4f6;
}

.score-option.selected {
    border-color: #7c3aed;
    background: #7c3aed;
    color: white;
}

.score-labels {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.recommendation-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.recommendation-card {
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.recommendation-card:hover {
    border-color: #7c3aed;
    background: #f9fafb;
}

.recommendation-card.selected {
    border-color: #7c3aed;
    background: #7c3aed;
    color: white;
}

.recommendation-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.recommendation-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.recommendation-desc {
    font-size: 0.875rem;
    opacity: 0.8;
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="review-form-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/reviewer/dashboard" class="text-decoration-none">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/reviewer/assigned" class="text-decoration-none">
                    <i class="fas fa-clipboard-list me-1"></i>Assigned Abstracts
                </a>
            </li>
            <li class="breadcrumb-item active">Review Abstract</li>
        </ol>
    </nav>

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

    <?php if ($abstract): ?>
        <!-- Abstract Display -->
        <div class="abstract-display">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><?= esc($abstract['title']) ?></h2>
                <span class="badge badge-info">ID: #<?= $abstract['id'] ?></span>
            </div>

            <!-- Abstract Meta Information -->
            <div class="abstract-meta">
                <div class="meta-item">
                    <div class="meta-label">Author</div>
                    <div class="meta-value">
                        <?= esc($abstract['first_name']) ?> <?= esc($abstract['last_name']) ?>
                        <br><small class="text-muted"><?= esc($abstract['email']) ?></small>
                    </div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Institution</div>
                    <div class="meta-value"><?= esc($abstract['institution'] ?? 'Not specified') ?></div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Event</div>
                    <div class="meta-value"><?= esc($abstract['event_title']) ?></div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Category</div>
                    <div class="meta-value"><?= esc($abstract['category_name']) ?></div>
                </div>
                
                <?php if (!empty($abstract['keywords'])): ?>
                <div class="meta-item">
                    <div class="meta-label">Keywords</div>
                    <div class="meta-value"><?= esc($abstract['keywords']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Abstract Text -->
            <h5 class="mb-3">Abstract</h5>
            <div class="abstract-text">
                <?= nl2br(esc($abstract['abstract_text'])) ?>
            </div>

            <?php if (!empty($abstract['file_path'])): ?>
            <div class="mt-3">
                <a href="/uploads/abstracts/<?= esc($abstract['file_path']) ?>" 
                   class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Download Full Paper
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Review Form -->
        <div class="review-form">
            <h3 class="mb-4">
                <?php if ($existing_review): ?>
                    <i class="fas fa-edit me-2"></i>Update Review
                <?php else: ?>
                    <i class="fas fa-plus-circle me-2"></i>Submit Review
                <?php endif; ?>
            </h3>

            <form id="reviewForm" action="/reviewer/submit-review" method="POST">
                <input type="hidden" name="abstract_id" value="<?= $abstract['id'] ?>">
                
                <!-- Score Section -->
                <div class="form-section">
                    <h4 class="section-title">Quality Score</h4>
                    <p class="text-muted mb-3">
                        Rate the overall quality of this abstract on a scale of 1-10 based on scientific merit, 
                        clarity, originality, and significance.
                    </p>
                    
                    <div class="score-labels">
                        <span>Poor (1-2)</span>
                        <span>Fair (3-4)</span>
                        <span>Good (5-6)</span>
                        <span>Very Good (7-8)</span>
                        <span>Excellent (9-10)</span>
                    </div>
                    
                    <div class="score-selector">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <div class="score-option" 
                                 data-score="<?= $i ?>"
                                 <?= ($existing_review && $existing_review['score'] == $i) ? 'class="score-option selected"' : '' ?>>
                                <?= $i ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="score" id="selectedScore" 
                           value="<?= $existing_review['score'] ?? '' ?>" required>
                </div>

                <!-- Recommendation Section -->
                <div class="form-section">
                    <h4 class="section-title">Recommendation</h4>
                    <p class="text-muted mb-3">
                        Provide your recommendation based on your evaluation of the abstract.
                    </p>
                    
                    <div class="recommendation-options">
                        <div class="recommendation-card" data-recommendation="accept"
                             <?= ($existing_review && $existing_review['recommendation'] == 'accept') ? 'class="recommendation-card selected"' : '' ?>>
                            <div class="recommendation-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="recommendation-title">Accept</div>
                            <div class="recommendation-desc">Recommend for publication/presentation</div>
                        </div>
                        
                        <div class="recommendation-card" data-recommendation="minor_revision"
                             <?= ($existing_review && $existing_review['recommendation'] == 'minor_revision') ? 'class="recommendation-card selected"' : '' ?>>
                            <div class="recommendation-icon text-warning">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="recommendation-title">Minor Revision</div>
                            <div class="recommendation-desc">Accept with minor changes</div>
                        </div>
                        
                        <div class="recommendation-card" data-recommendation="major_revision"
                             <?= ($existing_review && $existing_review['recommendation'] == 'major_revision') ? 'class="recommendation-card selected"' : '' ?>>
                            <div class="recommendation-icon text-info">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="recommendation-title">Major Revision</div>
                            <div class="recommendation-desc">Significant changes required</div>
                        </div>
                        
                        <div class="recommendation-card" data-recommendation="reject"
                             <?= ($existing_review && $existing_review['recommendation'] == 'reject') ? 'class="recommendation-card selected"' : '' ?>>
                            <div class="recommendation-icon text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="recommendation-title">Reject</div>
                            <div class="recommendation-desc">Not suitable for publication</div>
                        </div>
                    </div>
                    <input type="hidden" name="recommendation" id="selectedRecommendation" 
                           value="<?= $existing_review['recommendation'] ?? '' ?>" required>
                </div>

                <!-- Comments Section -->
                <div class="form-section">
                    <h4 class="section-title">Review Comments</h4>
                    <p class="text-muted mb-3">
                        Provide detailed feedback to help the author improve their work. 
                        Include specific comments about strengths, weaknesses, and suggestions for improvement.
                    </p>
                    
                    <textarea name="comments" 
                              id="comments" 
                              class="form-control" 
                              rows="8" 
                              placeholder="Enter your detailed review comments here..."
                              required><?= esc($existing_review['comments'] ?? '') ?></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="d-flex justify-content-between">
                        <a href="/reviewer/assigned" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Assigned
                        </a>
                        
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="saveAsDraft()">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                <?= $existing_review ? 'Update Review' : 'Submit Review' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    <?php else: ?>
        <div class="alert alert-danger">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Abstract Not Found</h4>
            <p>The requested abstract could not be found or you don't have permission to review it.</p>
            <a href="/reviewer/assigned" class="btn btn-outline-danger">
                <i class="fas fa-arrow-left me-2"></i>Back to Assigned Abstracts
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeReviewForm();
});

function initializeReviewForm() {
    // Score selector
    document.querySelectorAll('.score-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            document.querySelectorAll('.score-option').forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Update hidden input
            document.getElementById('selectedScore').value = this.dataset.score;
        });
    });

    // Recommendation selector
    document.querySelectorAll('.recommendation-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.recommendation-card').forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Update hidden input
            document.getElementById('selectedRecommendation').value = this.dataset.recommendation;
        });
    });

    // Form submission
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const score = document.getElementById('selectedScore').value;
        const recommendation = document.getElementById('selectedRecommendation').value;
        const comments = document.getElementById('comments').value.trim();
        
        // Validation
        if (!score) {
            showAlert('Please select a quality score.', 'warning');
            return;
        }
        
        if (!recommendation) {
            showAlert('Please select a recommendation.', 'warning');
            return;
        }
        
        if (!comments) {
            showAlert('Please provide review comments.', 'warning');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        submitBtn.disabled = true;
        
        // Submit via AJAX
        const formData = new FormData(this);
        
        fetch('/reviewer/submit-review', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '/reviewer/assigned';
                }, 2000);
            } else {
                showAlert(data.message || 'Failed to submit review', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while submitting the review', 'danger');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

function saveAsDraft() {
    showAlert('Draft functionality will be implemented soon', 'info');
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.review-form-container');
    container.insertBefore(alertDiv, container.children[1]);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?php $this->endSection(); ?>