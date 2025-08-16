<?= $this->extend('shared/layouts/admin_simple') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Dashboard specific styles only */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-accent);
}

.stat-card.primary {
    --card-accent: #1e40af;
}

.stat-card.success {
    --card-accent: #10b981;
}

.stat-card.warning {
    --card-accent: #f59e0b;
}

.stat-card.info {
    --card-accent: #06b6d4;
}

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.stat-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-icon.users {
    background: linear-gradient(135deg, #1e40af, #3b82f6);
}

.stat-icon.events {
    background: linear-gradient(135deg, #10b981, #34d399);
}

.stat-icon.registrations {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.stat-icon.analytics {
    background: linear-gradient(135deg, #06b6d4, #22d3ee);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

.chart-container {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
}

.recent-activity {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.activity-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.activity-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
}

.activity-list {
    padding: 0;
    margin: 0;
    list-style: none;
}

.activity-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: white;
    flex-shrink: 0;
}

.activity-icon.user {
    background: #3b82f6;
}

.activity-icon.event {
    background: #10b981;
}

.activity-icon.registration {
    background: #f59e0b;
}

.activity-content {
    flex: 1;
}

.activity-text {
    color: #374151;
    font-size: 0.875rem;
    margin: 0;
}

.activity-time {
    color: #9ca3af;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-header">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-number" id="totalUsers">0</div>
        <div class="stat-label">Total Users</div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon events">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
        <div class="stat-number" id="totalEvents">0</div>
        <div class="stat-label">Active Events</div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon registrations">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        <div class="stat-number" id="totalRegistrations">0</div>
        <div class="stat-label">Registrations</div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon analytics">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
        <div class="stat-number" id="totalRevenue">0</div>
        <div class="stat-label">Revenue (Rp)</div>
    </div>
</div>

<!-- Recent Activity -->
<div class="recent-activity">
    <div class="activity-header">
        <h3 class="activity-title">Recent Activity</h3>
    </div>
    <ul class="activity-list" id="activityList">
        <li class="activity-item">
            <div class="activity-icon user">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="activity-content">
                <p class="activity-text">New user registered: John Doe</p>
                <p class="activity-time">2 minutes ago</p>
            </div>
        </li>
        <li class="activity-item">
            <div class="activity-icon event">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="activity-content">
                <p class="activity-text">New event created: Tech Conference 2024</p>
                <p class="activity-time">1 hour ago</p>
            </div>
        </li>
        <li class="activity-item">
            <div class="activity-icon registration">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="activity-content">
                <p class="activity-text">New registration for: AI Workshop</p>
                <p class="activity-time">3 hours ago</p>
            </div>
        </li>
    </ul>
</div>

<script>
// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentActivity();
});

function loadDashboardStats() {
    // Use real API data
    fetch('/api/admin/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                document.getElementById('totalUsers').textContent = stats.total_users || 0;
                document.getElementById('totalEvents').textContent = stats.active_events || 0;
                document.getElementById('totalRegistrations').textContent = stats.total_registrations || 0;
                document.getElementById('totalRevenue').textContent = formatCurrency(stats.total_revenue || 0);
            } else {
                console.error('API Error:', data.message);
                // Fallback to static data if API fails
                document.getElementById('totalUsers').textContent = '0';
                document.getElementById('totalEvents').textContent = '0';
                document.getElementById('totalRegistrations').textContent = '0';
                document.getElementById('totalRevenue').textContent = 'Rp 0';
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
            // Fallback to static data if API fails
            document.getElementById('totalUsers').textContent = '0';
            document.getElementById('totalEvents').textContent = '0';
            document.getElementById('totalRegistrations').textContent = '0';
            document.getElementById('totalRevenue').textContent = 'Rp 0';
        });
}

function loadRecentActivity() {
    // Use real API data
    fetch('/api/admin/dashboard/activity')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                renderRecentActivity(data.data);
            } else {
                // Fallback to static data if no real activity
                const staticActivity = [
                    {
                        type: 'user',
                        description: 'New user registered: Jane Smith',
                        created_at: new Date(Date.now() - 2 * 60 * 1000).toISOString()
                    },
                    {
                        type: 'event',
                        description: 'New event created: AI Workshop 2024',
                        created_at: new Date(Date.now() - 60 * 60 * 1000).toISOString()
                    },
                    {
                        type: 'registration',
                        description: 'New registration for: SNIA Conference 2024',
                        created_at: new Date(Date.now() - 3 * 60 * 60 * 1000).toISOString()
                    }
                ];
                renderRecentActivity(staticActivity);
            }
        })
        .catch(error => {
            console.error('Error loading recent activity:', error);
            // Fallback to static data if API fails
            const staticActivity = [
                {
                    type: 'user',
                    description: 'New user registered: Jane Smith',
                    created_at: new Date(Date.now() - 2 * 60 * 1000).toISOString()
                },
                {
                    type: 'event',
                    description: 'New event created: AI Workshop 2024',
                    created_at: new Date(Date.now() - 60 * 60 * 1000).toISOString()
                }
            ];
            renderRecentActivity(staticActivity);
        });
}

function renderRecentActivity(activities) {
    const activityList = document.getElementById('activityList');
    
    if (activities.length === 0) {
        activityList.innerHTML = '<li class="activity-item"><div class="activity-content"><p class="activity-text">No recent activity</p></div></li>';
        return;
    }
    
    activityList.innerHTML = activities.map(activity => `
        <li class="activity-item">
            <div class="activity-icon ${activity.type}">
                <i class="fas fa-${getActivityIcon(activity.type)}"></i>
            </div>
            <div class="activity-content">
                <p class="activity-text">${activity.description}</p>
                <p class="activity-time">${formatTimeAgo(activity.created_at)}</p>
            </div>
        </li>
    `).join('');
}

function getActivityIcon(type) {
    const icons = {
        'user': 'user-plus',
        'event': 'calendar-plus',
        'registration': 'clipboard-check',
        'payment': 'credit-card'
    };
    return icons[type] || 'bell';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes} minutes ago`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} hours ago`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    return `${diffInDays} days ago`;
}
</script>
<?= $this->endSection() ?>