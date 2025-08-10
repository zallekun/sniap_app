<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your SNIA Account</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
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
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
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
        <!-- Header -->
        <div class="header">
            <h1>🎓 SNIA Conference</h1>
            <p>Welcome to Scientific Conference Excellence</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Welcome, <?= esc($userName) ?>! 👋</h2>
            
            <p>Thank you for registering for the SNIA Scientific Conference. We're excited to have you join our community of researchers and academics!</p>
            
            <p>To complete your registration and activate your account, please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="<?= esc($verificationUrl) ?>" class="verify-button">
                    ✅ Verify My Email Address
                </a>
            </div>
            
            <div class="info-box">
                <strong>🔒 Security Note:</strong><br>
                This verification link is valid for 24 hours and can only be used once. If you didn't create this account, please ignore this email.
            </div>
            
            <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace;">
                <?= esc($verificationUrl) ?>
            </p>
            
            <p>After verification, you'll be able to:</p>
            <ul>
                <li>📝 Submit your research abstracts</li>
                <li>💳 Complete your conference registration</li>
                <li>📅 Access conference schedules and materials</li>
                <li>🎓 Receive your participation certificates</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>SNIA Scientific Conference</strong><br>
                Advancing Research, Inspiring Innovation
            </p>
            <p>
                Need help? Contact us at <a href="mailto:sniaevents@gmail.com">sniaevents@gmail.com</a>
            </p>
            <p style="font-size: 12px; color: #aaa;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>