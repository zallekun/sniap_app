<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - SNIA Conference</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Audience Dashboard CSS -->
    <link rel="stylesheet" href="<?= base_url('css/audience/dashboard.css') ?>">
    
    <?= $this->renderSection('head') ?>
</head>
<body class="audience-body">
    <div class="audience-layout">
        <!-- Sidebar -->
        <aside class="audience-sidebar">
            <div class="sidebar-header">
                <a href="/dashboard" class="sidebar-logo">SNIA Audience</a>
                <div class="sidebar-subtitle">Conference Participant</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="/dashboard" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            Overview
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">My Events</div>
                    <div class="nav-item">
                        <a href="/my-registrations" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            My Registrations
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/event-schedule" class="nav-link">
                            <i class="fas fa-calendar"></i>
                            Event Schedule
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="/certificates" class="nav-link">
                            <i class="fas fa-certificate"></i>
                            Certificates
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Payment</div>
                    <div class="nav-item">
                        <a href="/payment-history" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            Payment History
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Profile</div>
                    <div class="nav-item">
                        <a href="/profile/edit" class="nav-link">
                            <i class="fas fa-user"></i>
                            Edit Profile
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
        
        <!-- Main Content -->
        <main class="audience-main">
            <!-- Header -->
            <header class="audience-header">
                <h1 class="header-title"><?= $this->renderSection('title') ?></h1>
                <div class="header-actions">
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['first_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1f2937;">
                                <?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?>
                            </div>
                            <div style="font-size: 0.875rem; color: #6b7280;">
                                Participant
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="audience-content">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('additional_js') ?>
</body>
</html>