<?= $this->extend('shared/layouts/presenter_layout') ?>

<?= $this->section('title') ?>My Abstracts<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-file-alt me-2"></i>
                My Abstracts
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/presenter/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Abstracts</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-success" onclick="showSubmitModal()">
            <i class="fas fa-plus me-1"></i> Submit New Abstract
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
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon abstracts">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= count($abstracts ?? []) ?></h3>
                    <p>Total Submitted</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon presentations">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $accepted = 0;
                        if (isset($abstracts)) {
                            foreach ($abstracts as $abstract) {
                                if (($abstract['review_status'] ?? '') === 'accepted') $accepted++;
                            }
                        }
                        echo $accepted;
                        ?>
                    </h3>
                    <p>Accepted</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon events">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $pending = 0;
                        if (isset($abstracts)) {
                            foreach ($abstracts as $abstract) {
                                if (in_array($abstract['review_status'] ?? '', ['pending', 'under_review'])) $pending++;
                            }
                        }
                        echo $pending;
                        ?>
                    </h3>
                    <p>Under Review</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon certificates">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $rejected = 0;
                        if (isset($abstracts)) {
                            foreach ($abstracts as $abstract) {
                                if (($abstract['review_status'] ?? '') === 'rejected') $rejected++;
                            }
                        }
                        echo $rejected;
                        ?>
                    </h3>
                    <p>Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Abstracts List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Abstract Submissions
            </h5>
            <span class="badge bg-success"><?= count($abstracts ?? []) ?> submissions</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($abstracts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Abstracts Yet</h4>
                    <?php if (empty($registered_events ?? [])): ?>
                        <p class="text-muted">You need to register for an event before you can submit abstracts.</p>
                        <a href="/presenter/registrations" class="btn btn-success">
                            <i class="fas fa-calendar-plus me-2"></i>Register for Event
                        </a>
                        <button class="btn btn-outline-secondary ms-2" onclick="showSubmitModal()" disabled>
                            <i class="fas fa-plus me-2"></i>Submit Abstract (Disabled)
                        </button>
                    <?php else: ?>
                        <p class="text-muted">You haven't submitted any abstracts. Start by submitting your first abstract for review.</p>
                        <button class="btn btn-success" onclick="showSubmitModal()">
                            <i class="fas fa-plus me-2"></i>Submit Your First Abstract
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Title</th>
                                <th class="border-0">Event</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Submitted</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($abstracts as $abstract): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= esc($abstract['title'] ?? 'Untitled') ?></h6>
                                            <small class="text-muted">ID: #<?= $abstract['id'] ?? '' ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-light text-dark"><?= esc($abstract['event_title'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $status = $abstract['review_status'] ?? 'pending';
                                        $statusClass = match($status) {
                                            'accepted' => 'bg-success',
                                            'rejected' => 'bg-danger', 
                                            'under_review' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-sm">
                                            <?= date('M d, Y', strtotime($abstract['submitted_at'] ?? 'now')) ?>
                                        </div>
                                        <small class="text-muted"><?= date('H:i', strtotime($abstract['submitted_at'] ?? 'now')) ?></small>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewAbstract(<?= $abstract['id'] ?? 0 ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (($abstract['review_status'] ?? '') === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="editAbstract(<?= $abstract['id'] ?? 0 ?>)"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="downloadAbstract(<?= $abstract['id'] ?? 0 ?>)"
                                                    title="Download">
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

<!-- Submit Abstract Modal -->
<div class="modal fade" id="submitAbstractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Submit New Abstract
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="submitAbstractForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="abstractTitle" class="form-label">
                                    <i class="fas fa-heading me-1"></i>Title *
                                </label>
                                <input type="text" class="form-control" id="abstractTitle" 
                                       name="title" required maxlength="200">
                                <div class="form-text">Maximum 200 characters</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventId" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Event *
                                </label>
                                <select class="form-select" id="eventId" name="event_id" required <?= empty($registered_events ?? []) ? 'disabled' : '' ?>>
                                    <option value="">Select Event</option>
                                    <?php if (empty($registered_events ?? [])): ?>
                                        <option value="" disabled>No registered events. Please register for an event first.</option>
                                    <?php else: ?>
                                        <?php foreach ($registered_events as $event): ?>
                                            <option value="<?= $event['id'] ?>"><?= esc($event['title']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (empty($registered_events ?? [])): ?>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        You must register for an event before submitting an abstract. 
                                        <a href="/presenter/registrations" class="text-success">Register here</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Category *
                                </label>
                                <select class="form-select" id="category" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <option value="1">Artificial Intelligence</option>
                                    <option value="2">Data Science</option>
                                    <option value="3">Machine Learning</option>
                                    <option value="4">Computer Vision</option>
                                    <option value="5">Natural Language Processing</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="keywords" class="form-label">
                                    <i class="fas fa-key me-1"></i>Keywords
                                </label>
                                <input type="text" class="form-control" id="keywords" 
                                       name="keywords" placeholder="AI, Machine Learning, Data Science">
                                <div class="form-text">Separate with commas</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="abstractText" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Abstract Content *
                        </label>
                        <textarea class="form-control" id="abstractText" name="abstract_text" 
                                  rows="8" required maxlength="2000"
                                  placeholder="Enter your abstract content here..."></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/2000 characters
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="abstractFile" class="form-label">
                            <i class="fas fa-file-upload me-1"></i>Upload Abstract File (Optional)
                        </label>
                        <input type="file" class="form-control" id="abstractFile" 
                               name="abstract_file" accept=".pdf,.doc,.docx">
                        <div class="form-text">Supported formats: PDF, DOC, DOCX (Max 5MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" <?= empty($registered_events ?? []) ? 'disabled' : '' ?>>
                        <i class="fas fa-paper-plane me-1"></i>Submit Abstract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Abstract Modal -->
<div class="modal fade" id="viewAbstractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Abstract Details
                </h5>
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

<?= $this->section('presenter_scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Presenter abstracts page loaded');
    
    // Character counter for abstract text
    const abstractText = document.getElementById('abstractText');
    const charCount = document.getElementById('charCount');
    
    if (abstractText && charCount) {
        abstractText.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});

function showSubmitModal() {
    // Check if there are registered events
    const eventSelect = document.getElementById('eventId');
    const hasRegisteredEvents = eventSelect && !eventSelect.disabled && eventSelect.options.length > 1;
    
    if (!hasRegisteredEvents) {
        alert('You must register for an event before submitting an abstract. Please go to My Events to register first.');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('submitAbstractModal'));
    modal.show();
}

function viewAbstract(abstractId) {
    const modal = new bootstrap.Modal(document.getElementById('viewAbstractModal'));
    const content = document.getElementById('abstractDetailsContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading abstract details...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch abstract details via AJAX
    fetch(`/presenter/api/abstracts/${abstractId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const abstract = data.data;
            content.innerHTML = `
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-alt text-success me-2"></i>
                            ${abstract.title}
                        </h6>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Event & Category</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div><strong>${abstract.event_title || 'N/A'}</strong></div>
                                ${abstract.category_name ? `<div class="text-muted">${abstract.category_name}</div>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <span class="badge bg-secondary">${abstract.review_status ? abstract.review_status.replace('_', ' ').toUpperCase() : 'PENDING'}</span>
                                <div class="text-muted mt-1">Submitted: ${new Date(abstract.submitted_at || abstract.created_at).toLocaleDateString()}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${abstract.keywords ? `
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Keywords</h6>
                    <div class="d-flex flex-wrap gap-1">
                        ${abstract.keywords.split(',').map(keyword => 
                            `<span class="badge bg-success">${keyword.trim()}</span>`
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
                
                ${abstract.file_name ? `
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Attached File</h6>
                    <a href="/presenter/abstracts/${abstract.id}/download" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-1"></i>${abstract.file_name}
                    </a>
                </div>
                ` : ''}
                
                <div class="text-center">
                    <small class="text-muted">Abstract ID: #${abstract.id}</small>
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

function editAbstract(abstractId) {
    // TODO: Load abstract data into edit form
    console.log('Edit abstract:', abstractId);
    showSubmitModal();
}

function downloadAbstract(abstractId) {
    // Download abstract file via direct link
    window.open(`/presenter/abstracts/${abstractId}/download`, '_blank');
}

// Handle form submission
document.getElementById('submitAbstractForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
    submitBtn.disabled = true;
    
    // Submit via AJAX
    fetch('/presenter/api/abstracts/submit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${data.message || 'Abstract submitted successfully!'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstElementChild.nextElementSibling);
            
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('submitAbstractModal')).hide();
            
            // Reset form
            this.reset();
            document.getElementById('charCount').textContent = '0';
            
            // Reload page to show new abstract
            setTimeout(() => location.reload(), 1000);
        } else {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                ${data.message || 'Failed to submit abstract. Please try again.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.modal-body').insertBefore(alertDiv, document.querySelector('.modal-body').firstElementChild);
        }
    })
    .catch(error => {
        console.error('Error submitting abstract:', error);
        // Show error message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            Network error. Please check your connection and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.modal-body').insertBefore(alertDiv, document.querySelector('.modal-body').firstElementChild);
    })
    .finally(() => {
        // Restore submit button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
<?= $this->endSection() ?>