<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>My Certificates<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">My Certificates</h2>
                        <p class="mb-0">View and download your certificates from attended events</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-certificate" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-award" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="totalCertificates">0</h4>
                <small class="text-muted">Total Certificates</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-user-graduate" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="participantCertificates">0</h4>
                <small class="text-muted">Participant</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="presenterCertificates">0</h4>
                <small class="text-muted">Presenter</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="thisYearCertificates">0</h4>
                <small class="text-muted">This Year</small>
            </div>
        </div>
    </div>
</div>

<!-- Certificates List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt me-2"></i>Certificate Collection
                </h5>
                <button class="btn btn-outline-primary btn-sm" onclick="refreshCertificates()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <!-- Loading State -->
                <div id="certificatesLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading certificates...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading your certificates...</p>
                </div>

                <!-- Empty State -->
                <div id="certificatesEmpty" class="text-center py-5" style="display: none;">
                    <div class="mb-3">
                        <i class="fas fa-certificate text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="text-muted">No Certificates Yet</h5>
                    <p class="text-muted mb-4">You haven't received any certificates yet. Attend events to earn certificates!</p>
                    <a href="<?= base_url('events') ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Browse Events
                    </a>
                </div>

                <!-- Certificates Table -->
                <div id="certificatesContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Event</th>
                                    <th>Type</th>
                                    <th>Event Date</th>
                                    <th>Issued Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="certificatesTableBody">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Error State -->
                <div id="certificatesError" class="alert alert-danger" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Error loading certificates</strong>
                            <p class="mb-0">Please try refreshing the page or contact support if the problem persists.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Detail Modal -->
<div class="modal fade" id="certificateDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-certificate me-2"></i>Certificate Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="certificateDetailContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadCertificateBtn">
                    <i class="fas fa-download me-2"></i>Download Certificate
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCertificates();
});

function loadCertificates() {
    showLoading();
    
    fetch('<?= base_url('audience/api/certificates') ?>')
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.status === 'success') {
                displayCertificates(data.data);
                updateStatistics(data.data);
            } else {
                showError();
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            showError();
            console.error('Network error:', error);
        });
}

function displayCertificates(certificates) {
    const tableBody = document.getElementById('certificatesTableBody');
    
    if (certificates.length === 0) {
        document.getElementById('certificatesEmpty').style.display = 'block';
        return;
    }
    
    document.getElementById('certificatesContent').style.display = 'block';
    
    tableBody.innerHTML = certificates.map(cert => {
        const eventDate = new Date(cert.event_date).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        const issuedDate = cert.generated_at ? new Date(cert.generated_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }) : 'Processing...';
        
        const typeClass = cert.certificate_type === 'presenter' ? 'warning' : 'primary';
        const typeIcon = cert.certificate_type === 'presenter' ? 'chalkboard-teacher' : 'user-graduate';
        
        const hasFile = cert.file_path && cert.file_path.trim() !== '';
        
        return `
            <tr>
                <td>
                    <span class="badge bg-info">${cert.certificate_number}</span>
                </td>
                <td>
                    <strong>${cert.event_title}</strong>
                </td>
                <td>
                    <span class="badge bg-${typeClass}">
                        <i class="fas fa-${typeIcon} me-1"></i>
                        ${cert.certificate_type.charAt(0).toUpperCase() + cert.certificate_type.slice(1)}
                    </span>
                </td>
                <td>${eventDate}</td>
                <td>${issuedDate}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="viewCertificateDetail(${cert.id})"
                                title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${hasFile ? `
                            <button class="btn btn-sm btn-success" 
                                    onclick="downloadCertificate(${cert.id})"
                                    title="Download PDF">
                                <i class="fas fa-download"></i>
                            </button>
                        ` : `
                            <button class="btn btn-sm btn-secondary" 
                                    disabled
                                    title="Certificate being processed">
                                <i class="fas fa-clock"></i>
                            </button>
                        `}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateStatistics(certificates) {
    const totalCerts = certificates.length;
    const participantCerts = certificates.filter(c => c.certificate_type === 'participant').length;
    const presenterCerts = certificates.filter(c => c.certificate_type === 'presenter').length;
    const currentYear = new Date().getFullYear();
    const thisYearCerts = certificates.filter(c => new Date(c.event_date).getFullYear() === currentYear).length;
    
    document.getElementById('totalCertificates').textContent = totalCerts;
    document.getElementById('participantCertificates').textContent = participantCerts;
    document.getElementById('presenterCertificates').textContent = presenterCerts;
    document.getElementById('thisYearCertificates').textContent = thisYearCerts;
}

function viewCertificateDetail(certificateId) {
    // Load certificate details via API
    fetch(`<?= base_url('api/v1/certificates/') ?>${certificateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showCertificateDetail(data.data);
            } else {
                alert('Failed to load certificate details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading certificate details');
        });
}

function showCertificateDetail(certificate) {
    const eventDate = new Date(certificate.event_date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const issuedDate = certificate.generated_at ? new Date(certificate.generated_at).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }) : 'Processing...';
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Certificate Number</h6>
                <p class="fw-bold">${certificate.certificate_number}</p>
                
                <h6 class="text-muted">Event Title</h6>
                <p class="fw-bold">${certificate.event_title}</p>
                
                <h6 class="text-muted">Certificate Type</h6>
                <p>
                    <span class="badge bg-${certificate.certificate_type === 'presenter' ? 'warning' : 'primary'}">
                        <i class="fas fa-${certificate.certificate_type === 'presenter' ? 'chalkboard-teacher' : 'user-graduate'} me-1"></i>
                        ${certificate.certificate_type.charAt(0).toUpperCase() + certificate.certificate_type.slice(1)}
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Event Date</h6>
                <p class="fw-bold">${eventDate}</p>
                
                <h6 class="text-muted">Event Location</h6>
                <p>${certificate.location || 'Online Event'}</p>
                
                <h6 class="text-muted">Certificate Issued</h6>
                <p class="fw-bold">${issuedDate}</p>
            </div>
        </div>
    `;
    
    document.getElementById('certificateDetailContent').innerHTML = content;
    
    // Set download button
    const downloadBtn = document.getElementById('downloadCertificateBtn');
    const hasFile = certificate.file_path && certificate.file_path.trim() !== '';
    
    if (hasFile) {
        downloadBtn.style.display = 'inline-block';
        downloadBtn.onclick = () => downloadCertificate(certificate.id);
    } else {
        downloadBtn.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('certificateDetailModal'));
    modal.show();
}

function downloadCertificate(certificateId) {
    // Create a temporary link and trigger download
    const link = document.createElement('a');
    link.href = `<?= base_url('api/v1/certificates/') ?>${certificateId}/download`;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function refreshCertificates() {
    loadCertificates();
}

function showLoading() {
    document.getElementById('certificatesLoading').style.display = 'block';
    document.getElementById('certificatesContent').style.display = 'none';
    document.getElementById('certificatesEmpty').style.display = 'none';
    document.getElementById('certificatesError').style.display = 'none';
}

function hideLoading() {
    document.getElementById('certificatesLoading').style.display = 'none';
}

function showError() {
    document.getElementById('certificatesError').style.display = 'block';
    document.getElementById('certificatesContent').style.display = 'none';
    document.getElementById('certificatesEmpty').style.display = 'none';
}
</script>
<?= $this->endSection() ?>