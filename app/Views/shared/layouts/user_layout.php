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
    <!-- Shared User CSS -->
    <link rel="stylesheet" href="<?= base_url('css/shared/user_dashboard.css') ?>">
    
    <?= $this->renderSection('head') ?>
</head>
<body class="user-dashboard">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="/dashboard">
                <i class="fas fa-graduation-cap me-2"></i>
                SNIA Conference
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    <?php if (isset($user['role'])): ?>
                        <?php if ($user['role'] === 'presenter'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/presenter/abstracts">
                                    <i class="fas fa-file-alt me-1"></i> My Abstracts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/presenter/presentations">
                                    <i class="fas fa-presentation me-1"></i> Presentations
                                </a>
                            </li>
                        <?php elseif ($user['role'] === 'reviewer'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/reviewer/assigned">
                                    <i class="fas fa-tasks me-1"></i> Assigned Reviews
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/reviewer/reviews">
                                    <i class="fas fa-star me-1"></i> My Reviews
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/my-registrations">
                                    <i class="fas fa-calendar-check me-1"></i> My Events
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/certificates">
                                    <i class="fas fa-certificate me-1"></i> Certificates
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="/event-schedule">
                            <i class="fas fa-calendar me-1"></i> Schedule
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-semibold"><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></div>
                                <small class="text-muted text-capitalize"><?= esc($user['role'] ?? 'User') ?></small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/profile/edit">
                                    <i class="fas fa-user me-2"></i> Edit Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/payment-history">
                                    <i class="fas fa-credit-card me-2"></i> Payment History
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title"><?= $this->renderSection('title') ?></h1>
                        <?php if (isset($user['role'])): ?>
                            <div class="role-badge badge-<?= $user['role'] ?>">
                                <i class="fas fa-user-tag me-1"></i>
                                <?= ucfirst($user['role']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer bg-light mt-5">
        <div class="container-fluid py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        © <?= date('Y') ?> SNIA Conference Management System
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-heart text-danger"></i> Made with care for academic excellence
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('additional_js') ?>
</body>
</html>