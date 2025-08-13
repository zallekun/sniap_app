<?= $this->extend('layouts/main') ?>

<?= $this->section('head') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/edit-profile.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard" class="text-decoration-none">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="profile-photo-wrapper">
                        <?php if (!empty($user['profile_photo'])): ?>
                            <img src="<?= base_url('uploads/' . $user['profile_photo']) ?>" alt="Profile Photo" class="profile-photo" id="currentPhoto">
                        <?php else: ?>
                            <div class="profile-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-user fa-4x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <button type="button" class="photo-upload-btn" onclick="triggerPhotoUpload()">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;">
                </div>
                <div class="col-md-9">
                    <h1 class="mb-2"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i><?= esc($user['email']) ?></p>
                    <p class="mb-1"><i class="fas fa-user-tag me-2"></i><?= ucfirst(esc($user['role'])) ?></p>
                    <?php if (!empty($user['institution'])): ?>
                    <p class="mb-0"><i class="fas fa-building me-2"></i><?= esc($user['institution']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Personal Information Form -->
        <div class="col-lg-8">
            <form action="/profile/update" method="POST" enctype="multipart/form-data" id="profileForm">
                <?= csrf_field() ?>
                <input type="hidden" id="photoChanged" name="photo_changed" value="0">
                
                <!-- Personal Information -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-user me-2"></i>Informasi Pribadi
                    </h4>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Nama Depan *</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" 
                                   value="<?= old('first_name', $user['first_name']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Nama Belakang *</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" 
                                   value="<?= old('last_name', $user['last_name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" value="<?= esc($user['email']) ?>" readonly>
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= old('phone', $user['phone'] ?? '') ?>" placeholder="+62xxx">
                        </div>

                        <div class="col-12">
                            <label for="institution" class="form-label">Institusi/Organisasi</label>
                            <input type="text" class="form-control" id="institution" name="institution" 
                                   value="<?= old('institution', $user['institution'] ?? '') ?>" 
                                   placeholder="Nama universitas, perusahaan, atau organisasi">
                        </div>
                    </div>
                </div>

                <!-- Password Change -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-lock me-2"></i>Ubah Password
                        <small class="text-muted fw-normal">(Opsional)</small>
                    </h4>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="currentPassword" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" name="current_password">
                                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="new_password">
                                <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="d-flex justify-content-between">
                        <a href="/dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Profile Statistics -->
        <div class="col-lg-4">
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-chart-bar me-2"></i>Statistik Profil
                </h4>
                
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="bg-primary text-white rounded p-3">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <h3 class="mb-0"><?= $stats['total_registrations'] ?? 0 ?></h3>
                            <small>Total Registrasi Event</small>
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <div class="bg-success text-white rounded p-3">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                            <h3 class="mb-0"><?= $stats['upcoming_events'] ?? 0 ?></h3>
                            <small>Event Mendatang</small>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="bg-info text-white rounded p-3">
                            <i class="fas fa-user-clock fa-2x mb-2"></i>
                            <h3 class="mb-0"><?= date('d M Y', strtotime($user['created_at'])) ?></h3>
                            <small>Bergabung Sejak</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h4>
                
                <div class="d-grid gap-2">
                    <a href="/dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="#" class="btn btn-outline-success" onclick="showAlert('Fitur akan segera tersedia', 'info')">
                        <i class="fas fa-download me-2"></i>Download Data
                    </a>
                    <a href="#" class="btn btn-outline-warning" onclick="showAlert('Fitur akan segera tersedia', 'info')">
                        <i class="fas fa-certificate me-2"></i>Sertifikat Saya
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Profile photo upload
function triggerPhotoUpload() {
    document.getElementById('profilePhotoInput').click();
}

// Handle photo upload
document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showAlert('File harus berupa gambar (JPEG, PNG, GIF, atau WebP)', 'warning');
            this.value = '';
            return;
        }
        
        // Validate file size
        if (file.size > 2 * 1024 * 1024) {
            showAlert('Ukuran file foto maksimal 2MB', 'warning');
            this.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentPhoto = document.getElementById('currentPhoto');
            const placeholder = document.getElementById('photoPlaceholder');
            
            if (currentPhoto) {
                currentPhoto.src = e.target.result;
                currentPhoto.style.display = 'block';
            } else if (placeholder) {
                placeholder.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="profile-photo" id="currentPhoto">`;
            }
            
            // Mark that photo has changed
            document.getElementById('photoChanged').value = '1';
        };
        reader.readAsDataURL(file);
        
        showAlert('Foto profil dipilih. Klik "Simpan Perubahan" untuk menyimpan!', 'info');
    }
});

// Password toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validate password fields if any is filled
    if (currentPassword || newPassword || confirmPassword) {
        if (!currentPassword) {
            e.preventDefault();
            showAlert('Password saat ini wajib diisi untuk mengubah password', 'warning');
            document.getElementById('currentPassword').focus();
            return;
        }
        
        if (!newPassword) {
            e.preventDefault();
            showAlert('Password baru wajib diisi', 'warning');
            document.getElementById('newPassword').focus();
            return;
        }
        
        if (newPassword.length < 6) {
            e.preventDefault();
            showAlert('Password baru minimal 6 karakter', 'warning');
            document.getElementById('newPassword').focus();
            return;
        }
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            showAlert('Konfirmasi password tidak sama', 'warning');
            document.getElementById('confirmPassword').focus();
            return;
        }
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Show alert function
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.children[1]);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?= $this->endSection() ?>