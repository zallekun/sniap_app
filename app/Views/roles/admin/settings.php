<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('title') ?>System Settings<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-header">
    <div class="admin-header-content">
        <h1>System Settings</h1>
        <p>Configure system-wide settings and preferences</p>
    </div>
</div>

<div class="admin-content">
    <div class="admin-settings-container">
        <!-- Settings Navigation -->
        <div class="admin-settings-nav">
            <div class="admin-settings-nav-item active" onclick="showSettingsTab('general')">
                <i class="fas fa-cog"></i>
                <span>General</span>
            </div>
            <div class="admin-settings-nav-item" onclick="showSettingsTab('email')">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            </div>
            <div class="admin-settings-nav-item" onclick="showSettingsTab('payment')">
                <i class="fas fa-credit-card"></i>
                <span>Payment</span>
            </div>
            <div class="admin-settings-nav-item" onclick="showSettingsTab('security')">
                <i class="fas fa-shield-alt"></i>
                <span>Security</span>
            </div>
            <div class="admin-settings-nav-item" onclick="showSettingsTab('notifications')">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </div>
            <div class="admin-settings-nav-item" onclick="showSettingsTab('maintenance')">
                <i class="fas fa-tools"></i>
                <span>Maintenance</span>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="admin-settings-content">
            <!-- General Settings -->
            <div id="generalSettings" class="admin-settings-tab active">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>General Configuration</h3>
                    </div>
                    <form id="generalSettingsForm" class="admin-card-body">
                        <div class="admin-form-group">
                            <label for="siteName">Site Name</label>
                            <input type="text" id="siteName" name="site_name" class="admin-input" value="SNIA Conference">
                        </div>
                        <div class="admin-form-group">
                            <label for="siteDescription">Site Description</label>
                            <textarea id="siteDescription" name="site_description" class="admin-textarea" rows="3">Professional conference management system</textarea>
                        </div>
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone" name="timezone" class="admin-select">
                                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                            <div class="admin-form-group">
                                <label for="defaultLanguage">Default Language</label>
                                <select id="defaultLanguage" name="default_language" class="admin-select">
                                    <option value="id" selected>Bahasa Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label for="contactEmail">Contact Email</label>
                            <input type="email" id="contactEmail" name="contact_email" class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <button type="submit" class="btn btn-primary">Save General Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Settings -->
            <div id="emailSettings" class="admin-settings-tab">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Email Configuration</h3>
                    </div>
                    <form id="emailSettingsForm" class="admin-card-body">
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="smtpHost">SMTP Host</label>
                                <input type="text" id="smtpHost" name="smtp_host" class="admin-input" placeholder="smtp.gmail.com">
                            </div>
                            <div class="admin-form-group">
                                <label for="smtpPort">SMTP Port</label>
                                <input type="number" id="smtpPort" name="smtp_port" class="admin-input" value="587">
                            </div>
                        </div>
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="smtpUsername">SMTP Username</label>
                                <input type="text" id="smtpUsername" name="smtp_username" class="admin-input">
                            </div>
                            <div class="admin-form-group">
                                <label for="smtpPassword">SMTP Password</label>
                                <input type="password" id="smtpPassword" name="smtp_password" class="admin-input">
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label for="fromEmail">From Email</label>
                            <input type="email" id="fromEmail" name="from_email" class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label for="fromName">From Name</label>
                            <input type="text" id="fromName" name="from_name" class="admin-input" value="SNIA Conference">
                        </div>
                        <div class="admin-form-group">
                            <button type="button" class="btn btn-secondary" onclick="testEmailConnection()">Test Connection</button>
                            <button type="submit" class="btn btn-primary">Save Email Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Settings -->
            <div id="paymentSettings" class="admin-settings-tab">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Payment Gateway Configuration</h3>
                    </div>
                    <form id="paymentSettingsForm" class="admin-card-body">
                        <div class="admin-form-group">
                            <label for="paymentGateway">Payment Gateway</label>
                            <select id="paymentGateway" name="payment_gateway" class="admin-select">
                                <option value="midtrans">Midtrans</option>
                                <option value="xendit">Xendit</option>
                                <option value="manual">Manual Transfer</option>
                            </select>
                        </div>
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="merchantId">Merchant ID</label>
                                <input type="text" id="merchantId" name="merchant_id" class="admin-input">
                            </div>
                            <div class="admin-form-group">
                                <label for="clientKey">Client Key</label>
                                <input type="text" id="clientKey" name="client_key" class="admin-input">
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label for="serverKey">Server Key</label>
                            <input type="password" id="serverKey" name="server_key" class="admin-input">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-checkbox">
                                <input type="checkbox" id="sandboxMode" name="sandbox_mode">
                                <span class="checkmark"></span>
                                Sandbox Mode (Testing)
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <button type="submit" class="btn btn-primary">Save Payment Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="securitySettings" class="admin-settings-tab">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Security Configuration</h3>
                    </div>
                    <form id="securitySettingsForm" class="admin-card-body">
                        <div class="admin-form-row">
                            <div class="admin-form-group">
                                <label for="sessionTimeout">Session Timeout (minutes)</label>
                                <input type="number" id="sessionTimeout" name="session_timeout" class="admin-input" value="120" min="30" max="1440">
                            </div>
                            <div class="admin-form-group">
                                <label for="maxLoginAttempts">Max Login Attempts</label>
                                <input type="number" id="maxLoginAttempts" name="max_login_attempts" class="admin-input" value="5" min="3" max="10">
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-checkbox">
                                <input type="checkbox" id="enableTwoFactor" name="enable_two_factor">
                                <span class="checkmark"></span>
                                Enable Two-Factor Authentication
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-checkbox">
                                <input type="checkbox" id="forcePasswordChange" name="force_password_change">
                                <span class="checkmark"></span>
                                Force Password Change Every 90 Days
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-checkbox">
                                <input type="checkbox" id="enableCaptcha" name="enable_captcha">
                                <span class="checkmark"></span>
                                Enable CAPTCHA on Login
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <button type="submit" class="btn btn-primary">Save Security Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notificationSettings" class="admin-settings-tab">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Notification Configuration</h3>
                    </div>
                    <form id="notificationSettingsForm" class="admin-card-body">
                        <div class="admin-form-group">
                            <h4>Email Notifications</h4>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="emailNewRegistration" name="email_new_registration" checked>
                                <span class="checkmark"></span>
                                New Registration
                            </label>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="emailPaymentReceived" name="email_payment_received" checked>
                                <span class="checkmark"></span>
                                Payment Received
                            </label>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="emailAbstractSubmitted" name="email_abstract_submitted" checked>
                                <span class="checkmark"></span>
                                Abstract Submitted
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <h4>System Notifications</h4>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="notifyLowStorage" name="notify_low_storage" checked>
                                <span class="checkmark"></span>
                                Low Storage Warning
                            </label>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="notifyFailedLogins" name="notify_failed_logins" checked>
                                <span class="checkmark"></span>
                                Failed Login Attempts
                            </label>
                        </div>
                        <div class="admin-form-group">
                            <button type="submit" class="btn btn-primary">Save Notification Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Maintenance Settings -->
            <div id="maintenanceSettings" class="admin-settings-tab">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3>Maintenance & Backup</h3>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-form-group">
                            <h4>Maintenance Mode</h4>
                            <label class="admin-checkbox">
                                <input type="checkbox" id="maintenanceMode" name="maintenance_mode">
                                <span class="checkmark"></span>
                                Enable Maintenance Mode
                            </label>
                            <small class="admin-help-text">When enabled, only administrators can access the system</small>
                        </div>
                        <div class="admin-form-group">
                            <h4>Database Backup</h4>
                            <button type="button" class="btn btn-success" onclick="createBackup()">
                                <i class="fas fa-download"></i> Create Backup Now
                            </button>
                            <button type="button" class="btn btn-primary" onclick="scheduleBackup()">
                                <i class="fas fa-clock"></i> Schedule Automatic Backup
                            </button>
                        </div>
                        <div class="admin-form-group">
                            <h4>System Cleanup</h4>
                            <button type="button" class="btn btn-warning" onclick="clearCache()">
                                <i class="fas fa-broom"></i> Clear Cache
                            </button>
                            <button type="button" class="btn btn-warning" onclick="clearLogs()">
                                <i class="fas fa-trash"></i> Clear Old Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Settings functionality
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentSettings();
});

function showSettingsTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.admin-settings-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.admin-settings-nav-item').forEach(item => {
        item.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + 'Settings').classList.add('active');
    event.target.closest('.admin-settings-nav-item').classList.add('active');
}

function loadCurrentSettings() {
    // TODO: Load current settings from server
    console.log('Loading current settings...');
}

function testEmailConnection() {
    // TODO: Test email connection
    console.log('Testing email connection...');
}

function createBackup() {
    // TODO: Create database backup
    console.log('Creating backup...');
}

function scheduleBackup() {
    // TODO: Schedule automatic backup
    console.log('Scheduling backup...');
}

function clearCache() {
    // TODO: Clear system cache
    console.log('Clearing cache...');
}

function clearLogs() {
    // TODO: Clear old logs
    console.log('Clearing logs...');
}

// Form submissions
document.getElementById('generalSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Save general settings
    console.log('Saving general settings...');
});

document.getElementById('emailSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Save email settings
    console.log('Saving email settings...');
});

document.getElementById('paymentSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Save payment settings
    console.log('Saving payment settings...');
});

document.getElementById('securitySettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Save security settings
    console.log('Saving security settings...');
});

document.getElementById('notificationSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Save notification settings
    console.log('Saving notification settings...');
});
</script>

<style>
.admin-settings-container {
    display: flex;
    gap: 2rem;
    max-width: 100%;
}

.admin-settings-nav {
    min-width: 250px;
    background: white;
    border-radius: 8px;
    padding: 1rem;
    height: fit-content;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-settings-nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 0.5rem;
}

.admin-settings-nav-item:hover {
    background-color: #f8f9fa;
}

.admin-settings-nav-item.active {
    background-color: #3498db;
    color: white;
}

.admin-settings-content {
    flex: 1;
}

.admin-settings-tab {
    display: none;
}

.admin-settings-tab.active {
    display: block;
}

.admin-help-text {
    color: #666;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}
</style>
<?= $this->endSection() ?>