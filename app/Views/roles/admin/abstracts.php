<?php
$this->extend('shared/layouts/main');
$this->section('title');
echo $title ?? 'Abstract Management - Admin Panel';
$this->endSection();

$this->section('additional_css');
?>
<link rel="stylesheet" href="<?= base_url('css/admin/dashboard.css') ?>">
<style>
.abstract-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.assignment-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
}

.reviewer-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
    color: white;
    background: #7c3aed;
    margin-right: 0.5rem;
}

.abstract-preview {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.status-filter {
    margin-bottom: 1rem;
}

.bulk-actions {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: none;
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/admin/dashboard" class="sidebar-logo">SNIA Admin</a>
            <div class="sidebar-subtitle">Conference Management</div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Dashboard</div>
                <div class="nav-item">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Overview
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <div class="nav-item">
                    <a href="/admin/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        User Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/events" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        Event Management
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/registrations" class="nav-link">
                        <i class="fas fa-user-check"></i>
                        Registrations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="/admin/abstracts" class="nav-link active">
                        <i class="fas fa-file-alt"></i>
                        Abstract & Reviews
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Reports & Analytics</div>
                <div class="nav-item">
                    <a href="/admin/analytics" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <div class="nav-item">
                    <a href="/admin/settings" class="nav-link">
                        <i class="fas fa-cog"></i>
                        System Settings
                    </a>
                </div>
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
    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <h1 class="header-title">Abstract & Review Management</h1>
            <div class="header-actions">
                <div class="user-menu">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['first_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: #1f2937;">
                            <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                        </div>
                        <div style="font-size: 0.875rem; color: #6b7280;">
                            Administrator
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="admin-content">
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
                        <div class="stat-label">Total Abstracts</div>
                        <div class="stat-value" id="totalAbstracts">-</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Pending Assignment</div>
                        <div class="stat-value" id="pendingAssignment">-</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Under Review</div>
                        <div class="stat-value" id="underReview">-</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-label">Completed Reviews</div>
                        <div class="stat-value" id="completedReviews">-</div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="content-card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchAbstracts" 
                                       placeholder="Search abstracts...">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchAbstracts()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter" onchange="filterByStatus()">
                                <option value="">All Status</option>
                                <option value="unassigned">Unassigned</option>
                                <option value="assigned">Assigned</option>
                                <option value="under_review">Under Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary" onclick="bulkAssignReviewers()">
                                <i class="fas fa-user-plus"></i> Bulk Assign
                            </button>
                            <button class="btn btn-outline-secondary" onclick="refreshAbstracts()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Panel -->
            <div class="bulk-actions" id="bulkActionsPanel">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span id="selectedCount">0</span> abstracts selected
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">Clear Selection</button>
                        <button class="btn btn-primary btn-sm" onclick="showBulkAssignModal()">
                            <i class="fas fa-user-plus"></i> Assign Reviewers
                        </button>
                    </div>
                </div>
            </div>

            <!-- Abstracts Table -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Abstract Submissions</h3>
                    <div class="card-actions">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('excel')">Excel</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('pdf')">PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('csv')">CSV</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="abstractsTableContainer">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading abstracts...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Assign Reviewer Modal -->
<div class="modal fade" id="assignReviewerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Reviewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignReviewerForm">
                    <input type="hidden" id="abstractIdToAssign" name="abstract_id">
                    
                    <div class="mb-3">
                        <label for="reviewerSelect" class="form-label">Select Reviewer</label>
                        <select class="form-select" id="reviewerSelect" name="reviewer_id" required>
                            <option value="">Loading reviewers...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assignmentNotes" class="form-label">Assignment Notes (Optional)</label>
                        <textarea class="form-control" id="assignmentNotes" name="notes" rows="3"
                                  placeholder="Additional instructions for the reviewer..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dueDate" class="form-label">Review Due Date</label>
                        <input type="date" class="form-control" id="dueDate" name="due_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitReviewerAssignment()">
                    <i class="fas fa-user-plus"></i> Assign Reviewer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Assign Reviewers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkAssignForm">
                    <div class="mb-3">
                        <label class="form-label">Assignment Strategy</label>
                        <select class="form-select" id="assignmentStrategy">
                            <option value="single">Assign same reviewer to all</option>
                            <option value="distribute">Distribute evenly among reviewers</option>
                            <option value="category">Assign by category expertise</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="singleReviewerSelect" style="display:none;">
                        <label for="bulkReviewerSelect" class="form-label">Select Reviewer</label>
                        <select class="form-select" id="bulkReviewerSelect">
                            <option value="">Loading reviewers...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="multipleReviewersSelect" style="display:none;">
                        <label class="form-label">Select Reviewers</label>
                        <div id="reviewerCheckboxes">
                            <!-- Reviewer checkboxes loaded dynamically -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulkDueDate" class="form-label">Review Due Date</label>
                        <input type="date" class="form-control" id="bulkDueDate" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkAssignment()">
                    <i class="fas fa-user-plus"></i> Assign Reviewers
                </button>
            </div>
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
<?php $this->endSection(); ?>

<?php $this->section('additional_js'); ?>
<script>
let selectedAbstracts = new Set();
let allAbstracts = [];
let filteredAbstracts = [];
let availableReviewers = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeAbstractManagement();
});

async function initializeAbstractManagement() {
    await Promise.all([
        loadAbstracts(),
        loadReviewers(),
        loadStatistics()
    ]);
    
    setupEventListeners();
}

function setupEventListeners() {
    // Assignment strategy change
    document.getElementById('assignmentStrategy').addEventListener('change', function() {
        const strategy = this.value;
        const singleSelect = document.getElementById('singleReviewerSelect');
        const multipleSelect = document.getElementById('multipleReviewersSelect');
        
        if (strategy === 'single') {
            singleSelect.style.display = 'block';
            multipleSelect.style.display = 'none';
        } else {
            singleSelect.style.display = 'none';
            multipleSelect.style.display = 'block';
        }
    });
    
    // Set default due date to 7 days from now
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 7);
    document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];
    document.getElementById('bulkDueDate').value = dueDate.toISOString().split('T')[0];
}

async function loadAbstracts() {
    try {
        const response = await fetch('/api/admin/abstracts');
        const data = await response.json();
        
        if (data.status === 'success') {
            allAbstracts = data.data;
            filteredAbstracts = [...allAbstracts];
            renderAbstractsTable();
        } else {
            showAlert('Failed to load abstracts', 'danger');
        }
    } catch (error) {
        console.error('Error loading abstracts:', error);
        showAlert('Error loading abstracts', 'danger');
    }
}

async function loadReviewers() {
    try {
        const response = await fetch('/api/admin/reviewers');
        const data = await response.json();
        
        if (data.status === 'success') {
            availableReviewers = data.data;
            populateReviewerSelects();
        } else {
            showAlert('Failed to load reviewers', 'warning');
        }
    } catch (error) {
        console.error('Error loading reviewers:', error);
    }
}

async function loadStatistics() {
    try {
        const response = await fetch('/api/admin/abstract-stats');
        const data = await response.json();
        
        if (data.status === 'success') {
            const stats = data.data;
            document.getElementById('totalAbstracts').textContent = stats.total || 0;
            document.getElementById('pendingAssignment').textContent = stats.pending_assignment || 0;
            document.getElementById('underReview').textContent = stats.under_review || 0;
            document.getElementById('completedReviews').textContent = stats.completed || 0;
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

function populateReviewerSelects() {
    const singleSelect = document.getElementById('reviewerSelect');
    const bulkSelect = document.getElementById('bulkReviewerSelect');
    const checkboxContainer = document.getElementById('reviewerCheckboxes');
    
    // Clear existing options
    singleSelect.innerHTML = '<option value="">Select a reviewer</option>';
    bulkSelect.innerHTML = '<option value="">Select a reviewer</option>';
    checkboxContainer.innerHTML = '';
    
    availableReviewers.forEach(reviewer => {
        const option = `<option value="${reviewer.id}">${reviewer.first_name} ${reviewer.last_name} - ${reviewer.email}</option>`;
        singleSelect.innerHTML += option;
        bulkSelect.innerHTML += option;
        
        // Add checkbox for multiple selection
        const checkbox = `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${reviewer.id}" id="reviewer_${reviewer.id}">
                <label class="form-check-label" for="reviewer_${reviewer.id}">
                    ${reviewer.first_name} ${reviewer.last_name} (${reviewer.email})
                    <small class="text-muted d-block">Assignments: ${reviewer.current_assignments || 0}</small>
                </label>
            </div>
        `;
        checkboxContainer.innerHTML += checkbox;
    });
}

function renderAbstractsTable() {
    const container = document.getElementById('abstractsTableContainer');
    
    if (filteredAbstracts.length === 0) {
        container.innerHTML = `
            <div class="empty-state text-center p-4">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h4>No Abstracts Found</h4>
                <p>No abstracts match the current filter criteria.</p>
            </div>
        `;
        return;
    }
    
    let tableHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Assigned Reviewer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    filteredAbstracts.forEach(abstract => {
        const assignmentStatus = getAssignmentStatus(abstract);
        const reviewerInfo = getReviewerInfo(abstract);
        
        tableHTML += `
            <tr>
                <td>
                    <input type="checkbox" class="abstract-checkbox" 
                           value="${abstract.id}" onchange="updateSelection()">
                </td>
                <td>
                    <div class="abstract-title">
                        <strong>${escapeHtml(abstract.title)}</strong>
                        <br><small class="text-muted">ID: #${abstract.id}</small>
                    </div>
                </td>
                <td>
                    <div class="author-info">
                        <strong>${escapeHtml(abstract.author_name || '')}</strong>
                        <br><small class="text-muted">${escapeHtml(abstract.author_email || '')}</small>
                    </div>
                </td>
                <td>${escapeHtml(abstract.event_title || 'N/A')}</td>
                <td>${escapeHtml(abstract.category_name || 'N/A')}</td>
                <td>${assignmentStatus}</td>
                <td>${reviewerInfo}</td>
                <td>
                    <div class="abstract-actions">
                        <button class="btn btn-outline-primary btn-sm" 
                                onclick="viewAbstractDetails(${abstract.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm" 
                                onclick="assignReviewer(${abstract.id})" title="Assign Reviewer">
                            <i class="fas fa-user-plus"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="downloadAbstract(${abstract.id})">
                                    <i class="fas fa-download"></i> Download
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="editAbstract(${abstract.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteAbstract(${abstract.id})">
                                    <i class="fas fa-trash"></i> Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody></table></div>';
    container.innerHTML = tableHTML;
}

function getAssignmentStatus(abstract) {
    if (abstract.assigned_reviewer_id) {
        if (abstract.review_status === 'completed') {
            return '<span class="badge bg-success">Completed</span>';
        } else {
            return '<span class="badge bg-warning">Under Review</span>';
        }
    } else {
        return '<span class="badge bg-secondary">Unassigned</span>';
    }
}

function getReviewerInfo(abstract) {
    if (abstract.assigned_reviewer_id && abstract.reviewer_name) {
        return `
            <div class="d-flex align-items-center">
                <div class="reviewer-avatar">${abstract.reviewer_name.charAt(0)}</div>
                <div>
                    <small><strong>${abstract.reviewer_name}</strong></small>
                    <br><small class="text-muted">${abstract.reviewer_email || ''}</small>
                </div>
            </div>
        `;
    } else {
        return '<span class="text-muted">Not assigned</span>';
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.abstract-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            selectedAbstracts.add(parseInt(checkbox.value));
        } else {
            selectedAbstracts.delete(parseInt(checkbox.value));
        }
    });
    
    updateBulkActionsPanel();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.abstract-checkbox');
    selectedAbstracts.clear();
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedAbstracts.add(parseInt(checkbox.value));
        }
    });
    
    updateBulkActionsPanel();
    
    // Update select all checkbox
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = selectedAbstracts.size === checkboxes.length;
    selectAll.indeterminate = selectedAbstracts.size > 0 && selectedAbstracts.size < checkboxes.length;
}

function updateBulkActionsPanel() {
    const panel = document.getElementById('bulkActionsPanel');
    const count = document.getElementById('selectedCount');
    
    count.textContent = selectedAbstracts.size;
    panel.style.display = selectedAbstracts.size > 0 ? 'block' : 'none';
}

function clearSelection() {
    selectedAbstracts.clear();
    document.querySelectorAll('.abstract-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActionsPanel();
}

function assignReviewer(abstractId) {
    document.getElementById('abstractIdToAssign').value = abstractId;
    const modal = new bootstrap.Modal(document.getElementById('assignReviewerModal'));
    modal.show();
}

function showBulkAssignModal() {
    if (selectedAbstracts.size === 0) {
        showAlert('Please select at least one abstract', 'warning');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
    modal.show();
}

async function submitReviewerAssignment() {
    const form = document.getElementById('assignReviewerForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/api/admin/assign-reviewer', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('assignReviewerModal'));
            modal.hide();
            await loadAbstracts();
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error assigning reviewer:', error);
        showAlert('Error assigning reviewer', 'danger');
    }
}

async function submitBulkAssignment() {
    const strategy = document.getElementById('assignmentStrategy').value;
    const dueDate = document.getElementById('bulkDueDate').value;
    
    let reviewers = [];
    
    if (strategy === 'single') {
        const reviewerId = document.getElementById('bulkReviewerSelect').value;
        if (!reviewerId) {
            showAlert('Please select a reviewer', 'warning');
            return;
        }
        reviewers = [reviewerId];
    } else {
        const checkboxes = document.querySelectorAll('#reviewerCheckboxes input:checked');
        if (checkboxes.length === 0) {
            showAlert('Please select at least one reviewer', 'warning');
            return;
        }
        reviewers = Array.from(checkboxes).map(cb => cb.value);
    }
    
    const data = {
        abstract_ids: Array.from(selectedAbstracts),
        reviewer_ids: reviewers,
        strategy: strategy,
        due_date: dueDate
    };
    
    try {
        const response = await fetch('/api/admin/bulk-assign-reviewers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showAlert(result.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal'));
            modal.hide();
            clearSelection();
            await loadAbstracts();
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        console.error('Error bulk assigning reviewers:', error);
        showAlert('Error bulk assigning reviewers', 'danger');
    }
}

function searchAbstracts() {
    const searchTerm = document.getElementById('searchAbstracts').value.toLowerCase();
    filteredAbstracts = allAbstracts.filter(abstract => 
        abstract.title.toLowerCase().includes(searchTerm) ||
        (abstract.author_name && abstract.author_name.toLowerCase().includes(searchTerm)) ||
        (abstract.author_email && abstract.author_email.toLowerCase().includes(searchTerm))
    );
    renderAbstractsTable();
}

function filterByStatus() {
    const status = document.getElementById('statusFilter').value;
    
    if (!status) {
        filteredAbstracts = [...allAbstracts];
    } else {
        filteredAbstracts = allAbstracts.filter(abstract => {
            switch(status) {
                case 'unassigned':
                    return !abstract.assigned_reviewer_id;
                case 'assigned':
                    return abstract.assigned_reviewer_id && abstract.review_status !== 'completed';
                case 'under_review':
                    return abstract.assigned_reviewer_id && abstract.review_status === 'in_progress';
                case 'completed':
                    return abstract.review_status === 'completed';
                default:
                    return true;
            }
        });
    }
    
    renderAbstractsTable();
}

function refreshAbstracts() {
    loadAbstracts();
}

function viewAbstractDetails(abstractId) {
    // Implementation for viewing abstract details
    showAlert('Abstract details view will be implemented', 'info');
}

function downloadAbstract(abstractId) {
    window.open(`/api/admin/download-abstract/${abstractId}`, '_blank');
}

function editAbstract(abstractId) {
    showAlert('Abstract editing will be implemented', 'info');
}

function deleteAbstract(abstractId) {
    if (confirm('Are you sure you want to delete this abstract? This action cannot be undone.')) {
        // Implementation for deleting abstract
        showAlert('Abstract deletion will be implemented', 'info');
    }
}

function exportAbstracts(format) {
    const selectedIds = Array.from(selectedAbstracts);
    const params = selectedIds.length > 0 ? `?ids=${selectedIds.join(',')}` : '';
    window.open(`/api/admin/export-abstracts/${format}${params}`, '_blank');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.admin-content');
    container.insertBefore(alertDiv, container.children[1]);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?php $this->endSection(); ?>