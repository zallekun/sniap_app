<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>My Dashboard<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--primary), #1e40af) !important;
}

.card.border-0 {
    border: none !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.welcome-section .btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.opacity-75 {
    opacity: 0.75;
}

.new-user-events .card {
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.new-user-events .card-header {
    background: linear-gradient(45deg, #f8f9fa, #fff);
    border-bottom: 2px solid var(--primary);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">Welcome, <?= esc($user['first_name']) ?>!</h2>
                        <p class="mb-0">Track your conference registrations, view schedules, and access your certificates</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-graduation-cap" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">My Registrations</div>
                <div class="stat-value"><?= number_format($stats['total_registrations'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Confirmed Events</div>
                <div class="stat-value"><?= number_format($stats['confirmed_registrations'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Upcoming Events</div>
                <div class="stat-value"><?= number_format($stats['upcoming_events'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Certificates</div>
                <div class="stat-value"><?= number_format($stats['certificates'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="quick-actions-grid">
                    <a href="/audience/registrations" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        My Registrations
                    </a>
                    <a href="/audience/events" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>
                        My Schedule
                    </a>
                    <a href="/audience/certificates" class="btn btn-outline-primary">
                        <i class="fas fa-certificate me-2"></i>
                        My Certificates
                    </a>
                    <a href="/audience/payments" class="btn btn-outline-primary">
                        <i class="fas fa-credit-card me-2"></i>
                        Payment History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($registrations ?? [])): ?>
<!-- New User Layout: Focus on Available Events -->
<div class="row">
    <!-- Welcome Section for New Users -->
    <div class="col-12 mb-4">
        <div class="card border-0 bg-gradient-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">Welcome to SNIA Conference!</h4>
                        <p class="mb-3 opacity-75">Discover amazing events and start your journey with us. Join our vibrant community of researchers and practitioners.</p>
                        <a href="/audience/events" class="btn btn-light">
                            <i class="fas fa-calendar-check me-2"></i>Browse All Events
                        </a>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-rocket" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Events Section (Prominent for New Users) -->
<div class="row new-user-events">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Available Events</h5>
                <small class="text-muted">Choose an event to get started with your registration</small>
            </div>
            <div class="card-body" id="upcomingEventsContainer">
                <!-- Events will be loaded here -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 text-muted">Loading available events...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Existing User Layout: Focus on My Registrations -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">My Registrations</h5>
            </div>
            <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($registrations ?? [], 0, 5) as $registration): ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?= esc($registration['event_title']) ?></div>
                                        <small class="text-muted"><?= esc($registration['registration_type']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($registration['event_date']): ?>
                                            <div><?= date('M j, Y', strtotime($registration['event_date'])) ?></div>
                                            <small class="text-muted"><?= date('g:i A', strtotime($registration['event_time'])) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Date TBA</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = match($registration['registration_status'] ?? 'pending') {
                                            'confirmed' => 'bg-success',
                                            'cancelled' => 'bg-danger', 
                                            'approved' => 'bg-success',
                                            default => 'bg-warning'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($registration['registration_status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $paymentClass = match($registration['payment_status'] ?? 'pending') {
                                            'paid' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            default => 'bg-warning'
                                        };
                                        ?>
                                        <span class="badge <?= $paymentClass ?>">
                                            <?= ucfirst($registration['payment_status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        // Clean status values (trim whitespace and convert to lowercase for comparison)
                                        $paymentStatus = trim(strtolower($registration['payment_status'] ?? 'pending'));
                                        $registrationStatus = trim(strtolower($registration['registration_status'] ?? 'pending'));
                                        
                                        ?>
                                        
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Always show details button -->
                                            <a href="/audience/registrations?highlight=<?= $registration['id'] ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Details
                                            </a>
                                            
                                            <!-- Show Pay button if payment is pending -->
                                            <?php if ($paymentStatus === 'pending'): ?>
                                            <a href="/payment/<?= $registration['id'] ?>" class="btn btn-outline-success">
                                                <i class="fas fa-credit-card me-1"></i>Pay
                                            </a>
                                            <?php endif; ?>
                                            
                                            <!-- Show Cancel button if registration is pending -->
                                            <?php if ($registrationStatus === 'pending'): ?>
                                            <button class="btn btn-outline-danger" onclick="cancelRegistration(<?= $registration['id'] ?>)">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/my-registrations" class="btn btn-outline-primary">
                            View All Registrations
                        </a>
                    </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Upcoming Events</h5>
            </div>
            <div class="card-body" id="upcomingEventsContainer">
                <!-- Loading indicator -->
                <div class="text-center py-3" id="eventsLoading">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading events...</div>
                </div>
                
                <!-- Removed hardcoded events - using only database data -->
                
                <!-- Empty state -->
                <div style="display: none;" id="noEvents" class="text-center py-4">
                    <i class="fas fa-calendar-plus text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No upcoming events</p>
                    <a href="/audience/events" class="btn btn-sm btn-primary">Browse Events</a>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Quick Help</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-question-circle me-2"></i>How to register for events
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-credit-card me-2"></i>Payment methods
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-certificate me-2"></i>How to get certificates
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-decoration-none">
                            <i class="fas fa-envelope me-2"></i>Contact support
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load real-time statistics
    loadAudienceStats();
    
    // Load registrations table
    loadRegistrationsTable();
    
    // Load upcoming events (only if data is static)
    loadUpcomingEvents();
});

function loadAudienceStats() {
    fetch('/audience/api/stats')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const stats = data.data;
                
                // Update statistics cards
                updateStatCard('total_registrations', stats.total_registrations || 0);
                updateStatCard('upcoming_events', stats.upcoming_events || 0);
                updateStatCard('completed_events', stats.completed_events || 0);
                updateStatCard('certificates_earned', stats.certificates_earned || 0);
            }
        })
        .catch(error => {
            console.error('Error loading audience stats:', error);
        });
}

function updateStatCard(type, value) {
    const cards = document.querySelectorAll('.stat-card');
    
    cards.forEach(card => {
        const icon = card.querySelector('.stat-icon i');
        if (!icon) return;
        
        let targetCard = null;
        
        if (type === 'total_registrations' && icon.classList.contains('fa-calendar-check')) {
            targetCard = card;
        } else if (type === 'upcoming_events' && icon.classList.contains('fa-clock')) {
            targetCard = card;
        } else if (type === 'completed_events' && icon.classList.contains('fa-check-circle')) {
            targetCard = card;
        } else if (type === 'certificates_earned' && icon.classList.contains('fa-certificate')) {
            targetCard = card;
        }
        
        if (targetCard) {
            const valueElement = targetCard.querySelector('.stat-value');
            if (valueElement) {
                valueElement.textContent = value.toLocaleString();
            }
        }
    });
}

function loadRegistrationsTable() {
    fetch('/audience/api/registrations')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const registrations = data.data;
                const tableContainer = document.querySelector('.table-responsive');
                
                if (registrations.length === 0) {
                    // Show empty state (already exists in template)
                    return;
                }
                
                // Update the table with real data
                updateRegistrationsTable(registrations.slice(0, 5));
            }
        })
        .catch(error => {
            console.error('Error loading registrations:', error);
        });
}

function updateRegistrationsTable(registrations) {
    const tbody = document.querySelector('.table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    registrations.forEach(registration => {
        const row = document.createElement('tr');
        
        // Status badge classes
        const statusClass = getStatusBadgeClass(registration.registration_status);
        const paymentClass = getStatusBadgeClass(registration.payment_status);
        
        row.innerHTML = `
            <td>
                <div class="fw-medium">${escapeHtml(registration.event_title || 'Event')}</div>
                <small class="text-muted">${escapeHtml(registration.registration_type || 'Standard')}</small>
            </td>
            <td>
                ${registration.event_date ? 
                    `<div>${formatDate(registration.event_date)}</div>
                     <small class="text-muted">${formatTime(registration.event_time)}</small>` 
                    : '<small class="text-muted">Date TBA</small>'
                }
            </td>
            <td>
                <span class="badge ${statusClass}">
                    ${capitalizeFirst(registration.registration_status || 'pending')}
                </span>
            </td>
            <td>
                <span class="badge ${paymentClass}">
                    ${capitalizeFirst(registration.payment_status || 'pending')}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <!-- Always show details button -->
                    <a href="/audience/registrations?highlight=${registration.id}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Details
                    </a>
                    
                    ${(registration.payment_status || 'pending').toLowerCase().trim() === 'pending' ? 
                        `<a href="/payment/${registration.id}" class="btn btn-outline-success">
                            <i class="fas fa-credit-card me-1"></i>Pay
                        </a>` : ''
                    }
                    
                    ${(registration.registration_status || 'pending').toLowerCase().trim() === 'pending' ? 
                        `<button class="btn btn-outline-danger" onclick="cancelRegistration(${registration.id})">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>` : ''
                    }
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function loadUpcomingEvents() {
    fetch('/audience/api/events')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const events = data.data;
                updateUpcomingEventsSection(events.slice(0, 3));
            }
        })
        .catch(error => {
            console.error('Error loading upcoming events:', error);
        });
}

function updateUpcomingEventsSection(events) {
    const eventsContainer = document.querySelector('.col-lg-4 .card .card-body');
    if (!eventsContainer || events.length === 0) return;
    
    eventsContainer.innerHTML = '';
    
    events.forEach((event, index) => {
        const alertClass = index % 2 === 0 ? 'alert-info' : 'alert-success';
        const iconClass = index % 2 === 0 ? 'fa-calendar' : 'fa-laptop';
        
        const eventDiv = document.createElement('div');
        eventDiv.className = `alert ${alertClass}`;
        eventDiv.innerHTML = `
            <h6><i class="fas ${iconClass} me-2"></i>${escapeHtml(event.title)}</h6>
            <p class="mb-2">${escapeHtml(event.description || 'Conference event')}</p>
            <small class="text-muted">
                <i class="fas fa-calendar me-1"></i>${formatDate(event.event_date)}<br>
                <i class="fas fa-map-marker-alt me-1"></i>${escapeHtml(event.location || 'Venue TBA')}
            </small>
            <div class="mt-2">
                <a href="/event-details/${event.id}" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
        `;
        
        eventsContainer.appendChild(eventDiv);
    });
}

// Utility functions
function getStatusBadgeClass(status) {
    switch (status?.toLowerCase()) {
        case 'confirmed':
        case 'approved':
        case 'paid':
            return 'bg-success';
        case 'cancelled':
        case 'failed':
            return 'bg-danger';
        case 'pending':
        default:
            return 'bg-warning';
    }
}

function capitalizeFirst(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'Date TBA';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function formatTime(timeStr) {
    if (!timeStr) return 'Time TBA';
    const time = new Date(`2000-01-01 ${timeStr}`);
    return time.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit' 
    });
}

// Load upcoming events
async function loadUpcomingEvents() {
    try {
        // Use existing dashboard events endpoint
        let response = await fetch('/dashboard/events');
        let data = await response.json();
        
        // If that fails, try the API v1 events endpoint
        if (!response.ok || data.status !== 'success') {
            console.log('Dashboard API failed, trying v1 API...');
            response = await fetch('/api/v1/events');
            data = await response.json();
        }
        
        const loadingEl = document.getElementById('eventsLoading');
        if (loadingEl) loadingEl.style.display = 'none';
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            // Debug: Log data untuk verify realtime dari database
            console.log('🔍 Events loaded from:', data.source || 'unknown');
            console.log('🕒 Data timestamp:', data.timestamp || 'no timestamp');
            console.log('📊 Total events in DB:', data.total_in_db || 'unknown');
            console.log('🎯 Events data:', data.data);
            
            // Store full event list in global scope for modal usage
            window.upcomingEvents = data.data;
            
            // Filter to only upcoming events (future events) and limit to 3 for dashboard display
            const upcomingEvents = data.data
                .filter(event => new Date(event.event_date) >= new Date())
                .slice(0, 3);
            
            if (upcomingEvents.length > 0) {
                displayUpcomingEvents(upcomingEvents);
            } else {
                // No upcoming events
                const noEventsEl = document.getElementById('noEvents');
                if (noEventsEl) noEventsEl.style.display = 'block';
            }
        } else {
            // Show no events message if no data
            const noEventsEl = document.getElementById('noEvents');
            if (noEventsEl) noEventsEl.style.display = 'block';
        }
        
        return Promise.resolve();
    } catch (error) {
        console.error('Error loading upcoming events:', error);
        const loadingEl = document.getElementById('eventsLoading');
        const noEventsEl = document.getElementById('noEvents');
        if (loadingEl) loadingEl.style.display = 'none';
        if (noEventsEl) noEventsEl.style.display = 'block';
        
        return Promise.reject(error);
    }
}

function displayUpcomingEvents(events) {
    const container = document.getElementById('upcomingEventsContainer');
    if (!container) {
        console.error('upcomingEventsContainer element not found');
        return;
    }
    
    let html = '';
    
    events.forEach(event => {
        // Determine alert class based on registration status
        let alertClass = 'alert-info';
        let actionButton = '';
        
        if (event.is_registered) {
            alertClass = 'alert-success';
            const statusText = event.registration_status === 'pending' ? 'Pending' : 'Confirmed';
            const paymentText = event.payment_status === 'paid' ? 'Paid' : 'Unpaid';
            
            actionButton = `
                <div class="mt-2">
                    <span class="badge bg-success me-2">
                        <i class="fas fa-check me-1"></i>Registered (${statusText})
                    </span>
                    ${event.payment_status === 'pending' ? `
                        <a href="/payment/${event.registration_id}" class="btn btn-sm btn-warning">
                            <i class="fas fa-credit-card me-1"></i>Pay Now
                        </a>
                    ` : `
                        <span class="badge bg-info">
                            <i class="fas fa-money-check-alt me-1"></i>${paymentText}
                        </span>
                    `}
                </div>
            `;
        } else {
            alertClass = event.format === 'online' ? 'alert-primary' : 'alert-info';
            actionButton = `
                <div class="mt-2">
                    <button class="btn btn-sm btn-primary" onclick="showRegistrationModal('${event.id}')">
                        <i class="fas fa-user-plus me-1"></i>Register Now
                    </button>
                </div>
            `;
        }
        
        const icon = event.format === 'online' ? 'fas fa-video' : 'fas fa-map-marker-alt';
        
        html += `
            <div class="alert ${alertClass}">
                <h6>
                    <i class="fas fa-calendar me-2"></i>${escapeHtml(event.title)}
                    ${event.is_registered ? '<i class="fas fa-check-circle text-success ms-2"></i>' : ''}
                </h6>
                <p class="mb-2">${escapeHtml(event.description) || 'Event description'}</p>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>${formatDate(event.event_date)}<br>
                    <i class="${icon} me-1"></i>${escapeHtml(event.location) || 'Location TBA'}
                    ${event.registration_fee > 0 ? `<br><i class="fas fa-tag me-1"></i>Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : '<br><i class="fas fa-gift me-1"></i>Free Event'}
                </small>
                ${actionButton}
            </div>
        `;
    });
    
    if (html === '') {
        html = `
            <div class="alert alert-light text-center">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h6>No Upcoming Events</h6>
                <p class="text-muted mb-0">Check back later for new events!</p>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

// Helper function to get current registration data
function getCurrentRegistrationData(registrationId) {
    // Try to get data from current page context
    const registrations = window.currentRegistrations || [];
    return registrations.find(r => r.id == registrationId) || {
        id: registrationId,
        event_title: 'Unknown Event',
        event_date: null,
        registration_type: 'Standard'
    };
}

// Show cancel confirmation modal
function showCancelConfirmationModal(registrationData) {
    return new Promise((resolve) => {
        // Populate modal with registration details
        const detailsContainer = document.getElementById('cancelRegistrationDetails');
        detailsContainer.innerHTML = `
            <div class="card-body">
                <h6 class="mb-2">${registrationData.event_title || 'Unknown Event'}</h6>
                <div class="row text-muted small">
                    <div class="col-6">
                        <i class="fas fa-calendar me-1"></i>
                        ${registrationData.event_date ? new Date(registrationData.event_date).toLocaleDateString() : 'Date TBA'}
                    </div>
                    <div class="col-6">
                        <i class="fas fa-tag me-1"></i>
                        ${registrationData.registration_type || 'Standard'}
                    </div>
                </div>
            </div>
        `;
        
        // Reset modal state
        const confirmCheckbox = document.getElementById('confirmCancellation');
        let confirmBtn = document.getElementById('confirmCancelBtn');
        
        confirmCheckbox.checked = false;
        confirmBtn.disabled = true;
        
        // Show loading state
        const btnText = confirmBtn.querySelector('.btn-text');
        const btnLoading = confirmBtn.querySelector('.btn-loading');
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
        
        // Handle confirm button click (remove previous event listeners to prevent duplicates)
        confirmBtn.replaceWith(confirmBtn.cloneNode(true));
        confirmBtn = document.getElementById('confirmCancelBtn');
        
        // Handle checkbox change
        confirmCheckbox.onchange = function() {
            confirmBtn.disabled = !this.checked;
        };
        
        confirmBtn.onclick = function() {
            // Prevent multiple clicks
            if (confirmBtn.disabled) return;
            
            // Show loading state
            const newBtnText = confirmBtn.querySelector('.btn-text');
            const newBtnLoading = confirmBtn.querySelector('.btn-loading');
            newBtnText.classList.add('d-none');
            newBtnLoading.classList.remove('d-none');
            confirmBtn.disabled = true;
            
            // Store the registration ID for processing
            confirmBtn.dataset.registrationId = registrationData.id;
            
            resolve(true);
        };
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('cancelConfirmationModal'));
        modal.show();
        
        // Handle modal dismiss
        document.getElementById('cancelConfirmationModal').addEventListener('hidden.bs.modal', function() {
            if (!confirmBtn.dataset.registrationId) {
                resolve(false);
            }
        }, { once: true });
    });
}

// Cancel registration function
async function cancelRegistration(registrationId) {
    // Find registration data for better UI
    const registrationData = getCurrentRegistrationData(registrationId);
    
    // Show custom confirmation modal instead of simple confirm
    const shouldCancel = await showCancelConfirmationModal(registrationData);
    if (!shouldCancel) {
        return;
    }
    
    try {
        // Get fresh CSRF token from meta tag (may have been updated)
        const csrfName = window.CI?.csrf_name || 'csrf_test_name';
        let csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '';
        
        // Fallback to window.CI if meta tag not found
        if (!csrfToken && window.CI?.csrf_token) {
            csrfToken = window.CI.csrf_token;
        }
        
        // Debug CSRF
        console.log('CSRF Name:', csrfName);
        console.log('CSRF Token:', csrfToken);
        console.log('Window CI:', window.CI);
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            showErrorAlert('Security Error', 'CSRF token not found. Please refresh the page.');
            return;
        }
        
        // Show processing state in modal if it exists
        const modal = document.getElementById('cancelConfirmationModal');
        const confirmBtn = document.getElementById('confirmCancelBtn');
        
        if (confirmBtn) {
            const btnText = confirmBtn.querySelector('.btn-text');
            const btnLoading = confirmBtn.querySelector('.btn-loading');
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
        }
        
        // Create form data with correct CodeIgniter CSRF format
        const params = new URLSearchParams();
        params.append('registration_id', registrationId);
        params.append(csrfName, csrfToken); // Use correct CSRF field name
        
        // Debug request
        console.log('Cancel request:', {
            url: '/audience/cancel-registration',
            registrationId: registrationId,
            csrfField: csrfName,
            csrfValue: csrfToken,
            body: params.toString()
        });
        
        // Use session-based cancel endpoint with proper CSRF
        const response = await fetch('/audience/cancel-registration', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            // Update CSRF token if provided in response
            if (data.csrf_token) {
                document.querySelector('meta[name="X-CSRF-TOKEN"]')?.setAttribute('content', data.csrf_token);
                if (window.CI) {
                    window.CI.csrf_token = data.csrf_token;
                }
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cancelConfirmationModal'));
            if (modal) {
                modal.hide();
            }
            
            // Show success message with better UX
            showSuccessAlert('Registration Cancelled Successfully', 
                           'Your registration has been cancelled. You can register again if spots are available.');
            
            // Update UI without full page reload
            setTimeout(() => {
                // Try to reload just the registrations data
                if (typeof loadRegistrationsTable === 'function') {
                    loadRegistrationsTable();
                } else {
                    // Fallback to page reload
                    window.location.reload();
                }
            }, 2000);
        } else {
            // Reset modal button state
            if (confirmBtn) {
                const btnText = confirmBtn.querySelector('.btn-text');
                const btnLoading = confirmBtn.querySelector('.btn-loading');
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                confirmBtn.disabled = false;
            }
            
            showErrorAlert('Cancellation Failed', data.message || 'Failed to cancel registration. Please try again.');
        }
    } catch (error) {
        console.error('Cancel registration error:', error);
        
        // Reset modal button state
        if (confirmBtn) {
            const btnText = confirmBtn.querySelector('.btn-text');
            const btnLoading = confirmBtn.querySelector('.btn-loading');
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            confirmBtn.disabled = false;
        }
        
        showErrorAlert('Network Error', 'Failed to connect to server. Please check your connection and try again.');
    }
}

// Enhanced alert functions
function showSuccessAlert(title, message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>${title}</strong><br>
            <small>${message}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container-fluid') || document.body;
    const alertDiv = document.createElement('div');
    alertDiv.innerHTML = alertHtml;
    container.insertBefore(alertDiv.firstElementChild, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert-success');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

function showErrorAlert(title, message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>${title}</strong><br>
            <small>${message}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container-fluid') || document.body;
    const alertDiv = document.createElement('div');
    alertDiv.innerHTML = alertHtml;
    container.insertBefore(alertDiv.firstElementChild, container.firstChild);
    
    // Auto-dismiss after 8 seconds (longer for errors)
    setTimeout(() => {
        const alert = container.querySelector('.alert-danger');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 8000);
}

// Store current registrations data for modal
function loadRegistrationsTable() {
    fetch('/audience/api/registrations')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Store for modal use
                window.currentRegistrations = data.data;
                // Update the dashboard table
                updateRegistrationsTable(data.data.slice(0, 5));
            }
        })
        .catch(error => {
            console.error('Error loading registrations:', error);
        });
}

// Show registration modal with event details
function showRegistrationModal(eventId) {
    // Find event data from upcomingEvents global variable
    let eventData = null;
    if (window.upcomingEvents) {
        eventData = window.upcomingEvents.find(event => event.id == eventId);
    }
    
    if (!eventData) {
        showErrorAlert('Event Not Found', 'Could not find event details. Please refresh the page and try again.');
        return;
    }
    
    // Populate event details in modal
    const eventDetailsContainer = document.getElementById('registrationEventDetails');
    eventDetailsContainer.innerHTML = `
        <div class="card-body">
            <h6 class="card-title">${escapeHtml(eventData.title)}</h6>
            <div class="row text-sm">
                <div class="col-6">
                    <strong>Date:</strong><br>
                    <span class="text-muted">${formatDate(eventData.event_date)}</span>
                </div>
                <div class="col-6">
                    <strong>Time:</strong><br>
                    <span class="text-muted">${formatTime(eventData.event_time)} WIB</span>
                </div>
                <div class="col-6 mt-2">
                    <strong>Location:</strong><br>
                    <span class="text-muted">${eventData.location || 'Location TBA'}</span>
                </div>
                <div class="col-6 mt-2">
                    <strong>Registration Fee:</strong><br>
                    <span class="text-success fw-bold">
                        ${eventData.registration_fee > 0 ? `Rp ${parseInt(eventData.registration_fee).toLocaleString('id-ID')}` : 'FREE'}
                    </span>
                </div>
            </div>
            ${eventData.description ? `<div class="mt-2"><small class="text-muted">${escapeHtml(eventData.description)}</small></div>` : ''}
        </div>
    `;
    
    // Reset form
    const form = document.getElementById('registrationForm');
    form.reset();
    document.getElementById('agreeTerms').checked = false;
    
    // Store eventId in modal for later use
    const modal = document.getElementById('registrationConfirmationModal');
    modal.dataset.eventId = eventId;
    
    // Setup confirm button event
    const confirmBtn = document.getElementById('confirmRegistrationBtn');
    confirmBtn.onclick = () => registerForEvent(eventId);
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Register for event function
async function registerForEvent(eventId) {
    if (!eventId) {
        showErrorAlert('Missing Information', 'Event ID is required');
        return;
    }
    
    // Validate form
    const form = document.getElementById('registrationForm');
    const agreeTerms = document.getElementById('agreeTerms');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    if (!agreeTerms.checked) {
        showErrorAlert('Terms Required', 'Please agree to the terms and conditions before registering');
        return;
    }
    
    // Show loading state
    const confirmBtn = document.getElementById('confirmRegistrationBtn');
    const btnText = confirmBtn.querySelector('.btn-text');
    const btnLoading = confirmBtn.querySelector('.btn-loading');
    btnText.classList.add('d-none');
    btnLoading.classList.remove('d-none');
    confirmBtn.disabled = true;
    
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content');
        const csrfTokenName = window.CI?.csrf_name || 'csrf_test_name';
        
        // Get form data
        const formData = new FormData(form);
        
        // Prepare request data
        const params = new URLSearchParams({
            event_id: eventId,
            registration_type: formData.get('registration_type') || 'audience'
        });
        
        // Add CSRF token if available
        if (csrfToken) {
            params.append(csrfTokenName, csrfToken);
        }
        
        // Try audience endpoint first
        const response = await fetch('/audience/register-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        });
        
        const data = await response.json();
        
        if (response.ok && data.status === 'success') {
            // Update CSRF token if provided
            if (data.csrf_token) {
                document.querySelector('meta[name="X-CSRF-TOKEN"]')?.setAttribute('content', data.csrf_token);
                if (window.CI) {
                    window.CI.csrf_token = data.csrf_token;
                }
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('registrationConfirmationModal'));
            if (modal) {
                modal.hide();
            }
            
            showSuccessAlert('Registration Successful!', 'You have been successfully registered for this event. Check My Registrations for details.');
            
            // Reload page to update data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Handle different error cases
            let errorMessage = 'Unknown error occurred during registration';
            
            if (response.status === 409) {
                // Conflict - already registered
                errorMessage = 'You are already registered for this event. Check My Registrations panel for details.';
            } else if (response.status === 403) {
                // CSRF or authorization error
                errorMessage = 'Security validation failed. Please refresh the page and try again.';
            } else if (response.status === 422) {
                // Validation error
                errorMessage = data.message || 'Invalid registration data. Please check your input and try again.';
            } else if (data && data.message) {
                errorMessage = data.message;
            }
            
            showErrorAlert('Registration Failed', errorMessage);
        }
    } catch (error) {
        console.error('Error registering for event:', error);
        showErrorAlert('Network Error', 'Failed to connect to server. Please check your connection and try again.');
    } finally {
        // Reset button state
        const confirmBtn = document.getElementById('confirmRegistrationBtn');
        if (confirmBtn) {
            const btnText = confirmBtn.querySelector('.btn-text');
            const btnLoading = confirmBtn.querySelector('.btn-loading');
            if (btnText && btnLoading) {
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
            confirmBtn.disabled = false;
        }
    }
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load registrations data for modal use (only if user has registrations)
    const hasRegistrations = <?= !empty($registrations ?? []) ? 'true' : 'false' ?>;
    
    if (hasRegistrations) {
        loadRegistrationsTable();
    }
    
    // Always load upcoming events if the container exists
    if (document.getElementById('upcomingEventsContainer')) {
        loadUpcomingEvents();
    }
});
</script>

<!-- Cancel Registration Confirmation Modal -->
<div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancel Registration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-calendar-times text-danger" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
                <h6 class="text-center mb-3">Are you sure you want to cancel this registration?</h6>
                
                <div id="cancelRegistrationDetails" class="card bg-light mb-3">
                    <!-- Registration details will be populated here -->
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-info-circle me-2"></i>Important Notes:</h6>
                    <ul class="mb-0 small">
                        <li>This action cannot be undone</li>
                        <li>You will lose your registration spot</li>
                        <li>Refund policies apply based on cancellation timing</li>
                        <li>You can re-register if spots are still available</li>
                    </ul>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmCancellation">
                    <label class="form-check-label" for="confirmCancellation">
                        I understand the consequences and want to cancel this registration
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Keep Registration
                </button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn" disabled>
                    <span class="btn-text">
                        <i class="fas fa-trash-alt me-1"></i>Cancel Registration
                    </span>
                    <span class="btn-loading d-none">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Cancelling...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Registration Confirmation Modal -->
<div class="modal fade" id="registrationConfirmationModal" tabindex="-1" aria-labelledby="registrationConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="registrationConfirmationModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Event Registration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-calendar-plus text-success" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
                <h6 class="text-center mb-3">Register for this event?</h6>
                
                <div id="registrationEventDetails" class="card bg-light mb-3">
                    <!-- Event details will be populated here -->
                </div>
                
                <form id="registrationForm">
                    <div class="mb-3">
                        <label class="form-label">Registration Type:</label>
                        <select class="form-select" name="registration_type" required>
                            <option value="audience">Participant/Audience</option>
                            <option value="presenter">Presenter (if applicable)</option>
                        </select>
                    </div>
                    
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the terms and conditions of this event
                        </label>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Registration Policy:</strong> Free events are confirmed immediately. 
                        Paid events require payment confirmation before final registration.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmRegistrationBtn">
                    <span class="btn-text">
                        <i class="fas fa-check me-1"></i>Confirm Registration
                    </span>
                    <span class="btn-loading d-none">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Registering...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>