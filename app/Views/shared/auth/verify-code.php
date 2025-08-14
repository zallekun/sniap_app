<?= $this->extend('shared/layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/auth.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="card">
            <div class="card-body p-5">
                <!-- Logo Section -->
                <div class="logo-section">
                    <h1><i class="fas fa-envelope-check text-primary"></i></h1>
                    <h2>Verifikasi Email</h2>
                    <p>Masukkan kode 6 digit yang telah dikirim ke email Anda</p>
                </div>

                <!-- Email Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Email:</strong> <?= esc($email) ?><br>
                    <small>Tidak menerima kode? <a href="#" id="resendCode">Kirim ulang</a></small>
                </div>

                <!-- Alert Container -->
                <div class="alert-container"></div>

                <!-- Verification Form -->
                <form id="verifyForm" method="POST">
                    <!-- <?= csrf_field() ?> Temporary disabled for testing -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Kode Verifikasi</label>
                        <div class="verification-code-container">
                            <input type="text" class="code-input" maxlength="1" data-index="0">
                            <input type="text" class="code-input" maxlength="1" data-index="1">
                            <input type="text" class="code-input" maxlength="1" data-index="2">
                            <input type="text" class="code-input" maxlength="1" data-index="3">
                            <input type="text" class="code-input" maxlength="1" data-index="4">
                            <input type="text" class="code-input" maxlength="1" data-index="5">
                        </div>
                        <input type="hidden" id="verification_code" name="verification_code">
                        <div class="invalid-feedback">Masukkan kode verifikasi 6 digit</div>
                        <small class="text-muted">Kode berlaku selama 15 menit</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <span class="btn-text">Verifikasi</span>
                            <span class="btn-loading d-none">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Memverifikasi...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="text-center mt-4">
                    <a href="/login" class="text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verifyForm');
    const submitBtn = document.getElementById('submitBtn');
    const hiddenInput = document.getElementById('verification_code');
    const codeInputs = document.querySelectorAll('.code-input');
    const resendLink = document.getElementById('resendCode');

    // Handle individual code inputs
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Remove validation classes
            this.classList.remove('is-invalid');
            
            // Add filled class if has value
            if (this.value) {
                this.classList.add('filled');
                
                // Move to next input
                if (index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            } else {
                this.classList.remove('filled');
            }
            
            // Update hidden input
            updateHiddenInput();
            
            // Auto-submit if all fields filled
            checkAutoSubmit();
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && !this.value && index > 0) {
                codeInputs[index - 1].focus();
            }
            
            // Handle paste
            if (e.key === 'Enter') {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            
            if (paste.length === 6) {
                paste.split('').forEach((digit, i) => {
                    if (i < codeInputs.length) {
                        codeInputs[i].value = digit;
                        codeInputs[i].classList.add('filled');
                    }
                });
                updateHiddenInput();
                checkAutoSubmit();
            }
        });
    });

    function updateHiddenInput() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        hiddenInput.value = code;
    }

    function checkAutoSubmit() {
        const code = hiddenInput.value;
        if (code.length === 6) {
            setTimeout(() => {
                form.dispatchEvent(new Event('submit'));
            }, 300);
        }
    }

    function clearInputs() {
        codeInputs.forEach(input => {
            input.value = '';
            input.classList.remove('filled', 'is-invalid');
        });
        hiddenInput.value = '';
        codeInputs[0].focus();
    }

    function markInputsInvalid() {
        codeInputs.forEach(input => {
            input.classList.add('is-invalid');
        });
    }

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = hiddenInput.value;

        // Validate code
        if (!code || code.length !== 6) {
            markInputsInvalid();
            showAlert('Masukkan kode verifikasi 6 digit', 'warning');
            return;
        }

        showLoading(submitBtn);

        try {
            // Create FormData to include CSRF token
            const formData = new FormData(form);
            formData.set('verification_code', code);
            
            // Debug: Log FormData contents
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            const response = await fetch('/auth/verify-code', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Email berhasil diverifikasi! Mengarahkan ke login...', 'success');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            } else {
                markInputsInvalid();
                showAlert(data.message || 'Kode verifikasi tidak valid', 'danger');
            }
        } catch (error) {
            console.error('Verification error:', error);
            showAlert('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
        } finally {
            hideLoading(submitBtn);
        }
    });

    // Handle resend code
    resendLink.addEventListener('click', async function(e) {
        e.preventDefault();
        
        try {
            // Create FormData with CSRF token  
            const formData = new FormData(form);
            
            const response = await fetch('/auth/resend-code', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Kode verifikasi baru telah dikirim ke email Anda', 'success');
                clearInputs();
            } else {
                showAlert(data.message || 'Gagal mengirim ulang kode', 'danger');
            }
        } catch (error) {
            console.error('Resend error:', error);
            showAlert('Terjadi kesalahan sistem. Silakan coba lagi.', 'danger');
        }
    });

    // Focus on first input
    codeInputs[0].focus();
});
</script>
<?= $this->endSection() ?>