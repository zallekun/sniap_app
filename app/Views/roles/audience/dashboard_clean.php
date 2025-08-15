<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>My Dashboard<?= $this->endSection() ?>

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

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">My Registrations</h5>
            </div>
            <div class="card-body">
                <?php if (empty($registrations ?? [])): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">No registrations yet</h6>
                        <p class="text-muted">Register for your first event to get started</p>
                        <a href="/my-registrations" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Register Now
                        </a>
                    </div>
                <?php else: ?>
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
                                        <a href="/registration/<?= $registration['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Details
                                        </a>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Upcoming Events</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-calendar me-2"></i>SNIA 2024</h6>
                    <p class="mb-2">Seminar Nasional Informatika</p>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>December 15, 2024<br>
                        <i class="fas fa-map-marker-alt me-1"></i>Gedung Serbaguna
                    </small>
                    <div class="mt-2">
                        <a href="/event-details/1" class="btn btn-sm btn-outline-primary">View Details</a>
                    </div>
                </div>
                
                <div class="alert alert-success">
                    <h6><i class="fas fa-laptop me-2"></i>SNIA Workshop</h6>
                    <p class="mb-2">Workshop khusus teknologi terbaru</p>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>December 16, 2024<br>
                        <i class="fas fa-video me-1"></i>Online Event
                    </small>
                    <div class="mt-2">
                        <a href="/event-details/2" class="btn btn-sm btn-outline-success">View Details</a>
                    </div>
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
                <a href="/registration/${registration.id}" class="btn btn-sm btn-outline-primary">
                    Details
                </a>
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
</script>

<?= $this->endSection() ?>