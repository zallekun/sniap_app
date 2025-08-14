<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 10px;
        }
        .title {
            color: #2c3e50;
            font-size: 1.8em;
            margin: 0;
        }
        .subtitle {
            color: #7f8c8d;
            font-size: 1em;
            margin: 5px 0 0 0;
        }
        .verification-code {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 2.5em;
            font-weight: bold;
            letter-spacing: 0.3em;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            font-family: 'Courier New', monospace;
        }
        .message {
            margin: 20px 0;
            line-height: 1.8;
        }
        .instructions {
            background-color: #e8f4fd;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .contact-info {
            margin-top: 15px;
            font-size: 0.8em;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .verification-code {
                font-size: 2em;
                letter-spacing: 0.2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎓</div>
            <h1 class="title"><?= esc($eventData['title']) ?></h1>
            <p class="subtitle">Kode Verifikasi Email</p>
        </div>

        <div class="message">
            <p><strong>Halo <?= esc($name) ?>,</strong></p>
            
            <p>Terima kasih telah mendaftar di <?= esc($eventData['title']) ?>. Untuk menyelesaikan pendaftaran Anda, silakan gunakan kode verifikasi berikut:</p>
        </div>

        <div class="verification-code">
            <?= esc($code) ?>
        </div>

        <div class="instructions">
            <strong>📋 Instruksi:</strong>
            <ul>
                <li>Masukkan kode 6 digit di atas pada halaman verifikasi</li>
                <li>Kode ini berlaku selama <strong><?= esc($expiresIn) ?></strong></li>
                <li>Jangan bagikan kode ini kepada orang lain</li>
            </ul>
        </div>

        <div class="warning">
            <strong>⚠️ Penting:</strong> Jika Anda tidak melakukan pendaftaran di <?= esc($eventData['title']) ?>, abaikan email ini. Kode verifikasi akan kedaluwarsa secara otomatis.
        </div>

        <div class="message">
            <p>Setelah verifikasi berhasil, Anda dapat login ke akun Anda dan mengakses semua fitur konferensi.</p>
            
            <p>Jika Anda mengalami kesulitan atau tidak menerima kode verifikasi, silakan hubungi tim support kami.</p>
        </div>

        <div class="footer">
            <p><strong>Salam hangat,</strong><br>
            Tim <?= esc($eventData['title']) ?></p>
            
            <div class="contact-info">
                <p><strong>Informasi Kontak:</strong><br>
                📧 Email: <?= esc($eventData['contactEmail'] ?? 'admin@snia.ac.id') ?><br>
                📱 WA: <?= esc($eventData['contactPhone'] ?? '+62 812-3456-7890') ?><br>
                🌐 Website: <?= esc($eventData['website'] ?? base_url()) ?></p>
            </div>
            
            <p style="margin-top: 20px; font-size: 0.8em; color: #aaa;">
                Email ini dikirim secara otomatis, mohon jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>