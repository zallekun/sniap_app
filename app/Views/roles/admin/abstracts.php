<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>Abstract Management<?= $this->endSection() ?>

<?= $this->section('head') ?>
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-card:nth-child(1) .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card:nth-child(2) .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card:nth-child(3) .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card:nth-child(4) .stat-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: between;
    align-items: center;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 3rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #9ca3af;
}

.alert {
    border: none;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
    padding: 0.4em 0.8em;
}

.author-info strong {
    font-size: 0.95rem;
}

.abstract-title strong {
    font-size: 0.95rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .abstract-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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
<div class="stats-grid">
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
<div class="content-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchAbstracts" 
                           placeholder="Search abstracts..." onkeyup="searchAbstracts()">
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
                <button class="btn btn-primary" onclick="showBulkAssignModal()">
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Abstract Submissions</h3>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                    type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('excel')">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('pdf')">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportAbstracts('csv')">
                    <i class="fas fa-file-csv me-2"></i>CSV
                </a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div id="abstractsTableContainer">
            <div class="text-center p-4">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Loading abstracts...</p>
            </div>
        </div>
    </div>
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
                    
                    <div class="mb-3" id="singleReviewerSelect">
                        <label for="bulkReviewerSelect" class="form-label">Select Reviewer</label>
                        <select class="form-select" id="bulkReviewerSelect">
                            <option value="">Loading reviewers...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="multipleReviewersSelect" style="display:none;">
                        <label class="form-label">Select Reviewers</label>
                        <div id="reviewerCheckboxes" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
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
<?= $this->endSection() ?>

<?= $this->section('additional_js') ?>
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
        // Simulate API call with sample data
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        allAbstracts = sampleAbstracts;
        filteredAbstracts = [...allAbstracts];
        renderAbstractsTable();
        
    } catch (error) {
        console.error('Error loading abstracts:', error);
        showAlert('Error loading abstracts', 'danger');
    }
}

async function loadReviewers() {
    try {
        // Simulate API call with sample data
        await new Promise(resolve => setTimeout(resolve, 500));
        
        availableReviewers = sampleReviewers;
        populateReviewerSelects();
        
    } catch (error) {
        console.error('Error loading reviewers:', error);
    }
}

async function loadStatistics() {
    try {
        // Calculate statistics from sample data
        await new Promise(resolve => setTimeout(resolve, 300));
        
        const total = allAbstracts.length;
        const pending = allAbstracts.filter(a => !a.assigned_reviewer_id).length;
        const underReview = allAbstracts.filter(a => a.review_status === 'in_progress').length;
        const completed = allAbstracts.filter(a => a.review_status === 'completed').length;
        
        document.getElementById('totalAbstracts').textContent = total;
        document.getElementById('pendingAssignment').textContent = pending;
        document.getElementById('underReview').textContent = underReview;
        document.getElementById('completedReviews').textContent = completed;
        
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
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="${reviewer.id}" id="reviewer_${reviewer.id}">
                <label class="form-check-label" for="reviewer_${reviewer.id}">
                    <strong>${reviewer.first_name} ${reviewer.last_name}</strong>
                    <br><small class="text-muted">${reviewer.email}</small>
                    <br><small class="text-info">Current assignments: ${reviewer.current_assignments || 0}</small>
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
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h4>No Abstracts Found</h4>
                <p>No abstracts match the current filter criteria.</p>
            </div>
        `;
        return;
    }
    
    let tableHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
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
                        <br><small class="text-muted">Submitted: ${formatDate(abstract.submission_date)}</small>
                    </div>
                </td>
                <td>
                    <div class="author-info">
                        <strong>${escapeHtml(abstract.author_name || '')}</strong>
                        <br><small class="text-muted">${escapeHtml(abstract.author_email || '')}</small>
                    </div>
                </td>
                <td>${escapeHtml(abstract.event_title || 'N/A')}</td>
                <td>
                    <span class="badge bg-light text-dark">${escapeHtml(abstract.category_name || 'N/A')}</span>
                </td>
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
                                    <i class="fas fa-download me-2"></i> Download
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="editAbstract(${abstract.id})">
                                    <i class="fas fa-edit me-2"></i> Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteAbstract(${abstract.id})">
                                    <i class="fas fa-trash me-2"></i> Delete
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
        } else if (abstract.review_status === 'in_progress') {
            return '<span class="badge bg-warning">Under Review</span>';
        } else {
            return '<span class="badge bg-info">Assigned</span>';
        }
    } else {
        return '<span class="badge bg-secondary">Unassigned</span>';
    }
}

function getReviewerInfo(abstract) {
    if (abstract.assigned_reviewer_id && abstract.reviewer_name) {
        const initials = abstract.reviewer_name.split(' ').map(n => n[0]).join('');
        return `
            <div class="d-flex align-items-center">
                <div class="reviewer-avatar">${initials}</div>
                <div>
                    <small><strong>${abstract.reviewer_name}</strong></small>
                    <br><small class="text-muted">${abstract.reviewer_email || ''}</small>
                    ${abstract.due_date ? `<br><small class="text-warning">Due: ${formatDate(abstract.due_date)}</small>` : ''}
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
    document.getElementById('selectAll').indeterminate = false;
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
    const abstractId = document.getElementById('abstractIdToAssign').value;
    const reviewerId = document.getElementById('reviewerSelect').value;
    const notes = document.getElementById('assignmentNotes').value;
    const dueDate = document.getElementById('dueDate').value;
    
    if (!reviewerId) {
        showAlert('Please select a reviewer', 'warning');
        return;
    }
    
    if (!dueDate) {
        showAlert('Please set a due date', 'warning');
        return;
    }
    
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Find and update the abstract
        const abstractIndex = allAbstracts.findIndex(a => a.id == abstractId);
        const reviewer = availableReviewers.find(r => r.id == reviewerId);
        
        if (abstractIndex !== -1 && reviewer) {
            allAbstracts[abstractIndex].assigned_reviewer_id = reviewer.id;
            allAbstracts[abstractIndex].reviewer_name = `${reviewer.first_name} ${reviewer.last_name}`;
            allAbstracts[abstractIndex].reviewer_email = reviewer.email;
            allAbstracts[abstractIndex].review_status = 'in_progress';
            allAbstracts[abstractIndex].due_date = dueDate;
            
            // Update reviewer's assignment count
            reviewer.current_assignments = (reviewer.current_assignments || 0) + 1;
        }
        
        showAlert('Reviewer assigned successfully!', 'success');
        const modal = bootstrap.Modal.getInstance(document.getElementById('assignReviewerModal'));
        modal.hide();
        
        // Reset form
        form.reset();
        
        await loadAbstracts();
        await loadStatistics();
        
    } catch (error) {
        console.error('Error assigning reviewer:', error);
        showAlert('Error assigning reviewer', 'danger');
    }
}

async function submitBulkAssignment() {
    const strategy = document.getElementById('assignmentStrategy').value;
    const dueDate = document.getElementById('bulkDueDate').value;
    
    if (!dueDate) {
        showAlert('Please set a due date', 'warning');
        return;
    }
    
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
    
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        const selectedAbstractsList = Array.from(selectedAbstracts);
        let assignedCount = 0;
        
        if (strategy === 'single') {
            // Assign same reviewer to all selected abstracts
            const reviewer = availableReviewers.find(r => r.id == reviewers[0]);
            selectedAbstractsList.forEach(abstractId => {
                const abstractIndex = allAbstracts.findIndex(a => a.id === abstractId);
                if (abstractIndex !== -1) {
                    allAbstracts[abstractIndex].assigned_reviewer_id = reviewer.id;
                    allAbstracts[abstractIndex].reviewer_name = `${reviewer.first_name} ${reviewer.last_name}`;
                    allAbstracts[abstractIndex].reviewer_email = reviewer.email;
                    allAbstracts[abstractIndex].review_status = 'in_progress';
                    allAbstracts[abstractIndex].due_date = dueDate;
                    assignedCount++;
                }
            });
            reviewer.current_assignments = (reviewer.current_assignments || 0) + assignedCount;
        } else {
            // Distribute assignments among selected reviewers
            selectedAbstractsList.forEach((abstractId, index) => {
                const reviewerIndex = index % reviewers.length;
                const reviewer = availableReviewers.find(r => r.id == reviewers[reviewerIndex]);
                const abstractIndex = allAbstracts.findIndex(a => a.id === abstractId);
                
                if (abstractIndex !== -1 && reviewer) {
                    allAbstracts[abstractIndex].assigned_reviewer_id = reviewer.id;
                    allAbstracts[abstractIndex].reviewer_name = `${reviewer.first_name} ${reviewer.last_name}`;
                    allAbstracts[abstractIndex].reviewer_email = reviewer.email;
                    allAbstracts[abstractIndex].review_status = 'in_progress';
                    allAbstracts[abstractIndex].due_date = dueDate;
                    assignedCount++;
                    
                    reviewer.current_assignments = (reviewer.current_assignments || 0) + 1;
                }
            });
        }
        
        showAlert(`Successfully assigned ${assignedCount} abstracts to reviewers!`, 'success');
        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal'));
        modal.hide();
        
        clearSelection();
        await loadAbstracts();
        await loadStatistics();
        populateReviewerSelects();
        
    } catch (error) {
        console.error('Error bulk assigning reviewers:', error);
        showAlert('Error bulk assigning reviewers', 'danger');
    }
}

function searchAbstracts() {
    const searchTerm = document.getElementById('searchAbstracts').value.toLowerCase();
    if (!searchTerm) {
        filteredAbstracts = [...allAbstracts];
    } else {
        filteredAbstracts = allAbstracts.filter(abstract => 
            abstract.title.toLowerCase().includes(searchTerm) ||
            (abstract.author_name && abstract.author_name.toLowerCase().includes(searchTerm)) ||
            (abstract.author_email && abstract.author_email.toLowerCase().includes(searchTerm)) ||
            (abstract.category_name && abstract.category_name.toLowerCase().includes(searchTerm))
        );
    }
    renderAbstractsTable();
    clearSelection();
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
                    return abstract.assigned_reviewer_id && abstract.review_status === 'in_progress';
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
    clearSelection();
}

function refreshAbstracts() {
    const refreshButton = document.querySelector('.btn[onclick="refreshAbstracts()"]');
    const originalHTML = refreshButton.innerHTML;
    refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    refreshButton.disabled = true;
    
    setTimeout(() => {
        loadAbstracts();
        loadStatistics();
        refreshButton.innerHTML = originalHTML;
        refreshButton.disabled = false;
        showAlert('Data refreshed successfully!', 'success');
    }, 1000);
}

function viewAbstractDetails(abstractId) {
    const abstract = allAbstracts.find(a => a.id === abstractId);
    if (!abstract) {
        showAlert('Abstract not found', 'danger');
        return;
    }
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>ID:</strong></td><td>#${abstract.id}</td></tr>
                    <tr><td><strong>Title:</strong></td><td>${escapeHtml(abstract.title)}</td></tr>
                    <tr><td><strong>Category:</strong></td><td>${escapeHtml(abstract.category_name || 'N/A')}</td></tr>
                    <tr><td><strong>Event:</strong></td><td>${escapeHtml(abstract.event_title || 'N/A')}</td></tr>
                    <tr><td><strong>Submission Date:</strong></td><td>${formatDate(abstract.submission_date)}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Author Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>${escapeHtml(abstract.author_name || 'N/A')}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${escapeHtml(abstract.author_email || 'N/A')}</td></tr>
                </table>
                
                <h6 class="text-muted mt-3">Review Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Status:</strong></td><td>${getAssignmentStatus(abstract)}</td></tr>
                    <tr><td><strong>Reviewer:</strong></td><td>${escapeHtml(abstract.reviewer_name || 'Not assigned')}</td></tr>
                    ${abstract.due_date ? `<tr><td><strong>Due Date:</strong></td><td>${formatDate(abstract.due_date)}</td></tr>` : ''}
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <h6 class="text-muted">Actions</h6>
            <button class="btn btn-primary btn-sm me-2" onclick="assignReviewer(${abstract.id}); bootstrap.Modal.getInstance(document.getElementById('abstractDetailsModal')).hide();">
                <i class="fas fa-user-plus"></i> Assign Reviewer
            </button>
            <button class="btn btn-outline-secondary btn-sm me-2" onclick="downloadAbstract(${abstract.id})">
                <i class="fas fa-download"></i> Download
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="editAbstract(${abstract.id})">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
    `;
    
    document.getElementById('abstractDetailsContent').innerHTML = content;
    const modal = new bootstrap.Modal(document.getElementById('abstractDetailsModal'));
    modal.show();
}

function downloadAbstract(abstractId) {
    showAlert('Download functionality would be implemented here', 'info');
    // In real implementation: window.open(`/api/admin/download-abstract/${abstractId}`, '_blank');
}

function editAbstract(abstractId) {
    showAlert('Edit functionality would be implemented here', 'info');
    // In real implementation: window.location.href = `/admin/abstracts/edit/${abstractId}`;
}

function deleteAbstract(abstractId) {
    if (confirm('Are you sure you want to delete this abstract? This action cannot be undone.')) {
        // Simulate deletion
        const index = allAbstracts.findIndex(a => a.id === abstractId);
        if (index !== -1) {
            allAbstracts.splice(index, 1);
            filteredAbstracts = filteredAbstracts.filter(a => a.id !== abstractId);
            renderAbstractsTable();
            loadStatistics();
            showAlert('Abstract deleted successfully', 'success');
        }
    }
}

function exportAbstracts(format) {
    const selectedIds = Array.from(selectedAbstracts);
    const message = selectedIds.length > 0 
        ? `Exporting ${selectedIds.length} selected abstracts as ${format.toUpperCase()}...`
        : `Exporting all abstracts as ${format.toUpperCase()}...`;
    
    showAlert(message, 'info');
    
    // In real implementation:
    // const params = selectedIds.length > 0 ? `?ids=${selectedIds.join(',')}` : '';
    // window.open(`/api/admin/export-abstracts/${format}${params}`, '_blank');
}

// Utility functions
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

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function showAlert(message, type = 'info') {
    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => {
        if (!alert.classList.contains('alert-permanent')) {
            alert.remove();
        }
    });
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${getAlertIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const contentDiv = document.querySelector('.admin-content') || document.body;
    contentDiv.insertBefore(alertDiv, contentDiv.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}
</script>
<?= $this->endSection() ?>