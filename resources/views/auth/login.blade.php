<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Proposal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .user-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .user-type-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .user-type-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3 class="mb-0">Sistem Pengajuan Proposal</h3>
            <p class="mb-0 mt-2">Silakan login untuk melanjutkan</p>
        </div>
        
        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="user-type-selector">
                    <div class="user-type-btn active" data-type="mahasiswa">Mahasiswa</div>
                    <div class="user-type-btn" data-type="dosen">Dosen</div>
                    <div class="user-type-btn" data-type="reviewer">Reviewer</div>
                    <div class="user-type-btn" data-type="operator">Operator</div>
                </div>

                <input type="hidden" name="user_type" id="user_type_input" value="mahasiswa">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    Login
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Password default: <strong>password123</strong>
                </small>
            </div>
            <div class="mt-3 text-center">
                <a id="register-link" href="#" class="text-primary">Belum punya akun? Daftar di sini</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ambil user_type dari query string jika ada
        function getQueryParam(param) {
            let urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }
        document.addEventListener('DOMContentLoaded', function() {
            let userType = getQueryParam('user_type');
            if (userType) {
                document.querySelectorAll('.user-type-btn').forEach(function(btn) {
                    btn.classList.remove('active');
                    if (btn.getAttribute('data-type') === userType) {
                        btn.classList.add('active');
                    }
                });
                document.getElementById('user_type_input').value = userType;
            }
            // Event klik pada tombol role
            document.querySelectorAll('.user-type-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.user-type-btn').forEach(function(b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    document.getElementById('user_type_input').value = btn.getAttribute('data-type');
                });
            });
        });

        document.getElementById('register-link').addEventListener('click', function(e) {
            e.preventDefault();
            let userType = document.getElementById('user_type_input').value;
            if (userType) {
                window.location.href = '/register?user_type=' + userType;
            } else {
                alert('Silakan pilih role terlebih dahulu.');
            }
        });
    </script>
</body>
</html> 