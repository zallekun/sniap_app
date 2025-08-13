<?= $this->extend('layouts/main') ?>

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
        <!-- Left Column - Actions & Tasks -->
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
                                    <button class="btn btn-outline-primary" onclick="loadRegistrationForm()">
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
            let html = '';
            data.data.forEach(registration => {
                const statusBadge = getStatusBadge(registration.registration_status);
                const paymentBadge = getPaymentBadge(registration.payment_status);
                
                html += `
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${registration.event_name || 'Event'}</h6>
                                <p class="mb-1 small">${registration.registration_type || 'audience'}</p>
                                <small class="text-muted">Terdaftar: ${formatDate(registration.created_at)}</small>
                            </div>
                            <div class="text-end">
                                ${statusBadge}
                                ${paymentBadge}
                                <div class="mt-2">
                                    <button class="btn btn-outline-primary btn-sm me-1" onclick="viewRegistration(${registration.id})">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    ${registration.payment_status === 'pending' ? 
                                        `<button class="btn btn-outline-success btn-sm" onclick="payRegistration(${registration.id})">
                                            <i class="fas fa-credit-card"></i> Bayar
                                        </button>` : ''
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
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
        const { response, data } = await apiRequest(`/api/v1/registrations/${registrationId}`);
        
        if (data.status === 'success') {
            showAlert('Detail pendaftaran berhasil dimuat', 'success');
            console.log('Registration details:', data.data);
        } else {
            showAlert('Gagal memuat detail pendaftaran', 'danger');
        }
    } catch (error) {
        console.error('Error loading registration details:', error);
        showAlert('Terjadi kesalahan saat memuat detail', 'danger');
    }
}

// Pay for registration
async function payRegistration(registrationId) {
    try {
        const { response, data } = await apiRequest('/api/v1/payments', {
            method: 'POST',
            body: JSON.stringify({
                registration_id: registrationId,
                payment_method: 'midtrans',
                amount: 500000 // This should come from registration data
            })
        });
        
        if (data.status === 'success') {
            showAlert('Pembayaran berhasil dibuat', 'success');
            loadRegistrations(); // Refresh the list
        } else {
            showAlert(data.message || 'Gagal membuat pembayaran', 'danger');
        }
    } catch (error) {
        console.error('Payment error:', error);
        showAlert('Terjadi kesalahan saat memproses pembayaran', 'danger');
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
        
        if (data.status === 'success' && data.data) {
            let html = '<div class="row">';
            data.data.forEach(event => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">${event.title || 'Event'}</h6>
                                <p class="card-text small">${event.description || 'Tidak ada deskripsi'}</p>
                                <p class="small text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    ${formatDate(event.event_date)}
                                </p>
                                <button class="btn btn-primary btn-sm" onclick="registerForEvent(${event.id})">
                                    <i class="fas fa-plus me-1"></i>Daftar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            document.getElementById('availableEvents').innerHTML = html;
            showAlert('Event tersedia dimuat berhasil', 'success');
        } else {
            document.getElementById('availableEvents').innerHTML = '<p class="text-muted text-center">Tidak ada event tersedia</p>';
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