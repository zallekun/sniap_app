<?= $this->extend('shared/layouts/reviewer_layout') ?>

<?= $this->section('title') ?>Assigned Abstracts<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Assigned Abstracts</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/reviewer/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Assigned Abstracts</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-outline-secondary" onclick="refreshAbstracts()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
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

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon assigned">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3><?= count($assigned_abstracts ?? []) ?></h3>
                    <p>Total Assigned</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3>
                        <?php 
                        $pending = 0;
                        if (isset($assigned_abstracts)) {
                            foreach ($assigned_abstracts as $abstract) {
                                if (empty($abstract['reviewed_at'])) $pending++;
                            }
                        }
                        echo $pending;
                        ?>
                    </h3>
                    <p>Pending Reviews</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="reviewer-stat-card">
                <div class="reviewer-stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="reviewer-stat-content">
                    <h3>
                        <?php 
                        $completed = 0;
                        if (isset($assigned_abstracts)) {
                            foreach ($assigned_abstracts as $abstract) {
                                if (!empty($abstract['reviewed_at'])) $completed++;
                            }
                        }
                        echo $completed;
                        ?>
                    </h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Abstracts List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Assigned Abstracts
            </h5>
            <span class="badge bg-primary"><?= count($assigned_abstracts ?? []) ?> items</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($assigned_abstracts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Abstracts Assigned</h4>
                    <p class="text-muted">You currently have no abstracts assigned for review. Check back later for new assignments.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Title</th>
                                <th class="border-0">Author</th>
                                <th class="border-0">Event</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Assigned Date</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assigned_abstracts as $abstract): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= esc($abstract['title']) ?></h6>
                                            <small class="text-muted">ID: #<?= $abstract['id'] ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <div class="fw-semibold"><?= esc($abstract['first_name'] ?? '') ?> <?= esc($abstract['last_name'] ?? '') ?></div>
                                            <small class="text-muted"><?= esc($abstract['email'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-light text-dark"><?= esc($abstract['event_title'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!empty($abstract['reviewed_at'])): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Completed
                                            </span>
                                            <?php if (isset($abstract['score'])): ?>
                                                <br><small class="text-muted mt-1">Score: <?= $abstract['score'] ?>/10</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i> Pending Review
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-sm">
                                            <?= date('M d, Y', strtotime($abstract['assigned_at'] ?? 'now')) ?>
                                        </div>
                                        <small class="text-muted"><?= date('H:i', strtotime($abstract['assigned_at'] ?? 'now')) ?></small>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <a href="/reviewer/review/<?= $abstract['id'] ?>" 
                                               class="btn btn-sm <?= empty($abstract['reviewed_at']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                                                <?php if (!empty($abstract['reviewed_at'])): ?>
                                                    <i class="fas fa-eye"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-edit"></i>
                                                <?php endif; ?>
                                            </a>
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                    onclick="viewAbstractDetails(<?= $abstract['id'] ?>)"
                                                    title="View Details">
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
<?= $this->endSection() ?>

<?= $this->section('reviewer_scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Assigned abstracts page loaded');
    
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});

function refreshAbstracts() {
    // Show loading state
    const refreshBtn = document.querySelector('button[onclick="refreshAbstracts()"]');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload after a brief delay to show loading state
    setTimeout(() => {
        location.reload();
    }, 500);
}

function viewAbstractDetails(abstractId) {
    const modal = new bootstrap.Modal(document.getElementById('abstractDetailsModal'));
    const content = document.getElementById('abstractDetailsContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading abstract details...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch abstract details
    fetch(`/reviewer/api/abstract-details/${abstractId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const abstract = data.abstract;
            content.innerHTML = `
                <div class="row">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            ${abstract.title}
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Author Information</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div><strong>${abstract.first_name} ${abstract.last_name}</strong></div>
                                <div class="text-muted">${abstract.email}</div>
                                ${abstract.institution ? `<div class="text-muted">${abstract.institution}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Event & Category</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div><strong>${abstract.event_title || 'N/A'}</strong></div>
                                ${abstract.category_name ? `<div class="text-muted">${abstract.category_name}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                
                ${abstract.keywords ? `
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Keywords</h6>
                    <div class="d-flex flex-wrap gap-1">
                        ${abstract.keywords.split(',').map(keyword => 
                            `<span class="badge bg-secondary">${keyword.trim()}</span>`
                        ).join('')}
                    </div>
                </div>
                ` : ''}
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Abstract Content</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div style="line-height: 1.6; white-space: pre-line;">${abstract.abstract_text}</div>
                        </div>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-md-6">
                        <small class="text-muted">Submitted: ${new Date(abstract.created_at).toLocaleDateString()}</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Abstract ID: #${abstract.id}</small>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${data.message || 'Failed to load abstract details.'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading abstract details:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading abstract details. Please try again.
            </div>
        `;
    });
}
</script>
<?= $this->endSection() ?>