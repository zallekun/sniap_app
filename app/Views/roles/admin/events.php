<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('title') ?>Event Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-header">
    <div class="admin-header-content">
        <h1>Event Management</h1>
        <p>Manage conference events, schedules, and settings</p>
        <div class="admin-header-actions">
            <button class="btn btn-primary" onclick="showCreateEventModal()">
                <i class="fas fa-plus"></i> Create Event
            </button>
        </div>
    </div>
</div>

<div class="admin-content">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>All Events</h3>
            <div class="admin-filters">
                <select id="statusFilter" class="admin-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>
                <input type="text" id="searchEvents" class="admin-input" placeholder="Search events...">
            </div>
        </div>
        <div class="admin-card-body">
            <div class="admin-table-container">
                <table id="eventsTable" class="admin-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date & Time</th>
                            <th>Format</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Events will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Event Modal -->
<div id="eventModal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3 id="eventModalTitle">Create Event</h3>
            <button class="admin-modal-close" onclick="closeEventModal()">&times;</button>
        </div>
        <form id="eventForm" class="admin-modal-body">
            <div class="admin-form-group">
                <label for="eventTitle">Event Title</label>
                <input type="text" id="eventTitle" name="title" class="admin-input" required>
            </div>
            <div class="admin-form-group">
                <label for="eventDescription">Description</label>
                <textarea id="eventDescription" name="description" class="admin-textarea" rows="3"></textarea>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="eventDate">Event Date</label>
                    <input type="date" id="eventDate" name="event_date" class="admin-input" required>
                </div>
                <div class="admin-form-group">
                    <label for="eventTime">Event Time</label>
                    <input type="time" id="eventTime" name="event_time" class="admin-input" required>
                </div>
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="eventFormat">Format</label>
                    <select id="eventFormat" name="format" class="admin-select" required>
                        <option value="">Select Format</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label for="maxParticipants">Max Participants</label>
                    <input type="number" id="maxParticipants" name="max_participants" class="admin-input" min="1">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="admin-input" placeholder="Physical location or platform">
            </div>
            <div class="admin-form-group">
                <label for="zoomLink">Zoom Link (if online/hybrid)</label>
                <input type="url" id="zoomLink" name="zoom_link" class="admin-input" placeholder="https://zoom.us/j/...">
            </div>
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="registrationFee">Registration Fee (IDR)</label>
                    <input type="number" id="registrationFee" name="registration_fee" class="admin-input" min="0" step="1000">
                </div>
                <div class="admin-form-group">
                    <label for="registrationDeadline">Registration Deadline</label>
                    <input type="datetime-local" id="registrationDeadline" name="registration_deadline" class="admin-input">
                </div>
            </div>
            <div class="admin-form-group">
                <label for="abstractDeadline">Abstract Deadline</label>
                <input type="datetime-local" id="abstractDeadline" name="abstract_deadline" class="admin-input">
            </div>
            <div class="admin-form-group">
                <label class="admin-checkbox">
                    <input type="checkbox" id="isActive" name="is_active" checked>
                    <span class="checkmark"></span>
                    Active Event
                </label>
            </div>
        </form>
        <div class="admin-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeEventModal()">Cancel</button>
            <button type="submit" form="eventForm" class="btn btn-primary">Save Event</button>
        </div>
    </div>
</div>

<script>
// Event management functionality will be implemented here
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    setupEventFilters();
});

function loadEvents() {
    // TODO: Implement event loading via AJAX
    console.log('Loading events...');
}

function setupEventFilters() {
    // TODO: Implement filtering functionality
    console.log('Setting up event filters...');
}

function showCreateEventModal() {
    document.getElementById('eventModalTitle').textContent = 'Create Event';
    document.getElementById('eventForm').reset();
    document.getElementById('eventModal').style.display = 'block';
}

function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

// Event form submission
document.getElementById('eventForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Implement event creation/update
    console.log('Saving event...');
});
</script>
<?= $this->endSection() ?>