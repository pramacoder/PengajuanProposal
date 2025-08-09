<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
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
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .role-badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <h3 class="mb-0">Registrasi Mahasiswa</h3>
            <p class="mb-0 mt-2">Silakan isi data diri Anda</p>
        </div>
        
        <div class="register-body">
            <div class="text-center mb-3">
                <span class="role-badge">Mahasiswa</span>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="user_type" value="mahasiswa">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_mhs" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="nama_mhs" name="nama_mhs" value="{{ old('nama_mhs') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nim" class="form-label">NIM *</label>
                        <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email_mhs" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email_mhs" name="email_mhs" value="{{ old('email_mhs') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="no_hp_mhs" class="form-label">No. HP *</label>
                        <input type="text" class="form-control" id="no_hp_mhs" name="no_hp_mhs" value="{{ old('no_hp_mhs') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fakultas" class="form-label">Fakultas *</label>
                        <select class="form-control" id="fakultas" name="fakultas" required>
                            <option value="">Pilih Fakultas</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id_fakultas }}" {{ old('fakultas') == $f->id_fakultas ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prodi" class="form-label">Program Studi *</label>
                        <select class="form-control" id="prodi" name="prodi" required>
                            <option value="">Pilih Program Studi</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-register">Daftar</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}?user_type=mahasiswa" class="text-primary">Sudah punya akun? Login di sini</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ketika fakultas dipilih
            $('#fakultas').change(function() {
                var id_fakultas = $(this).val();
                var prodiSelect = $('#prodi');
                
                // Reset prodi dropdown
                prodiSelect.html('<option value="">Pilih Program Studi</option>');
                
                if (id_fakultas) {
                    // Ambil data prodi berdasarkan fakultas
                    $.ajax({
                        url: '/get-prodi/' + id_fakultas,
                        type: 'GET',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                prodiSelect.append('<option value="' + value.id_prodi + '">' + value.nama_prodi + '</option>');
                            });
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data program studi.');
                        }
                    });
                }
            });

            // Jika ada old value untuk fakultas, trigger change event
            @if(old('fakultas'))
                $('#fakultas').trigger('change');
                // Set prodi value setelah dropdown terisi
                setTimeout(function() {
                    $('#prodi').val('{{ old("prodi") }}');
                }, 500);
            @endif
        });
    </script>
</body>
</html> 