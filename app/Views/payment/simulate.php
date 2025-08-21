<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Payment Simulation<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
.simulation-container {
    background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.simulation-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto;
}

.simulation-header {
    background: var(--primary);
    color: white;
    padding: 2rem;
    text-align: center;
}

.simulation-header h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
}

.simulation-body {
    padding: 2rem;
}

.payment-info {
    background: var(--gray-50);
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.info-item:last-child {
    border-bottom: none;
    font-weight: 600;
    color: var(--primary);
}

.simulation-options {
    display: grid;
    gap: 1rem;
    margin-bottom: 2rem;
}

.option-card {
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.option-card:hover {
    border-color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
    transform: translateY(-2px);
}

.option-card.success {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.option-card.success:hover {
    border-color: #059669;
    background: rgba(16, 185, 129, 0.15);
}

.option-card.failed {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.option-card.failed:hover {
    border-color: #dc2626;
    background: rgba(239, 68, 68, 0.15);
}

.option-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.option-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.option-description {
    color: var(--gray-600);
    font-size: 0.875rem;
}

.processing {
    display: none;
    text-align: center;
    padding: 2rem;
}

.spinner {
    display: inline-block;
    width: 3rem;
    height: 3rem;
    border: 4px solid #f3f4f6;
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.back-link {
    text-align: center;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .simulation-container {
        padding: 1rem;
    }
    
    .simulation-body {
        padding: 1.5rem;
    }
    
    .option-icon {
        font-size: 2rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="simulation-container">
    <div class="container">
        <div class="simulation-card">
            <!-- Simulation Header -->
            <div class="simulation-header">
                <h1><i class="fas fa-cogs me-2"></i>Payment Simulation</h1>
                <p class="mb-0">Choose how to simulate your payment for testing</p>
            </div>

            <!-- Simulation Body -->
            <div class="simulation-body">
                <!-- Payment Information -->
                <div class="payment-info">
                    <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Payment Details</h5>
                    
                    <div class="info-item">
                        <span>Event</span>
                        <span><?= esc($payment['event_title']) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span>Payment Method</span>
                        <span class="text-capitalize"><?= esc($payment['payment_method']) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span>Amount</span>
                        <span>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span>Payment ID</span>
                        <span>#<?= $payment['id'] ?></span>
                    </div>
                </div>

                <!-- Simulation Options -->
                <div id="simulationOptions">
                    <h5 class="mb-3"><i class="fas fa-play-circle me-2"></i>Simulate Payment Result</h5>
                    
                    <div class="simulation-options">
                        <div class="option-card success" onclick="simulatePayment('success')">
                            <div class="option-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="option-title text-success">Successful Payment</div>
                            <div class="option-description">
                                Simulate a successful payment transaction. Your registration will be confirmed.
                            </div>
                        </div>
                        
                        <div class="option-card failed" onclick="simulatePayment('failed')">
                            <div class="option-icon text-danger">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="option-title text-danger">Failed Payment</div>
                            <div class="option-description">
                                Simulate a failed payment transaction. You can try again later.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Processing Animation -->
                <div id="processingAnimation" class="processing">
                    <div class="spinner"></div>
                    <h5>Processing Payment...</h5>
                    <p class="text-muted mb-0">Please wait while we simulate your payment</p>
                </div>

                <!-- Back Link -->
                <div class="back-link">
                    <a href="<?= base_url('audience/dashboard') ?>" class="text-muted">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function simulatePayment(result) {
    // Hide options and show processing
    document.getElementById('simulationOptions').style.display = 'none';
    document.getElementById('processingAnimation').style.display = 'block';
    
    // Simulate processing time
    setTimeout(() => {
        if (result === 'success') {
            processSuccessfulPayment();
        } else {
            processFailedPayment();
        }
    }, 3000); // 3 second delay for realism
}

function processSuccessfulPayment() {
    // Prepare data with CSRF token
    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('payment/complete/' . $payment['id']) ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message and redirect
            alert('Payment Successful!\n\nTransaction completed successfully. Your registration has been confirmed.');
            window.location.href = data.data.redirect_url;
        } else {
            throw new Error(data.message || 'Payment completion failed');
        }
    })
    .catch(error => {
        console.error('Payment completion error:', error);
        alert('Payment completion failed: ' + error.message);
        
        // Reset to options
        document.getElementById('simulationOptions').style.display = 'block';
        document.getElementById('processingAnimation').style.display = 'none';
    });
}

function processFailedPayment() {
    // Simulate failed payment
    setTimeout(() => {
        alert('Payment Failed!\n\nYour payment could not be processed. Please try again with a different payment method.');
        
        // Redirect back to payment gateway
        window.location.href = '/payment/<?= $payment['registration_id'] ?>';
    }, 1000);
}
</script>
<?= $this->endSection() ?>