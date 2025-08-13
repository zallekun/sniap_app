<?= $this->extend('layouts/main') ?>

<?= $this->section('head') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1rem;
}

.card-hover {
    transition: all 0.3s ease;
    border: 1px solid #e0e6ed !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    border-color: #3b82f6 !important;
}

.border-4 {
    border-width: 4px !important;
}

.card-registration {
    border: 2px solid #f1f5f9;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-registration:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #e2e8f0;
}

.card-event {
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    border-color: #3b82f6;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.5rem;
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.status-indicator {
    position: relative;
}

.status-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--bs-success);
    border: 2px solid white;
}

.status-indicator.pending::before {
    background: var(--bs-warning);
}

.status-indicator.failed::before {
    background: var(--bs-danger);
}

.info-divider {
    border-top: 1px solid #e2e8f0;
    margin: 1rem 0 0.75rem 0;
    padding-top: 0.75rem;
}

.metadata-section {
    background-color: #f8fafc;
    border-radius: 0.5rem;
    padding: 0.75rem;
    margin-top: 0.5rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
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
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Panel Peserta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary" onclick="showEventRegistrationModal()">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Daftar Event
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-success" onclick="loadEventSchedule()">
                                        <i class="fas fa-calendar me-2"></i>
                                        Jadwal Acara
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-info" onclick="loadMyQRCodes()">
                                        <i class="fas fa-qrcode me-2"></i>
                                        QR Code Saya
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-warning" onclick="loadMyCertificates()">
                                        <i class="fas fa-certificate me-2"></i>
                                        Sertifikat Saya
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-secondary" onclick="loadPaymentHistory()">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Riwayat Pembayaran
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-dark" onclick="loadVoucherSection()">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Voucher Saya
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Registration Section -->
                <div class="card mb-4">
                    <div class="card-header">
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
                        <button class="btn btn-outline-primary btn-sm" onclick="editProfile()">
                            <i class="fas fa-edit me-1"></i>Edit Profil
                        </button>
                    </div>
                </div>
            </div>

            <!-- My Registrations -->
            <div class="card mb-4">
                <div class="card-header">
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
                    <div class="col-12 mb-4">
                        <div class="card card-registration border-start border-4 ${registration.payment_status === 'success' ? 'border-success' : registration.payment_status === 'pending' ? 'border-warning' : 'border-secondary'}">
                            <div class="card-body p-4">
                                <!-- Header Section -->
                                <div class="row align-items-start mb-3">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm ${registration.payment_status === 'success' ? 'bg-success' : registration.payment_status === 'pending' ? 'bg-warning' : 'bg-secondary'} rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-calendar-check text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2 text-dark fw-bold">${registration.event_name || 'Event'}</h6>
                                                <p class="text-muted mb-0 small lh-sm">${(registration.event_description || 'Tidak ada deskripsi').substring(0, 120)}${(registration.event_description || '').length > 120 ? '...' : ''}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="mb-2">
                                            ${statusBadge}
                                            ${paymentBadge}
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="info-divider"></div>

                                <!-- Info Section -->
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="metadata-section">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <small class="text-muted d-flex align-items-center">
                                                        <i class="fas fa-user me-2 text-primary"></i>
                                                        <span>${registration.registration_type || 'audience'}</span>
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-flex align-items-center">
                                                        <i class="fas fa-hashtag me-2 text-info"></i>
                                                        <span>ID: ${registration.id}</span>
                                                    </small>
                                                </div>
                                                <div class="col-12">
                                                    <small class="text-muted d-flex align-items-center">
                                                        <i class="fas fa-calendar me-2 text-success"></i>
                                                        <span>Terdaftar: ${eventDate}</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="d-grid gap-2 d-md-block">
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewRegistration(${registration.id})" title="Lihat Detail">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </button>
                                            ${registration.payment_status === 'pending' ? 
                                                `<button class="btn btn-success btn-sm ms-md-2" onclick="payRegistration(${registration.id})" title="Bayar Sekarang">
                                                    <i class="fas fa-credit-card me-1"></i>Bayar
                                                </button>` : 
                                                registration.payment_status === 'success' ?
                                                `<button class="btn btn-outline-info btn-sm ms-md-2" onclick="downloadTicket(${registration.id})" title="Download Tiket" disabled>
                                                    <i class="fas fa-download me-1"></i>Tiket
                                                </button>` : ''
                                            }
                                        </div>
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
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="form-check p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="bank_transfer" value="bank_transfer" checked>
                                    <label class="form-check-label w-100" for="bank_transfer">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-university me-3 text-primary"></i>
                                            <div>
                                                <strong>Transfer Bank</strong>
                                                <small class="text-muted d-block">Transfer ke rekening bank</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet" value="ewallet">
                                    <label class="form-check-label w-100" for="ewallet">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-mobile-alt me-3 text-success"></i>
                                            <div>
                                                <strong>E-Wallet</strong>
                                                <small class="text-muted d-block">OVO, GoPay, DANA, ShopeePay</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="credit_card" value="credit_card">
                                    <label class="form-check-label w-100" for="credit_card">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card me-3 text-info"></i>
                                            <div>
                                                <strong>Kartu Kredit/Debit</strong>
                                                <small class="text-muted d-block">Visa, MasterCard, JCB</small>
                                            </div>
                                        </div>
                                    </label>
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

// Load event schedule - Placeholder for now
async function loadEventSchedule() {
    showAlert('Fitur jadwal acara akan segera tersedia', 'info');
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

function editProfile() {
    showAlert('Fitur edit profil akan segera tersedia', 'info');
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