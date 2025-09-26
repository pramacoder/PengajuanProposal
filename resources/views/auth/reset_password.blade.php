<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Pengajuan Proposal PKM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .reset-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .reset-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .reset-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .reset-body {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .btn-reset {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .btn-back {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .info-box h6 {
            color: #1976d2;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
        }
        .info-box li {
            color: #1976d2;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2><i class="fas fa-key me-2"></i>Reset Password</h2>
            <p>Masukkan data Anda untuk mendapatkan password baru</p>
        </div>
        
        <div class="reset-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="info-box">
                <h6><i class="fas fa-info-circle me-2"></i>Informasi Reset Password:</h6>
                <ul>
                    <li>Pilih peran Anda (Mahasiswa atau Dosen)</li>
                    <li>Masukkan {{ $role ?? 'NIM/NUPTK' }} sesuai dengan peran yang dipilih</li>
                    <li>Masukkan Gmail personal untuk menerima password baru</li>
                </ul>
            </div>

            <form action="{{ route('password.reset') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="role" class="form-label required-field">Peran</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">-- Pilih Peran --</option>
                        <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="identifier" class="form-label required-field" id="identifier-label">NIM/NUPTK</label>
                    <input type="text" name="identifier" id="identifier" class="form-control" 
                           value="{{ old('identifier') }}" placeholder="Masukkan NIM atau NUPTK" required>
                </div>

                <div class="form-group">
                    <label for="email_personal" class="form-label required-field">Gmail Personal</label>
                    <input type="email" name="email_personal" id="email_personal" class="form-control" 
                           value="{{ old('email_personal') }}" placeholder="Masukkan Gmail untuk menerima password baru" required>
                </div>

                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-key me-2"></i>Reset Password
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update label berdasarkan role yang dipilih
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const label = document.getElementById('identifier-label');
            const input = document.getElementById('identifier');
            
            switch(role) {
                case 'mahasiswa':
                    label.textContent = 'NIM';
                    input.placeholder = 'Masukkan NIM mahasiswa';
                    break;
                case 'dosen':
                    label.textContent = 'NUPTK';
                    input.placeholder = 'Masukkan NUPTK dosen';
                    break;
                default:
                    label.textContent = 'NIM/NUPTK';
                    input.placeholder = 'Masukkan NIM atau NUPTK';
            }
        });
    </script>
</body>
</html>
