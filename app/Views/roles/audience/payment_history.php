<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Payment History<?= $this->endSection() ?>

<?= $this->section('head') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-credit-card me-2"></i>
                                Riwayat Pembayaran
                            </h2>
                            <p class="mb-0 opacity-75">
                                Kelola dan pantau semua transaksi pembayaran Anda
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="fs-1">
                                <i class="fas fa-chart-line" style="opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h4 class="mb-1" id="totalPaid">Rp 0</h4>
                    <small class="text-muted">Total Dibayar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h4 class="mb-1" id="successfulPayments">0</h4>
                    <small class="text-muted">Berhasil</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h4 class="mb-1" id="pendingPayments">0</h4>
                    <small class="text-muted">Menunggu</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h4 class="mb-1" id="failedPayments">0</h4>
                    <small class="text-muted">Gagal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label small">Filter Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="all">Semua Status</option>
                                <option value="success">Berhasil</option>
                                <option value="pending">Menunggu</option>
                                <option value="failed">Gagal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Metode Pembayaran</label>
                            <select class="form-select" id="methodFilter">
                                <option value="all">Semua Metode</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="credit_card">Kartu Kredit</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Cari Transaksi</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari event, ID transaksi...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="resetFilters()">
                                    <i class="fas fa-refresh me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Transaksi Pembayaran
                    </h5>
                    <div class="card-header-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="exportPaymentHistory()">
                            <i class="fas fa-download me-1"></i>Export PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Loading State -->
                    <div id="loadingIndicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat riwayat pembayaran...</div>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5" style="display: none;">
                        <i class="fas fa-credit-card text-muted fa-4x mb-3"></i>
                        <h6 class="text-muted">Belum Ada Riwayat Pembayaran</h6>
                        <p class="text-muted mb-3">Anda belum melakukan pembayaran apapun</p>
                        <a href="/dashboard" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Daftar Event
                        </a>
                    </div>
                    
                    <!-- Payments Table -->
                    <div id="paymentsContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">ID Transaksi</th>
                                        <th class="border-0">Event</th>
                                        <th class="border-0">Jumlah</th>
                                        <th class="border-0">Metode</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Tanggal</th>
                                        <th class="border-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <!-- Payment data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="small text-muted">
                                        Menampilkan <span id="showingStart">0</span> - <span id="showingEnd">0</span> dari <span id="totalRecords">0</span> transaksi
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Payment pagination">
                                        <ul class="pagination pagination-sm justify-content-end mb-0" id="paginationNav">
                                            <!-- Pagination will be generated here -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailModalLabel">
                    <i class="fas fa-receipt me-2"></i>Detail Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat detail pembayaran...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" id="downloadInvoiceBtn" style="display: none;">
                    <i class="fas fa-download me-1"></i>Download Invoice
                </button>
                <button type="button" class="btn btn-warning" id="retryPaymentBtn" style="display: none;">
                    <i class="fas fa-redo me-1"></i>Coba Lagi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal (for failed/pending payments) -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="makePaymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Lakukan Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="makePaymentContent">
                    <!-- Payment form will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-success" id="processPaymentBtn" disabled>
                    <i class="fas fa-credit-card me-1"></i>Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment History page loading...');
    loadPaymentHistory();
    setupFilters();
});

// Global variables
let allPayments = [];
let filteredPayments = [];
let currentPage = 1;
const itemsPerPage = 10;

async function loadPaymentHistory() {
    try {
        // Show loading
        showLoading();
        
        // Fetch payment history from dashboard registrations endpoint
        const response = await fetch('/dashboard/registrations', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        console.log('Payment data received:', data);
        
        if (data.status === 'success' && data.data) {
            // Transform registration data to payment format
            const payments = transformRegistrationsToPayments(data.data);
            allPayments = payments;
            filteredPayments = [...payments];
            
            hideLoading();
            
            if (payments.length === 0) {
                showEmptyState();
            } else {
                showPaymentsTable();
                updatePaymentStatistics(payments);
                renderPaymentsTable(filteredPayments);
                setupPagination();
            }
        } else {
            throw new Error(data.message || 'Gagal memuat data');
        }
        
    } catch (error) {
        console.error('Error loading payment history:', error);
        hideLoading();
        showError('Gagal memuat riwayat pembayaran: ' + error.message);
    }
}

function transformRegistrationsToPayments(registrations) {
    return registrations.map(reg => ({
        id: reg.id,
        transaction_id: `TXN${reg.id.toString().padStart(6, '0')}`,
        external_id: `SNIA${reg.id}${new Date().getFullYear()}`,
        event_title: reg.event_name || 'Event',
        event_description: reg.event_description || '',
        registration_type: reg.registration_type || 'audience',
        amount: reg.registration_fee || 0,
        payment_method: reg.payment_method || 'bank_transfer',
        status: reg.payment_status || 'pending',
        registration_status: reg.registration_status || 'pending',
        created_at: reg.created_at || new Date().toISOString(),
        updated_at: reg.updated_at || new Date().toISOString(),
        invoice_url: null, // Will be generated if needed
        registration_id: reg.id
    }));
}

function showLoading() {
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('paymentsContainer').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loadingIndicator').style.display = 'none';
}

function showEmptyState() {
    document.getElementById('emptyState').style.display = 'block';
    document.getElementById('paymentsContainer').style.display = 'none';
}

function showPaymentsTable() {
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('paymentsContainer').style.display = 'block';
}

function showError(message) {
    hideLoading();
    document.getElementById('emptyState').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle text-danger fa-4x mb-3"></i>
            <h6 class="text-danger">Terjadi Kesalahan</h6>
            <p class="text-muted mb-3">${message}</p>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-refresh me-2"></i>Coba Lagi
            </button>
        </div>
    `;
    document.getElementById('emptyState').style.display = 'block';
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

function renderPaymentsTable(payments) {
    const tbody = document.getElementById('paymentsTableBody');
    
    // Calculate pagination
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedPayments = payments.slice(start, end);
    
    // Clear existing content
    tbody.innerHTML = '';
    
    if (paginatedPayments.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-search text-muted fa-2x mb-2"></i>
                    <div class="text-muted">Tidak ada transaksi yang ditemukan</div>
                </td>
            </tr>
        `;
        return;
    }
    
    paginatedPayments.forEach(payment => {
        const row = document.createElement('tr');
        row.className = 'payment-row';
        
        const statusBadge = getPaymentStatusBadge(payment.status);
        const amount = parseFloat(payment.amount || 0);
        const paymentMethod = getPaymentMethodDisplay(payment.payment_method);
        
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="payment-icon me-2">
                        <i class="fas fa-receipt text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-medium">#${payment.transaction_id}</div>
                        <small class="text-muted">${payment.external_id}</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="event-info">
                    <div class="fw-medium text-dark">${escapeHtml(payment.event_title)}</div>
                    <small class="text-muted text-capitalize">${escapeHtml(payment.registration_type)}</small>
                </div>
            </td>
            <td>
                <div class="amount-display">
                    <span class="fw-bold ${amount > 0 ? 'text-primary' : 'text-success'}">${amount > 0 ? 'Rp ' + amount.toLocaleString('id-ID') : 'GRATIS'}</span>
                </div>
            </td>
            <td>
                <div class="method-display">
                    <span class="text-capitalize">${paymentMethod.name}</span>
                    <div class="method-icon">${paymentMethod.icon}</div>
                </div>
            </td>
            <td>${statusBadge}</td>
            <td>
                <div class="date-display">
                    <div class="fw-medium">${formatDate(payment.created_at)}</div>
                    <small class="text-muted">${formatTime(payment.created_at)}</small>
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewPaymentDetails(${payment.id})" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${payment.status === 'pending' ? 
                            `<button class="btn btn-warning btn-sm" onclick="makePayment(${payment.id})" title="Bayar Sekarang">
                                <i class="fas fa-credit-card"></i>
                            </button>` : ''
                        }
                        ${(payment.status === 'success' || payment.status === 'paid') ? 
                            `<button class="btn btn-success btn-sm" onclick="downloadInvoice(${payment.id})" title="Download Invoice">
                                <i class="fas fa-download"></i>
                            </button>` : ''
                        }
                        ${payment.status === 'failed' ? 
                            `<button class="btn btn-outline-danger btn-sm" onclick="retryPayment(${payment.id})" title="Coba Lagi">
                                <i class="fas fa-redo"></i>
                            </button>` : ''
                        }
                    </div>
                </div>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Update pagination info
    updatePaginationInfo(payments.length);
}

function getPaymentStatusBadge(status) {
    const statusLower = status?.toLowerCase() || 'unknown';
    
    switch (statusLower) {
        case 'paid':
        case 'success':
            return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Berhasil</span>';
        case 'pending':
            return '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Menunggu</span>';
        case 'failed':
        case 'cancelled':
            return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Gagal</span>';
        case 'processing':
            return '<span class="badge bg-info"><i class="fas fa-spinner me-1"></i>Proses</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function getPaymentMethodDisplay(method) {
    const methods = {
        'bank_transfer': { name: 'Transfer Bank', icon: '<i class="fas fa-university text-primary"></i>' },
        'ewallet': { name: 'E-Wallet', icon: '<i class="fas fa-mobile-alt text-success"></i>' },
        'credit_card': { name: 'Kartu Kredit', icon: '<i class="fas fa-credit-card text-info"></i>' },
        'cash': { name: 'Tunai', icon: '<i class="fas fa-money-bill text-warning"></i>' }
    };
    
    return methods[method] || { name: method || 'N/A', icon: '<i class="fas fa-question text-muted"></i>' };
}

// Setup filters
function setupFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const methodFilter = document.getElementById('methodFilter');
    const searchInput = document.getElementById('searchInput');
    
    // Status filter
    statusFilter.addEventListener('change', applyFilters);
    
    // Method filter
    methodFilter.addEventListener('change', applyFilters);
    
    // Search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const methodFilter = document.getElementById('methodFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    filteredPayments = allPayments.filter(payment => {
        // Status filter
        if (statusFilter !== 'all' && payment.status.toLowerCase() !== statusFilter) {
            return false;
        }
        
        // Method filter
        if (methodFilter !== 'all' && payment.payment_method !== methodFilter) {
            return false;
        }
        
        // Search filter
        if (searchTerm) {
            const searchableText = [
                payment.transaction_id,
                payment.external_id,
                payment.event_title,
                payment.registration_type
            ].join(' ').toLowerCase();
            
            if (!searchableText.includes(searchTerm)) {
                return false;
            }
        }
        
        return true;
    });
    
    // Reset to first page
    currentPage = 1;
    
    // Re-render table
    renderPaymentsTable(filteredPayments);
    setupPagination();
}

function resetFilters() {
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('methodFilter').value = 'all';
    document.getElementById('searchInput').value = '';
    
    filteredPayments = [...allPayments];
    currentPage = 1;
    
    renderPaymentsTable(filteredPayments);
    setupPagination();
}

// Pagination
function setupPagination() {
    const totalPages = Math.ceil(filteredPayments.length / itemsPerPage);
    const paginationNav = document.getElementById('paginationNav');
    
    if (totalPages <= 1) {
        paginationNav.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    paginationNav.innerHTML = paginationHTML;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredPayments.length / itemsPerPage);
    
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    renderPaymentsTable(filteredPayments);
    setupPagination();
    
    // Scroll to top of table
    document.getElementById('paymentsContainer').scrollIntoView({ behavior: 'smooth' });
}

function updatePaginationInfo(totalItems) {
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, totalItems);
    
    document.getElementById('showingStart').textContent = totalItems > 0 ? start : 0;
    document.getElementById('showingEnd').textContent = end;
    document.getElementById('totalRecords').textContent = totalItems;
}

// Payment actions
async function viewPaymentDetails(paymentId) {
    try {
        const payment = allPayments.find(p => p.id === paymentId);
        if (!payment) {
            showAlert('Data pembayaran tidak ditemukan', 'danger');
            return;
        }
        
        displayPaymentDetailModal(payment);
        
    } catch (error) {
        console.error('Error viewing payment details:', error);
        showAlert('Gagal memuat detail pembayaran', 'danger');
    }
}

function displayPaymentDetailModal(payment) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
    const content = document.getElementById('paymentDetailContent');
    const downloadBtn = document.getElementById('downloadInvoiceBtn');
    const retryBtn = document.getElementById('retryPaymentBtn');
    
    const amount = parseFloat(payment.amount || 0);
    const statusBadge = getPaymentStatusBadge(payment.status);
    const paymentMethod = getPaymentMethodDisplay(payment.payment_method);
    const isFree = amount === 0;
    
    content.innerHTML = `
        <!-- Header Section -->
        <div class="d-flex align-items-start mb-4">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm ${payment.status === 'success' ? 'bg-success' : payment.status === 'pending' ? 'bg-warning' : 'bg-danger'} rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-receipt text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2 fw-bold text-dark">Pembayaran untuk: ${payment.event_title}</h5>
                <p class="text-muted mb-3">Detail lengkap transaksi pembayaran Anda</p>
                <div class="d-flex gap-2">
                    ${statusBadge}
                    <span class="badge bg-secondary text-capitalize">${payment.registration_type}</span>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="info-divider"></div>

        <div class="row">
            <!-- Transaction Details -->
            <div class="col-lg-8">
                <div class="metadata-section mb-4">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Detail Transaksi
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-hashtag me-2 text-info"></i>
                                <div>
                                    <small class="text-muted d-block">ID Transaksi</small>
                                    <code class="fw-bold">${payment.transaction_id}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-barcode me-2 text-secondary"></i>
                                <div>
                                    <small class="text-muted d-block">External ID</small>
                                    <code class="fw-bold">${payment.external_id}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar me-2 text-success"></i>
                                <div>
                                    <small class="text-muted d-block">Tanggal Transaksi</small>
                                    <strong class="text-dark">${formatDate(payment.created_at)}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock me-2 text-warning"></i>
                                <div>
                                    <small class="text-muted d-block">Waktu</small>
                                    <strong class="text-dark">${formatTime(payment.created_at)}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                ${paymentMethod.icon}
                                <div class="ms-2">
                                    <small class="text-muted d-block">Metode Pembayaran</small>
                                    <strong class="text-dark">${paymentMethod.name}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Jenis Pendaftaran</small>
                                    <strong class="text-dark text-capitalize">${payment.registration_type}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-divider"></div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Total Pembayaran</h6>
                            <small class="text-muted">Jumlah yang ${payment.status === 'success' ? 'telah dibayar' : 'harus dibayar'}</small>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0 fw-bold ${isFree ? 'text-success' : payment.status === 'success' ? 'text-success' : 'text-primary'}">${isFree ? 'GRATIS' : 'Rp ' + amount.toLocaleString('id-ID')}</h3>
                        </div>
                    </div>
                </div>
                
                <!-- Event Information -->
                <div class="metadata-section">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="fas fa-calendar-alt me-2 text-success"></i>Informasi Event
                    </h6>
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-dark">${payment.event_title}</h6>
                            <p class="card-text text-muted small">${payment.event_description || 'Tidak ada deskripsi tersedia'}</p>
                            <div class="row g-2">
                                <div class="col-auto">
                                    <span class="badge bg-primary">ID: ${payment.registration_id}</span>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-info text-capitalize">${payment.registration_type}</span>
                                </div>
                                <div class="col-auto">
                                    <span class="badge ${payment.registration_status === 'approved' ? 'bg-success' : payment.registration_status === 'pending' ? 'bg-warning' : 'bg-secondary'}">${payment.registration_status || 'pending'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status & Actions Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card border-0 bg-light">
                    <div class="card-body p-3">
                        <h6 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-info-circle me-2 text-info"></i>Status & Info
                        </h6>
                        <div class="mb-3">
                            <small class="text-muted">Status Saat Ini:</small><br>
                            <div class="mt-1">
                                <span class="fw-bold text-dark">Pembayaran:</span> ${payment.status}<br>
                                <span class="fw-bold text-dark">Pendaftaran:</span> ${payment.registration_status}
                            </div>
                        </div>
                        <div class="border-top pt-3">
                            <small class="text-muted">Timeline:</small><br>
                            <div class="mt-1">
                                <small class="text-muted">• Dibuat: ${formatDate(payment.created_at)}</small><br>
                                <small class="text-muted">• Update: ${formatDate(payment.updated_at)}</small><br>
                                ${payment.status === 'pending' ? 
                                    '<small class="text-warning">• Menunggu pembayaran</small>' : 
                                    payment.status === 'success' ?
                                    '<small class="text-success">• Pembayaran berhasil</small>' :
                                    '<small class="text-danger">• Pembayaran gagal</small>'
                                }
                            </div>
                        </div>
                        
                        ${payment.status === 'pending' ? `
                        <div class="border-top pt-3 mt-3">
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning btn-sm" onclick="makePaymentFromDetail(${payment.id})">
                                    <i class="fas fa-credit-card me-1"></i>Bayar Sekarang
                                </button>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${payment.status === 'success' ? `
                        <div class="border-top pt-3 mt-3">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-sm" onclick="downloadInvoiceFromDetail(${payment.id})">
                                    <i class="fas fa-download me-1"></i>Download Invoice
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="viewRegistrationFromPayment(${payment.registration_id})">
                                    <i class="fas fa-eye me-1"></i>Lihat Pendaftaran
                                </button>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Setup modal buttons
    if (payment.status === 'success' && payment.invoice_url) {
        downloadBtn.style.display = 'block';
        downloadBtn.onclick = () => downloadInvoice(payment.id);
    } else {
        downloadBtn.style.display = 'none';
    }
    
    if (payment.status === 'failed' || payment.status === 'pending') {
        retryBtn.style.display = 'block';
        retryBtn.onclick = () => {
            modal.hide();
            makePayment(payment.id);
        };
    } else {
        retryBtn.style.display = 'none';
    }
    
    modal.show();
}

// Payment action functions
async function makePayment(paymentId) {
    try {
        const payment = allPayments.find(p => p.id === paymentId);
        if (!payment) {
            showAlert('Data pembayaran tidak ditemukan', 'danger');
            return;
        }
        
        // Use the same payment modal from dashboard
        await displayPaymentModal(payment);
        
    } catch (error) {
        console.error('Error making payment:', error);
        showAlert('Gagal memproses pembayaran', 'danger');
    }
}

async function makePaymentFromDetail(paymentId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentDetailModal'));
    modal.hide();
    await makePayment(paymentId);
}

async function retryPayment(paymentId) {
    await makePayment(paymentId);
}

async function downloadInvoice(paymentId) {
    try {
        // In a real implementation, this would download the actual invoice
        showAlert('Fitur download invoice akan segera tersedia', 'info');
        
        // Simulate download
        // const payment = allPayments.find(p => p.id === paymentId);
        // if (payment && payment.invoice_url) {
        //     window.open(payment.invoice_url, '_blank');
        // }
        
    } catch (error) {
        console.error('Error downloading invoice:', error);
        showAlert('Gagal download invoice', 'danger');
    }
}

async function downloadInvoiceFromDetail(paymentId) {
    await downloadInvoice(paymentId);
}

async function viewRegistrationFromPayment(registrationId) {
    // Redirect to dashboard with registration detail
    window.location.href = `/dashboard?view=registration&id=${registrationId}`;
}

// Export functionality
async function exportPaymentHistory() {
    try {
        showAlert('Memproses export...', 'info');
        
        // In a real implementation, this would generate and download a PDF
        setTimeout(() => {
            showAlert('Fitur export PDF akan segera tersedia', 'info');
        }, 1000);
        
    } catch (error) {
        console.error('Error exporting payment history:', error);
        showAlert('Gagal export data', 'danger');
    }
}

// Reuse payment modal from dashboard
async function displayPaymentModal(payment) {
    // Create a modal for payment processing
    const modalHTML = `
        <div class="modal fade" id="makePaymentModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-credit-card me-2"></i>Lakukan Pembayaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="paymentModalContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-success" id="processPaymentBtn" disabled>
                            <i class="fas fa-credit-card me-1"></i>Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('makePaymentModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Populate modal content
    const content = document.getElementById('paymentModalContent');
    const processBtn = document.getElementById('processPaymentBtn');
    const amount = parseFloat(payment.amount || 0);
    const isFree = amount === 0;
    
    content.innerHTML = `
        <!-- Header Section -->
        <div class="d-flex align-items-start mb-4">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm ${isFree ? 'bg-success' : 'bg-primary'} rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-credit-card text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-2 fw-bold text-dark">Pembayaran untuk: ${payment.event_title}</h5>
                <p class="text-muted mb-0">Selesaikan pembayaran untuk mengkonfirmasi pendaftaran</p>
            </div>
        </div>

        <!-- Divider -->
        <div class="info-divider"></div>

        <div class="row">
            <!-- Payment Details -->
            <div class="col-lg-8">
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
                                    <code class="fw-bold">#${payment.registration_id}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Jenis Pendaftaran</small>
                                    <strong class="text-dark text-capitalize">${payment.registration_type}</strong>
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
                            <h4 class="mb-0 fw-bold ${isFree ? 'text-success' : 'text-primary'}">${isFree ? 'GRATIS' : 'Rp ' + amount.toLocaleString('id-ID')}</h4>
                        </div>
                    </div>
                </div>
                
                ${!isFree ? `
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
                                <small class="text-muted">• Status berubah ke "success"</small><br>
                                <small class="text-muted">• QR Code untuk acara</small><br>
                                <small class="text-muted">• E-Certificate setelah event</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Setup button action
    if (isFree) {
        processBtn.disabled = false;
        processBtn.innerHTML = '<i class="fas fa-check me-1"></i>Konfirmasi Pendaftaran Gratis';
        processBtn.onclick = () => confirmFreePayment(payment.id);
    } else {
        processBtn.disabled = false;
        processBtn.innerHTML = '<i class="fas fa-credit-card me-1"></i>Proses Pembayaran';
        processBtn.onclick = () => processPaymentAction(payment.id);
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('makePaymentModal'));
    modal.show();
    
    // Initialize payment methods after modal is shown
    setTimeout(() => {
        if (!isFree) {
            initializePaymentMethods();
        }
    }, 100);
}

// Payment method selection (reuse from dashboard)
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

function initializePaymentMethods() {
    const firstCard = document.querySelector('.payment-method-card[data-method="bank_transfer"]');
    if (firstCard) {
        selectPaymentMethod('bank_transfer', firstCard);
    }
}

// Process payment action
async function processPaymentAction(paymentId) {
    const processBtn = document.getElementById('processPaymentBtn');
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'bank_transfer';
    
    // Show loading state
    const originalText = processBtn.innerHTML;
    processBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Memproses...';
    processBtn.disabled = true;
    
    try {
        // Simulate payment processing
        showAlert('Pembayaran sedang diproses...', 'info');
        
        // For demo purposes, simulate successful payment
        setTimeout(async () => {
            try {
                // Update payment status in local data
                const paymentIndex = allPayments.findIndex(p => p.id === paymentId);
                if (paymentIndex !== -1) {
                    allPayments[paymentIndex].status = 'success';
                    allPayments[paymentIndex].payment_method = paymentMethod;
                    allPayments[paymentIndex].updated_at = new Date().toISOString();
                }
                
                showAlert('Pembayaran berhasil diproses!', 'success');
                
                // Close modal and refresh data
                const modal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
                modal.hide();
                
                // Refresh payment history
                loadPaymentHistory();
                
            } catch (error) {
                showAlert('Pembayaran berhasil tapi gagal update status', 'warning');
            } finally {
                processBtn.innerHTML = originalText;
                processBtn.disabled = false;
            }
        }, 2000);
        
    } catch (error) {
        console.error('Payment processing error:', error);
        showAlert('Terjadi kesalahan saat memproses pembayaran', 'danger');
        processBtn.innerHTML = originalText;
        processBtn.disabled = false;
    }
}

// Confirm free payment
async function confirmFreePayment(paymentId) {
    const processBtn = document.getElementById('processPaymentBtn');
    
    // Show loading state
    const originalText = processBtn.innerHTML;
    processBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Mengonfirmasi...';
    processBtn.disabled = true;
    
    try {
        // Update payment status for free event
        const paymentIndex = allPayments.findIndex(p => p.id === paymentId);
        if (paymentIndex !== -1) {
            allPayments[paymentIndex].status = 'success';
            allPayments[paymentIndex].updated_at = new Date().toISOString();
        }
        
        showAlert('Pendaftaran gratis dikonfirmasi!', 'success');
        
        // Close modal and refresh data
        const modal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
        modal.hide();
        
        // Refresh payment history
        loadPaymentHistory();
        
    } catch (error) {
        console.error('Free payment confirmation error:', error);
        showAlert('Terjadi kesalahan saat konfirmasi', 'danger');
    } finally {
        processBtn.innerHTML = originalText;
        processBtn.disabled = false;
    }
}

// Utility functions
function escapeHtml(text) {
    if (!text) return '';
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

// Alert system (reuse from dashboard)
function showAlert(message, type = 'info', duration = 5000) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-alert`;
    alertElement.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alertElement.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.appendChild(alertElement);
    
    // Auto dismiss
    if (duration > 0) {
        setTimeout(() => {
            if (alertElement && alertElement.parentNode) {
                alertElement.remove();
            }
        }, duration);
    }
}

// CSS for payment method cards and custom styling
const additionalCSS = `
<style>
.payment-method-card {
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-card:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.payment-method-card.selected {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.info-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #dee2e6, transparent);
    margin: 1.5rem 0;
}

.metadata-section {
    background: #fafafa;
    border-radius: 8px;
    padding: 1.5rem;
    border-left: 4px solid #007bff;
}

.avatar-sm {
    width: 48px;
    height: 48px;
}

.payment-row:hover {
    background-color: #f8f9fa;
}

.action-buttons .btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.custom-alert {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.date-display, .amount-display, .method-display {
    min-width: 120px;
}

.event-info {
    max-width: 200px;
}

.method-icon {
    font-size: 0.8rem;
}

.payment-icon {
    width: 24px;
    text-align: center;
}
</style>
`;

// Add CSS to head
document.head.insertAdjacentHTML('beforeend', additionalCSS);

// Initialize filters and search with proper event delegation
document.addEventListener('change', function(e) {
    if (e.target.id === 'statusFilter' || e.target.id === 'methodFilter') {
        applyFilters();
    }
});

document.addEventListener('input', function(e) {
    if (e.target.id === 'searchInput') {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(applyFilters, 300);
    }
});

console.log('Payment History page initialized');
</script>

<!-- Include dashboard utilities -->
<script>
// Reuse utility functions from dashboard
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Menunggu</span>',
        'approved': '<span class="badge bg-success">Disetujui</span>',
        'rejected': '<span class="badge bg-danger">Ditolak</span>',
        'confirmed': '<span class="badge bg-success">Dikonfirmasi</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getPaymentBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Belum Bayar</span>',
        'paid': '<span class="badge bg-success">Lunas</span>',
        'success': '<span class="badge bg-success">Berhasil</span>',
        'failed': '<span class="badge bg-danger">Gagal</span>',
        'cancelled': '<span class="badge bg-secondary">Dibatalkan</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

// API request helper (from dashboard)
async function apiRequest(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(endpoint, config);
        const data = await response.json();
        
        return { response, data };
    } catch (error) {
        console.error('API Request failed:', error);
        throw error;
    }
}

// Server-side session integration
const serverUser = <?= json_encode($user ?? null) ?>;

if (serverUser) {
    console.log('User session found:', serverUser.email);
    // User session is available, proceed with loading
} else {
    console.log('No server session, redirecting to login');
    window.location.href = '/login';
}
</script>
<?= $this->endSection() ?>