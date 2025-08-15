<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Statistics Overview -->
<div class="admin-stats">
    <div class="stat-card-simple primary">
        <div class="stat-number"><?= number_format($stats['total_users'] ?? 0) ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    
    <div class="stat-card-simple success">
        <div class="stat-number"><?= number_format($stats['total_events'] ?? 0) ?></div>
        <div class="stat-label">Total Events</div>
    </div>
    
    <div class="stat-card-simple warning">
        <div class="stat-number"><?= number_format($stats['total_registrations'] ?? 0) ?></div>
        <div class="stat-label">Registrations</div>
    </div>
    
    <div class="stat-card-simple info">
        <div class="stat-number"><?= number_format($stats['total_abstracts'] ?? 0) ?></div>
        <div class="stat-label">Abstracts</div>
    </div>
    
    <div class="stat-card-simple danger">
        <div class="stat-number"><?= number_format($stats['pending_reviews'] ?? 0) ?></div>
        <div class="stat-label">Pending Reviews</div>
    </div>
    
    <div class="stat-card-simple success">
        <div class="stat-number"><?= number_format($stats['total_payments'] ?? 0) ?></div>
        <div class="stat-label">Successful Payments</div>
    </div>
    
    <div class="stat-card-simple primary">
        <div class="stat-number">Rp <?= number_format($stats['revenue'] ?? 0) ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="/admin/users" class="btn-admin btn-admin-primary">
        <i class="fas fa-users me-1"></i> Manage Users
    </a>
    <a href="/admin/events" class="btn-admin btn-admin-success">
        <i class="fas fa-calendar-plus me-1"></i> Add Event
    </a>
    <a href="/admin/abstracts" class="btn-admin btn-admin-warning">
        <i class="fas fa-file-alt me-1"></i> Review Abstracts
    </a>
    <a href="/admin/analytics" class="btn-admin btn-admin-outline">
        <i class="fas fa-chart-bar me-1"></i> Analytics
    </a>
</div>

<!-- System Status -->
<div class="content-body">
    <div style="padding: 1.5rem;">
        <h5 style="margin-bottom: 1rem; color: #212529;">System Status</h5>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="data-card">
                <div class="data-card-header">User Distribution</div>
                <div class="data-card-body">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Admin</span>
                        <span class="fw-semibold">3</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Presenter</span>
                        <span class="fw-semibold">12</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Reviewer</span>
                        <span class="fw-semibold">5</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Audience</span>
                        <span class="fw-semibold">25</span>
                    </div>
                </div>
            </div>
            
            <div class="data-card">
                <div class="data-card-header">Recent Activity</div>
                <div class="data-card-body">
                    <div style="margin-bottom: 0.5rem;">
                        <span class="status-badge status-active">Active</span>
                        <span class="text-small">Last user login: 2 minutes ago</span>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <span class="status-badge status-pending">Pending</span>
                        <span class="text-small">New registrations: 3</span>
                    </div>
                    <div>
                        <span class="status-badge status-active">Active</span>
                        <span class="text-small">System uptime: 99.9%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Users Table -->
        <h6 style="margin-bottom: 1rem; color: #495057;">Recent User Registrations</h6>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>john.doe@example.com</td>
                    <td><span class="status-badge status-active">Presenter</span></td>
                    <td>2 hours ago</td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <a href="/admin/users/view/1" class="btn-admin-outline" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
                    </td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>jane.smith@example.com</td>
                    <td><span class="status-badge status-pending">Reviewer</span></td>
                    <td>5 hours ago</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td>
                        <a href="/admin/users/view/2" class="btn-admin-outline" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
                    </td>
                </tr>
                <tr>
                    <td>Bob Wilson</td>
                    <td>bob.wilson@example.com</td>
                    <td><span class="status-badge status-active">Audience</span></td>
                    <td>1 day ago</td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <a href="/admin/users/view/3" class="btn-admin-outline" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View</a>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 1rem; text-align: center;">
            <a href="/admin/users" class="btn-admin btn-admin-outline">View All Users</a>
        </div>
        
        <!-- Action Required -->
        <?php if (($stats['pending_reviews'] ?? 0) > 0): ?>
        <div class="error-message" style="margin-top: 1.5rem;">
            <strong>Action Required:</strong> There are <?= $stats['pending_reviews'] ?> abstracts pending review. 
            <a href="/admin/abstracts" style="color: #721c24; font-weight: 600;">Review now →</a>
        </div>
        <?php endif; ?>
        
        <div class="success-message" style="margin-top: 1rem;">
            <strong>System Status:</strong> All services are operational. Database connection: OK. Last backup: 2 hours ago.
        </div>
    </div>
</div>
<?= $this->endSection() ?>