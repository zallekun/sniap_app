<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Simple CSS -->
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    
    <?= $this->renderSection('head') ?>
</head>
<body class="admin-simple">
    <!-- Top Bar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-cogs me-2"></i>
                SNIA Admin Panel
            </span>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3">
                    <i class="fas fa-user-shield me-1"></i>
                    <?= esc($user['first_name'] ?? 'Admin') ?> <?= esc($user['last_name'] ?? '') ?>
                </span>
                <a href="/logout" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-menu">
                <a href="/admin/dashboard" class="menu-item <?= (current_url() === site_url('/admin/dashboard')) ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <div class="menu-section">User Management</div>
                <a href="/admin/users" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                
                <div class="menu-section">System</div>
                <a href="/admin/events" class="menu-item">
                    <i class="fas fa-calendar"></i>
                    <span>Events</span>
                </a>
                <a href="/admin/registrations" class="menu-item">
                    <i class="fas fa-user-check"></i>
                    <span>Registrations</span>
                </a>
                <a href="/admin/abstracts" class="menu-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Abstracts</span>
                </a>
                
                <div class="menu-section">Reports</div>
                <a href="/admin/analytics" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
                
                <div class="menu-section">Settings</div>
                <a href="/admin/settings" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>System Config</span>
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="admin-content">
            <div class="content-header">
                <h1 class="content-title"><?= $this->renderSection('title') ?></h1>
                <div class="content-actions">
                    <?= $this->renderSection('actions') ?>
                </div>
            </div>
            
            <div class="content-body">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('additional_js') ?>
</body>
</html>