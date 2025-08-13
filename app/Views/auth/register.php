<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="card">
            <div class="card-body p-5">
                <!-- Logo Section -->
                <div class="logo-section">
                    <h1><i class="fas fa-graduation-cap text-primary"></i></h1>
                    <h2>Daftar Akun</h2>
                    <p>SNIA Conference Management System</p>
                </div>

                <!-- Alert Container -->
                <div class="alert-container"></div>
                
                <!-- Show validation errors -->
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Show success/error messages -->
                <?php if (session('success')): ?>
                    <div class="alert alert-success"><?= esc(session('success')) ?></div>
                <?php endif; ?>
                <?php if (session('error')): ?>
                    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form id="registerForm" method="POST">
                    <?= csrf_field() ?>
                    <!-- Role Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Jenis Peserta</label>
                        <div class="role-selection">
                            <div class="role-option" data-role="presenter">
                                <i class="fas fa-microphone"></i>
                                <h6>Presenter</h6>
                                <small>Saya akan mempresentasikan makalah</small>
                            </div>
                            <div class="role-option" data-role="audience">
                                <i class="fas fa-users"></i>
                                <h6>Peserta</h6>
                                <small>Saya akan menghadiri acara</small>
                            </div>
                        </div>
                        <input type="hidden" id="role" name="role" required>
                        <div class="invalid-feedback">Pilih jenis peserta</div>
                    </div>

                    <!-- Personal Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Nama Depan *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            <div class="invalid-feedback">Nama depan wajib diisi</div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Nama Belakang *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                            <div class="invalid-feedback">Nama belakang wajib diisi</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Email tidak valid</div>
                    </div>

                    <div class="mb-3">
                        <label for="institution" class="form-label">Institusi/Universitas *</label>
                        <input type="text" class="form-control" id="institution" name="institution" required>
                        <div class="invalid-feedback">Institusi wajib diisi</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+62812xxxxxxxx" required>
                        <div class="invalid-feedback">Nomor telepon tidak valid</div>
                    </div>

                    <!-- Password -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Password minimal 6 karakter</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Password tidak cocok</div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya setuju dengan <a href="#" class="text-primary">syarat dan ketentuan</a> yang berlaku
                            </label>
                            <div class="invalid-feedback">Anda harus menyetujui syarat dan ketentuan</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                        <span class="btn-text">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                        </span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="mb-0">Sudah punya akun? 
                        <a href="/login" class="text-primary text-decoration-none fw-bold">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const roleOptions = document.querySelectorAll('.role-option');
    const roleInput = document.getElementById('role');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');

    // Role selection
    roleOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            roleOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Set role value
            roleInput.value = this.dataset.role;
            roleInput.classList.remove('is-invalid');
        });
    });

    // Password toggle functionality
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Password confirmation validation
    confirmPasswordField.addEventListener('input', function() {
        if (this.value !== passwordField.value) {
            this.classList.add('is-invalid');
            this.nextElementSibling.nextElementSibling.textContent = 'Password tidak cocok';
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Phone number formatting
    document.getElementById('phone').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            value = '62' + value.substring(1);
        } else if (!value.startsWith('62')) {
            value = '62' + value;
        }
        this.value = '+' + value;
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        showLoading(submitBtn);
        
        // DEBUG: Simple form submission without complex JS
        if (true) {
            // Check if role is selected
            const roleSelected = document.querySelector('.role-option.selected');
            if (!roleSelected) {
                showAlert('Pilih jenis peserta terlebih dahulu', 'warning');
                hideLoading(submitBtn);
                return;
            }
            
            // Add required fields and submit directly
            if (!document.querySelector('input[name="terms"]')) {
                let termsInput = document.createElement('input');
                termsInput.type = 'hidden';
                termsInput.name = 'terms';
                termsInput.value = 'accepted';
                form.appendChild(termsInput);
            }
            
            // Add role field
            if (!document.querySelector('input[name="role"]')) {
                let roleInput = document.createElement('input');
                roleInput.type = 'hidden';
                roleInput.name = 'role';
                roleInput.value = roleSelected.dataset.role;
                form.appendChild(roleInput);
            }
            
            form.action = '/register';
            form.method = 'POST';
            form.submit();
            return;
        }

        // Validate role selection
        if (!roleInput.value) {
            roleInput.classList.add('is-invalid');
            hideLoading(submitBtn);
            showAlert('Silakan pilih jenis peserta', 'warning');
            return;
        }

        // Validate password match
        if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.classList.add('is-invalid');
            hideLoading(submitBtn);
            showAlert('Password dan konfirmasi password tidak cocok', 'warning');
            return;
        }

        // Collect form data
        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            email: document.getElementById('email').value,
            institution: document.getElementById('institution').value,
            phone_number: document.getElementById('phone').value,
            password: passwordField.value,
            confirm_password: confirmPasswordField.value,
            role: roleInput.value
        };

        try {
            // Submit form normally to allow CodeIgniter to handle redirects
            showLoading(submitBtn);
            showAlert('Memproses pendaftaran...', 'info');
            
            // Create hidden inputs for form data
            Object.entries(formData).forEach(([key, value]) => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            });
            
            // Add terms acceptance
            let termsInput = document.createElement('input');
            termsInput.type = 'hidden';
            termsInput.name = 'terms';
            termsInput.value = 'accepted';
            form.appendChild(termsInput);
            
            // Submit the form normally (this will trigger redirect)
            form.action = '/register';
            form.method = 'POST';
            form.submit();
        } catch (error) {
            console.error('Registration error:', error);
            showAlert('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
        } finally {
            // Don't hide loading since we're redirecting
            // hideLoading(submitBtn);
        }
    });

    // Remove validation classes on input
    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>
<?= $this->endSection() ?>