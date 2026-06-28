<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Sistem Pengajuan Proposal PKM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .login-button {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔒 Reset Password</h1>
        <p>Sistem Pengajuan Proposal PKM</p>
    </div>
    
    <div class="content">
        <h2>Halo {{ $nama }}!</h2>
        
        <p>Password Anda telah berhasil direset. Berikut adalah kredensial login baru untuk mengakses Sistem Pengajuan Proposal PKM:</p>
        
        <div class="credentials">
            <h3>Kredensial Login Baru:</h3>
            <p><strong>Email Login:</strong> {{ $email_login }}</p>
            <p><strong>{{ ucfirst($role) == 'Mahasiswa' ? 'NIM' : ($role == 'Dosen' ? 'NUPTK' : 'Email') }}:</strong> {{ $identifier }}</p>
            <p><strong>Password Baru:</strong> <code style="background-color: #e9ecef; padding: 2px 6px; border-radius: 3px;">{{ $password }}</code></p>
        </div>
        
        <div class="warning">
            <h4 style="color: #856404; margin-top: 0;">⚠️ Informasi Penting:</h4>
            <p style="color: #856404; margin-bottom: 0;">
                <strong>Untuk login ke sistem:</strong><br>
                • Email: <strong>{{ $email_login }}</strong><br>
                • {{ $role == 'mahasiswa' ? 'NIM' : ($role == 'dosen' ? 'NUPTK' : 'Email') }}: <strong>{{ $identifier }}</strong><br>
                • Password: <strong>{{ $password }}</strong>
            </p>
        </div>
        
        <p><strong>Peran Anda:</strong> {{ ucfirst($role) }}</p>
        
        @if($role == 'mahasiswa')
        <p>Sebagai mahasiswa, Anda dapat:</p>
        <ul>
            <li>Mengajukan proposal PKM</li>
            <li>Melakukan revisi proposal</li>
            <li>Melihat status dan detail proposal</li>
        </ul>
        @elseif($role == 'dosen')
        <p>Sebagai dosen, Anda memiliki akses ke:</p>
        <ul>
            <li>Dashboard Dosen Pembimbing - untuk melihat proposal mahasiswa bimbingan</li>
            <li>Dashboard Dosen Pendamping - untuk memvalidasi proposal yang ditugaskan</li>
        </ul>
        @endif
        
        <p>Silakan klik tombol di bawah ini untuk masuk ke sistem dengan password baru:</p>
        
        <a href="{{ $login_url }}" class="login-button">Masuk ke Sistem</a>
        
        <div class="footer">    
            <p><strong>Penting:</strong> Simpan kredensial ini dengan aman dan jangan bagikan kepada siapapun.</p>
            <p>Password ini akan berlaku untuk login ke sistem. Jika Anda mengalami masalah, silakan hubungi administrator sistem.</p>
            <p style="color: #dc3545;"><strong>⚠️ Password lama Anda sudah tidak berlaku lagi!</strong></p>
        </div>
    </div>
</body>
</html>
