<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - SNIA Conference</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 18px;
            opacity: 0.9;
        }
        .success-badge {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            margin-top: 15px;
            display: inline-block;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 26px;
            text-align: center;
        }
        .content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .receipt-container {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0;
            margin: 30px 0;
            overflow: hidden;
        }
        .receipt-header {
            background-color: #495057;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .receipt-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .receipt-body {
            padding: 25px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .receipt-row:last-child {
            border-bottom: none;
            border-top: 2px solid #28a745;
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
            margin-top: 15px;
            padding-top: 15px;
        }
        .receipt-label {
            color: #495057;
            font-weight: 500;
        }
        .receipt-value {
            color: #212529;
            font-weight: 600;
        }
        .payment-details {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9ff 100%);
            border-left: 4px solid #28a745;
            border-radius: 0 8px 8px 0;
            padding: 25px;
            margin: 25px 0;
        }
        .payment-details h4 {
            margin: 0 0 15px 0;
            color: #28a745;
            font-size: 20px;
        }
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .payment-item {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .payment-item .icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .payment-item .label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .payment-item .value {
            font-size: 14px;
            color: #495057;
            font-weight: 600;
        }
        .access-confirmation {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .access-confirmation .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .access-confirmation h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 20px;
        }
        .access-confirmation p {
            margin: 0;
            color: #856404;
            font-weight: 500;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white !important;
            padding: 18px 35px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
            transition: transform 0.2s;
            font-size: 16px;
        }
        .action-button:hover {
            transform: translateY(-2px);
        }
        .benefits-section {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            border-radius: 0 8px 8px 0;
            padding: 25px;
            margin: 25px 0;
        }
        .benefits-section h4 {
            margin: 0 0 15px 0;
            color: #0056b3;
            font-size: 20px;
        }
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .benefits-list li {
            margin-bottom: 12px;
            padding: 12px;
            background-color: white;
            border-radius: 6px;
            border-left: 3px solid #007bff;
            color: #495057;
        }
        .support-section {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .support-section h4 {
            color: #721c24;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .support-section p {
            color: #721c24;
            margin: 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #888;
            font-size: 14px;
        }
        .footer a {
            color: #28a745;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                width: 100%;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .payment-grid {
                grid-template-columns: 1fr;
            }
            .receipt-row {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Header -->
        <div class="header">
            <h1>💳 Payment Successful!</h1>
            <p>Your conference registration is now complete</p>
            <div class="success-badge">
                ✅ Payment Confirmed
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Thank you, <?= esc($userName) ?>!</h2>
            
            <p>We have successfully received your payment for <?= esc($eventTitle) ?>. Your registration is now <strong>complete and confirmed</strong>!</p>

            <!-- Payment Receipt -->
            <div class="receipt-container">
                <div class="receipt-header">
                    <h3>🧾 Payment Receipt</h3>
                </div>
                <div class="receipt-body">
                    <div class="receipt-row">
                        <span class="receipt-label">Conference Registration</span>
                        <span class="receipt-value"><?= esc($eventTitle) ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Participant Name</span>
                        <span class="receipt-value"><?= esc($userName) ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Payment Date</span>
                        <span class="receipt-value"><?= date('F d, Y - H:i', strtotime($paymentDate)) ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Transaction ID</span>
                        <span class="receipt-value"><?= esc($paymentId) ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Total Amount Paid</span>
                        <span class="receipt-value">Rp <?= number_format($amount, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="payment-details">
                <h4>💰 Payment Information</h4>
                <div class="payment-grid">
                    <div class="payment-item">
                        <div class="icon">💳</div>
                        <div class="label">Payment Method</div>
                        <div class="value">Online Transfer</div>
                    </div>
                    <div class="payment-item">
                        <div class="icon">✅</div>
                        <div class="label">Status</div>
                        <div class="value">Confirmed</div>
                    </div>
                    <div class="payment-item">
                        <div class="icon">🔐</div>
                        <div class="label">Security</div>
                        <div class="value">SSL Encrypted</div>
                    </div>
                    <div class="payment-item">
                        <div class="icon">📧</div>
                        <div class="label">Receipt</div>
                        <div class="value">Email Delivered</div>
                    </div>
                </div>
            </div>

            <!-- Access Confirmation -->
            <div class="access-confirmation">
                <div class="icon">🎫</div>
                <h4>Conference Access Activated</h4>
                <p>You now have full access to all conference materials and events</p>
            </div>

            <div style="text-align: center;">
                <a href="<?= base_url('dashboard') ?>" class="action-button">
                    🎯 Access Your Dashboard
                </a>
            </div>

            <!-- What You Get -->
            <div class="benefits-section">
                <h4>🎁 Your Conference Package Includes:</h4>
                <ul class="benefits-list">
                    <li><strong>📅 Full Conference Access:</strong> All sessions, presentations, and workshops</li>
                    <li><strong>📚 Conference Materials:</strong> Digital proceedings and resource materials</li>
                    <li><strong>🥪 Refreshments:</strong> Coffee breaks and networking lunch included</li>
                    <li><strong>🎓 Certificate:</strong> Official participation certificate after the event</li>
                    <li><strong>🤝 Networking:</strong> Access to exclusive networking sessions and events</li>
                    <li><strong>📱 Mobile App:</strong> Conference app with schedule and networking features</li>
                </ul>
            </div>

            <!-- Important Next Steps -->
            <div class="payment-details">
                <h4>📋 What's Next?</h4>
                <p><strong>📅 Conference Schedule:</strong> Detailed schedule will be sent 1 week before the event</p>
                <p><strong>📍 Venue Information:</strong> Location details and travel guide coming soon</p>
                <p><strong>📱 Mobile App:</strong> Download link will be provided closer to the event date</p>
                <p><strong>🎫 QR Code:</strong> Your attendance QR code will be generated before the event</p>
            </div>

            <!-- Support Section -->
            <div class="support-section">
                <h4>❓ Need Help?</h4>
                <p>If you have any questions about your payment, registration, or the conference, our support team is here to help you 24/7.</p>
            </div>

            <p>We're excited to have you join us at <?= esc($eventTitle) ?>! This will be an excellent opportunity to learn, network, and contribute to the advancement of scientific research.</p>

            <p><strong>Thank you for your participation and see you at the conference!</strong> 🎓</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>💳 <?= esc($eventTitle) ?></strong><br>
                Payment Processing & Registration
            </p>
            <p>
                📧 Support: <a href="mailto:sniaevents@gmail.com">sniaevents@gmail.com</a><br>
                🌐 Website: <a href="<?= base_url() ?>">www.sniaconference.com</a><br>
                📞 Emergency Contact: Available 24/7
            </p>
            <p style="font-size: 12px; color: #aaa; margin-top: 20px;">
                This is your official payment confirmation. Please keep this email for your records.<br>
                Transaction processed securely through our payment gateway.
            </p>
        </div>
    </div>
</body>
</html>