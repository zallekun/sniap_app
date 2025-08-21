<?= $this->extend('shared/layouts/presenter_layout') ?>

<?= $this->section('title') ?>My Events<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-calendar-check me-2"></i>
                My Events
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/presenter/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">My Events</li>
                </ol>
            </nav>
        </div>
        <a href="/events" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Browse Events
        </a>
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
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon abstracts">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3><?= count($registrations ?? []) ?></h3>
                    <p>Total Events</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon presentations">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $confirmed = 0;
                        if (isset($registrations)) {
                            foreach ($registrations as $reg) {
                                if (($reg['status'] ?? '') === 'confirmed') $confirmed++;
                            }
                        }
                        echo $confirmed;
                        ?>
                    </h3>
                    <p>Confirmed</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon events">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $pending = 0;
                        if (isset($registrations)) {
                            foreach ($registrations as $reg) {
                                if (($reg['status'] ?? '') === 'pending') $pending++;
                            }
                        }
                        echo $pending;
                        ?>
                    </h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="presenter-stat-card">
                <div class="presenter-stat-icon certificates">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="presenter-stat-content">
                    <h3>
                        <?php 
                        $unpaid = 0;
                        if (isset($registrations)) {
                            foreach ($registrations as $reg) {
                                if (empty($reg['payment_status']) || $reg['payment_status'] === 'pending') $unpaid++;
                            }
                        }
                        echo $unpaid;
                        ?>
                    </h3>
                    <p>Payment Pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Event Registrations
            </h5>
            <span class="badge bg-success"><?= count($registrations ?? []) ?> events</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($registrations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Event Registrations</h4>
                    <p class="text-muted">You haven't registered for any events yet. Browse available events and register as a presenter.</p>
                    <a href="/events" class="btn btn-success">
                        <i class="fas fa-search me-2"></i>Browse Events
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Event</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Payment</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $registration): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= esc($registration['title'] ?? 'Untitled Event') ?></h6>
                                            <small class="text-muted"><?= esc($registration['location'] ?? 'Location TBA') ?></small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="text-sm">
                                            <?= date('M d, Y', strtotime($registration['start_date'] ?? 'now')) ?>
                                        </div>
                                        <small class="text-muted"><?= date('H:i', strtotime($registration['start_date'] ?? 'now')) ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $status = $registration['status'] ?? 'pending';
                                        $statusClass = match($status) {
                                            'confirmed' => 'bg-success',
                                            'cancelled' => 'bg-danger', 
                                            'pending' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $paymentStatus = $registration['payment_status'] ?? 'pending';
                                        $paymentClass = match($paymentStatus) {
                                            'completed', 'verified' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            'pending' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $paymentClass ?>">
                                            <?= ucfirst(str_replace('_', ' ', $paymentStatus)) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewEvent(<?= $registration['event_id'] ?? 0 ?>)"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (($registration['payment_status'] ?? '') === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="makePayment(<?= $registration['id'] ?? 0 ?>)"
                                                        title="Make Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (($registration['status'] ?? '') === 'confirmed'): ?>
                                                <a href="/presenter/abstracts" class="btn btn-sm btn-outline-info" 
                                                   title="Submit Abstract">
                                                    <i class="fas fa-file-upload"></i>
                                                </a>
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
<?= $this->endSection() ?>

<?= $this->section('presenter_scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Presenter registrations page loaded');
});

function viewEvent(eventId) {
    // TODO: Implement event details modal
    console.log('View event:', eventId);
    alert('Event details will be implemented');
}

function makePayment(registrationId) {
    // TODO: Implement payment flow
    console.log('Make payment for registration:', registrationId);
    alert('Payment system will be implemented');
}
</script>
<?= $this->endSection() ?>