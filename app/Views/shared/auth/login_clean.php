<?= $this->extend('shared/layouts/base_layout') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="auth-wrapper">
        <!-- Left Side - Form -->
        <div class="auth-form-section">
            <div class="auth-form-container">
                <!-- Logo -->
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>SNIA</span>
                </div>
                
                <!-- Form -->
                <div class="auth-form">
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to your account</p>
                    
                    <!-- Alert Container -->
                    <div id="alertContainer"></div>
                    
                    <form id="loginForm" method="POST" action="<?= site_url('auth/login') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Enter your password" required>
                        </div>
                        
                        <div class="form-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="<?= site_url('auth/forgot-password') ?>" class="forgot-link">
                                Forgot password?
                            </a>
                        </div>
                        
                        <button type="submit" class="btn-primary btn-login">
                            <span class="btn-text">Sign In</span>
                            <i class="fas fa-spinner fa-spin btn-loading d-none"></i>
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Don't have an account? 
                            <a href="<?= site_url('auth/register') ?>">Create one</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Visual -->
        <div class="auth-visual-section">
            <div class="visual-content">
                <div class="visual-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h2>Conference Management</h2>
                <p>Manage your academic conference experience with ease</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Submit and track abstracts</li>
                    <li><i class="fas fa-check"></i> Review submissions</li>
                    <li><i class="fas fa-check"></i> Register for events</li>
                    <li><i class="fas fa-check"></i> Manage presentations</li>
                </ul>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    
    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('.btn-login');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        const alertContainer = document.getElementById('alertContainer');
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        
        // Clear previous alerts
        alertContainer.innerHTML = '';
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                alertContainer.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect || '/dashboard';
                }, 1000);
            } else {
                // Show error message
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
                
                // Reset button
                submitBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    An error occurred. Please try again.
                </div>
            `;
            
            // Reset button
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        });
    });
    </script>
<?= $this->endSection() ?>