<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>System Settings<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-header">
    <div class="admin-header-content">
        <h1>System Settings</h1>
        <p>Configure system-wide settings and preferences</p>
    </div>
</div>

<div class="admin-content">
    <div class="settings-layout">
        <!-- Settings Navigation -->
        <div class="settings-sidebar">
            <div class="settings-nav">
                <div class="settings-nav-item active" onclick="showSettingsTab('general')">
                    <i class="fas fa-cog"></i>
                    <span>General</span>
                </div>
                <div class="settings-nav-item" onclick="showSettingsTab('email')">
                    <i class="fas fa-envelope"></i>
                    <span>Email</span>
                </div>
                <div class="settings-nav-item" onclick="showSettingsTab('payment')">
                    <i class="fas fa-credit-card"></i>
                    <span>Payment</span>
                </div>
                <div class="settings-nav-item" onclick="showSettingsTab('security')">
                    <i class="fas fa-shield-alt"></i>
                    <span>Security</span>
                </div>
                <div class="settings-nav-item" onclick="showSettingsTab('notifications')">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </div>
                <div class="settings-nav-item" onclick="showSettingsTab('maintenance')">
                    <i class="fas fa-tools"></i>
                    <span>Maintenance</span>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="settings-main">
            <!-- General Settings -->
            <div id="generalSettings" class="settings-panel active">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>General Configuration</h3>
                        <p class="settings-card-desc">Basic system configuration and site information</p>
                    </div>
                    <form id="generalSettingsForm" class="settings-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="siteName">Site Name</label>
                                <input type="text" id="siteName" name="site_name" class="form-input" value="SNIA Conference">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="siteDescription">Site Description</label>
                                <textarea id="siteDescription" name="site_description" class="form-textarea" rows="3" placeholder="Enter site description...">Professional conference management system</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone" name="timezone" class="form-select">
                                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="defaultLanguage">Default Language</label>
                                <select id="defaultLanguage" name="default_language" class="form-select">
                                    <option value="id" selected>Bahasa Indonesia</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="contactEmail">Contact Email</label>
                                <input type="email" id="contactEmail" name="contact_email" class="form-input" placeholder="contact@example.com">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email Settings -->
            <div id="emailSettings" class="settings-panel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>Email Configuration</h3>
                        <p class="settings-card-desc">Configure SMTP settings for email delivery</p>
                    </div>
                    <form id="emailSettingsForm" class="settings-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="smtpHost">SMTP Host</label>
                                <input type="text" id="smtpHost" name="smtp_host" class="form-input" placeholder="smtp.gmail.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpPort">SMTP Port</label>
                                <input type="number" id="smtpPort" name="smtp_port" class="form-input" value="587">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpUsername">SMTP Username</label>
                                <input type="text" id="smtpUsername" name="smtp_username" class="form-input" placeholder="your-email@gmail.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtpPassword">SMTP Password</label>
                                <input type="password" id="smtpPassword" name="smtp_password" class="form-input" placeholder="••••••••">
                            </div>
                            
                            <div class="form-group">
                                <label for="fromEmail">From Email</label>
                                <input type="email" id="fromEmail" name="from_email" class="form-input" placeholder="noreply@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="fromName">From Name</label>
                                <input type="text" id="fromName" name="from_name" class="form-input" value="SNIA Conference">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="testEmailConnection()">
                                <i class="fas fa-paper-plane"></i>
                                Test Connection
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Email Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Settings -->
            <div id="paymentSettings" class="settings-panel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>Payment Gateway Configuration</h3>
                        <p class="settings-card-desc">Configure payment gateway settings</p>
                    </div>
                    <form id="paymentSettingsForm" class="settings-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="paymentGateway">Payment Gateway</label>
                                <select id="paymentGateway" name="payment_gateway" class="form-select">
                                    <option value="midtrans">Midtrans</option>
                                    <option value="xendit">Xendit</option>
                                    <option value="manual">Manual Transfer</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="merchantId">Merchant ID</label>
                                <input type="text" id="merchantId" name="merchant_id" class="form-input" placeholder="Enter merchant ID">
                            </div>
                            
                            <div class="form-group">
                                <label for="clientKey">Client Key</label>
                                <input type="text" id="clientKey" name="client_key" class="form-input" placeholder="Enter client key">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="serverKey">Server Key</label>
                                <input type="password" id="serverKey" name="server_key" class="form-input" placeholder="Enter server key">
                            </div>
                            
                            <div class="form-group full-width">
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="sandboxMode" name="sandbox_mode">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Sandbox Mode (Testing)</strong>
                                            <small>Enable this for testing payments without real transactions</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Payment Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="securitySettings" class="settings-panel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>Security Configuration</h3>
                        <p class="settings-card-desc">Configure security settings and access controls</p>
                    </div>
                    <form id="securitySettingsForm" class="settings-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="sessionTimeout">Session Timeout (minutes)</label>
                                <input type="number" id="sessionTimeout" name="session_timeout" class="form-input" value="120" min="30" max="1440">
                                <small class="form-help">How long users stay logged in when inactive</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="maxLoginAttempts">Max Login Attempts</label>
                                <input type="number" id="maxLoginAttempts" name="max_login_attempts" class="form-input" value="5" min="3" max="10">
                                <small class="form-help">Number of failed attempts before account lockout</small>
                            </div>
                            
                            <div class="form-group full-width">
                                <h4 class="security-section-title">Security Features</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="enableTwoFactor" name="enable_two_factor">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Enable Two-Factor Authentication</strong>
                                            <small>Require additional verification for login</small>
                                        </span>
                                    </label>
                                    
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="forcePasswordChange" name="force_password_change">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Force Password Change Every 90 Days</strong>
                                            <small>Require users to update passwords regularly</small>
                                        </span>
                                    </label>
                                    
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="enableCaptcha" name="enable_captcha">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Enable CAPTCHA on Login</strong>
                                            <small>Add CAPTCHA verification to prevent bots</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Security Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notificationSettings" class="settings-panel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>Notification Configuration</h3>
                        <p class="settings-card-desc">Configure system and email notifications</p>
                    </div>
                    <form id="notificationSettingsForm" class="settings-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <h4 class="notification-section-title">Email Notifications</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="emailNewRegistration" name="email_new_registration" checked>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>New Registration</strong>
                                            <small>Notify when new users register</small>
                                        </span>
                                    </label>
                                    
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="emailPaymentReceived" name="email_payment_received" checked>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Payment Received</strong>
                                            <small>Notify when payments are processed</small>
                                        </span>
                                    </label>
                                    
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="emailAbstractSubmitted" name="email_abstract_submitted" checked>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Abstract Submitted</strong>
                                            <small>Notify when new abstracts are submitted</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <h4 class="notification-section-title">System Notifications</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="notifyLowStorage" name="notify_low_storage" checked>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Low Storage Warning</strong>
                                            <small>Alert when disk space is running low</small>
                                        </span>
                                    </label>
                                    
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="notifyFailedLogins" name="notify_failed_logins" checked>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Failed Login Attempts</strong>
                                            <small>Alert when multiple failed login attempts occur</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Maintenance Settings -->
            <div id="maintenanceSettings" class="settings-panel">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h3>Maintenance & Backup</h3>
                        <p class="settings-card-desc">System maintenance and backup operations</p>
                    </div>
                    <div class="settings-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <h4 class="maintenance-section-title">Maintenance Mode</h4>
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="maintenanceMode" name="maintenance_mode">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <strong>Enable Maintenance Mode</strong>
                                            <small>When enabled, only administrators can access the system</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <h4 class="maintenance-section-title">Database Backup</h4>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-success" onclick="createBackup()">
                                        <i class="fas fa-download"></i>
                                        Create Backup Now
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="scheduleBackup()">
                                        <i class="fas fa-clock"></i>
                                        Schedule Automatic Backup
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group full-width">
                                <h4 class="maintenance-section-title">System Cleanup</h4>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-warning" onclick="clearCache()">
                                        <i class="fas fa-broom"></i>
                                        Clear Cache
                                    </button>
                                    <button type="button" class="btn btn-orange" onclick="clearLogs()">
                                        <i class="fas fa-trash"></i>
                                        Clear Old Logs
                                    </button>
                                </div>
                            </div>
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
    initializeSettingsHandlers();
});

function showSettingsTab(tabName) {
    // Hide all panels
    document.querySelectorAll('.settings-panel').forEach(panel => {
        panel.classList.remove('active');
    });
    document.querySelectorAll('.settings-nav-item').forEach(item => {
        item.classList.remove('active');
    });

    // Show selected panel
    document.getElementById(tabName + 'Settings').classList.add('active');
    event.target.closest('.settings-nav-item').classList.add('active');
}

function initializeSettingsHandlers() {
    // Form submissions
    const forms = [
        'generalSettingsForm',
        'emailSettingsForm', 
        'paymentSettingsForm',
        'securitySettingsForm',
        'notificationSettingsForm'
    ];

    forms.forEach(formId => {
        document.getElementById(formId).addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(formId, this);
        });
    });
}

function handleFormSubmission(formId, form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitButton.disabled = true;
    
    // TODO: Send data to server
    console.log(`Saving ${formId}...`, Object.fromEntries(formData));
    
    // Simulate API call
    setTimeout(() => {
        submitButton.innerHTML = '<i class="fas fa-check"></i> Saved!';
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 2000);
    }, 1000);
}

function loadCurrentSettings() {
    // TODO: Load current settings from server
    console.log('Loading current settings...');
}

function testEmailConnection() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    button.disabled = true;
    
    // TODO: Test email connection
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Connection Successful!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 3000);
    }, 2000);
}

function createBackup() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    button.disabled = true;
    
    // TODO: Create database backup
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Backup Created!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 3000);
    }, 3000);
}

function scheduleBackup() {
    // TODO: Schedule automatic backup
    alert('Backup scheduling dialog would open here');
}

function clearCache() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clearing...';
    button.disabled = true;
    
    // TODO: Clear system cache
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Cache Cleared!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    }, 1500);
}

function clearLogs() {
    if (confirm('Are you sure you want to clear old logs? This action cannot be undone.')) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clearing...';
        button.disabled = true;
        
        // TODO: Clear old logs
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check"></i> Logs Cleared!';
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 2000);
        }, 2000);
    }
}
</script>

<style>
/* Settings Layout */
.settings-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 2rem;
    max-width: 100%;
    margin: 0;
}

/* Settings Sidebar */
.settings-sidebar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.settings-nav {
    padding: 1.5rem;
}

.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #64748b;
}

.settings-nav-item:hover {
    background-color: #f1f5f9;
    color: #475569;
    transform: translateX(4px);
}

.settings-nav-item.active {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.settings-nav-item i {
    font-size: 1.1rem;
    width: 20px;
}

/* Settings Main Content */
.settings-main {
    min-width: 0; /* Prevents flex/grid overflow */
}

.settings-panel {
    display: none;
}

.settings-panel.active {
    display: block;
}

.settings-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.settings-card-header {
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}

.settings-card-header h3 {
    margin: 0 0 0.5rem;
    color: #1e293b;
    font-size: 1.5rem;
    font-weight: 600;
}

.settings-card-desc {
    margin: 0;
    color: #64748b;
    font-size: 0.95rem;
}

/* Forms */
.settings-form {
    padding: 2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.form-help {
    margin-top: 0.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

/* Checkboxes */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    cursor: pointer;
    padding: 1rem;
    border-radius: 10px;
    transition: background-color 0.2s ease;
}

.checkbox-label:hover {
    background-color: #f8fafc;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: linear-gradient(135deg, #10b981, #059669);
    border-color: #10b981;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: "✓";
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
}

.checkbox-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.checkbox-text strong {
    color: #374151;
    font-weight: 600;
}

.checkbox-text small {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Section Titles */
.security-section-title,
.notification-section-title,
.maintenance-section-title {
    color: #1f2937;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #64748b, #475569);
    color: white;
    box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
}

.btn-secondary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(100, 116, 139, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-success:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: white;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

.btn-info:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(14, 165, 233, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.btn-warning:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4);
}

.btn-orange {
    background: linear-gradient(135deg, #ea580c, #c2410c);
    color: white;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
}

.btn-orange:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(234, 88, 12, 0.4);
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .settings-layout {
        grid-template-columns: 250px 1fr;
        gap: 1.5rem;
    }
    
    .settings-sidebar {
        position: static;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-group.full-width {
        grid-column: 1;
    }
}

@media (max-width: 768px) {
    .settings-layout {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .settings-sidebar {
        order: 2;
        position: static;
    }
    
    .settings-main {
        order: 1;
    }
    
    .settings-nav {
        padding: 1rem;
    }
    
    .settings-nav-item {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .settings-card-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .settings-form {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .admin-content {
        padding: 1rem;
    }
    
    .settings-card-header h3 {
        font-size: 1.25rem;
    }
    
    .settings-nav-item {
        justify-content: center;
        text-align: center;
        flex-direction: column;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
    }
    
    .settings-nav-item span {
        font-size: 0.875rem;
    }
    
    .checkbox-label {
        padding: 0.75rem;
    }
    
    .btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
        justify-content: center;
    }
}

/* Loading States */
.btn .fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success States */
.btn .fa-check {
    color: inherit;
}

/* Focus States for Accessibility */
.settings-nav-item:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

.btn:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Smooth Transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom Scrollbar */
.settings-sidebar::-webkit-scrollbar {
    width: 6px;
}

.settings-sidebar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.settings-sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.settings-sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced Visual Hierarchy */
.settings-card {
    border: 1px solid #e2e8f0;
}

.form-input:hover,
.form-select:hover,
.form-textarea:hover {
    border-color: #cbd5e1;
}

.settings-nav-item.active i {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Improved spacing for better readability */
.checkbox-text strong {
    line-height: 1.4;
}

.checkbox-text small {
    line-height: 1.5;
    margin-top: 2px;
}

.form-help {
    line-height: 1.4;
}

/* Enhanced button interactions */
.btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary:active {
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-success:active {
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.btn-warning:active {
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.btn-secondary:active {
    box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
}
</style>

<?= $this->endSection() ?>