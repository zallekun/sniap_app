<?= $this->extend('shared/layouts/audience_layout') ?>

<?= $this->section('title') ?><?= $title ?? 'Audience Dashboard' ?><?= $this->endSection() ?>

<?= $this->section('head') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 dashboard-page">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Selamat Datang, <span id="userName">Loading...</span>!
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-user-tag me-2"></i>
                                <span id="userRole">Loading...</span> di SNIA Conference 2025
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="fs-1">
                                <i id="roleIcon" class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4" id="statsRow">
        <!-- Stats will be loaded dynamically -->
    </div>

    <!-- Main Content Area -->
    <div class="row">
        <!-- Left Column - Main Content -->
        <div class="col-lg-8">
            <!-- Presenter-specific content -->
            <div id="presenterContent" class="d-none">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-microphone me-2"></i>Panel Presenter</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary" onclick="showRegistrationForm()">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Daftar sebagai Presenter
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-success" onclick="showAbstractForm()">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Submit Abstract
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-info" onclick="viewMyAbstracts()">
                                        <i class="fas fa-list me-2"></i>
                                        Kelola Abstract Saya
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-warning" onclick="viewCertificates()">
                                        <i class="fas fa-certificate me-2"></i>
                                        Sertifikat Saya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Abstracts -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-file-text me-2"></i>Abstract Saya</h6>
                    </div>
                    <div class="card-body">
                        <div id="abstractsList">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2">Memuat data abstract...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audience-specific content -->
            <div id="audienceContent" class="d-none">
                <div class="card mb-4 panel-card">
                    <div class="card-header section-header-secondary">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Panel Peserta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn panel-btn-primary" onclick="showEventRegistrationModal()">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Daftar Event
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="/dashboard/event-schedule-page" class="btn panel-btn-success">
                                        <i class="fas fa-calendar me-2"></i>
                                        Jadwal Acara
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn panel-btn-secondary" onclick="loadMyQRCodes()">
                                        <i class="fas fa-qrcode me-2"></i>
                                        QR Code Saya
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn panel-btn-warning" onclick="loadMyCertificates()">
                                        <i class="fas fa-certificate me-2"></i>
                                        Sertifikat Saya
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn panel-btn-primary" onclick="loadPaymentHistory()">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Riwayat Pembayaran
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn panel-btn-secondary" onclick="loadVoucherSection()">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Voucher Saya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Registration Section -->
                <div class="card mb-4 panel-card">
                    <div class="card-header section-header-light">
                        <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Event Tersedia</h6>
                    </div>
                    <div class="card-body">
                        <div id="availableEvents">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2">Memuat event...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div id="recentActivities">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2">Memuat aktivitas...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Info & Notifications -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Profil Saya</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                    <h6 id="profileName">Loading...</h6>
                    <p class="text-muted mb-1" id="profileEmail">Loading...</p>
                    <p class="text-muted mb-3" id="profileInstitution">Loading...</p>
                    <div class="d-grid">
                        <a href="/profile/edit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Registrations -->
            <div class="card mb-4 panel-card">
                <div class="card-header section-header-light">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Pendaftaran Saya</h6>
                </div>
                <div class="card-body">
                    <div id="registrationsList">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2">Memuat data...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Notifikasi</h6>
                </div>
                <div class="card-body">
                    <div id="notificationsList">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2">Memuat notifikasi...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header section-header-primary">
                <h5 class="modal-title" id="editProfileModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Edit Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProfileForm">
                    <!-- Profile Photo Section -->
                    <div class="text-center mb-4">
                        <div class="profile-photo-wrapper position-relative d-inline-block">
                            <div id="currentProfilePhoto" class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <button type="button" class="btn btn-sm panel-btn-secondary position-absolute bottom-0 end-0 rounded-circle p-2" onclick="triggerPhotoUpload()">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Klik ikon kamera untuk mengubah foto profil</small>
                        </div>
                        <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;">
                    </div>

                    <div class="row g-3">
                        <!-- Personal Information -->
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-user me-2 text-primary"></i>Informasi Pribadi
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="editFirstName" class="form-label">Nama Depan *</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="editLastName" class="form-label">Nama Belakang *</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="editEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required readonly>
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>

                        <div class="col-md-6">
                            <label for="editPhone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="editPhone" name="phone">
                        </div>

                        <div class="col-12">
                            <label for="editInstitution" class="form-label">Institusi/Organisasi</label>
                            <input type="text" class="form-control" id="editInstitution" name="institution">
                        </div>

                        <!-- Security Section -->
                        <div class="col-12 mt-4">
                            <hr>
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-lock me-2 text-warning"></i>Ubah Password
                                <small class="text-muted fw-normal">(Opsional)</small>
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="currentPassword" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" name="current_password">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="new_password">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn panel-btn-primary" id="saveProfileBtn" onclick="saveProfile()">
                    <span class="loading spinner-border spinner-border-sm me-2" role="status" style="display: none;"></span>
                    <span class="btn-text">
                        <i class="fas fa-save me-1"></i>Simpan Perubahan
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin logout dari sistem?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmLogout()">
                    <i class="fas fa-sign-out-alt me-1"></i>Ya, Logout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Registration Confirmation Modal -->
<div class="modal fade" id="eventRegistrationModal" tabindex="-1" aria-labelledby="eventRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventRegistrationModalLabel">
                    <i class="fas fa-ticket-alt me-2"></i>Konfirmasi Pendaftaran Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventRegistrationContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat informasi event...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmEventRegistration" disabled>
                    <i class="fas fa-check me-1"></i>Konfirmasi Daftar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Registration Detail Modal -->
<div class="modal fade" id="registrationDetailModal" tabindex="-1" aria-labelledby="registrationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationDetailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail Pendaftaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="registrationDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat detail pendaftaran...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="payFromDetailBtn" style="display: none;">
                    <i class="fas fa-credit-card me-1"></i>Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran Pendaftaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat informasi pembayaran...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmPaymentBtn" disabled>
                    <i class="fas fa-check me-1"></i>Konfirmasi Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Schedule Modal -->
<div class="modal fade" id="eventScheduleModal" tabindex="-1" aria-labelledby="eventScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header section-header-secondary">
                <h5 class="modal-title" id="eventScheduleModalLabel">
                    <i class="fas fa-calendar me-2"></i>Jadwal Acara SNIA Conference
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- View Mode Tabs -->
                <div class="border-bottom">
                    <nav class="nav nav-tabs justify-content-center" id="scheduleViewTabs" role="tablist">
                        <button class="nav-link active" id="calendar-view-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab">
                            <i class="fas fa-calendar-alt me-2"></i>Kalender
                        </button>
                        <button class="nav-link" id="list-view-tab" data-bs-toggle="tab" data-bs-target="#list-view" type="button" role="tab">
                            <i class="fas fa-list me-2"></i>Daftar
                        </button>
                        <button class="nav-link" id="timeline-view-tab" data-bs-toggle="tab" data-bs-target="#timeline-view" type="button" role="tab">
                            <i class="fas fa-stream me-2"></i>Timeline
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="scheduleViewContent">
                    <!-- Calendar View -->
                    <div class="tab-pane fade show active p-4" id="calendar-view" role="tabpanel">
                        <div id="calendar-container">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar me-2 text-primary"></i>
                                        <span id="current-month-year"></span>
                                    </h6>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" id="prev-month">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="today-btn">Hari Ini</button>
                                        <button type="button" class="btn btn-outline-primary" id="next-month">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="event-calendar"></div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div class="tab-pane fade p-4" id="list-view" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="mb-0">
                                    <i class="fas fa-list me-2 text-success"></i>
                                    Daftar Event Tersedia
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="event-search" placeholder="Cari event...">
                                </div>
                            </div>
                        </div>
                        <div id="event-list-container">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2">Memuat daftar event...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline View -->
                    <div class="tab-pane fade p-4" id="timeline-view" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="mb-0">
                                    <i class="fas fa-stream me-2 text-info"></i>
                                    Timeline Acara
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="timeline-filter">
                                    <option value="all">Semua Event</option>
                                    <option value="registered">Sudah Terdaftar</option>
                                    <option value="available">Belum Terdaftar</option>
                                </select>
                            </div>
                        </div>
                        <div id="event-timeline-container">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2">Memuat timeline event...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <span class="badge bg-success me-2">Terdaftar</span>
                        <span class="badge bg-primary">Tersedia</span>
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loading...');
    
    // Use server-side session data (primary source)
    const serverUser = <?= json_encode($user ?? null) ?>;
    
    if (serverUser) {
        console.log('Using server session data:', serverUser);
        
        // Load dashboard with server session data
        updateUserProfile(serverUser);
        loadDashboardContent(serverUser);
        
        // Load additional data
        loadRecentActivities();
        loadNotifications();
        
        // Load registrations data
        loadRegistrations();
        
        // For audience users, auto-load available events
        if (serverUser.role === 'audience') {
            loadRegistrationForm();
        }
    } else {
        // No session found, redirect to login
        console.log('No session found, redirecting to login');
        window.location.href = '/login';
    }
});

async function verifyTokenAndLoadData() {
    // Prevent infinite loops by adding a flag
    if (window.verificationInProgress) {
        return;
    }
    window.verificationInProgress = true;
    
    try {
        const { response, data } = await apiRequest('/api/v1/auth/profile');
        
        if (data.status === 'success') {
            // Token is valid, update stored user data
            localStorage.setItem('snia_user', JSON.stringify(data.data));
            loadDashboardFromAPI(data.data);
        } else {
            console.error('Token invalid:', data);
            // Token invalid, clear and redirect
            localStorage.removeItem('snia_token');
            localStorage.removeItem('snia_user');
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Token verification failed:', error);
        // If it's a fetch error, use stored user data as fallback
        const storedUser = JSON.parse(localStorage.getItem('snia_user') || '{}');
        if (storedUser.email) {
            console.log('Using cached user data');
            loadDashboardFromAPI(storedUser);
        } else {
            localStorage.removeItem('snia_token');
            localStorage.removeItem('snia_user');
            window.location.href = '/login';
        }
    } finally {
        window.verificationInProgress = false;
    }
}

function loadDashboardContent(user) {
    // Load dashboard content without API calls that might cause loops
    console.log('Loading dashboard content for:', user.email);
    
    // Load basic UI components without external API calls
    loadBasicStats(user);
    loadBasicNotifications();
}

function loadBasicStats(user) {
    // Load basic stats without API calls
    const statsContainer = document.getElementById('statisticsContainer');
    if (statsContainer) {
        statsContainer.innerHTML = `
            <div class="text-center py-3">
                <p class="mb-0">Welcome, ${user.first_name}!</p>
                <p class="text-muted small">Dashboard loaded successfully</p>
            </div>
        `;
    }
}

function loadBasicNotifications() {
    // Load basic notifications without API calls
    const notificationsContainer = document.getElementById('notificationsList');
    if (notificationsContainer) {
        notificationsContainer.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <p class="mb-0">No new notifications</p>
            </div>
        `;
    }
}

function loadDashboardFromSession(user) {
    // Update UI with session data
    updateUserProfile(user);
    loadDashboardContent(user);
}

function loadDashboardFromAPI(user) {
    // Update UI with API data
    updateUserProfile(user);
    loadDashboardContent(user);
}

function updateUserProfile(user) {
    document.getElementById('userName').textContent = user.first_name + ' ' + user.last_name;
    document.getElementById('userRole').textContent = getRoleDisplayName(user.role);
    document.getElementById('profileName').textContent = user.first_name + ' ' + user.last_name;
    document.getElementById('profileEmail').textContent = user.email;
    document.getElementById('profileInstitution').textContent = user.institution || 'Tidak ada data';
    
    // Update role icon
    const roleIcon = document.getElementById('roleIcon');
    const roleIcons = {
        'presenter': 'fas fa-microphone',
        'audience': 'fas fa-users',
        'reviewer': 'fas fa-user-graduate',
        'admin': 'fas fa-user-shield'
    };
    roleIcon.className = roleIcons[user.role] || 'fas fa-user';
    
    // Show role-specific content
    showRoleContent(user.role);
    
    // Load role-specific data
    if (user.role === 'presenter') {
        loadMyAbstracts();
    }
}


async function loadDashboardStats() {
    try {
        // Create stats cards based on user role
        const user = JSON.parse(localStorage.getItem('snia_user') || '{}');
        const statsRow = document.getElementById('statsRow');
        
        let statsHtml = '';
        
        if (user.role === 'presenter') {
            // Load presenter stats
            const { data: regData } = await apiRequest('/api/v1/registrations');
            const { data: abstractData } = await apiRequest('/api/v1/abstracts');
            const { data: certData } = await apiRequest('/api/v1/certificates');
            
            const registrationCount = regData.status === 'success' ? regData.data.length : 0;
            const abstractCount = abstractData.status === 'success' ? abstractData.data.length : 0;
            const certificateCount = certData.status === 'success' ? certData.data.length : 0;
            
            statsHtml = `
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <h3>${registrationCount}</h3>
                            <p class="mb-0">Pendaftaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h3>${abstractCount}</h3>
                            <p class="mb-0">Abstract</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-certificate fa-3x mb-3"></i>
                            <h3>${certificateCount}</h3>
                            <p class="mb-0">Sertifikat</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Load audience stats
            const { data: regData } = await apiRequest('/api/v1/registrations');
            const { data: qrData } = await apiRequest('/api/v1/qr/my-codes');
            const { data: certData } = await apiRequest('/api/v1/certificates');
            
            const registrationCount = regData.status === 'success' ? regData.data.length : 0;
            const qrCount = qrData.status === 'success' ? qrData.data.length : 0;
            const certificateCount = certData.status === 'success' ? certData.data.length : 0;
            
            statsHtml = `
                <div class="col-md-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                            <h3>${registrationCount}</h3>
                            <p class="mb-0">Pendaftaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-qrcode fa-3x mb-3"></i>
                            <h3>${qrCount}</h3>
                            <p class="mb-0">QR Code</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-certificate fa-3x mb-3"></i>
                            <h3>${certificateCount}</h3>
                            <p class="mb-0">Sertifikat</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        statsRow.innerHTML = statsHtml;
    } catch (error) {
        console.error('Error loading stats:', error);
        document.getElementById('statsRow').innerHTML = '<div class="col-12"><p class="text-muted">Gagal memuat statistik</p></div>';
    }
}

async function loadRegistrations() {
    try {
        const response = await fetch('/dashboard/registrations', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        const container = document.getElementById('registrationsList');
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            let html = '<div class="row g-3">';
            
            data.data.forEach(registration => {
                const statusBadge = getStatusBadge(registration.registration_status);
                const paymentBadge = getPaymentBadge(registration.payment_status);
                const eventDate = registration.created_at ? formatDate(registration.created_at) : 'Tidak diketahui';
                
                html += `
                    <div class="col-12 mb-3">
                        <div class="card card-registration border-start border-4 ${registration.payment_status === 'success' ? 'border-success' : registration.payment_status === 'pending' ? 'border-warning' : 'border-secondary'}">
                            <div class="card-body p-4">
                                <!-- Header Section -->
                                <div class="registration-card-header">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm ${registration.payment_status === 'success' ? 'bg-success' : registration.payment_status === 'pending' ? 'bg-warning' : 'bg-secondary'} rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-calendar-check text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1 text-dark fw-bold">${registration.event_name || 'Event'}</h6>
                                                    <p class="text-muted mb-0 small lh-sm">${(registration.event_description || 'Tidak ada deskripsi').substring(0, 100)}${(registration.event_description || '').length > 100 ? '...' : ''}</p>
                                                </div>
                                                <div class="d-flex gap-2 align-items-start flex-shrink-0">
                                                    ${statusBadge}
                                                    ${paymentBadge}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Metadata & Actions Section -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="registration-meta-grid flex-grow-1 me-3">
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-hashtag me-2 text-info"></i>
                                            <span>ID: ${registration.id}</span>
                                        </small>
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <span class="text-capitalize">${registration.registration_type || 'audience'}</span>
                                        </small>
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-calendar me-2 text-success"></i>
                                            <span>${eventDate}</span>
                                        </small>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="registration-action-buttons">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewRegistration(${registration.id})" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        ${registration.payment_status === 'pending' ? 
                                            `<button class="btn btn-success btn-sm" onclick="payRegistration(${registration.id})" title="Bayar Sekarang">
                                                <i class="fas fa-credit-card me-1"></i>Bayar
                                            </button>` : 
                                            registration.payment_status === 'success' ?
                                            `<button class="btn btn-outline-info btn-sm" onclick="downloadTicket(${registration.id})" title="Download Tiket" disabled>
                                                <i class="fas fa-download me-1"></i>Tiket
                                            </button>` : ''
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-muted text-center">Belum ada pendaftaran</p>';
        }
    } catch (error) {
        console.error('Error loading registrations:', error);
        if (container) {
            container.innerHTML = '<p class="text-danger text-center">Gagal memuat data pendaftaran</p>';
        }
    }
}

// Alias for consistency
async function loadMyRegistrations() {
    return await loadRegistrations();
}

// View registration details
async function viewRegistration(registrationId) {
    try {
        // Get registration details from loaded registrations data
        const response = await fetch('/dashboard/registrations', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            const registration = data.data.find(reg => reg.id == registrationId);
            if (registration) {
                displayRegistrationDetail(registration);
            } else {
                showAlert('Data pendaftaran tidak ditemukan', 'danger');
            }
        } else {
            showAlert('Gagal memuat detail pendaftaran', 'danger');
        }
    } catch (error) {
        console.error('Error loading registration details:', error);
        showAlert('Terjadi kesalahan saat memuat detail', 'danger');
    }
}

// Display registration detail modal
function displayRegistrationDetail(registration) {
    const modal = new bootstrap.Modal(document.getElementById('registrationDetailModal'));
    const content = document.getElementById('registrationDetailContent');
    const payBtn = document.getElementById('payFromDetailBtn');
    
    const eventDate = registration.created_at ? formatDate(registration.created_at) : 'Tidak diketahui';
    const registrationStatusBadge = getStatusBadge(registration.registration_status);
    const paymentStatusBadge = getPaymentBadge(registration.payment_status);
    
    content.innerHTML = `
        <!-- Header Section -->
        <div class="d-flex align-items-start mb-4">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm ${registration.payment_status === 'success' ? 'bg-success' : registration.payment_status === 'pending' ? 'bg-warning' : 'bg-secondary'} rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2 fw-bold text-dark">${registration.event_name || 'Event'}</h5>
                <p class="text-muted mb-3 lh-sm">${registration.event_description || 'Tidak ada deskripsi tersedia'}</p>
                <div class="d-flex gap-2">
                    ${registrationStatusBadge}
                    ${paymentStatusBadge}
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="info-divider"></div>

        <div class="row">
            <!-- Main Details -->
            <div class="col-lg-8">
                <div class="metadata-section">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Detail Pendaftaran
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-hashtag me-2 text-info"></i>
                                <div>
                                    <small class="text-muted d-block">ID Pendaftaran</small>
                                    <code class="fw-bold">#${registration.id}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar me-2 text-success"></i>
                                <div>
                                    <small class="text-muted d-block">Tanggal Pendaftaran</small>
                                    <strong class="text-dark">${eventDate}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Jenis Pendaftaran</small>
                                    <strong class="text-dark text-capitalize">${registration.registration_type || 'audience'}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-warning"></i>
                                <div>
                                    <small class="text-muted d-block">Terakhir Diupdate</small>
                                    <strong class="text-dark">${eventDate}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Info Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-info-circle me-2 text-info"></i>Status & Langkah Selanjutnya
                        </h6>
                        <div class="mb-3">
                            <small class="text-muted">Status Saat Ini:</small><br>
                            <div class="mt-1">
                                <span class="fw-bold text-dark">Pendaftaran:</span> ${registration.registration_status}<br>
                                <span class="fw-bold text-dark">Pembayaran:</span> ${registration.payment_status}
                            </div>
                        </div>
                        <div class="border-top pt-3">
                            <small class="text-muted">Langkah Selanjutnya:</small><br>
                            <div class="mt-1">
                                ${registration.payment_status === 'pending' ? 
                                    '<span class="text-warning">• Lakukan pembayaran untuk konfirmasi</span><br><span class="text-muted">• Setelah bayar, status berubah ke "confirmed"</span>' : 
                                    registration.payment_status === 'success' ?
                                    '<span class="text-success">• Pendaftaran sudah dikonfirmasi</span><br><span class="text-muted">• Tunggu informasi lebih lanjut via email</span>' :
                                    '<span class="text-danger">• Hubungi admin untuk bantuan</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Show/hide pay button based on payment status
    if (registration.payment_status === 'pending') {
        payBtn.style.display = 'block';
        payBtn.onclick = () => {
            modal.hide();
            payRegistration(registration.id);
        };
    } else {
        payBtn.style.display = 'none';
    }
    
    modal.show();
}

// Pay for registration
async function payRegistration(registrationId) {
    try {
        // First, get registration details to calculate payment
        const response = await fetch('/dashboard/registrations', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            const registration = data.data.find(reg => reg.id == registrationId);
            if (registration) {
                displayPaymentModal(registration);
            } else {
                showAlert('Data pendaftaran tidak ditemukan', 'danger');
            }
        } else {
            showAlert('Gagal memuat data pembayaran', 'danger');
        }
    } catch (error) {
        console.error('Payment error:', error);
        showAlert('Terjadi kesalahan saat memproses pembayaran', 'danger');
    }
}

// Display payment modal
async function displayPaymentModal(registration) {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const content = document.getElementById('paymentContent');
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    
    // Get event details for payment calculation
    try {
        const eventsResponse = await fetch('/dashboard/events', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const eventsData = await eventsResponse.json();
        
        let eventDetails = null;
        if (eventsData.status === 'success') {
            eventDetails = eventsData.data.find(event => event.title === registration.event_name);
        }
        
        const registrationFee = eventDetails?.registration_fee ? parseInt(eventDetails.registration_fee) : 0;
        const formattedFee = registrationFee > 0 ? `Rp ${registrationFee.toLocaleString('id-ID')}` : 'Gratis';
        
        content.innerHTML = `
            <!-- Header Section -->
            <div class="d-flex align-items-start mb-4">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-sm ${registrationFee > 0 ? 'bg-primary' : 'bg-success'} rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-2 fw-bold text-dark">Pembayaran untuk: ${registration.event_name}</h5>
                    <p class="text-muted mb-0">Selesaikan pembayaran untuk mengkonfirmasi pendaftaran Anda</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="info-divider"></div>

            <div class="row">
                <!-- Payment Details -->
                <div class="col-lg-8">
                    <!-- Invoice Section -->
                    <div class="metadata-section mb-4">
                        <h6 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-receipt me-2 text-primary"></i>Detail Pembayaran
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-hashtag me-2 text-info"></i>
                                    <div>
                                        <small class="text-muted d-block">ID Pendaftaran</small>
                                        <code class="fw-bold">#${registration.id}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Jenis Pendaftaran</small>
                                        <strong class="text-dark text-capitalize">${registration.registration_type || 'audience'}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-divider"></div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Total Pembayaran</h6>
                                <small class="text-muted">Biaya pendaftaran event</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0 fw-bold ${registrationFee > 0 ? 'text-primary' : 'text-success'}">${formattedFee}</h4>
                            </div>
                        </div>
                    </div>
                    
                    ${registrationFee > 0 ? `
                    <!-- Payment Methods -->
                    <div class="metadata-section">
                        <h6 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-credit-card me-2 text-warning"></i>Pilih Metode Pembayaran
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="payment-method-card p-3 rounded cursor-pointer" data-method="bank_transfer" onclick="selectPaymentMethod('bank_transfer', this)">
                                    <input type="radio" name="paymentMethod" value="bank_transfer" style="display: none;" checked>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university me-3 text-primary fs-4"></i>
                                            <div>
                                                <strong class="text-dark">Transfer Bank</strong>
                                                <small class="text-muted d-block">Transfer ke rekening bank</small>
                                            </div>
                                        </div>
                                        <i class="fas fa-check-circle text-primary payment-check d-none"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-method-card p-3 rounded cursor-pointer" data-method="ewallet" onclick="selectPaymentMethod('ewallet', this)">
                                    <input type="radio" name="paymentMethod" value="ewallet" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-mobile-alt me-3 text-success fs-4"></i>
                                            <div>
                                                <strong class="text-dark">E-Wallet</strong>
                                                <small class="text-muted d-block">OVO, GoPay, DANA, ShopeePay</small>
                                            </div>
                                        </div>
                                        <i class="fas fa-check-circle text-success payment-check d-none"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-method-card p-3 rounded cursor-pointer" data-method="credit_card" onclick="selectPaymentMethod('credit_card', this)">
                                    <input type="radio" name="paymentMethod" value="credit_card" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card me-3 text-info fs-4"></i>
                                            <div>
                                                <strong class="text-dark">Kartu Kredit/Debit</strong>
                                                <small class="text-muted d-block">Visa, MasterCard, JCB</small>
                                            </div>
                                        </div>
                                        <i class="fas fa-check-circle text-info payment-check d-none"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : `
                    <div class="alert alert-success border-0 d-flex align-items-center">
                        <i class="fas fa-gift fa-2x me-3 text-success"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Event Gratis!</h6>
                            <p class="mb-0">Event ini tidak memerlukan pembayaran. Klik konfirmasi untuk menyelesaikan pendaftaran.</p>
                        </div>
                    </div>
                    `}
                </div>
                
                <!-- Security Info Sidebar -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-3">
                            <h6 class="mb-3 fw-bold text-dark">
                                <i class="fas fa-shield-alt me-2 text-success"></i>Pembayaran Aman
                            </h6>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <small>SSL Encryption</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <small>Powered by Midtrans</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <small>Data pribadi terlindungi</small>
                                </div>
                            </div>
                            <div class="border-top pt-3">
                                <small class="text-muted">Setelah Pembayaran:</small><br>
                                <div class="mt-1">
                                    <small class="text-muted">• Konfirmasi otomatis via email</small><br>
                                    <small class="text-muted">• Status berubah ke "confirmed"</small><br>
                                    <small class="text-muted">• QR Code untuk acara</small><br>
                                    <small class="text-muted">• E-Certificate setelah event</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Enable/disable confirm button based on payment requirement
        if (registrationFee > 0) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-credit-card me-1"></i>Proses Pembayaran';
            confirmBtn.onclick = () => processPayment(registration.id, registrationFee);
        } else {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Konfirmasi Pendaftaran Gratis';
            confirmBtn.onclick = () => confirmFreeRegistration(registration.id);
        }
        
    } catch (error) {
        console.error('Error loading payment details:', error);
        content.innerHTML = '<div class="alert alert-danger">Gagal memuat detail pembayaran</div>';
        confirmBtn.disabled = true;
    }
    
    modal.show();
    
    // Initialize payment methods after modal is shown
    setTimeout(() => {
        initializePaymentMethods();
    }, 100);
}

// Select payment method function
function selectPaymentMethod(method, element) {
    // Remove selected class from all payment cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
        card.querySelector('.payment-check').classList.add('d-none');
        card.querySelector('input[type="radio"]').checked = false;
    });
    
    // Add selected class to clicked card
    element.classList.add('selected');
    element.querySelector('.payment-check').classList.remove('d-none');
    element.querySelector('input[type="radio"]').checked = true;
    
    console.log('Payment method selected:', method);
}

// Initialize first payment method as selected
function initializePaymentMethods() {
    const firstCard = document.querySelector('.payment-method-card[data-method="bank_transfer"]');
    if (firstCard) {
        selectPaymentMethod('bank_transfer', firstCard);
    }
}

// Process payment
async function processPayment(registrationId, amount) {
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'bank_transfer';
    
    // Show loading state
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Memproses...';
    confirmBtn.disabled = true;
    
    try {
        // Simulate payment processing (replace with actual payment API)
        showAlert('Fitur pembayaran akan segera terintegrasi dengan gateway payment', 'info');
        
        // For demo purposes, simulate successful payment
        setTimeout(async () => {
            try {
                // Simulate payment success by updating registration status
                showAlert('Pembayaran berhasil diproses! Status pendaftaran akan diupdate.', 'success');
                
                // Close modal and refresh registrations
                const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                modal.hide();
                
                // Refresh registrations list
                loadMyRegistrations();
                
            } catch (error) {
                showAlert('Pembayaran berhasil tapi gagal update status. Hubungi admin.', 'warning');
            } finally {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        }, 2000);
        
    } catch (error) {
        console.error('Payment processing error:', error);
        showAlert('Terjadi kesalahan saat memproses pembayaran', 'danger');
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    }
}

// Confirm free registration
async function confirmFreeRegistration(registrationId) {
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    
    // Show loading state
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Mengonfirmasi...';
    confirmBtn.disabled = true;
    
    try {
        // For free events, just confirm the registration
        showAlert('Pendaftaran gratis dikonfirmasi!', 'success');
        
        // Close modal and refresh registrations
        const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
        modal.hide();
        
        // Refresh registrations list
        loadMyRegistrations();
        
    } catch (error) {
        console.error('Free registration confirmation error:', error);
        showAlert('Terjadi kesalahan saat konfirmasi pendaftaran', 'danger');
    } finally {
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    }
}

async function loadMyAbstracts() {
    try {
        const { response, data } = await apiRequest('/api/v1/abstracts');
        const container = document.getElementById('abstractsList');
        
        if (data.status === 'success' && data.data.length > 0) {
            let html = '';
            data.data.forEach(abstract => {
                const statusBadge = getReviewStatusBadge(abstract.review_status);
                
                html += `
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${abstract.title}</h6>
                                <p class="mb-1 text-muted">${abstract.category}</p>
                                <small class="text-muted">Submit: ${formatDate(abstract.created_at)}</small>
                            </div>
                            <div class="text-end">
                                ${statusBadge}
                                <div class="btn-group btn-group-sm mt-2">
                                    <button class="btn btn-outline-primary" onclick="viewAbstract(${abstract.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="downloadAbstract(${abstract.id})">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-muted text-center">Belum ada abstract yang disubmit</p>';
        }
    } catch (error) {
        console.error('Error loading abstracts:', error);
        document.getElementById('abstractsList').innerHTML = '<p class="text-danger text-center">Gagal memuat data abstract</p>';
    }
}

async function loadRecentActivities() {
    // Mock recent activities - in real implementation, this would come from API
    const activitiesContainer = document.getElementById('recentActivities');
    
    setTimeout(() => {
        const activities = [
            { icon: 'fas fa-user-plus', text: 'Akun berhasil dibuat', time: '2 jam yang lalu', type: 'success' },
            { icon: 'fas fa-sign-in-alt', text: 'Login ke sistem', time: 'Baru saja', type: 'info' }
        ];
        
        let html = '';
        activities.forEach(activity => {
            html += `
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-${activity.type} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="${activity.icon}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold">${activity.text}</div>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                </div>
            `;
        });
        
        if (html) {
            activitiesContainer.innerHTML = html;
        } else {
            activitiesContainer.innerHTML = '<p class="text-muted text-center">Belum ada aktivitas</p>';
        }
    }, 1000);
}

async function loadNotifications() {
    // Mock notifications - in real implementation, this would come from API
    const notificationsContainer = document.getElementById('notificationsList');
    
    setTimeout(() => {
        const notifications = [
            { title: 'Selamat Datang!', message: 'Terima kasih telah bergabung dengan SNIA Conference 2025', time: '1 jam yang lalu', type: 'info' }
        ];
        
        let html = '';
        notifications.forEach(notification => {
            html += `
                <div class="mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${notification.title}</h6>
                            <p class="mb-1 small">${notification.message}</p>
                            <small class="text-muted">${notification.time}</small>
                        </div>
                        <span class="badge bg-${notification.type}">Baru</span>
                    </div>
                </div>
            `;
        });
        
        if (html) {
            notificationsContainer.innerHTML = html;
        } else {
            notificationsContainer.innerHTML = '<p class="text-muted text-center">Tidak ada notifikasi</p>';
        }
    }, 1500);
}

// Helper functions
function getRoleDisplayName(role) {
    const roleNames = {
        'presenter': 'Presenter',
        'audience': 'Peserta',
        'reviewer': 'Reviewer',
        'admin': 'Administrator'
    };
    return roleNames[role] || role;
}

function showRoleContent(role) {
    document.getElementById('presenterContent').classList.add('d-none');
    document.getElementById('audienceContent').classList.add('d-none');
    
    if (role === 'presenter') {
        document.getElementById('presenterContent').classList.remove('d-none');
    } else if (role === 'audience') {
        document.getElementById('audienceContent').classList.remove('d-none');
    }
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Menunggu</span>',
        'approved': '<span class="badge bg-success">Disetujui</span>',
        'rejected': '<span class="badge bg-danger">Ditolak</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getPaymentBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Belum Bayar</span>',
        'paid': '<span class="badge bg-success">Lunas</span>',
        'failed': '<span class="badge bg-danger">Gagal</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getReviewStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Review</span>',
        'accepted': '<span class="badge bg-success">Diterima</span>',
        'accepted_with_revision': '<span class="badge bg-info">Revisi</span>',
        'rejected': '<span class="badge bg-danger">Ditolak</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Action functions - Updated to use real API endpoints

// Load available events for registration
async function loadRegistrationForm() {
    try {
        const response = await fetch('/dashboard/events', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        console.log('Events data received:', data);
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            let html = '<div class="row">';
            data.data.forEach(event => {
                const eventDate = event.event_date ? formatDate(event.event_date) : 'Tanggal belum ditentukan';
                const eventFee = event.registration_fee ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'Gratis';
                const eventTime = event.event_time ? ` - ${event.event_time}` : '';
                const isFree = !event.registration_fee || parseInt(event.registration_fee) === 0;
                
                html += `
                    <div class="col-lg-6 mb-4">
                        <div class="card card-event h-100">
                            <div class="card-body p-4">
                                <!-- Header Section -->
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm ${isFree ? 'bg-success' : 'bg-primary'} rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas ${isFree ? 'fa-gift' : 'fa-calendar-alt'} text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title mb-1 fw-bold text-dark">${event.title || 'Event'}</h6>
                                                <p class="card-text text-muted small mb-0 lh-sm text-truncate-2">${event.description || 'Tidak ada deskripsi'}</p>
                                            </div>
                                            ${isFree ? '<span class="badge bg-success ms-2 px-2 py-1">GRATIS</span>' : ''}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Divider -->
                                <div class="info-divider"></div>
                                
                                <!-- Details Section -->
                                <div class="metadata-section mb-3">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <strong class="text-dark">${eventDate}</strong>
                                                ${eventTime ? `<span class="text-muted ms-2">${eventTime}</span>` : ''}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                                ${event.format === 'online' ? '<span class="badge bg-info">Online</span>' : `<span class="text-dark">${event.location || 'Lokasi TBD'}</span>`}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag me-2 text-warning"></i>
                                                <strong class="${isFree ? 'text-success' : 'text-primary'} fw-bold">${eventFee}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <i class="fas fa-users me-2 text-info"></i>
                                                <span class="text-muted">${event.max_participants || 'Unlimited'} peserta</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="d-grid">
                                    <button class="btn ${isFree ? 'btn-success' : 'btn-primary'} fw-bold" onclick="registerForEvent(${event.id})">
                                        <i class="fas fa-plus me-2"></i>Daftar Event
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('availableEvents').innerHTML = html;
            showAlert('Event tersedia dimuat berhasil', 'success');
        } else {
            console.log('No events data or empty array:', data);
            document.getElementById('availableEvents').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum Ada Event Tersedia</h6>
                    <p class="small text-muted">Event baru akan segera dibuka. Pantau terus halaman ini!</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading events:', error);
        document.getElementById('availableEvents').innerHTML = '<p class="text-danger text-center">Gagal memuat event</p>';
        showAlert('Gagal memuat daftar event', 'danger');
    }
}

// Register for specific event
async function registerForEvent(eventId) {
    try {
        const formData = new FormData();
        formData.append('event_id', eventId);
        formData.append('registration_type', 'audience');
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('csrf_test_name', csrfToken.getAttribute('content'));
        }
        
        const response = await fetch('/dashboard/register-event', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert('Pendaftaran berhasil!', 'success');
            loadMyRegistrations(); // Refresh registrations list
        } else {
            showAlert(data.message || 'Pendaftaran gagal', 'danger');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showAlert('Terjadi kesalahan saat mendaftar', 'danger');
    }
}

// Load event schedule with multiple views
async function loadEventSchedule() {
    try {
        // Show the modal first
        const modal = new bootstrap.Modal(document.getElementById('eventScheduleModal'));
        modal.show();
        
        console.log('Loading event schedule...');
        
        // Load event schedule data
        const response = await fetch('/dashboard/event-schedule', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Event schedule data:', data);
        console.log('Data count:', data.count);
        console.log('Raw events:', data.data);
        
        if (data.status === 'success') {
            if (data.data && data.data.length > 0) {
                // Store events globally for different views
                window.scheduleEvents = data.data;
                
                console.log('Initializing views with', data.data.length, 'events');
                console.log('Sample event:', data.data[0]);
                
                // Initialize all views immediately
                try {
                    initializeCalendarView(data.data);
                    console.log('Calendar view initialized');
                } catch (e) {
                    console.error('Calendar view error:', e);
                }
                
                try {
                    initializeListView(data.data);
                    console.log('List view initialized');
                } catch (e) {
                    console.error('List view error:', e);
                }
                
                try {
                    initializeTimelineView(data.data);
                    console.log('Timeline view initialized');
                } catch (e) {
                    console.error('Timeline view error:', e);
                }
                
                // Set up event listeners for tabs
                setupScheduleViewListeners();
            } else {
                console.log('No events found, showing empty state');
                // Show empty state
                showEmptyEventState();
            }
        } else {
            console.error('API error:', data);
            showAlert('Gagal memuat jadwal acara: ' + (data.message || 'Unknown error'), 'danger');
        }
    } catch (error) {
        console.error('Error loading event schedule:', error);
        showAlert('Terjadi kesalahan saat memuat jadwal acara: ' + error.message, 'danger');
    }
}

// Show empty state when no events
function showEmptyEventState() {
    console.log('Showing empty state for all views');
    
    const emptyHTML = `
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum Ada Event Tersedia</h5>
            <p class="text-muted">Jadwal acara akan muncul di sini ketika sudah tersedia.</p>
        </div>
    `;
    
    const calendarEl = document.getElementById('event-calendar');
    const listEl = document.getElementById('event-list-container'); 
    const timelineEl = document.getElementById('event-timeline-container');
    
    if (calendarEl) {
        calendarEl.innerHTML = emptyHTML;
        console.log('Calendar empty state set');
    }
    
    if (listEl) {
        listEl.innerHTML = emptyHTML;
        console.log('List empty state set');
    }
    
    if (timelineEl) {
        timelineEl.innerHTML = emptyHTML;
        console.log('Timeline empty state set');
    }
}

// Initialize Calendar View
function initializeCalendarView(events) {
    console.log('Starting calendar view initialization with events:', events);
    
    // Check if required elements exist
    const calendarElement = document.getElementById('event-calendar');
    const monthYearElement = document.getElementById('current-month-year');
    
    if (!calendarElement) {
        console.error('Calendar element not found');
        return;
    }
    
    if (!monthYearElement) {
        console.error('Month-year element not found');
        return;
    }
    
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    function renderCalendar(month, year) {
        console.log('Rendering calendar for', month, year, 'with', events.length, 'events');
        
        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        monthYearElement.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        let calendarHTML = `
            <div class="calendar-grid">
                <div class="calendar-header">
                    <div class="calendar-day-header">Min</div>
                    <div class="calendar-day-header">Sen</div>
                    <div class="calendar-day-header">Sel</div>
                    <div class="calendar-day-header">Rab</div>
                    <div class="calendar-day-header">Kam</div>
                    <div class="calendar-day-header">Jum</div>
                    <div class="calendar-day-header">Sab</div>
                </div>
                <div class="calendar-body">
        `;
        
        // Add empty cells for days before the month starts
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += '<div class="calendar-day empty"></div>';
        }
        
        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayEvents = events.filter(event => event.date === dateStr);
            const isToday = day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear();
            
            calendarHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateStr}">
                    <div class="day-number">${day}</div>
            `;
            
            if (dayEvents.length > 0) {
                console.log(`Found ${dayEvents.length} events for ${dateStr}`);
                dayEvents.forEach(event => {
                    const eventClass = event.is_registered ? 'event-registered' : 'event-available';
                    calendarHTML += `
                        <div class="calendar-event ${eventClass}" onclick="showEventDetail('${event.id}')" title="${event.title}">
                            <div class="event-time">${event.time}</div>
                            <div class="event-title">${event.title.length > 20 ? event.title.substring(0, 20) + '...' : event.title}</div>
                        </div>
                    `;
                });
            }
            
            calendarHTML += '</div>';
        }
        
        calendarHTML += '</div></div>';
        calendarElement.innerHTML = calendarHTML;
        console.log('Calendar rendered successfully');
    }
    
    // Initial render
    renderCalendar(currentMonth, currentYear);
    
    // Navigation event listeners (only add if elements exist)
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });
    }
    
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            const today = new Date();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            renderCalendar(currentMonth, currentYear);
        });
    }
}

// Initialize List View
function initializeListView(events) {
    console.log('Initializing list view with events:', events);
    
    const listContainer = document.getElementById('event-list-container');
    const searchInput = document.getElementById('event-search');
    
    if (!listContainer) {
        console.error('List container element not found');
        return;
    }
    
    let filteredEvents = [...events];
    
    function renderEventList(eventsToShow) {
        console.log('Rendering event list with', eventsToShow.length, 'events');
        
        if (eventsToShow.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Tidak ada event ditemukan</h6>
                </div>
            `;
            return;
        }
        
        let listHTML = '<div class="event-list">';
        
        eventsToShow.forEach(event => {
            const eventDate = new Date(event.date);
            const formattedDate = eventDate.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            const statusBadge = event.is_registered ? 
                '<span class="badge bg-success">Terdaftar</span>' : 
                '<span class="badge bg-primary">Tersedia</span>';
            
            const formatBadge = event.format === 'online' ? 
                '<span class="badge bg-info">Online</span>' : 
                '<span class="badge bg-warning">Offline</span>';
            
            listHTML += `
                <div class="event-item card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="event-icon me-3">
                                        <i class="fas fa-calendar-day fa-2x text-primary"></i>
                                    </div>
                                    <div class="event-info">
                                        <h6 class="event-title mb-1">${event.title}</h6>
                                        <div class="event-meta text-muted mb-2">
                                            <i class="fas fa-clock me-1"></i>${formattedDate}, ${event.time}
                                            ${event.location ? `<br><i class="fas fa-map-marker-alt me-1"></i>${event.location}` : ''}
                                        </div>
                                        <p class="event-description mb-2">${event.description.length > 150 ? event.description.substring(0, 150) + '...' : event.description}</p>
                                        <div class="event-badges">
                                            ${statusBadge}
                                            ${formatBadge}
                                            <span class="badge bg-secondary">Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="event-actions">
                                    <button class="btn btn-outline-primary btn-sm mb-2" onclick="showEventDetail('${event.id}')">
                                        <i class="fas fa-info-circle me-1"></i>Detail
                                    </button>
                                    ${!event.is_registered ? 
                                        `<button class="btn btn-primary btn-sm" onclick="registerForEventFromSchedule('${event.id}')">
                                            <i class="fas fa-user-plus me-1"></i>Daftar
                                        </button>` : 
                                        `<button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Terdaftar
                                        </button>`
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        listHTML += '</div>';
        listContainer.innerHTML = listHTML;
        console.log('List view rendered successfully');
    }
    
    // Initial render
    renderEventList(filteredEvents);
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            filteredEvents = events.filter(event => 
                event.title.toLowerCase().includes(searchTerm) || 
                event.description.toLowerCase().includes(searchTerm)
            );
            renderEventList(filteredEvents);
        });
    }
}

// Initialize Timeline View
function initializeTimelineView(events) {
    console.log('Initializing timeline view with events:', events);
    
    const timelineContainer = document.getElementById('event-timeline-container');
    const filterSelect = document.getElementById('timeline-filter');
    
    if (!timelineContainer) {
        console.error('Timeline container element not found');
        return;
    }
    
    let filteredEvents = [...events];
    
    function renderTimeline(eventsToShow) {
        console.log('Rendering timeline with', eventsToShow.length, 'events');
        
        if (eventsToShow.length === 0) {
            timelineContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-stream fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Tidak ada event ditemukan</h6>
                </div>
            `;
            return;
        }
        
        // Sort events by date and time
        const sortedEvents = eventsToShow.sort((a, b) => {
            const dateA = new Date(a.date + 'T' + a.time);
            const dateB = new Date(b.date + 'T' + b.time);
            return dateA - dateB;
        });
        
        let timelineHTML = '<div class="timeline">';
        
        sortedEvents.forEach((event, index) => {
            const eventDate = new Date(event.date);
            const isLast = index === sortedEvents.length - 1;
            const isPast = eventDate < new Date();
            const timelineClass = isPast ? 'timeline-past' : 'timeline-future';
            
            timelineHTML += `
                <div class="timeline-item ${timelineClass}">
                    <div class="timeline-marker ${event.is_registered ? 'registered' : 'available'}">
                        <i class="fas ${event.is_registered ? 'fa-check' : 'fa-calendar'}"></i>
                    </div>
                    ${!isLast ? '<div class="timeline-line"></div>' : ''}
                    <div class="timeline-content">
                        <div class="timeline-card card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="timeline-title">${event.title}</h6>
                                        <div class="timeline-meta text-muted mb-2">
                                            <i class="fas fa-calendar me-1"></i>${eventDate.toLocaleDateString('id-ID')}
                                            <i class="fas fa-clock ms-3 me-1"></i>${event.time}
                                            ${event.format === 'online' ? 
                                                '<i class="fas fa-video ms-3 me-1"></i>Online' : 
                                                '<i class="fas fa-map-marker-alt ms-3 me-1"></i>' + (event.location || 'Offline')
                                            }
                                        </div>
                                        <p class="timeline-description">${event.description.length > 200 ? event.description.substring(0, 200) + '...' : event.description}</p>
                                        <div class="timeline-badges">
                                            ${event.is_registered ? 
                                                '<span class="badge bg-success">Terdaftar</span>' : 
                                                '<span class="badge bg-primary">Tersedia</span>'
                                            }
                                            <span class="badge bg-info">${event.format.charAt(0).toUpperCase() + event.format.slice(1)}</span>
                                            <span class="badge bg-secondary">Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="timeline-actions">
                                            <button class="btn btn-outline-primary btn-sm mb-2" onclick="showEventDetail('${event.id}')">
                                                <i class="fas fa-info-circle me-1"></i>Detail
                                            </button>
                                            ${!event.is_registered && !isPast ? 
                                                `<button class="btn btn-primary btn-sm" onclick="registerForEventFromSchedule('${event.id}')">
                                                    <i class="fas fa-user-plus me-1"></i>Daftar
                                                </button>` : 
                                                event.is_registered ? 
                                                    `<button class="btn btn-success btn-sm" disabled>
                                                        <i class="fas fa-check me-1"></i>Terdaftar
                                                    </button>` :
                                                    `<button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-clock me-1"></i>Berakhir
                                                    </button>`
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        timelineHTML += '</div>';
        timelineContainer.innerHTML = timelineHTML;
        console.log('Timeline rendered successfully');
    }
    
    // Initial render
    renderTimeline(filteredEvents);
    
    // Filter functionality
    if (filterSelect) {
        filterSelect.addEventListener('change', (e) => {
            const filterValue = e.target.value;
            
            switch (filterValue) {
                case 'registered':
                    filteredEvents = events.filter(event => event.is_registered);
                    break;
                case 'available':
                    filteredEvents = events.filter(event => !event.is_registered);
                    break;
                default:
                    filteredEvents = [...events];
            }
            
            renderTimeline(filteredEvents);
        });
    }
}

// Setup event listeners for view switching
function setupScheduleViewListeners() {
    document.querySelectorAll('#scheduleViewTabs button').forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            
            // Re-render the active view when tab is shown
            if (target === '#calendar-view' && window.scheduleEvents) {
                // Calendar view is already initialized
            } else if (target === '#list-view' && window.scheduleEvents) {
                // List view is already initialized
            } else if (target === '#timeline-view' && window.scheduleEvents) {
                // Timeline view is already initialized
            }
        });
    });
}

// Show event detail in modal or expand
function showEventDetail(eventId) {
    const event = window.scheduleEvents.find(e => e.id == eventId);
    if (!event) return;
    
    const eventDate = new Date(event.date);
    const formattedDate = eventDate.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const detailHTML = `
        <div class="event-detail-content">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3">${event.title}</h5>
                    <div class="event-detail-meta mb-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong><i class="fas fa-calendar me-2"></i>Tanggal:</strong><br>
                                ${formattedDate}
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fas fa-clock me-2"></i>Waktu:</strong><br>
                                ${event.time}
                            </div>
                            <div class="col-sm-6 mt-2">
                                <strong><i class="fas fa-${event.format === 'online' ? 'video' : 'map-marker-alt'} me-2"></i>Lokasi:</strong><br>
                                ${event.format === 'online' ? 'Online' : event.location || 'TBA'}
                            </div>
                            <div class="col-sm-6 mt-2">
                                <strong><i class="fas fa-money-bill me-2"></i>Biaya:</strong><br>
                                Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}
                            </div>
                        </div>
                    </div>
                    <div class="event-description">
                        <strong>Deskripsi:</strong>
                        <p>${event.description}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                ${event.is_registered ? 
                                    '<i class="fas fa-check-circle fa-3x text-success mb-2"></i><br><strong class="text-success">Sudah Terdaftar</strong>' :
                                    '<i class="fas fa-calendar-plus fa-3x text-primary mb-2"></i><br><strong class="text-primary">Tersedia untuk Registrasi</strong>'
                                }
                            </div>
                            ${!event.is_registered ? 
                                `<button class="btn btn-primary" onclick="registerForEventFromSchedule('${event.id}')">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                </button>` :
                                `<div class="alert alert-success">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Status: ${event.registration_status || 'Terdaftar'}<br>
                                    Pembayaran: ${event.payment_status || 'Pending'}
                                </div>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // You can show this in a modal or expand in place
    showAlert(detailHTML, 'info', 10000); // Show for 10 seconds
}

// Register for event from schedule view
async function registerForEventFromSchedule(eventId) {
    try {
        const response = await fetch('/dashboard/register-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `event_id=${eventId}&registration_type=audience`
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert('Berhasil mendaftar event!', 'success');
            // Refresh the schedule data
            loadEventSchedule();
        } else {
            showAlert('Gagal mendaftar: ' + (data.message || 'Unknown error'), 'danger');
        }
    } catch (error) {
        console.error('Error registering for event:', error);
        showAlert('Terjadi kesalahan saat mendaftar', 'danger');
    }
}

// Show event registration modal with available events
async function showEventRegistrationModal() {
    try {
        // Load available events
        const response = await fetch('/dashboard/events', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            // Show modal with first available event (or let user choose)
            displayEventRegistrationModal(data.data);
        } else {
            showAlert('Tidak ada event tersedia saat ini', 'warning');
        }
    } catch (error) {
        console.error('Error loading events for modal:', error);
        showAlert('Gagal memuat informasi event', 'danger');
    }
}

// Display event registration modal content
function displayEventRegistrationModal(events) {
    const modal = new bootstrap.Modal(document.getElementById('eventRegistrationModal'));
    const content = document.getElementById('eventRegistrationContent');
    const confirmBtn = document.getElementById('confirmEventRegistration');
    
    if (events.length === 1) {
        // Single event - show details directly
        const event = events[0];
        displaySingleEventModal(event, content, confirmBtn);
    } else {
        // Multiple events - let user choose
        displayEventSelectionModal(events, content, confirmBtn);
    }
    
    modal.show();
}

// Display single event confirmation
function displaySingleEventModal(event, content, confirmBtn) {
    const eventDate = event.event_date ? formatDate(event.event_date) : 'Tanggal belum ditentukan';
    const eventFee = event.registration_fee ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'Gratis';
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <h5 class="mb-3">${event.title || 'Event'}</h5>
                <p class="text-muted mb-3">${event.description || 'Tidak ada deskripsi'}</p>
                
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted">Tanggal & Waktu</small><br>
                        <i class="fas fa-calendar me-1"></i>
                        ${eventDate}
                        ${event.event_time ? ` - ${event.event_time}` : ''}
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted">Lokasi</small><br>
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ${event.format === 'online' ? 'Online' : (event.location || 'Lokasi TBD')}
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted">Biaya Pendaftaran</small><br>
                        <i class="fas fa-tag me-1"></i>
                        <strong>${eventFee}</strong>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted">Kapasitas</small><br>
                        <i class="fas fa-users me-1"></i>
                        ${event.max_participants || 'Unlimited'} peserta
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-1"></i>Informasi Pendaftaran</h6>
                    <small>
                        • Pendaftaran akan berstatus "pending"<br>
                        • Lakukan pembayaran untuk konfirmasi<br>
                        • Sertifikat akan diberikan setelah event
                    </small>
                </div>
            </div>
        </div>
    `;
    
    confirmBtn.disabled = false;
    confirmBtn.onclick = () => confirmSingleEventRegistration(event.id);
}

// Display event selection modal
function displayEventSelectionModal(events, content, confirmBtn) {
    let html = '<h6 class="mb-3">Pilih Event yang Ingin Didaftarkan:</h6>';
    
    events.forEach((event, index) => {
        const eventDate = event.event_date ? formatDate(event.event_date) : 'Tanggal belum ditentukan';
        const eventFee = event.registration_fee ? `Rp ${parseInt(event.registration_fee).toLocaleString('id-ID')}` : 'Gratis';
        
        html += `
            <div class="border rounded p-3 mb-3 event-selection-item" data-event-id="${event.id}">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="selectedEvent" id="event${event.id}" value="${event.id}">
                    <label class="form-check-label" for="event${event.id}">
                        <div class="row">
                            <div class="col-8">
                                <h6 class="mb-1">${event.title || 'Event'}</h6>
                                <p class="small text-muted mb-1">${event.description ? event.description.substring(0, 100) + '...' : 'Tidak ada deskripsi'}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>${eventDate} •
                                    <i class="fas fa-map-marker-alt me-1"></i>${event.format === 'online' ? 'Online' : (event.location || 'TBD')}
                                </small>
                            </div>
                            <div class="col-4 text-end">
                                <h6 class="text-primary mb-0">${eventFee}</h6>
                                <small class="text-muted">${event.max_participants || 'Unlimited'} peserta</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        `;
    });
    
    content.innerHTML = html;
    
    // Add event listeners for radio buttons
    const radios = content.querySelectorAll('input[name="selectedEvent"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            confirmBtn.disabled = false;
            confirmBtn.onclick = () => confirmSingleEventRegistration(this.value);
        });
    });
    
    confirmBtn.disabled = true;
}

// Confirm single event registration
async function confirmSingleEventRegistration(eventId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('eventRegistrationModal'));
    const confirmBtn = document.getElementById('confirmEventRegistration');
    
    // Show loading state
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Mendaftar...';
    confirmBtn.disabled = true;
    
    try {
        await registerForEvent(eventId);
        modal.hide();
    } catch (error) {
        console.error('Registration confirmation error:', error);
        showAlert('Terjadi kesalahan saat mendaftar', 'danger');
    } finally {
        // Restore button state
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    }
}

// Load my QR codes - Placeholder for now  
async function loadMyQRCodes() {
    showAlert('Fitur QR Code akan segera tersedia', 'info');
}

// Load my certificates - Placeholder for now
async function loadMyCertificates() {
    showAlert('Fitur sertifikat akan segera tersedia', 'info');
}

// Download certificate - Placeholder for now
async function downloadCertificate(certId) {
    showAlert('Fitur download sertifikat akan segera tersedia', 'info');
}

// Load payment history - Placeholder for now
async function loadPaymentHistory() {
    showAlert('Fitur riwayat pembayaran akan segera tersedia', 'info');
}

// Load voucher section - Placeholder for now
async function loadVoucherSection() {
    showAlert('Fitur voucher akan segera tersedia', 'info');
}

// Presenter functions (unchanged)
function showAbstractForm() {
    showAlert('Fitur submit abstract akan segera tersedia', 'info');
}

function viewMyAbstracts() {
    showAlert('Fitur kelola abstract akan segera tersedia', 'info');
}

// Edit profile functionality
async function editProfile() {
    try {
        // Get current user data
        const userData = getCurrentUserData();
        if (!userData) {
            showAlert('Data user tidak ditemukan', 'danger');
            return;
        }
        
        // Populate form with current data
        populateEditProfileForm(userData);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        modal.show();
        
    } catch (error) {
        console.error('Error opening edit profile:', error);
        showAlert('Terjadi kesalahan saat membuka form edit profil', 'danger');
    }
}

// Get current user data from session or local storage
function getCurrentUserData() {
    // In a real app, this would come from session or API call
    // For now, get from session data that's already loaded
    return {
        first_name: document.getElementById('profileName')?.textContent?.split(' ')[0] || '',
        last_name: document.getElementById('profileName')?.textContent?.split(' ').slice(1).join(' ') || '',
        email: document.getElementById('profileEmail')?.textContent || '',
        institution: document.getElementById('profileInstitution')?.textContent || '',
        phone: '' // This should come from API in real implementation
    };
}

// Populate edit profile form with current data
function populateEditProfileForm(userData) {
    document.getElementById('editFirstName').value = userData.first_name;
    document.getElementById('editLastName').value = userData.last_name;
    document.getElementById('editEmail').value = userData.email;
    document.getElementById('editInstitution').value = userData.institution;
    document.getElementById('editPhone').value = userData.phone;
    
    // Clear password fields
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}

// Trigger photo upload
function triggerPhotoUpload() {
    document.getElementById('profilePhotoInput').click();
}

// Toggle password visibility
function togglePasswordVisibility(fieldId) {
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

// Save profile changes
async function saveProfile() {
    const saveBtn = document.getElementById('saveProfileBtn');
    const form = document.getElementById('editProfileForm');
    
    try {
        // Validate form
        if (!validateProfileForm()) {
            return;
        }
        
        // Show loading
        showLoading(saveBtn);
        
        // Prepare form data
        const formData = new FormData();
        formData.append('first_name', document.getElementById('editFirstName').value.trim());
        formData.append('last_name', document.getElementById('editLastName').value.trim());
        formData.append('institution', document.getElementById('editInstitution').value.trim());
        formData.append('phone', document.getElementById('editPhone').value.trim());
        
        // Add photo if uploaded
        const photoInput = document.getElementById('profilePhotoInput');
        if (photoInput.files[0]) {
            formData.append('profile_photo', photoInput.files[0]);
        }
        
        // Add password fields if filled
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        if (currentPassword && newPassword) {
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
        }
        
        // Send update request
        const response = await fetch('/profile/update', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showAlert('Profil berhasil diperbarui!', 'success');
            
            // Update display
            updateProfileDisplay(data.user);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.hide();
            
            // Refresh user info in dashboard
            loadUserInfo();
            
        } else {
            showAlert(data.message || 'Gagal memperbarui profil', 'danger');
        }
        
    } catch (error) {
        console.error('Error saving profile:', error);
        showAlert('Terjadi kesalahan saat menyimpan profil', 'danger');
    } finally {
        hideLoading(saveBtn);
    }
}

// Validate profile form
function validateProfileForm() {
    const firstName = document.getElementById('editFirstName').value.trim();
    const lastName = document.getElementById('editLastName').value.trim();
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Check required fields
    if (!firstName) {
        showAlert('Nama depan wajib diisi', 'warning');
        document.getElementById('editFirstName').focus();
        return false;
    }
    
    if (!lastName) {
        showAlert('Nama belakang wajib diisi', 'warning');
        document.getElementById('editLastName').focus();
        return false;
    }
    
    // Validate password change if any field is filled
    if (currentPassword || newPassword || confirmPassword) {
        if (!currentPassword) {
            showAlert('Password saat ini wajib diisi untuk mengubah password', 'warning');
            document.getElementById('currentPassword').focus();
            return false;
        }
        
        if (!newPassword) {
            showAlert('Password baru wajib diisi', 'warning');
            document.getElementById('newPassword').focus();
            return false;
        }
        
        if (newPassword.length < 6) {
            showAlert('Password baru minimal 6 karakter', 'warning');
            document.getElementById('newPassword').focus();
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            showAlert('Konfirmasi password tidak sama', 'warning');
            document.getElementById('confirmPassword').focus();
            return false;
        }
    }
    
    return true;
}

// Update profile display after successful save
function updateProfileDisplay(userData) {
    const profileName = document.getElementById('profileName');
    const profileEmail = document.getElementById('profileEmail');
    const profileInstitution = document.getElementById('profileInstitution');
    const userName = document.getElementById('userName');
    
    if (profileName) {
        profileName.textContent = `${userData.first_name} ${userData.last_name}`;
    }
    
    if (profileEmail) {
        profileEmail.textContent = userData.email;
    }
    
    if (profileInstitution) {
        profileInstitution.textContent = userData.institution || 'Tidak diisi';
    }
    
    if (userName) {
        userName.textContent = `${userData.first_name} ${userData.last_name}`;
    }
    
    // Update profile photo if provided
    if (userData.profile_photo_url) {
        const profilePhotos = document.querySelectorAll('.bg-primary.rounded-circle');
        profilePhotos.forEach(photo => {
            photo.innerHTML = `<img src="${userData.profile_photo_url}" alt="Profile" class="rounded-circle w-100 h-100" style="object-fit: cover;">`;
        });
    }
}

function viewAbstract(id) {
    showAlert(`Melihat abstract ID: ${id}`, 'info');
}

function downloadAbstract(id) {
    showAlert(`Download abstract ID: ${id}`, 'info');
}

function confirmLogout() {
    // Clear localStorage
    localStorage.removeItem('snia_token');
    localStorage.removeItem('snia_user');
    
    // Call server logout to destroy session
    fetch('/logout', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(() => {
        // Redirect to login page
        window.location.href = '/login';
    }).catch((error) => {
        console.error('Logout error:', error);
        // Still redirect even if logout fails
        window.location.href = '/login';
    });
}

// Add logout functionality to navbar
document.addEventListener('DOMContentLoaded', function() {
    const logoutLinks = document.querySelectorAll('a[href="/logout"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
            modal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>