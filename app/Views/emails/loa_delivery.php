<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Acceptance - SNIA Conference</title>
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
        .celebration-badge {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
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
        .abstract-highlight {
            background: linear-gradient(135deg, #e8f5e8 0%, #f0f9ff 100%);
            border-left: 4px solid #28a745;
            padding: 25px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .abstract-highlight h3 {
            margin: 0 0 10px 0;
            color: #28a745;
            font-size: 18px;
            font-weight: 600;
        }
        .abstract-highlight p {
            margin: 0;
            color: #495057;
            font-weight: 500;
        }
        .attachment-info {
            background-color: #fff3cd;
            border: 2px dashed #ffc107;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .attachment-info .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .attachment-info h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 20px;
        }
        .attachment-info p {
            margin: 0;
            color: #856404;
            font-weight: 500;
        }
        .conference-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .conference-details h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 20px;
            text-align: center;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .detail-item {
            text-align: center;
            padding: 15px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .detail-item .icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .detail-item .label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .detail-item .value {
            font-size: 14px;
            color: #495057;
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
        .next-steps {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            border-radius: 0 8px 8px 0;
            padding: 25px;
            margin: 25px 0;
        }
        .next-steps h4 {
            margin: 0 0 15px 0;
            color: #0056b3;
            font-size: 20px;
        }
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .steps-list li {
            margin-bottom: 12px;
            padding: 12px;
            background-color: white;
            border-radius: 6px;
            border-left: 3px solid #007bff;
            color: #495057;
        }
        .steps-list li strong {
            color: #0056b3;
        }
        .important-note {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .important-note h4 {
            color: #c53030;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .important-note p {
            color: #742a2a;
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
            .detail-grid {
                grid-template-columns: 1fr;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Celebration Header -->
        <div class="header">
            <h1>🎉 Congratulations!</h1>
            <p>Your presentation has been officially accepted</p>
            <div class="celebration-badge">
                ✅ Letter of Acceptance Delivered
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Dear <?= esc($userName) ?>,</h2>
            
            <p>We are <strong>delighted</strong> to confirm that your abstract has been accepted for presentation at the SNIA Scientific Conference. This email contains your official <strong>Letter of Acceptance (LOA)</strong>.</p>

            <!-- Abstract Highlight -->
            <div class="abstract-highlight">
                <h3>📄 Accepted Abstract</h3>
                <p><strong><?= esc($abstractTitle) ?></strong></p>
            </div>

            <!-- LOA Attachment Info -->
            <div class="attachment-info">
                <div class="icon">📎</div>
                <h4>Letter of Acceptance Attached</h4>
                <p>Your official LOA document is attached to this email</p>
                <p><small>Perfect for visa applications, funding requests, and travel arrangements</small></p>
            </div>

            <!-- Conference Details -->
            <div class="conference-details">
                <h4>📅 Conference Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="icon">🗓️</div>
                        <div class="label">Event Date</div>
                        <div class="value">Coming Soon</div>
                    </div>
                    <div class="detail-item">
                        <div class="icon">📍</div>
                        <div class="label">Location</div>
                        <div class="value">TBA</div>
                    </div>
                    <div class="detail-item">
                        <div class="icon">🎤</div>
                        <div class="label">Presentation Type</div>
                        <div class="value">Oral Presentation</div>
                    </div>
                    <div class="detail-item">
                        <div class="icon">⏰</div>
                        <div class="label">Duration</div>
                        <div class="value">15-20 Minutes</div>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="<?= base_url('presenter/dashboard') ?>" class="action-button">
                    🎯 Access Presenter Dashboard
                </a>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h4>🚀 What's Next?</h4>
                <ul class="steps-list">
                    <li><strong>💳 Complete Registration Payment:</strong> Finalize your conference registration to secure your presentation slot</li>
                    <li><strong>📋 Prepare Your Presentation:</strong> Start working on your slides and presentation materials</li>
                    <li><strong>✈️ Plan Your Travel:</strong> Use the attached LOA for visa applications and travel bookings</li>
                    <li><strong>📅 Wait for Schedule:</strong> Detailed presentation schedule will be sent closer to the event</li>
                    <li><strong>🤝 Network & Connect:</strong> Join our presenter community and connect with fellow researchers</li>
                </ul>
            </div>

            <!-- Important Notes -->
            <div class="important-note">
                <h4>⚠️ Important Reminders</h4>
                <p><strong>Registration Deadline:</strong> Please complete your payment within 14 days to confirm your participation. Failure to register may result in removal from the program.</p>
            </div>

            <p>We're excited to have you as part of our conference and look forward to your valuable contribution to the scientific community. Your research will help advance knowledge and inspire fellow researchers.</p>

            <p>Should you have any questions about your presentation, conference logistics, or need additional documentation, please don't hesitate to reach out to our organizing committee.</p>

            <p><strong>Congratulations once again, and welcome to SNIA Scientific Conference!</strong> 🎓</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>🎓 SNIA Scientific Conference</strong><br>
                Organizing Committee
            </p>
            <p>
                📧 Questions? <a href="mailto:sniaevents@gmail.com">sniaevents@gmail.com</a><br>
                🌐 Website: <a href="<?= base_url() ?>">www.sniaconference.com</a><br>
                📱 Follow us for updates and announcements
            </p>
            <p style="font-size: 12px; color: #aaa; margin-top: 20px;">
                This LOA email was sent automatically. The attached document is your official Letter of Acceptance.
            </p>
        </div>
    </div>
</body>
</html>