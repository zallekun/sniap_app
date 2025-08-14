<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Assigned Abstracts - Reviewer';
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
                    <a href="/reviewer/assigned" class="nav-link active">
                        <i class="fas fa-clipboard-list"></i>
                        Assigned Abstracts
                        <?php if (isset($assigned_abstracts) && count($assigned_abstracts) > 0): ?>
                            <span class="nav-badge"><?= count($assigned_abstracts) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/reviewer/reviews" class="nav-link">
                        <i class="fas fa-check-circle"></i>
                        Review History
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
            <h1 class="header-title">Assigned Abstracts</h1>
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

            <!-- Quick Stats -->
            <div class="stats-grid mb-4">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Total Assigned</div>
                        <div class="stat-value"><?= count($assigned_abstracts ?? []) ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Pending Reviews</div>
                        <div class="stat-value">
                            <?php 
                            $pending = 0;
                            if (isset($assigned_abstracts)) {
                                foreach ($assigned_abstracts as $abstract) {
                                    if (empty($abstract['reviewed_at'])) $pending++;
                                }
                            }
                            echo $pending;
                            ?>
                        </div>
                    </div>
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Completed</div>
                        <div class="stat-value">
                            <?php 
                            $completed = 0;
                            if (isset($assigned_abstracts)) {
                                foreach ($assigned_abstracts as $abstract) {
                                    if (!empty($abstract['reviewed_at'])) $completed++;
                                }
                            }
                            echo $completed;
                            ?>
                        </div>
                    </div>
                    <div class="stat-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Abstracts List -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Assigned Abstracts</h3>
                    <div class="card-actions">
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshAbstracts()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($assigned_abstracts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clipboard-list fa-3x"></i>
                            </div>
                            <h4>No Abstracts Assigned</h4>
                            <p>You currently have no abstracts assigned for review. Check back later for new assignments.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Event</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assigned_abstracts as $abstract): ?>
                                        <tr>
                                            <td>
                                                <div class="abstract-title">
                                                    <?= esc($abstract['title']) ?>
                                                </div>
                                                <small class="text-muted">
                                                    ID: #<?= $abstract['id'] ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="author-info">
                                                    <strong><?= esc($abstract['first_name'] ?? '') ?> <?= esc($abstract['last_name'] ?? '') ?></strong>
                                                    <br><small class="text-muted"><?= esc($abstract['email'] ?? '') ?></small>
                                                </div>
                                            </td>
                                            <td><?= esc($abstract['event_title'] ?? 'N/A') ?></td>
                                            <td><?= esc($abstract['category_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php if (!empty($abstract['reviewed_at'])): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Reviewed
                                                    </span>
                                                    <br><small class="text-muted">
                                                        Score: <?= $abstract['score'] ?? 'N/A' ?>/10
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                // Calculate due date (assuming 7 days from assignment)
                                                $dueDate = date('Y-m-d', strtotime('+7 days'));
                                                echo date('M d, Y', strtotime($dueDate));
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="/reviewer/review/<?= $abstract['id'] ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <?php if (!empty($abstract['reviewed_at'])): ?>
                                                            <i class="fas fa-eye"></i> View Review
                                                        <?php else: ?>
                                                            <i class="fas fa-edit"></i> Review
                                                        <?php endif; ?>
                                                    </a>
                                                    <button class="btn btn-outline-secondary btn-sm" 
                                                            onclick="viewAbstractDetails(<?= $abstract['id'] ?>)">
                                                        <i class="fas fa-info-circle"></i>
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

<!-- Abstract Details Modal -->
<div class="modal fade" id="abstractDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abstract Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="abstractDetailsContent">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Assigned abstracts page loaded');
});

function refreshAbstracts() {
    location.reload();
}

function viewAbstractDetails(abstractId) {
    const modal = new bootstrap.Modal(document.getElementById('abstractDetailsModal'));
    const content = document.getElementById('abstractDetailsContent');
    
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    modal.show();
    
    fetch(`/api/abstracts/${abstractId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const abstract = data.data;
                content.innerHTML = `
                    <div class="abstract-details">
                        <h6><strong>Title:</strong></h6>
                        <p>${abstract.title}</p>
                        
                        <h6><strong>Author:</strong></h6>
                        <p>${abstract.first_name} ${abstract.last_name} (${abstract.email})</p>
                        
                        <h6><strong>Institution:</strong></h6>
                        <p>${abstract.institution || 'Not specified'}</p>
                        
                        <h6><strong>Category:</strong></h6>
                        <p>${abstract.category_name}</p>
                        
                        <h6><strong>Keywords:</strong></h6>
                        <p>${abstract.keywords || 'Not specified'}</p>
                        
                        <h6><strong>Abstract:</strong></h6>
                        <div class="abstract-text">${abstract.abstract_text}</div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Failed to load abstract details.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading abstract details.</div>';
        });
}
</script>
<?php $this->endSection(); ?>