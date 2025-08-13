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
                                    <button class="btn btn-outline-primary" onclick="showRegistrationForm()">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Daftar sebagai Peserta
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-success" onclick="viewSchedule()">
                                        <i class="fas fa-calendar me-2"></i>
                                        Jadwal Acara
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button class="btn btn-outline-info" onclick="viewMyQRCodes()">
                                        <i class="fas fa-qrcode me-2"></i>
                                        QR Code Saya
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
    // Check authentication
    const token = localStorage.getItem('snia_token');
    const user = JSON.parse(localStorage.getItem('snia_user') || '{}');

    if (!token) {
        window.location.href = '/login';
        return;
    }

    // Load dashboard data
    loadUserProfile();
    loadDashboardStats();
    loadRegistrations();
    loadRecentActivities();
    loadNotifications();
});

async function loadUserProfile() {
    try {
        const { response, data } = await apiRequest('/api/v1/auth/profile');
        
        if (data.status === 'success') {
            const user = data.data;
            
            // Update profile info
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
        } else {
            // Token invalid, redirect to login
            localStorage.removeItem('snia_token');
            localStorage.removeItem('snia_user');
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Error loading profile:', error);
        showAlert('Gagal memuat profil pengguna', 'danger');
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
        const { response, data } = await apiRequest('/api/v1/registrations');
        const container = document.getElementById('registrationsList');
        
        if (data.status === 'success' && data.data.length > 0) {
            let html = '';
            data.data.forEach(registration => {
                const statusBadge = getStatusBadge(registration.registration_status);
                const paymentBadge = getPaymentBadge(registration.payment_status);
                
                html += `
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${registration.registration_type || 'Peserta'}</h6>
                                <small class="text-muted">Terdaftar: ${formatDate(registration.created_at)}</small>
                            </div>
                            <div class="text-end">
                                ${statusBadge}
                                ${paymentBadge}
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
        document.getElementById('registrationsList').innerHTML = '<p class="text-danger text-center">Gagal memuat data</p>';
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

// Action functions
function showRegistrationForm() {
    showAlert('Fitur pendaftaran akan segera tersedia', 'info');
}

function showAbstractForm() {
    showAlert('Fitur submit abstract akan segera tersedia', 'info');
}

function viewMyAbstracts() {
    showAlert('Fitur kelola abstract akan segera tersedia', 'info');
}

function viewCertificates() {
    showAlert('Fitur sertifikat akan segera tersedia', 'info');
}

function viewSchedule() {
    showAlert('Fitur jadwal acara akan segera tersedia', 'info');
}

function viewMyQRCodes() {
    showAlert('Fitur QR Code akan segera tersedia', 'info');
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
    localStorage.removeItem('snia_token');
    localStorage.removeItem('snia_user');
    window.location.href = '/login';
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