<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SNIA Conference Management System' ?></title>
    
    <!-- Font Awesome ONLY -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Page Specific Styles -->
    <?= $this->renderSection('styles') ?>
    
    <!-- Page-specific head content -->
    <?= $this->renderSection('head') ?>
</head>
<body>
    <!-- Navigation -->
    <?php if (!isset($hideNavbar) || !$hideNavbar): ?>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>
                SNIA Conference
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (session()->get('user_id')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                <?= session()->get('first_name') ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?php if (!isset($hideFooter) || !$hideFooter): ?>
    <footer class="footer mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 SNIA Conference Management System. All rights reserved.</p>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Common JavaScript -->
    <script>
        // Show loading spinner
        function showLoading(button) {
            const spinner = button.querySelector('.loading');
            const text = button.querySelector('.btn-text');
            if (spinner && text) {
                spinner.classList.add('show');
                text.style.display = 'none';
                button.disabled = true;
            }
        }

        // Hide loading spinner
        function hideLoading(button) {
            const spinner = button.querySelector('.loading');
            const text = button.querySelector('.btn-text');
            if (spinner && text) {
                spinner.classList.remove('show');
                text.style.display = 'inline';
                button.disabled = false;
            }
        }

        // Show alert message
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.alert-container') || document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
                
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }

        // API request helper
        async function apiRequest(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const token = localStorage.getItem('snia_token');
            if (token) {
                defaultOptions.headers.Authorization = `Bearer ${token}`;
            }

            const response = await fetch(url, { ...defaultOptions, ...options });
            const data = await response.json();
            
            return { response, data };
        }

        // Simple console log for debugging
        console.log('✅ Login page loaded');
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>