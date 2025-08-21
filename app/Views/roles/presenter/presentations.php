<?= $this->extend('shared/layouts/presenter_layout') ?>

<?= $this->section('title') ?>My Presentations<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                My Presentations
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/presenter/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Presentations</li>
                </ol>
            </nav>
        </div>
        <button class="btn btn-outline-secondary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon presentations">
                    <i class="fas fa-presentation-screen"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= count($presentations ?? []) ?></h3>
                    <p>Total Presentations</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon events">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $upcoming = 0;
                        if (isset($presentations)) {
                            foreach ($presentations as $pres) {
                                if (strtotime($pres['start_date'] ?? 'now') > time()) $upcoming++;
                            }
                        }
                        echo $upcoming;
                        ?>
                    </h3>
                    <p>Upcoming</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon certificates">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $completed = 0;
                        if (isset($presentations)) {
                            foreach ($presentations as $pres) {
                                if (strtotime($pres['end_date'] ?? 'now') < time()) $completed++;
                            }
                        }
                        echo $completed;
                        ?>
                    </h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Presentations List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Presentation Schedule
            </h5>
            <span class="badge bg-success"><?= count($presentations ?? []) ?> presentations</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($presentations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-presentation-screen text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Presentations Scheduled</h4>
                    <p class="text-muted">You don't have any presentations scheduled yet. Submit abstracts for review to get presentation slots.</p>
                    <a href="/presenter/abstracts" class="btn btn-success">
                        <i class="fas fa-file-upload me-2"></i>Submit Abstract
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event & Title</th>
                                <th class="border-0">Schedule</th>
                                <th class="border-0">Location</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($presentations as $presentation): ?>
                                <?php 
                                $isUpcoming = strtotime($presentation['start_date'] ?? 'now') > time();
                                $isCompleted = strtotime($presentation['end_date'] ?? 'now') < time();
                                ?>
                                <tr>
                                    <td class="align-middle">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= esc($presentation['title'] ?? 'Event Title') ?></h6>
                                            <small class="text-muted">Presentation Topic: Research Findings</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-sm">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('M d, Y', strtotime($presentation['start_date'] ?? 'now')) ?>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('H:i', strtotime($presentation['start_date'] ?? 'now')) ?> - 
                                            <?= date('H:i', strtotime($presentation['end_date'] ?? 'now')) ?>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-sm">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?= esc($presentation['location'] ?? 'Room TBA') ?>
                                        </div>
                                        <?php if (!empty($presentation['room'])): ?>
                                            <small class="text-muted"><?= esc($presentation['room']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($isCompleted): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Completed
                                            </span>
                                        <?php elseif ($isUpcoming): ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-clock me-1"></i>Upcoming
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-play me-1"></i>In Progress
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewPresentation(<?= $presentation['id'] ?? 0 ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($isUpcoming): ?>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="downloadLOA(<?= $presentation['id'] ?? 0 ?>)"
                                                        title="Download LOA">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="getQRCode(<?= $presentation['id'] ?? 0 ?>)"
                                                        title="Get QR Code">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($isCompleted): ?>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="downloadCertificate(<?= $presentation['id'] ?? 0 ?>)"
                                                        title="Download Certificate">
                                                    <i class="fas fa-certificate"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Presentation Details Modal -->
<div class="modal fade" id="presentationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-presentation-screen me-2"></i>Presentation Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="presentationDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('presenter_scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Presenter presentations page loaded');
});

function viewPresentation(presentationId) {
    const modal = new bootstrap.Modal(document.getElementById('presentationModal'));
    const content = document.getElementById('presentationDetailsContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading presentation details...</p>
        </div>
    `;
    
    modal.show();
    
    // TODO: Fetch presentation details via AJAX
    setTimeout(() => {
        content.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Presentation details system will be implemented
            </div>
        `;
    }, 1000);
}

function downloadLOA(presentationId) {
    // TODO: Implement LOA download
    console.log('Download LOA for presentation:', presentationId);
    alert('LOA download system will be implemented');
}

function getQRCode(presentationId) {
    // TODO: Implement QR code generation
    console.log('Get QR code for presentation:', presentationId);
    alert('QR code system will be implemented');
}

function downloadCertificate(presentationId) {
    // TODO: Implement certificate download
    console.log('Download certificate for presentation:', presentationId);
    alert('Certificate system will be implemented');
}
</script>
<?= $this->endSection() ?>