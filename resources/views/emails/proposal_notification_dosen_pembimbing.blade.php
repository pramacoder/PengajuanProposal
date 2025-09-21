<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifikasi Proposal Baru - Sistem Pengajuan Proposal PKM</title>
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
            background-color: #28a745;
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
        .proposal-info {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .dashboard-button {
            display: inline-block;
            background-color: #28a745;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Pengajuan Proposal PKM</h1>
        <p>Notifikasi Proposal Baru</p>
    </div>
    
    <div class="content">
        <h2>Halo Dosen Pembimbing!</h2>
        
        <p>Mahasiswa bimbingan Anda telah mengajukan proposal baru:</p>
        
        <div class="proposal-info">
            <h3>Informasi Proposal:</h3>
            <p><strong>Mahasiswa:</strong> {{ $mahasiswa_nama }}</p>
            <p><strong>Judul Proposal:</strong> {{ $proposal_judul }}</p>
            <p><strong>Tanggal Pengajuan:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        
        <p>Sebagai dosen pembimbing, Anda dapat:</p>
        <ul>
            <li>Melihat detail proposal mahasiswa bimbingan</li>
            <li>Memantau status dan progress proposal</li>
            <li>Memberikan feedback dan bimbingan</li>
        </ul>
        
        <p>Silakan klik tombol di bawah ini untuk melihat detail proposal:</p>
        
        <a href="{{ $dashboard_url }}" class="dashboard-button">Lihat Dashboard Pembimbing</a>
        
        <div class="footer">
            <p><strong>Catatan:</strong> Anda akan menerima notifikasi ini setiap kali mahasiswa bimbingan mengajukan proposal baru.</p>
            <p>Jika Anda mengalami masalah, silakan hubungi administrator sistem.</p>
        </div>
    </div>
</body>
</html>
