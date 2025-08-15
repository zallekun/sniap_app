<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('title') ?>Registration Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-header">
    <div class="admin-header-content">
        <h1>Registration Management</h1>
        <p>Manage event registrations and participant data</p>
        <div class="admin-header-actions">
            <button class="btn btn-success" onclick="exportRegistrations()">
                <i class="fas fa-download"></i> Export Data
            </button>
            <button class="btn btn-primary" onclick="showBulkActionModal()">
                <i class="fas fa-tasks"></i> Bulk Actions
            </button>
        </div>
    </div>
</div>

<div class="admin-content">
    <!-- Registration Statistics -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="admin-stat-info">
                <h3 id="totalRegistrations">0</h3>
                <p>Total Registrations</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="admin-stat-info">
                <h3 id="confirmedRegistrations">0</h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="admin-stat-info">
                <h3 id="pendingRegistrations">0</h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="admin-stat-info">
                <h3 id="paidRegistrations">0</h3>
                <p>Paid</p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>All Registrations</h3>
            <div class="admin-filters">
                <select id="eventFilter" class="admin-select">
                    <option value="">All Events</option>
                </select>
                <select id="statusFilter" class="admin-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select id="paymentFilter" class="admin-select">
                    <option value="">All Payments</option>
                    <option value="pending">Payment Pending</option>
                    <option value="success">Paid</option>
                    <option value="failed">Failed</option>
                </select>
                <input type="text" id="searchRegistrations" class="admin-input" placeholder="Search participants...">
            </div>
        </div>
        <div class="admin-card-body">
            <div class="admin-table-container">
                <table id="registrationsTable" class="admin-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Participant</th>
                            <th>Event</th>
                            <th>Registration Type</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Registrations will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Registration Detail Modal -->
<div id="registrationModal" class="admin-modal">
    <div class="admin-modal-content admin-modal-large">
        <div class="admin-modal-header">
            <h3>Registration Details</h3>
            <button class="admin-modal-close" onclick="closeRegistrationModal()">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="registration-detail-container">
                <!-- Registration details will be loaded here -->
            </div>
        </div>
        <div class="admin-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeRegistrationModal()">Close</button>
            <button type="button" class="btn btn-warning" onclick="updateRegistrationStatus()">Update Status</button>
            <button type="button" class="btn btn-success" onclick="confirmRegistration()">Confirm</button>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div id="bulkActionModal" class="admin-modal">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3>Bulk Actions</h3>
            <button class="admin-modal-close" onclick="closeBulkActionModal()">&times;</button>
        </div>
        <form id="bulkActionForm" class="admin-modal-body">
            <div class="admin-form-group">
                <label for="bulkAction">Select Action</label>
                <select id="bulkAction" name="action" class="admin-select" required>
                    <option value="">Choose Action</option>
                    <option value="confirm">Confirm Registrations</option>
                    <option value="cancel">Cancel Registrations</option>
                    <option value="send_email">Send Email</option>
                    <option value="export">Export Selected</option>
                </select>
            </div>
            <div id="emailContent" class="admin-form-group" style="display: none;">
                <label for="emailSubject">Email Subject</label>
                <input type="text" id="emailSubject" name="email_subject" class="admin-input">
                <label for="emailMessage">Message</label>
                <textarea id="emailMessage" name="email_message" class="admin-textarea" rows="4"></textarea>
            </div>
        </form>
        <div class="admin-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBulkActionModal()">Cancel</button>
            <button type="submit" form="bulkActionForm" class="btn btn-primary">Execute Action</button>
        </div>
    </div>
</div>

<script>
// Registration management functionality
document.addEventListener('DOMContentLoaded', function() {
    loadRegistrations();
    loadRegistrationStats();
    loadEventOptions();
    setupRegistrationFilters();
});

function loadRegistrations() {
    // TODO: Implement registration loading via AJAX
    console.log('Loading registrations...');
}

function loadRegistrationStats() {
    // TODO: Implement statistics loading
    console.log('Loading registration statistics...');
}

function loadEventOptions() {
    // TODO: Load events for filter dropdown
    console.log('Loading event options...');
}

function setupRegistrationFilters() {
    // TODO: Implement filtering functionality
    console.log('Setting up registration filters...');
}

function showRegistrationDetail(registrationId) {
    // TODO: Load and show registration details
    document.getElementById('registrationModal').style.display = 'block';
}

function closeRegistrationModal() {
    document.getElementById('registrationModal').style.display = 'none';
}

function showBulkActionModal() {
    document.getElementById('bulkActionModal').style.display = 'block';
}

function closeBulkActionModal() {
    document.getElementById('bulkActionModal').style.display = 'none';
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function exportRegistrations() {
    // TODO: Implement export functionality
    console.log('Exporting registrations...');
}

// Show/hide email content based on bulk action selection
document.getElementById('bulkAction').addEventListener('change', function() {
    const emailContent = document.getElementById('emailContent');
    emailContent.style.display = this.value === 'send_email' ? 'block' : 'none';
});

// Bulk action form submission
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Implement bulk action execution
    console.log('Executing bulk action...');
});
</script>
<?= $this->endSection() ?>