<?= $this->extend('shared/layouts/user_layout') ?>

<?= $this->section('title') ?>Payment Gateway<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
.payment-container {
    background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.payment-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 800px;
    margin: 0 auto;
}

.payment-header {
    background: var(--primary);
    color: white;
    padding: 2rem;
    text-align: center;
}

.payment-header h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
}

.payment-body {
    padding: 2rem;
}

.order-summary {
    background: var(--gray-50);
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.order-item:last-child {
    border-bottom: none;
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--primary);
}

.payment-methods {
    margin-bottom: 2rem;
}

.payment-method {
    border: 2px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}

.payment-method:hover {
    border-color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

.payment-method.selected {
    border-color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

.payment-method input[type="radio"] {
    margin-right: 0.75rem;
}

.security-info {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-pay {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    font-weight: 700;
    background: var(--primary);
    border: none;
    border-radius: 0.5rem;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-pay:hover:not(:disabled) {
    background: #1e40af;
    transform: translateY(-1px);
}

.btn-pay:disabled {
    background: var(--gray-400);
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .payment-container {
        padding: 1rem;
    }
    
    .payment-body {
        padding: 1.5rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="payment-container">
    <div class="container">
        <div class="payment-card">
            <!-- Payment Header -->
            <div class="payment-header">
                <h1><i class="fas fa-credit-card me-2"></i>Payment Gateway</h1>
                <p class="mb-0">Complete your registration for <?= esc($registration['event_title']) ?></p>
            </div>

            <!-- Payment Body -->
            <div class="payment-body">
                <!-- Order Summary -->
                <div class="order-summary">
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    
                    <div class="order-item">
                        <span>Event Registration</span>
                        <span><?= esc($registration['event_title']) ?></span>
                    </div>
                    
                    <div class="order-item">
                        <span>Registration Type</span>
                        <span class="text-capitalize"><?= esc($registration['registration_type']) ?></span>
                    </div>
                    
                    <div class="order-item">
                        <span>Event Date</span>
                        <span><?= date('d M Y', strtotime($registration['event_date'])) ?></span>
                    </div>
                    
                    <div class="order-item">
                        <span>Registration Fee</span>
                        <span>Rp <?= number_format($registration['registration_fee'], 0, ',', '.') ?></span>
                    </div>
                    
                    <div class="order-item">
                        <span><strong>Total Amount</strong></span>
                        <span><strong>Rp <?= number_format($registration['registration_fee'], 0, ',', '.') ?></strong></span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Select Payment Method</h5>
                    
                    <div class="payment-method selected" onclick="selectPaymentMethod('credit_card')">
                        <input type="radio" name="payment_method" value="credit_card" checked>
                        <span><strong>Credit/Debit Card</strong></span>
                        <div class="small text-muted">Visa, MasterCard, American Express</div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPaymentMethod('bank_transfer')">
                        <input type="radio" name="payment_method" value="bank_transfer">
                        <span><strong>Bank Transfer</strong></span>
                        <div class="small text-muted">Transfer via ATM, Internet Banking, Mobile Banking</div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPaymentMethod('e_wallet')">
                        <input type="radio" name="payment_method" value="e_wallet">
                        <span><strong>E-Wallet</strong></span>
                        <div class="small text-muted">OVO, GoPay, DANA, LinkAja</div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="security-info">
                    <i class="fas fa-shield-alt text-success"></i>
                    <div>
                        <strong>Secure Payment</strong>
                        <div class="small text-muted">Your payment information is encrypted and secure. We use industry-standard security protocols.</div>
                    </div>
                </div>

                <!-- Process Payment Button -->
                <button class="btn-pay" onclick="processPayment()" id="payButton">
                    <i class="fas fa-lock me-2"></i>Pay Now - Rp <?= number_format($registration['registration_fee'], 0, ',', '.') ?>
                </button>

                <!-- Back Link -->
                <div class="text-center mt-3">
                    <a href="/audience/registrations" class="text-muted">
                        <i class="fas fa-arrow-left me-1"></i>Back to My Registrations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Payment Gateway JavaScript
let selectedPaymentMethod = 'credit_card';

function selectPaymentMethod(method) {
    // Remove selected class from all methods
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selected class to clicked method
    event.currentTarget.classList.add('selected');
    
    // Update radio button
    document.querySelector(`input[value="${method}"]`).checked = true;
    selectedPaymentMethod = method;
}

function processPayment() {
    const payButton = document.getElementById('payButton');
    
    // Disable button and show loading
    payButton.disabled = true;
    payButton.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Processing Payment...';
    
    // Prepare payment data
    const paymentData = {
        registration_id: <?= $registration['id'] ?>,
        payment_method: selectedPaymentMethod,
        amount: <?= $registration['registration_fee'] ?>
    };

    // Process payment
    fetch('/payment/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Redirect to payment simulation
            window.location.href = data.data.redirect_url;
        } else {
            throw new Error(data.message || 'Payment initialization failed');
        }
    })
    .catch(error => {
        console.error('Payment Error:', error);
        alert('Payment processing failed: ' + error.message);
        
        // Reset button
        payButton.disabled = false;
        payButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pay Now - Rp <?= number_format($registration["registration_fee"], 0, ",", ".") ?>';
    });
}
</script>
<?= $this->endSection() ?>