<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="card">
            <div class="card-body p-5">
                <!-- Logo Section -->
                <div class="logo-section">
                    <h1><i class="fas fa-graduation-cap text-primary"></i></h1>
                    <h2>Login</h2>
                    <p>SNIA Conference Management System</p>
                </div>

                <!-- Alert Container -->
                <div class="alert-container"></div>

                <!-- Login Form -->
                <form id="loginForm" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="masukkan@email.com" required>
                        </div>
                        <div class="invalid-feedback">Email tidak valid</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Password wajib diisi</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </span>
                    </button>

                    <div class="text-center">
                        <a href="#" class="text-primary text-decoration-none" id="forgotPasswordLink">
                            Lupa password?
                        </a>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="text-center mt-4">
                    <p class="mb-0">Belum punya akun? 
                        <a href="/register" class="text-primary text-decoration-none fw-bold">Daftar di sini</a>
                    </p>
                </div>

                <!-- Demo Accounts -->
                <div class="mt-4">
                    <hr>
                    <h6 class="text-center text-muted mb-3">Demo Accounts</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-info btn-sm w-100" onclick="fillDemoAccount('presenter')">
                                <i class="fas fa-microphone me-1"></i>
                                Demo Presenter
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="fillDemoAccount('admin')">
                                <i class="fas fa-user-shield me-1"></i>
                                Demo Admin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="resetEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="resetEmail" placeholder="Masukkan email Anda" required>
                        <div class="form-text">Kami akan mengirimkan link reset password ke email Anda.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="sendResetLink">
                    <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                    <span class="btn-text">Kirim Link Reset</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');

    // Password toggle functionality
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Demo account functions
    window.fillDemoAccount = function(type) {
        if (type === 'presenter') {
            emailField.value = 'presenter@test.com';
            passwordField.value = 'test123';
        } else if (type === 'admin') {
            emailField.value = 'admin@test.com';
            passwordField.value = 'admin123';
        }
    };

    // Forgot password modal
    document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
        modal.show();
    });

    // Send reset link
    document.getElementById('sendResetLink').addEventListener('click', async function() {
        const resetEmail = document.getElementById('resetEmail').value;
        if (!resetEmail) {
            showAlert('Silakan masukkan email Anda', 'warning');
            return;
        }

        const button = this;
        showLoading(button);

        try {
            const { response, data } = await apiRequest('/api/v1/auth/forgot-password', {
                method: 'POST',
                body: JSON.stringify({ email: resetEmail })
            });

            if (data.status === 'success') {
                showAlert('Link reset password telah dikirim ke email Anda', 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                modal.hide();
            } else {
                showAlert(data.message || 'Terjadi kesalahan saat mengirim email', 'danger');
            }
        } catch (error) {
            console.error('Forgot password error:', error);
            showAlert('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
        } finally {
            hideLoading(button);
        }
    });

    // Login form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        showLoading(submitBtn);

        const formData = {
            email: emailField.value,
            password: passwordField.value
        };

        try {
            const { response, data } = await apiRequest('/api/v1/auth/login', {
                method: 'POST',
                body: JSON.stringify(formData)
            });

            if (data.status === 'success') {
                // Store token and user info
                localStorage.setItem('snia_token', data.data.token);
                localStorage.setItem('snia_user', JSON.stringify(data.data.user));

                showAlert('Login berhasil! Mengarahkan ke dashboard...', 'success');
                
                // Redirect based on role
                setTimeout(() => {
                    const userRole = data.data.user.role;
                    if (userRole === 'admin') {
                        window.location.href = '/admin/dashboard';
                    } else {
                        window.location.href = '/dashboard';
                    }
                }, 1500);
            } else {
                // Handle login errors
                if (data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        const fieldElement = document.getElementById(field);
                        if (fieldElement) {
                            fieldElement.classList.add('is-invalid');
                            const feedback = fieldElement.parentNode.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = message;
                            }
                        }
                    }
                }
                showAlert(data.message || 'Email atau password salah', 'danger');
            }
        } catch (error) {
            console.error('Login error:', error);
            showAlert('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
        } finally {
            hideLoading(submitBtn);
        }
    });

    // Remove validation classes on input
    [emailField, passwordField].forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Check if user is already logged in
    const token = localStorage.getItem('snia_token');
    if (token) {
        // Verify token validity
        apiRequest('/api/v1/auth/profile').then(({ data }) => {
            if (data.status === 'success') {
                const userRole = data.data.role;
                if (userRole === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else {
                    window.location.href = '/dashboard';
                }
            } else {
                // Token invalid, remove it
                localStorage.removeItem('snia_token');
                localStorage.removeItem('snia_user');
            }
        }).catch(() => {
            // Token invalid, remove it
            localStorage.removeItem('snia_token');
            localStorage.removeItem('snia_user');
        });
    }
});
</script>
<?= $this->endSection() ?>