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

<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadInvoiceBtn" style="display: none;">
                    <i class="fas fa-download me-2"></i>Download Invoice
                </button>
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
        const paymentStatus = payment.payment_status?.toLowerCase(); 
        const amount = parseFloat(payment.amount || 0);
        
        // Check both status fields for success
        if (status === 'success' || paymentStatus === 'success' || status === 'paid') {
            successfulCount++;
            totalPaid += amount;
        } else if (status === 'pending' || paymentStatus === 'pending') {
            pendingCount++;
        } else if (status === 'failed' || status === 'cancelled' || paymentStatus === 'failed') {
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
    // Fetch payment details from API
    fetch(`<?= base_url('audience/api/payments/details/') ?>${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showPaymentDetailModal(data.data);
            } else {
                alert('Failed to load payment details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading payment details:', error);
            alert('Error loading payment details. Please try again.');
        });
}

function showPaymentDetailModal(payment) {
    const eventDate = new Date(payment.event_date).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const paymentDate = new Date(payment.created_at).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const statusBadge = getPaymentStatusBadge(payment.payment_status);
    const amount = parseFloat(payment.amount || 0);
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted">Payment ID</h6>
                <p class="fw-bold">#${payment.id}</p>
                
                <h6 class="text-muted">Transaction ID</h6>
                <p class="fw-bold">${payment.transaction_id || 'N/A'}</p>
                
                <h6 class="text-muted">Event</h6>
                <p class="fw-bold">${escapeHtml(payment.event_title)}</p>
                
                <h6 class="text-muted">Event Date</h6>
                <p>${eventDate}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Amount</h6>
                <p class="fw-bold text-primary fs-4">Rp ${amount.toLocaleString('id-ID')}</p>
                
                <h6 class="text-muted">Payment Method</h6>
                <p class="text-capitalize">${escapeHtml(payment.payment_method || 'N/A')}</p>
                
                <h6 class="text-muted">Status</h6>
                <p>${statusBadge}</p>
                
                <h6 class="text-muted">Payment Date</h6>
                <p>${paymentDate}</p>
            </div>
        </div>
        
        ${payment.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-muted">Notes</h6>
                    <p class="text-muted">${escapeHtml(payment.notes)}</p>
                </div>
            </div>
        ` : ''}
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Payment Information:</strong><br>
                    ${payment.payment_status === 'success' ? 
                        'Your payment has been successfully processed. You can now access the event.' :
                        payment.payment_status === 'pending' ? 
                        'Your payment is being processed. Please wait for confirmation.' :
                        'There was an issue with your payment. Please contact support if you need assistance.'
                    }
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('paymentDetailContent').innerHTML = content;
    
    // Handle download invoice button
    const downloadBtn = document.getElementById('downloadInvoiceBtn');
    if (payment.invoice_url) {
        downloadBtn.style.display = 'inline-block';
        downloadBtn.onclick = () => window.open(payment.invoice_url, '_blank');
    } else {
        downloadBtn.style.display = 'none';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
    modal.show();
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