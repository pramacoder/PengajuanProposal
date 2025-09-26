<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa - Sistem Pengajuan Proposal PKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-body {
            padding: 40px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #333;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h2><i class="fas fa-key me-2"></i>Request Kredensial Login</h2>
                <p class="mb-0">Dapatkan kredensial login untuk mahasiswa dan dosen pembimbing</p>
            </div>
            
            <div class="register-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    
                    <!-- Informasi Mahasiswa -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-user me-2"></i>Informasi Mahasiswa
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_mahasiswa" class="form-label required-field">Nama Mahasiswa</label>
                                <input type="text" class="form-control @error('nama_mahasiswa') is-invalid @enderror" 
                                       id="nama_mahasiswa" name="nama_mahasiswa" 
                                       value="{{ old('nama_mahasiswa') }}" 
                                       placeholder="Masukkan nama lengkap Anda" required>
                                @error('nama_mahasiswa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nim" class="form-label required-field">NIM</label>
                                <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                                       id="nim" name="nim" 
                                       value="{{ old('nim') }}" 
                                       placeholder="Masukkan NIM Anda" required>
                                @error('nim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="email_mahasiswa" class="form-label required-field">Gmail Mahasiswa</label>
                                <input type="email" class="form-control @error('email_mahasiswa') is-invalid @enderror" 
                                       id="email_mahasiswa" name="email_mahasiswa" 
                                       value="{{ old('email_mahasiswa') }}" 
                                       placeholder="Masukkan Gmail Anda" required>
                                @error('email_mahasiswa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Sistem akan mencari data mahasiswa berdasarkan NIM dan mengirim kredensial login ke Gmail ini.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Dosen Pembimbing -->
                    <div class="form-section">
                        <h5 class="section-title">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Informasi Dosen Pembimbing
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_dosen" class="form-label required-field">Nama Dosen</label>
                                <input type="text" class="form-control @error('nama_dosen') is-invalid @enderror" 
                                       id="nama_dosen" name="nama_dosen" 
                                       value="{{ old('nama_dosen') }}" 
                                       placeholder="Masukkan nama dosen pembimbing" required>
                                @error('nama_dosen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nuptk_dosen" class="form-label required-field">NUPTK Dosen</label>
                                <input type="text" class="form-control @error('nuptk_dosen') is-invalid @enderror" 
                                       id="nuptk_dosen" name="nuptk_dosen" 
                                       value="{{ old('nuptk_dosen') }}" 
                                       placeholder="Masukkan NUPTK dosen pembimbing" required>
                                @error('nuptk_dosen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="email_dosen" class="form-label required-field">Gmail Dosen</label>
                                <input type="email" class="form-control @error('email_dosen') is-invalid @enderror" 
                                       id="email_dosen" name="email_dosen" 
                                       value="{{ old('email_dosen') }}" 
                                       placeholder="Masukkan Gmail dosen pembimbing" required>
                                @error('email_dosen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Sistem akan mencari data dosen berdasarkan NUPTK dan mengirim kredensial login ke Gmail ini.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Kredensial Login
                        </button>
                    </div>
                </form>

                <div class="login-link">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
