<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>Event Management<?= $this->endSection() ?>

<?= $this->section('header_actions') ?>
<button class="btn btn-primary" onclick="openAddEventModal()">
    <i class="fas fa-plus"></i> Add Event
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Event specific content styles */
.event-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.event-description {
    font-size: 0.75rem;
    color: #6b7280;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.event-date {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.event-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.location-info {
    font-size: 0.75rem;
    color: #6b7280;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.participants-info {
    text-align: center;
}

.participant-count {
    font-weight: 600;
    color: #1f2937;
    display: block;
}

.participant-limit {
    font-size: 0.75rem;
    color: #6b7280;
}
</style>

<!-- Events Card -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3>All Events</h3>
        <div class="admin-filters">
            <select id="formatFilter" class="admin-select">
                <option value="">All Formats</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="hybrid">Hybrid</option>
            </select>
            <select id="statusFilter" class="admin-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <input type="text" id="searchEvents" class="admin-input" placeholder="Search events...">
            <button class="btn btn-outline-primary" onclick="applyEventFilters()">Filter</button>
            <button class="btn btn-outline-secondary" onclick="resetEventFilters()">Reset</button>
        </div>
    </div>
    <div class="admin-card-body">
        <div class="admin-table-container">
            <table class="admin-table" id="eventsTable">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Date & Time</th>
                        <th>Format</th>
                        <th>Location/Link</th>
                        <th>Participants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="eventsTableBody">
                    <!-- Events will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="eventsPagination" style="margin-top: 1rem;">
            <!-- Pagination will be loaded here -->
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="section-title">Basic Information</h6>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="eventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventTitle" name="title" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="eventDescription" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="eventDescription" name="description" rows="3" required></textarea>
                        </div>
                        
                        <!-- Date and Time -->
                        <div class="col-12">
                            <h6 class="section-title">Date & Time</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventDate" class="form-label">Event Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventTime" class="form-label">Event Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="eventTime" name="event_time" required>
                        </div>
                        
                        <!-- Format and Location -->
                        <div class="col-12">
                            <h6 class="section-title">Format & Location</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="eventFormat" class="form-label">Format <span class="text-danger">*</span></label>
                            <select class="form-control" id="eventFormat" name="format" required onchange="toggleLocationFields()">
                                <option value="">Select Format</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                                <option value="hybrid">Hybrid</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="locationField">
                            <label for="eventLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="eventLocation" name="location" placeholder="Venue address">
                        </div>
                        
                        <div class="col-12 mb-3" id="zoomLinkField">
                            <label for="eventZoomLink" class="form-label">Zoom Link</label>
                            <input type="url" class="form-control" id="eventZoomLink" name="zoom_link" placeholder="https://zoom.us/j/...">
                        </div>
                        
                        <!-- Registration Settings -->
                        <div class="col-12">
                            <h6 class="section-title">Registration Settings</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="registrationFee" class="form-label">Registration Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="registrationFee" name="registration_fee" min="0" step="1000">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="maxParticipants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="maxParticipants" name="max_participants" min="1">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="registrationDeadline" class="form-label">Registration Deadline</label>
                            <input type="date" class="form-control" id="registrationDeadline" name="registration_deadline">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="abstractDeadline" class="form-label">Abstract Deadline</label>
                            <input type="date" class="form-control" id="abstractDeadline" name="abstract_deadline">
                        </div>
                        
                        <!-- Status Settings -->
                        <div class="col-12">
                            <h6 class="section-title">Status Settings</h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="registrationActive" name="registration_active">
                                <label class="form-check-label" for="registrationActive">
                                    Registration Active
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="abstractSubmissionActive" name="abstract_submission_active">
                                <label class="form-check-label" for="abstractSubmissionActive">
                                    Abstract Submission Active
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                <label class="form-check-label" for="isActive">
                                    Event Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="eventSubmitBtn">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Event Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEventModalLabel">Delete Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this event will also affect all related registrations and abstracts.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteEvent">Delete Event</button>
            </div>
        </div>
    </div>
</div>


<script>
// Event Management JavaScript
let currentEventPage = 1;
let eventSearchTimeout;
let isEditingEvent = false;
let editingEventId = null;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    
    // Setup search with debounce
    document.getElementById('searchEvents').addEventListener('input', function() {
        clearTimeout(eventSearchTimeout);
        eventSearchTimeout = setTimeout(() => {
            currentEventPage = 1;
            loadEvents();
        }, 500);
    });
});

// Load events with filters and pagination
function loadEvents(page = 1) {
    // Use real API data
    const search = document.getElementById('searchEvents').value.trim();
    const formatFilter = document.getElementById('formatFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    currentEventPage = page;
    
    const params = new URLSearchParams({
        page: page,
        limit: 10,
        search: search,
        format: formatFilter,
        status: statusFilter
    });
    
    fetch(`/api/admin/events?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Convert API response format to match expected format
                const events = data.data.events.map(event => ({
                    id: event.id,
                    title: event.title,
                    description: event.description,
                    event_date: event.event_date,
                    event_time: event.event_time,
                    format: event.format,
                    location: event.location,
                    zoom_link: event.zoom_link,
                    registration_count: 0, // Will be populated from separate query if needed
                    max_participants: event.max_participants,
                    is_active: event.is_active === 't' || event.is_active === true
                }));
                
                renderEventsTable(events);
                renderEventsPagination(data.data.pagination);
            } else {
                showAlert('Error loading events: ' + (data.message || 'API Error'), 'error');
                document.getElementById('eventsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Failed to load events</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading events:', error);
            showAlert('Failed to load events', 'error');
            document.getElementById('eventsTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Failed to load events</td></tr>';
        });
}

// Render events table
function renderEventsTable(events) {
    const tbody = document.getElementById('eventsTableBody');
    
    if (events.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No events found</td></tr>';
        return;
    }
    
    tbody.innerHTML = events.map(event => `
        <tr>
            <td>
                <div class="event-title">${event.title}</div>
                <div class="event-description" title="${event.description}">${event.description}</div>
            </td>
            <td>
                <div class="event-date">${formatDate(event.event_date)}</div>
                <div class="event-time">${formatTime(event.event_time)}</div>
            </td>
            <td>
                <span class="format-badge format-${event.format}">${event.format}</span>
            </td>
            <td>
                <div class="location-info">
                    ${event.format === 'online' ? (event.zoom_link || 'No link') : 
                      event.format === 'offline' ? (event.location || 'No location') :
                      (event.location || event.zoom_link || 'Not set')}
                </div>
            </td>
            <td class="participants-info">
                <span class="participant-count">${event.registration_count || 0}</span>
                <div class="participant-limit">/ ${event.max_participants || '∞'}</div>
            </td>
            <td>
                <span class="status-badge ${event.is_active ? 'active' : 'inactive'}">
                    ${event.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action btn-edit" onclick="editEvent(${event.id})" title="Edit Event">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-toggle ${event.is_active ? '' : 'inactive'}" 
                            onclick="toggleEventStatus(${event.id}, ${event.is_active})" 
                            title="${event.is_active ? 'Deactivate' : 'Activate'} Event">
                        <i class="fas fa-${event.is_active ? 'pause' : 'play'}"></i>
                    </button>
                    <button class="btn-action btn-delete" onclick="deleteEvent(${event.id})" title="Delete Event">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render events pagination
function renderEventsPagination(pagination) {
    const container = document.getElementById('eventsPagination');
    
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let paginationHTML = `
        <div class="pagination-info">
            Showing ${pagination.start_record} to ${pagination.end_record} of ${pagination.total_records} events
        </div>
        <div class="pagination">
    `;
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `<a href="#" class="page-link" onclick="loadEvents(${pagination.current_page - 1})">Previous</a>`;
    } else {
        paginationHTML += `<span class="page-link disabled">Previous</span>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
    
    if (startPage > 1) {
        paginationHTML += `<a href="#" class="page-link" onclick="loadEvents(1)">1</a>`;
        if (startPage > 2) {
            paginationHTML += `<span class="page-link disabled">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            paginationHTML += `<span class="page-link active">${i}</span>`;
        } else {
            paginationHTML += `<a href="#" class="page-link" onclick="loadEvents(${i})">${i}</a>`;
        }
    }
    
    if (endPage < pagination.total_pages) {
        if (endPage < pagination.total_pages - 1) {
            paginationHTML += `<span class="page-link disabled">...</span>`;
        }
        paginationHTML += `<a href="#" class="page-link" onclick="loadEvents(${pagination.total_pages})">${pagination.total_pages}</a>`;
    }
    
    // Next button
    if (pagination.current_page < pagination.total_pages) {
        paginationHTML += `<a href="#" class="page-link" onclick="loadEvents(${pagination.current_page + 1})">Next</a>`;
    } else {
        paginationHTML += `<span class="page-link disabled">Next</span>`;
    }
    
    paginationHTML += `</div>`;
    container.innerHTML = paginationHTML;
}

// Update table info
function updateEventTableInfo(pagination) {
    document.getElementById('eventTableInfo').textContent = 
        `Showing ${pagination.start_record} to ${pagination.end_record} of ${pagination.total_records} events`;
}

// Filter functions
function applyEventFilters() {
    currentEventPage = 1;
    loadEvents();
}

function resetEventFilters() {
    document.getElementById('searchEvents').value = '';
    document.getElementById('formatFilter').value = '';
    document.getElementById('statusFilter').value = '';
    currentEventPage = 1;
    loadEvents();
}

// Modal functions
function openAddEventModal() {
    isEditingEvent = false;
    editingEventId = null;
    document.getElementById('eventModalLabel').textContent = 'Add New Event';
    document.getElementById('eventSubmitBtn').textContent = 'Create Event';
    document.getElementById('eventForm').reset();
    toggleLocationFields(); // Reset field visibility
    new bootstrap.Modal(document.getElementById('eventModal')).show();
}

function editEvent(eventId) {
    isEditingEvent = true;
    editingEventId = eventId;
    document.getElementById('eventModalLabel').textContent = 'Edit Event';
    document.getElementById('eventSubmitBtn').textContent = 'Update Event';
    
    // Fetch event data
    fetch(`/api/admin/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEventForm(data.data);
                new bootstrap.Modal(document.getElementById('eventModal')).show();
            } else {
                showAlert('Error loading event data: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading event:', error);
            showAlert('Failed to load event data', 'error');
        });
}

function populateEventForm(event) {
    document.getElementById('eventTitle').value = event.title || '';
    document.getElementById('eventDescription').value = event.description || '';
    document.getElementById('eventDate').value = event.event_date || '';
    document.getElementById('eventTime').value = event.event_time || '';
    document.getElementById('eventFormat').value = event.format || '';
    document.getElementById('eventLocation').value = event.location || '';
    document.getElementById('eventZoomLink').value = event.zoom_link || '';
    document.getElementById('registrationFee').value = event.registration_fee || '';
    document.getElementById('maxParticipants').value = event.max_participants || '';
    document.getElementById('registrationDeadline').value = event.registration_deadline || '';
    document.getElementById('abstractDeadline').value = event.abstract_deadline || '';
    document.getElementById('registrationActive').checked = event.registration_active || false;
    document.getElementById('abstractSubmissionActive').checked = event.abstract_submission_active || false;
    document.getElementById('isActive').checked = event.is_active || false;
    
    toggleLocationFields(); // Update field visibility based on format
}

// Toggle location/zoom fields based on format
function toggleLocationFields() {
    const format = document.getElementById('eventFormat').value;
    const locationField = document.getElementById('locationField');
    const zoomLinkField = document.getElementById('zoomLinkField');
    const locationInput = document.getElementById('eventLocation');
    const zoomLinkInput = document.getElementById('eventZoomLink');
    
    // Reset required attributes
    locationInput.removeAttribute('required');
    zoomLinkInput.removeAttribute('required');
    
    if (format === 'online') {
        locationField.style.display = 'none';
        zoomLinkField.style.display = 'block';
        zoomLinkInput.setAttribute('required', 'required');
    } else if (format === 'offline') {
        locationField.style.display = 'block';
        zoomLinkField.style.display = 'none';
        locationInput.setAttribute('required', 'required');
    } else if (format === 'hybrid') {
        locationField.style.display = 'block';
        zoomLinkField.style.display = 'block';
        locationInput.setAttribute('required', 'required');
        zoomLinkInput.setAttribute('required', 'required');
    } else {
        locationField.style.display = 'block';
        zoomLinkField.style.display = 'block';
    }
}

// Form submission
document.getElementById('eventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const eventData = {};
    
    formData.forEach((value, key) => {
        if (key.endsWith('_active') || key === 'is_active') {
            eventData[key] = document.querySelector(`[name="${key}"]`).checked;
        } else {
            eventData[key] = value;
        }
    });
    
    const url = isEditingEvent ? `/api/admin/events/${editingEventId}/update` : '/api/admin/events';
    const method = 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(eventData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(isEditingEvent ? 'Event updated successfully!' : 'Event created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            loadEvents(currentEventPage);
        } else {
            console.error('Event save error:', data);
            let errorMsg = 'Error: ' + data.message;
            
            // Add debug information if available
            if (data.errors) {
                console.error('Validation errors:', data.errors);
                errorMsg += '\nValidation errors: ' + JSON.stringify(data.errors);
            }
            if (data.debug_input) {
                console.error('Input data sent:', data.debug_input);
            }
            if (data.debug_rules) {
                console.error('Validation rules used:', data.debug_rules);
            }
            
            showAlert(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving event:', error);
        showAlert('Failed to save event', 'error');
    });
});

// Delete event
function deleteEvent(eventId) {
    new bootstrap.Modal(document.getElementById('deleteEventModal')).show();
    
    document.getElementById('confirmDeleteEvent').onclick = function() {
        fetch(`/api/admin/events/${eventId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Event deleted successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteEventModal')).hide();
                loadEvents(currentEventPage);
            } else {
                showAlert('Error deleting event: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting event:', error);
            showAlert('Failed to delete event', 'error');
        });
    };
}

// Toggle event status
function toggleEventStatus(eventId, currentStatus) {
    fetch(`/api/admin/events/${eventId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Event ${currentStatus ? 'deactivated' : 'activated'} successfully!`, 'success');
            loadEvents(currentEventPage);
        } else {
            showAlert('Error updating event status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating event status:', error);
        showAlert('Failed to update event status', 'error');
    });
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return 'Not set';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTime(timeString) {
    if (!timeString) return 'Not set';
    const [hours, minutes] = timeString.split(':');
    const date = new Date();
    date.setHours(parseInt(hours), parseInt(minutes));
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?= $this->endSection() ?>