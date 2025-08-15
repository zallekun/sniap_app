<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>My Registrations<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">My Registrations</h2>
                        <p class="mb-0">Manage your event registrations and view details</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-calendar-check" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="totalRegistrations">0</h4>
                <small class="text-muted">Total Registrations</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="confirmedRegistrations">0</h4>
                <small class="text-muted">Confirmed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-clock" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="pendingRegistrations">0</h4>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-calendar-plus" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="upcomingEvents">0</h4>
                <small class="text-muted">Upcoming Events</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search registrations...">
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="confirmed">Confirmed</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<!-- Registrations Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Registration History</h5>
                <button class="btn btn-primary" onclick="window.location.href='/audience/events'">
                    <i class="fas fa-plus me-2"></i>Register for New Event
                </button>
            </div>
            <div class="card-body">
                <div id="loadingIndicator" class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading registrations...</p>
                </div>
                
                <div id="emptyState" class="text-center py-4" style="display: none;">
                    <i class="fas fa-calendar-plus text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">No registrations found</h6>
                    <p class="text-muted">You haven't registered for any events yet</p>
                    <a href="/audience/events" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Register for Event
                    </a>
                </div>
                
                <div id="registrationsTable" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Registration Type</th>
                                    <th>Date & Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="registrationsTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Detail Modal -->
<div class="modal fade" id="registrationDetailModal" tabindex="-1" aria-labelledby="registrationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationDetailModalLabel">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="registrationDetailContent">
                <!-- Registration details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadTicketBtn" style="display: none;">
                    <i class="fas fa-download me-2"></i>Download Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let allRegistrations = [];
let filteredRegistrations = [];

document.addEventListener('DOMContentLoaded', function() {
    loadRegistrations();
    
    // Setup search and filter
    document.getElementById('searchInput').addEventListener('input', filterRegistrations);
    document.getElementById('statusFilter').addEventListener('change', filterRegistrations);
});

function loadRegistrations() {
    fetch('/audience/api/registrations')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                allRegistrations = data.data;
                filteredRegistrations = [...allRegistrations];
                
                // Hide loading indicator
                document.getElementById('loadingIndicator').style.display = 'none';
                
                if (allRegistrations.length === 0) {
                    document.getElementById('emptyState').style.display = 'block';
                } else {
                    document.getElementById('registrationsTable').style.display = 'block';
                    updateRegistrationStatistics(allRegistrations);
                    updateRegistrationsTable(filteredRegistrations);
                }
            } else {
                showError('Failed to load registrations');
            }
        })
        .catch(error => {
            console.error('Error loading registrations:', error);
            showError('Failed to load registrations');
        });
}

function updateRegistrationStatistics(registrations) {
    let totalCount = registrations.length;
    let confirmedCount = 0;
    let pendingCount = 0;
    let upcomingCount = 0;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    registrations.forEach(registration => {
        const status = registration.registration_status?.toLowerCase();
        const eventDate = new Date(registration.event_date);
        
        if (status === 'confirmed' || status === 'approved') {
            confirmedCount++;
        } else if (status === 'pending') {
            pendingCount++;
        }
        
        if (eventDate >= today) {
            upcomingCount++;
        }
    });
    
    // Update statistics
    document.getElementById('totalRegistrations').textContent = totalCount;
    document.getElementById('confirmedRegistrations').textContent = confirmedCount;
    document.getElementById('pendingRegistrations').textContent = pendingCount;
    document.getElementById('upcomingEvents').textContent = upcomingCount;
}

function updateRegistrationsTable(registrations) {
    const tbody = document.getElementById('registrationsTableBody');
    tbody.innerHTML = '';
    
    if (registrations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No registrations match your search criteria</p>
                </td>
            </tr>
        `;
        return;
    }
    
    registrations.forEach(registration => {
        const row = document.createElement('tr');
        
        const statusBadge = getStatusBadge(registration.registration_status);
        const paymentBadge = getStatusBadge(registration.payment_status);
        
        row.innerHTML = `
            <td>
                <div class="fw-medium">${escapeHtml(registration.event_title || 'Event')}</div>
                <small class="text-muted">${escapeHtml(registration.description || '')}</small>
            </td>
            <td>
                <span class="badge bg-info">${escapeHtml(registration.registration_type || 'Standard')}</span>
            </td>
            <td>
                ${registration.event_date ? 
                    `<div>${formatDate(registration.event_date)}</div>
                     <small class="text-muted">${formatTime(registration.event_time)}</small>` 
                    : '<small class="text-muted">Date TBA</small>'
                }
            </td>
            <td>
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    ${escapeHtml(registration.location || 'Location TBA')}
                </small>
            </td>
            <td>${statusBadge}</td>
            <td>${paymentBadge}</td>
            <td>
                <div>${formatDate(registration.created_at)}</div>
                <small class="text-muted">${formatTime(registration.created_at)}</small>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewRegistrationDetails(${registration.id})">
                        <i class="fas fa-eye"></i> Details
                    </button>
                    ${registration.registration_status === 'confirmed' ? 
                        `<button class="btn btn-sm btn-outline-success" onclick="downloadTicket(${registration.id})">
                            <i class="fas fa-ticket-alt"></i> Ticket
                        </button>` : ''
                    }
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function filterRegistrations() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    
    filteredRegistrations = allRegistrations.filter(registration => {
        const matchesSearch = !searchTerm || 
            registration.event_title?.toLowerCase().includes(searchTerm) ||
            registration.registration_type?.toLowerCase().includes(searchTerm) ||
            registration.location?.toLowerCase().includes(searchTerm);
            
        const matchesStatus = !statusFilter || 
            registration.registration_status?.toLowerCase() === statusFilter;
            
        return matchesSearch && matchesStatus;
    });
    
    updateRegistrationsTable(filteredRegistrations);
}

function viewRegistrationDetails(registrationId) {
    const registration = allRegistrations.find(r => r.id == registrationId);
    if (!registration) return;
    
    const modalContent = document.getElementById('registrationDetailContent');
    modalContent.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Event Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Event:</strong></td><td>${escapeHtml(registration.event_title)}</td></tr>
                    <tr><td><strong>Date:</strong></td><td>${formatDate(registration.event_date)}</td></tr>
                    <tr><td><strong>Time:</strong></td><td>${formatTime(registration.event_time)}</td></tr>
                    <tr><td><strong>Location:</strong></td><td>${escapeHtml(registration.location || 'TBA')}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Registration Details</h6>
                <table class="table table-sm">
                    <tr><td><strong>Type:</strong></td><td>${escapeHtml(registration.registration_type)}</td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(registration.registration_status)}</td></tr>
                    <tr><td><strong>Payment:</strong></td><td>${getStatusBadge(registration.payment_status)}</td></tr>
                    <tr><td><strong>Registered:</strong></td><td>${formatDate(registration.created_at)}</td></tr>
                </table>
            </div>
        </div>
        ${registration.description ? `
            <div class="mt-3">
                <h6>Event Description</h6>
                <p class="text-muted">${escapeHtml(registration.description)}</p>
            </div>
        ` : ''}
    `;
    
    // Show/hide download ticket button
    const downloadBtn = document.getElementById('downloadTicketBtn');
    if (registration.registration_status === 'confirmed') {
        downloadBtn.style.display = 'block';
        downloadBtn.onclick = () => downloadTicket(registrationId);
    } else {
        downloadBtn.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('registrationDetailModal'));
    modal.show();
}

function downloadTicket(registrationId) {
    // Implement ticket download functionality
    alert(`Downloading ticket for registration ID: ${registrationId}\n\nThis feature will generate and download your event ticket.`);
}

function getStatusBadge(status) {
    const statusLower = status?.toLowerCase() || 'unknown';
    
    switch (statusLower) {
        case 'confirmed':
        case 'approved':
        case 'paid':
            return '<span class="badge bg-success">Confirmed</span>';
        case 'pending':
            return '<span class="badge bg-warning">Pending</span>';
        case 'cancelled':
        case 'failed':
            return '<span class="badge bg-danger">Cancelled</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function showError(message) {
    document.getElementById('loadingIndicator').style.display = 'none';
    document.getElementById('emptyState').innerHTML = `
        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
        <h6 class="mt-3 text-danger">Error</h6>
        <p class="text-muted">${message}</p>
        <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="fas fa-refresh me-2"></i>Try Again
        </button>
    `;
    document.getElementById('emptyState').style.display = 'block';
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

function formatTime(timeStr) {
    if (!timeStr) return 'N/A';
    const time = new Date(`2000-01-01 ${timeStr}`);
    return time.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}
</script>

<?= $this->endSection() ?>