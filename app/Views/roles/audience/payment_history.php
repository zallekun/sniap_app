<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Payment History<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">Payment History</h2>
                        <p class="mb-0">View all your payment transactions and invoices</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-credit-card" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-money-bill-wave" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="totalPaid">Loading...</h4>
                <small class="text-muted">Total Paid</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="successfulPayments">0</h4>
                <small class="text-muted">Successful Payments</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-clock" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="pendingPayments">0</h4>
                <small class="text-muted">Pending Payments</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-danger mb-2">
                    <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1" id="failedPayments">0</h4>
                <small class="text-muted">Failed Payments</small>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Payment Transactions</h5>
            </div>
            <div class="card-body">
                <div id="loadingIndicator" class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading payment history...</p>
                </div>
                
                <div id="emptyState" class="text-center py-4" style="display: none;">
                    <i class="fas fa-credit-card text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">No payment history</h6>
                    <p class="text-muted">You haven't made any payments yet</p>
                    <a href="/audience/registrations" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Register for Event
                    </a>
                </div>
                
                <div id="paymentsTable" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Event</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentHistory();
});

function loadPaymentHistory() {
    fetch('/audience/api/payments')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const payments = data.data;
                
                // Hide loading indicator
                document.getElementById('loadingIndicator').style.display = 'none';
                
                if (payments.length === 0) {
                    document.getElementById('emptyState').style.display = 'block';
                } else {
                    document.getElementById('paymentsTable').style.display = 'block';
                    updatePaymentStatistics(payments);
                    updatePaymentsTable(payments);
                }
            } else {
                showError('Failed to load payment history');
            }
        })
        .catch(error => {
            console.error('Error loading payment history:', error);
            showError('Failed to load payment history');
        });
}

function updatePaymentStatistics(payments) {
    let totalPaid = 0;
    let successfulCount = 0;
    let pendingCount = 0;
    let failedCount = 0;
    
    payments.forEach(payment => {
        const status = payment.status?.toLowerCase();
        const amount = parseFloat(payment.amount || 0);
        
        if (status === 'paid' || status === 'success') {
            successfulCount++;
            totalPaid += amount;
        } else if (status === 'pending') {
            pendingCount++;
        } else if (status === 'failed' || status === 'cancelled') {
            failedCount++;
        }
    });
    
    // Update statistics
    document.getElementById('totalPaid').textContent = 'Rp ' + totalPaid.toLocaleString('id-ID');
    document.getElementById('successfulPayments').textContent = successfulCount;
    document.getElementById('pendingPayments').textContent = pendingCount;
    document.getElementById('failedPayments').textContent = failedCount;
}

function updatePaymentsTable(payments) {
    const tbody = document.getElementById('paymentsTableBody');
    tbody.innerHTML = '';
    
    payments.forEach(payment => {
        const row = document.createElement('tr');
        
        const statusBadge = getPaymentStatusBadge(payment.status);
        const amount = parseFloat(payment.amount || 0);
        
        row.innerHTML = `
            <td>
                <span class="fw-medium">#${payment.id}</span>
                ${payment.external_id ? `<br><small class="text-muted">${payment.external_id}</small>` : ''}
            </td>
            <td>
                <div class="fw-medium">${escapeHtml(payment.event_title || 'Event')}</div>
                <small class="text-muted">${escapeHtml(payment.registration_type || 'Registration')}</small>
            </td>
            <td>
                <span class="fw-medium">Rp ${amount.toLocaleString('id-ID')}</span>
            </td>
            <td>
                <span class="text-capitalize">${escapeHtml(payment.payment_method || 'N/A')}</span>
            </td>
            <td>${statusBadge}</td>
            <td>
                <div>${formatDate(payment.created_at)}</div>
                <small class="text-muted">${formatTime(payment.created_at)}</small>
            </td>
            <td>
                <div class="btn-group" role="group">
                    ${payment.invoice_url ? 
                        `<a href="${payment.invoice_url}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-file-invoice"></i> Invoice
                        </a>` : ''
                    }
                    <button class="btn btn-sm btn-outline-secondary" onclick="viewPaymentDetails(${payment.id})">
                        <i class="fas fa-eye"></i> Details
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

function getPaymentStatusBadge(status) {
    const statusLower = status?.toLowerCase() || 'unknown';
    
    switch (statusLower) {
        case 'paid':
        case 'success':
            return '<span class="badge bg-success">Paid</span>';
        case 'pending':
            return '<span class="badge bg-warning">Pending</span>';
        case 'failed':
        case 'cancelled':
            return '<span class="badge bg-danger">Failed</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function viewPaymentDetails(paymentId) {
    // You can implement a modal or redirect to payment details page
    alert(`Payment details for ID: ${paymentId}\n\nThis feature will be implemented to show detailed payment information.`);
}

function showError(message) {
    document.getElementById('loadingIndicator').style.display = 'none';
    document.getElementById('emptyState').innerHTML = `
        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
        <h6 class="mt-3 text-danger">Error</h6>
        <p class="text-muted">${message}</p>
        <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="fas fa-refresh me-2"></i>Try Again
        </button>
    `;
    document.getElementById('emptyState').style.display = 'block';
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function formatTime(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}
</script>

<?= $this->endSection() ?>