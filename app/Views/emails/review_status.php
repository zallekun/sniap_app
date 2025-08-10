<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abstract Review Update - SNIA Conference</title>
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
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header.accepted {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .header.revision {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .header.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
        .status-badge.accepted {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .status-badge.revision {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .status-badge.rejected {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .abstract-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
        }
        .abstract-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }
        .comments-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .comments-box.accepted {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .comments-box.rejected {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .action-button {
            display: inline-block;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .action-button.accepted {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .action-button.revision {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .action-button:hover {
            transform: translateY(-2px);
        }
        .next-steps {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h4 {
            margin: 0 0 15px 0;
            color: #495057;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
            color: #6c757d;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #888;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                width: 100%;
            }
            .header, .content, .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Dynamic Header based on status -->
        <div class="header <?= strtolower($status) ?>">
            <h1>📋 Abstract Review Update</h1>
            <div class="status-badge <?= strtolower($status) ?>">
                <?php if ($status === 'ACCEPTED'): ?>
                    ✅ ACCEPTED
                <?php elseif ($status === 'REVISION'): ?>
                    🔄 REVISION REQUIRED
                <?php else: ?>
                    ❌ NOT ACCEPTED
                <?php endif; ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Dear <?= esc($userName) ?>,</h2>
            
            <!-- Abstract Information -->
            <div class="abstract-info">
                <h3>📄 Abstract Title:</h3>
                <p><strong><?= esc($abstractTitle) ?></strong></p>
            </div>

            <!-- Dynamic content based on review status -->
            <?php if ($status === 'ACCEPTED'): ?>
                <!-- ACCEPTED Content -->
                <p>🎉 <strong>Congratulations!</strong> We are pleased to inform you that your abstract has been <strong>accepted</strong> for presentation at <?= esc($eventTitle) ?>.</p>
                
                <p>Your research demonstrates excellent quality and aligns perfectly with our conference objectives. We look forward to your presentation!</p>
                
                <?php if (!empty($comments)): ?>
                <div class="comments-box accepted">
                    <h4>👨‍🏫 Reviewer Comments:</h4>
                    <p><?= nl2br(esc($comments)) ?></p>
                </div>
                <?php endif; ?>
                
                <div style="text-align: center;">
                    <a href="<?= base_url('presenter/dashboard') ?>" class="action-button accepted">
                        📋 View Dashboard & Next Steps
                    </a>
                </div>
                
                <div class="next-steps">
                    <h4>🎯 Next Steps:</h4>
                    <ul>
                        <li>📧 Letter of Acceptance (LOA) will be sent separately</li>
                        <li>💳 Complete your conference registration payment</li>
                        <li>📅 Check presentation schedule (will be updated soon)</li>
                        <li>🎤 Prepare your presentation materials</li>
                        <li>✈️ Arrange travel and accommodation if needed</li>
                    </ul>
                </div>

            <?php elseif ($status === 'REVISION'): ?>
                <!-- REVISION Content -->
                <p>📝 Thank you for submitting your abstract to <?= esc($eventTitle) ?>. After careful review, we would like to request some <strong>revisions</strong> before final acceptance.</p>
                
                <p>The reviewers have provided specific feedback to help improve your submission. Please address the comments below and resubmit your revised abstract.</p>
                
                <div class="comments-box">
                    <h4>👨‍🏫 Reviewer Feedback & Required Changes:</h4>
                    <p><?= nl2br(esc($comments)) ?></p>
                </div>
                
                <div style="text-align: center;">
                    <a href="<?= base_url('presenter/revise-abstract') ?>" class="action-button revision">
                        📝 Submit Revision
                    </a>
                </div>
                
                <div class="next-steps">
                    <h4>🔄 Revision Guidelines:</h4>
                    <ul>
                        <li>⏰ Revision deadline: 7 days from this notification</li>
                        <li>📝 Address all reviewer comments thoroughly</li>
                        <li>✨ Maintain the same abstract structure and format</li>
                        <li>💬 You may add a brief response letter explaining changes</li>
                        <li>🔍 Revised abstract will undergo final review</li>
                    </ul>
                </div>

            <?php else: ?>
                <!-- REJECTED Content -->
                <p>Thank you for your interest in <?= esc($eventTitle) ?> and for submitting your abstract for review.</p>
                
                <p>After careful consideration, we regret to inform you that your abstract has <strong>not been selected</strong> for presentation at this conference.</p>
                
                <?php if (!empty($comments)): ?>
                <div class="comments-box rejected">
                    <h4>👨‍🏫 Reviewer Feedback:</h4>
                    <p><?= nl2br(esc($comments)) ?></p>
                </div>
                <?php endif; ?>
                
                <p>We understand this may be disappointing, but we encourage you to:</p>
                
                <div class="next-steps">
                    <h4>🌟 Future Opportunities:</h4>
                    <ul>
                        <li>📚 Consider the feedback for future research development</li>
                        <li>🎯 Apply for our next conference call for papers</li>
                        <li>👥 Join as a conference attendee to network and learn</li>
                        <li>📧 Subscribe to our newsletter for future opportunities</li>
                        <li>🤝 Connect with our research community</li>
                    </ul>
                </div>
            <?php endif; ?>

            <p>If you have any questions regarding this review decision, please don't hesitate to contact our organizing committee.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong><?= esc($eventTitle) ?></strong><br>
                Organizing Committee
            </p>
            <p>
                📧 Contact: <a href="mailto:sniaevents@gmail.com">sniaevents@gmail.com</a><br>
                🌐 Website: <a href="<?= base_url() ?>">www.sniaconference.com</a>
            </p>
            <p style="font-size: 12px; color: #aaa;">
                This is an automated notification. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>