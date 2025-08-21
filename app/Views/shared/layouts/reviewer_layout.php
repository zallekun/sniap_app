<?= $this->extend('shared/layouts/base_layout') ?>

<?= $this->section('head') ?>
<!-- Reviewer specific styles -->
<link href="<?= base_url('css/roles/reviewer/reviewer.css') ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<?= $this->renderSection('reviewer_head') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Reviewer Navigation -->
<nav class="navbar navbar-expand-lg reviewer-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/reviewer/dashboard">
            <i class="fas fa-star-half-alt me-2"></i>
            SNIA Reviewer Portal
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/reviewer/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reviewer/assigned">
                        <i class="fas fa-tasks me-1"></i> Assigned Abstracts
                        <?php if (isset($stats['pending_reviews']) && $stats['pending_reviews'] > 0): ?>
                            <span class="badge bg-warning text-dark ms-1"><?= $stats['pending_reviews'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/reviewer/reviews">
                        <i class="fas fa-star me-1"></i> My Reviews
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="reviewer-user-avatar me-2">
                            <?= strtoupper(substr($user['first_name'] ?? 'R', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold"><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></div>
                            <small class="text-muted">
                                <span class="reviewer-role-badge">REVIEWER</span>
                            </small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/profile/edit">
                                <i class="fas fa-user me-2"></i> Edit Profile
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
    <!-- Page Content -->
    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<footer class="footer bg-light mt-5">
    <div class="container-fluid py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    © <?= date('Y') ?> SNIA Conference Management System - Reviewer Portal
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    <i class="fas fa-star text-warning"></i> Academic Review Excellence
                </small>
            </div>
        </div>
    </div>
</footer>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Reviewer layout specific scripts -->
<?= $this->renderSection('reviewer_scripts') ?>
<?= $this->endSection() ?>