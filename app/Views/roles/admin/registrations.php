<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>Registration Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Registration Management</h1>
            <p class="mb-0 text-muted">Manage event registrations and participant data</p>
        </div>
        <div class="d-none d-sm-inline-block">
            <button class="btn btn-success me-2" onclick="exportRegistrations()">
                <i class="fas fa-download fa-sm"></i> Export Data
            </button>
            <button class="btn btn-primary" onclick="showBulkActionModal()">
                <i class="fas fa-tasks fa-sm"></i> Bulk Actions
            </button>
        </div>
    </div>
    <!-- Registration Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRegistrations">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confirmed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="confirmedRegistrations">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingRegistrations">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Paid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="paidRegistrations">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Registrations</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <select id="eventFilter" class="form-select">
                        <option value="">All Events</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select id="paymentFilter" class="form-select">
                        <option value="">All Payments</option>
                        <option value="pending">Payment Pending</option>
                        <option value="success">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-5 mb-2">
                    <input type="text" id="searchRegistrations" class="form-control" placeholder="Search participants...">
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="registrationsTable" class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="form-check-input">
                            </th>
                            <th>Participant</th>
                            <th>Event</th>
                            <th>Registration Type</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Registrations will be loaded via JavaScript -->
                    </tbody>
                </table>
                
                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center p-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading registrations...</div>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5" style="display: none;">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No registrations found</h5>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Detail Modal -->
<div id="registrationModal" class="modal fade" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationModalLabel">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeRegistrationModal()"></button>
            </div>
            <div class="modal-body">
                <div class="registration-detail-container">
                    <!-- Registration details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeRegistrationModal()">Close</button>
                <button type="button" class="btn btn-warning" id="updateStatusBtn" onclick="showUpdateStatusModal()">Update Status</button>
                <button type="button" class="btn btn-success" id="confirmBtn" onclick="confirmRegistration()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="modal fade" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Registration Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeUpdateStatusModal()"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select id="newStatus" name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statusReason" class="form-label">Reason (Optional)</label>
                        <textarea id="statusReason" name="reason" class="form-control" rows="3" placeholder="Enter reason for status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeUpdateStatusModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div id="bulkActionModal" class="modal fade" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeBulkActionModal()"></button>
            </div>
            <form id="bulkActionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulkAction" class="form-label">Select Action</label>
                        <select id="bulkAction" name="action" class="form-select" required>
                            <option value="">Choose Action</option>
                            <option value="confirm">Confirm Registrations</option>
                            <option value="cancel">Cancel Registrations</option>
                            <option value="send_email">Send Email</option>
                            <option value="export">Export Selected</option>
                        </select>
                    </div>
                    <div id="emailContent" class="mb-3" style="display: none;">
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Email Subject</label>
                            <input type="text" id="emailSubject" name="email_subject" class="form-control" placeholder="Enter email subject...">
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea id="emailMessage" name="email_message" class="form-control" rows="4" placeholder="Enter your message..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeBulkActionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Execute Action</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('additional_js') ?>
<script>
// Registration management functionality
let currentRegistrationId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadRegistrations();
    loadRegistrationStats();
    loadEventOptions();
    setupRegistrationFilters();
});

// API Base URLs sesuai dengan routes yang ada
const API_ROUTES = {
    registrations: '<?= base_url() ?>/admin/api/registrations',
    events: '<?= base_url() ?>/admin/api/events',
    stats: '<?= base_url() ?>/admin/api/stats'
};

function loadRegistrations() {
    showTableLoading();
    fetch(API_ROUTES.registrations)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            hideTableLoading();
            if (data.success) {
                populateRegistrationsTable(data.data || []);
            } else {
                console.error('Error loading registrations:', data.message);
                showError('Failed to load registrations: ' + (data.message || 'Unknown error'));
                showEmptyState();
            }
        })
        .catch(error => {
            hideTableLoading();
            console.error('Error:', error);
            showError('Failed to load registrations');
            showEmptyState();
        });
}

function loadRegistrationStats() {
    fetch(API_ROUTES.stats)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('totalRegistrations').textContent = data.data.total_registrations || 0;
                document.getElementById('confirmedRegistrations').textContent = data.data.confirmed_registrations || 0;
                document.getElementById('pendingRegistrations').textContent = data.data.pending_registrations || 0;
                document.getElementById('paidRegistrations').textContent = data.data.paid_registrations || 0;
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function loadEventOptions() {
    fetch(API_ROUTES.events)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const eventFilter = document.getElementById('eventFilter');
                data.data.forEach(event => {
                    const option = document.createElement('option');
                    option.value = event.id;
                    option.textContent = event.title || event.name;
                    eventFilter.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading events:', error);
        });
}

function populateRegistrationsTable(registrations) {
    const tbody = document.querySelector('#registrationsTable tbody');
    tbody.innerHTML = '';
    
    hideEmptyState();

    if (!registrations || registrations.length === 0) {
        showEmptyState();
        return;
    }

    registrations.forEach(registration => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="checkbox" name="registration_ids[]" value="${registration.id}">
            </td>
            <td>
                <div class="participant-info">
                    <strong>${registration.user_name || registration.name || 'N/A'}</strong>
                    <small class="d-block text-muted">${registration.user_email || registration.email || 'N/A'}</small>
                </div>
            </td>
            <td>${registration.event_title || registration.event_name || 'N/A'}</td>
            <td><span class="badge bg-info">${registration.registration_type || 'Standard'}</span></td>
            <td><small>${formatDate(registration.created_at || registration.registration_date)}</small></td>
            <td><span class="badge bg-${getStatusClass(registration.status)}">${capitalizeFirst(registration.status) || 'Pending'}</span></td>
            <td><span class="badge bg-${getPaymentClass(registration.payment_status)}">${capitalizeFirst(registration.payment_status) || 'Pending'}</span></td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="showRegistrationDetail(${registration.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="showUpdateStatusModal(${registration.id})" title="Update Status">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="confirmSingleRegistration(${registration.id})" title="Confirm" ${registration.status === 'confirmed' ? 'disabled' : ''}>
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="cancelRegistration(${registration.id})" title="Cancel" ${registration.status === 'cancelled' ? 'disabled' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function setupRegistrationFilters() {
    const eventFilter = document.getElementById('eventFilter');
    const statusFilter = document.getElementById('statusFilter');
    const paymentFilter = document.getElementById('paymentFilter');
    const searchInput = document.getElementById('searchRegistrations');

    [eventFilter, statusFilter, paymentFilter].forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });

    searchInput.addEventListener('input', debounce(applyFilters, 300));
}

function applyFilters() {
    const eventId = document.getElementById('eventFilter').value;
    const status = document.getElementById('statusFilter').value;
    const paymentStatus = document.getElementById('paymentFilter').value;
    const search = document.getElementById('searchRegistrations').value;

    const params = new URLSearchParams();
    if (eventId) params.append('event_id', eventId);
    if (status) params.append('status', status);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    if (search) params.append('search', search);

    const url = params.toString() ? `${API_ROUTES.registrations}?${params.toString()}` : API_ROUTES.registrations;

    showTableLoading();
    fetch(url)
        .then(response => response.json())
        .then(data => {
            hideTableLoading();
            if (data.success) {
                populateRegistrationsTable(data.data || []);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideTableLoading();
            showEmptyState();
        });
}

function showRegistrationDetail(registrationId) {
    currentRegistrationId = registrationId;
    
    fetch(`${API_ROUTES.registrations}/${registrationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                displayRegistrationDetail(data.data);
                // Use Bootstrap 5 modal API
                const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                modal.show();
            } else {
                showError('Error loading registration details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error loading registration details');
        });
}

function displayRegistrationDetail(registration) {
    const container = document.querySelector('.registration-detail-container');
    container.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="detail-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-user text-primary"></i> Participant Information
                    </h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Name:</strong></td><td>${registration.user_name || registration.name || 'N/A'}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${registration.user_email || registration.email || 'N/A'}</td></tr>
                        <tr><td><strong>Phone:</strong></td><td>${registration.user_phone || registration.phone || 'N/A'}</td></tr>
                        <tr><td><strong>Institution:</strong></td><td>${registration.institution || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-calendar text-success"></i> Event Information
                    </h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Event:</strong></td><td>${registration.event_title || registration.event_name || 'N/A'}</td></tr>
                        <tr><td><strong>Type:</strong></td><td><span class="badge bg-info">${registration.registration_type || 'Standard'}</span></td></tr>
                        <tr><td><strong>Date:</strong></td><td>${formatDate(registration.created_at || registration.registration_date)}</td></tr>
                        <tr><td><strong>Amount:</strong></td><td><strong class="text-success">${formatCurrency(registration.amount || 0)}</strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="detail-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-info-circle text-warning"></i> Status Information
                    </h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-${getStatusClass(registration.status)}">${capitalizeFirst(registration.status) || 'Pending'}</span></td></tr>
                        <tr><td><strong>Payment:</strong></td><td><span class="badge bg-${getPaymentClass(registration.payment_status)}">${capitalizeFirst(registration.payment_status) || 'Pending'}</span></td></tr>
                        <tr><td><strong>Updated:</strong></td><td>${formatDate(registration.updated_at)}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-section mb-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-sticky-note text-info"></i> Additional Information
                    </h5>
                    <table class="table table-borderless">
                        <tr><td><strong>Special Requirements:</strong></td><td>${registration.special_requirements || 'None'}</td></tr>
                        <tr><td><strong>Dietary Restrictions:</strong></td><td>${registration.dietary_restrictions || 'None'}</td></tr>
                        <tr><td><strong>Notes:</strong></td><td>${registration.notes || 'None'}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function showUpdateStatusModal(registrationId) {
    currentRegistrationId = registrationId;
    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    modal.show();
}

function closeUpdateStatusModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
    if (modal) modal.hide();
    document.getElementById('updateStatusForm').reset();
    currentRegistrationId = null;
}

function closeRegistrationModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
    if (modal) modal.hide();
    currentRegistrationId = null;
}

function showBulkActionModal() {
    const selectedIds = getSelectedRegistrationIds();
    if (selectedIds.length === 0) {
        showError('Please select at least one registration');
        return;
    }
    const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
    modal.show();
}

function closeBulkActionModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionModal'));
    if (modal) modal.hide();
    document.getElementById('bulkActionForm').reset();
    document.getElementById('emailContent').style.display = 'none';
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function confirmSingleRegistration(registrationId) {
    if (confirm('Are you sure you want to confirm this registration?')) {
        updateRegistrationStatus(registrationId, 'confirmed');
    }
}

function cancelRegistration(registrationId) {
    if (confirm('Are you sure you want to cancel this registration?')) {
        updateRegistrationStatus(registrationId, 'cancelled');
    }
}

function confirmRegistration() {
    if (currentRegistrationId) {
        confirmSingleRegistration(currentRegistrationId);
    }
}

function updateRegistrationStatus(registrationId, status, reason = '') {
    const formData = new FormData();
    formData.append('status', status);
    if (reason) formData.append('reason', reason);

    showLoading();
    fetch(`${API_ROUTES.registrations}/${registrationId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showSuccess('Registration status updated successfully');
            loadRegistrations();
            loadRegistrationStats();
            closeRegistrationModal();
            closeUpdateStatusModal();
        } else {
            showError('Error updating registration: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('Error updating registration');
    });
}

function exportRegistrations() {
    const selectedIds = getSelectedRegistrationIds();
    const params = selectedIds.length > 0 ? `?ids=${selectedIds.join(',')}` : '';
    
    showLoading();
    window.open(`${API_ROUTES.registrations}/export${params}`, '_blank');
    hideLoading();
}

function getSelectedRegistrationIds() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Event Listeners
document.getElementById('bulkAction').addEventListener('change', function() {
    const emailContent = document.getElementById('emailContent');
    emailContent.style.display = this.value === 'send_email' ? 'block' : 'none';
});

document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedIds = getSelectedRegistrationIds();
    if (selectedIds.length === 0) {
        showError('Please select at least one registration');
        return;
    }

    const action = document.getElementById('bulkAction').value;
    const formData = new FormData(this);
    formData.append('registration_ids', selectedIds.join(','));

    showLoading();
    fetch(`${API_ROUTES.registrations}/bulk-action`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showSuccess('Bulk action executed successfully');
            closeBulkActionModal();
            loadRegistrations();
            loadRegistrationStats();
            // Uncheck select all
            document.getElementById('selectAll').checked = false;
        } else {
            showError('Error executing bulk action: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('Error executing bulk action');
    });
});

document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentRegistrationId) return;

    const status = document.getElementById('newStatus').value;
    const reason = document.getElementById('statusReason').value;

    updateRegistrationStatus(currentRegistrationId, status, reason);
});

// UI Helper functions
function showTableLoading() {
    document.getElementById('loadingIndicator').style.display = 'block';
    document.querySelector('#registrationsTable tbody').innerHTML = '';
    hideEmptyState();
}

function hideTableLoading() {
    document.getElementById('loadingIndicator').style.display = 'none';
}

function showEmptyState() {
    document.getElementById('emptyState').style.display = 'block';
    hideTableLoading();
}

function hideEmptyState() {
    document.getElementById('emptyState').style.display = 'none';
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount || 0);
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function getStatusClass(status) {
    const statusClasses = {
        'pending': 'warning',
        'confirmed': 'success',
        'cancelled': 'danger',
        'completed': 'info'
    };
    return statusClasses[status] || 'secondary';
}

function getPaymentClass(paymentStatus) {
    const paymentClasses = {
        'pending': 'warning',
        'success': 'success',
        'paid': 'success',
        'failed': 'danger',
        'cancelled': 'danger'
    };
    return paymentClasses[paymentStatus] || 'secondary';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showLoading() {
    document.body.style.cursor = 'wait';
    // You can add a loading overlay here
}

function hideLoading() {
    document.body.style.cursor = 'default';
}

function showSuccess(message) {
    // Create Bootstrap toast for success
    const toastHtml = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    showToast(toastHtml);
}

function showError(message) {
    // Create Bootstrap toast for error
    const toastHtml = `
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    showToast(toastHtml);
}

function showToast(toastHtml) {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Initialize and show the toast
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = ['registrationModal', 'bulkActionModal', 'updateStatusModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
            if (modalId === 'updateStatusModal') {
                currentRegistrationId = null;
            }
        }
    });
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['registrationModal', 'bulkActionModal', 'updateStatusModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                if (modalId === 'updateStatusModal') {
                    currentRegistrationId = null;
                }
            }
        });
    }
});
</script>

<style>
/* Custom styles for better Bootstrap 5 integration */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.no-gutters {
    margin-right: 0;
    margin-left: 0;
}

.no-gutters > .col,
.no-gutters > [class*="col-"] {
    padding-right: 0;
    padding-left: 0;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-uppercase {
    text-transform: uppercase !important;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.h-100 {
    height: 100% !important;
}

.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.mr-2 {
    margin-right: 0.5rem !important;
}

.mb-1 {
    margin-bottom: 0.25rem !important;
}

.mb-0 {
    margin-bottom: 0 !important;
}

.h5 {
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.h3 {
    font-size: 1.75rem;
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 0.5rem;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table-hover tbody tr:hover td {
    background-color: rgba(0, 0, 0, 0.075);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.participant-info {
    max-width: 250px;
}

.participant-info strong {
    display: block;
    font-size: 14px;
    margin-bottom: 2px;
}

.participant-info small {
    font-size: 12px;
}

/* Toast container positioning */
.toast-container {
    z-index: 9999;
}

/* Loading spinner animation */
@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

.spinner-border {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

.text-primary {
    color: #4e73df !important;
}

.text-muted {
    color: #6c757d !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-none.d-sm-inline-block {
        display: none !important;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 2px;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 2px;
    }
    
    .table-responsive {
        border: 0;
    }
}

/* Card enhancements */
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Modal improvements */
.modal-xl {
    max-width: 1140px;
}

@media (max-width: 1200px) {
    .modal-xl {
        max-width: 90%;
    }
}

/* Form control focus states */
.form-control:focus,
.form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Badge improvements */
.badge {
    font-size: 0.75em;
    font-weight: 500;
}

/* Detail section styling */
.detail-section .table td {
    padding: 0.5rem 0.75rem;
    vertical-align: top;
    border: none;
}

.detail-section .table td:first-child {
    width: 35%;
    color: #495057;
    font-weight: 500;
}

.section-title {
    border-bottom: 2px solid #e3e6f0;
    padding-bottom: 8px;
    font-weight: 600;
    color: #5a5c69;
}
</style>
<?= $this->endSection() ?>

<?= $this->endSection() ?>