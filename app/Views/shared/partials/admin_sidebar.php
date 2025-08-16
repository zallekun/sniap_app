<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="/admin/dashboard" class="sidebar-logo">SNIA Admin</a>
        <div class="sidebar-subtitle">Conference Management</div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Dashboard</div>
            <div class="nav-item">
                <a href="/admin/dashboard" class="nav-link <?= (current_url() === site_url('/admin/dashboard') || current_url() === site_url('/admin')) ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Overview
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Management</div>
            <div class="nav-item">
                <a href="/admin/users" class="nav-link <?= (strpos(current_url(), '/admin/users') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    User Management
                </a>
            </div>
            <div class="nav-item">
                <a href="/admin/events" class="nav-link <?= (strpos(current_url(), '/admin/events') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-calendar"></i>
                    Event Management
                </a>
            </div>
            <div class="nav-item">
                <a href="/admin/registrations" class="nav-link <?= (strpos(current_url(), '/admin/registrations') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i>
                    Registration Management
                </a>
            </div>
            <div class="nav-item">
                <a href="/admin/abstracts" class="nav-link <?= (strpos(current_url(), '/admin/abstracts') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    Abstract Management
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Analytics</div>
            <div class="nav-item">
                <a href="/admin/analytics" class="nav-link <?= (strpos(current_url(), '/admin/analytics') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    Reports & Analytics
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <div class="nav-item">
                <a href="/admin/settings" class="nav-link <?= (strpos(current_url(), '/admin/settings') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-cogs"></i>
                    Settings
                </a>
            </div>
            <div class="nav-item">
                <a href="/logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>
</aside>