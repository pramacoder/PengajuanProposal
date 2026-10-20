<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Kredensial - Sistem Pengajuan Proposal PKM</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #0b0f19;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Full page background matching Login */
        .page-bg {
            min-height: 100vh;
            width: 100%;
            background-image: url('{{ asset('Backgroundlogin.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        /* Dark gradient overlay matching Login */
        .page-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(10, 15, 25, 0.85) 0%,
                    rgba(20, 32, 48, 0.78) 55%,
                    rgba(40, 10, 18, 0.72) 100%);
            z-index: 0;
        }

        /* Floating split card */
        .split-card-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1040px;
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: cardFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Left visual panel with dark maroon aesthetic */
        .visual-panel {
            background-image: url('{{ asset('Backgroundlogin.jpg') }}');
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .visual-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg,
                    rgba(45, 10, 18, 0.93) 0%,
                    rgba(20, 24, 38, 0.91) 60%,
                    rgba(15, 18, 28, 0.95) 100%);
            z-index: 1;
        }

        .visual-panel-content {
            position: relative;
            z-index: 2;
        }

        /* Custom inputs */
        .custom-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 0.875rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            color: #0f172a;
            background-color: #f8fafc;
        }

        .custom-input:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
        }

        .custom-input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #8F0B13;
            box-shadow: 0 0 0 3px rgba(143, 11, 19, 0.12);
        }

        /* Submit Button */
        .btn-submit-maroon {
            background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%);
            box-shadow: 0 4px 14px rgba(143, 11, 19, 0.35);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-submit-maroon:hover {
            background: linear-gradient(135deg, #2a0a10 0%, #70090f 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(143, 11, 19, 0.45);
        }

        .btn-submit-maroon:active {
            transform: translateY(0);
        }

        /* Role switcher active state */
        .role-pill-btn {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body>
    <div class="page-bg">
        <div class="split-card-container p-3 md:p-5 lg:p-6" x-data="{ role: '{{ old('role_type', 'mahasiswa') }}' }">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                
                <!-- Left Visual Panel (Reference Layout Left Card) -->
                <div class="hidden lg:flex lg:col-span-5 visual-panel p-8 min-h-[580px]">
                    <div class="visual-panel-content">
                        <!-- Top Brand Pill & Logo -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('UNUDLOGO.png') }}" alt="Logo Udayana" class="w-10 h-10 object-contain drop-shadow">
                                <div>
                                    <span class="block text-xs font-semibold uppercase tracking-wider text-red-200">Universitas Udayana</span>
                                    <span class="block text-base font-bold text-white tracking-tight">Sistem PKM</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider bg-white/10 border border-white/20 px-3 py-1 rounded-full text-white/90">
                                Kredensial
                            </span>
                        </div>

                        <!-- Mid Showcase Copy -->
                        <div class="space-y-4 my-auto pt-6">
                            <div class="inline-flex items-center gap-2 bg-red-950/60 border border-red-500/30 text-red-200 text-xs font-semibold px-3 py-1.5 rounded-full">
                                <i class="fas fa-shield-alt text-red-400"></i>
                                <span>Verifikasi Akun Terintegrasi</span>
                            </div>
                            
                            <h2 class="text-3xl font-extrabold text-white leading-tight tracking-tight">
                                Dapatkan Kredensial <br>
                                <span class="bg-gradient-to-r from-red-100 via-rose-200 to-amber-200 bg-clip-text text-transparent">
                                    Login Sistem PKM
                                </span>
                            </h2>
                            
                            <p class="text-sm text-slate-300 leading-relaxed">
                                Mahasiswa dan Dosen Universitas Udayana yang belum menerima atau lupa kredensial akun dapat meminta pengiriman kredensial otomatis ke alamat Gmail resmi/personal.
                            </p>

                            <!-- Feature list -->
                            <div class="space-y-2.5 pt-3">
                                <div class="flex items-center gap-3 text-xs text-slate-200">
                                    <div class="w-5 h-5 rounded-full bg-red-600/30 border border-red-500/40 flex items-center justify-center text-red-300">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <span>Pencocokan otomatis basis data NIM / NUPTK</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-200">
                                    <div class="w-5 h-5 rounded-full bg-red-600/30 border border-red-500/40 flex items-center justify-center text-red-300">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <span>Pengiriman kredensial instan ke kotak masuk Gmail</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-200">
                                    <div class="w-5 h-5 rounded-full bg-red-600/30 border border-red-500/40 flex items-center justify-center text-red-300">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <span>Akses penuh dashboard pengajuan & evaluasi PKM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Info Card -->
                    <div class="visual-panel-content pt-8 border-t border-white/10 mt-6">
                        <div class="flex items-center justify-between text-xs text-slate-300">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-university text-red-300"></i>
                                <span>Biro Kemahasiswaan Unud</span>
                            </div>
                            <span class="text-white/60">Tahun Akademik {{ date('Y') }}/{{ date('Y')+1 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Form Panel (Reference Layout Right Form) -->
                <div class="lg:col-span-7 p-4 sm:p-6 lg:p-8 flex flex-col justify-between">
                    <div>
                        <!-- Header Bar with Brand & Back Button -->
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-lg tracking-tight text-slate-900">
                                    SIM-PKM <span class="text-[#8F0B13]">UNUD</span>
                                </span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#8F0B13] bg-slate-100 hover:bg-red-50 px-3 py-1.5 rounded-full transition-colors">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali ke Login</span>
                            </a>
                        </div>

                        <!-- Title Section -->
                        <div class="mb-6">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                Request Kredensial
                            </h1>
                            <p class="text-sm text-slate-500 mt-1">
                                Dapatkan kredensial akun login Anda untuk masuk ke sistem pengajuan proposal.
                            </p>
                        </div>

                        <!-- Alerts -->
                        @if(session('success'))
                            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 text-sm">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 text-base flex-shrink-0"></i>
                                <div>
                                    <span class="font-semibold block mb-0.5">Permintaan Berhasil!</span>
                                    <span>{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 text-sm">
                                <i class="fas fa-exclamation-circle text-rose-600 mt-0.5 text-base flex-shrink-0"></i>
                                <div>
                                    <span class="font-semibold block mb-0.5">Permintaan Gagal</span>
                                    <span>{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Role Switcher Pills -->
                        <div class="bg-slate-100 p-1.5 rounded-2xl flex gap-1.5 mb-6">
                            <button type="button"
                                    @click="role = 'mahasiswa'"
                                    :class="role === 'mahasiswa' ? 'bg-white text-[#8F0B13] shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="role-pill-btn flex-1 py-2.5 px-4 rounded-xl text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-user-graduate"></i>
                                <span>Mahasiswa</span>
                            </button>
                            <button type="button"
                                    @click="role = 'dosen'"
                                    :class="role === 'dosen' ? 'bg-white text-[#8F0B13] shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="role-pill-btn flex-1 py-2.5 px-4 rounded-xl text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Dosen Pendamping</span>
                            </button>
                        </div>

                        <!-- Form Mahasiswa -->
                        <div x-show="role === 'mahasiswa'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0">
                            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="role_type" value="mahasiswa">

                                <div>
                                    <label for="nama_mahasiswa" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <input type="text" 
                                               id="nama_mahasiswa" 
                                               name="nama_mahasiswa" 
                                               value="{{ old('nama_mahasiswa') }}" 
                                               class="custom-input @error('nama_mahasiswa') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="Contoh: I Made Surya Wijaya" 
                                               required>
                                    </div>
                                    @error('nama_mahasiswa')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nim" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Nomor Induk Mahasiswa (NIM) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <input type="text" 
                                               id="nim" 
                                               name="nim" 
                                               value="{{ old('nim') }}" 
                                               class="custom-input @error('nim') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="Contoh: 2001234567" 
                                               required>
                                    </div>
                                    @error('nim')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email_mahasiswa" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Alamat Gmail Penerima <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input type="email" 
                                               id="email_mahasiswa" 
                                               name="email_mahasiswa" 
                                               value="{{ old('email_mahasiswa') }}" 
                                               class="custom-input @error('email_mahasiswa') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="nama.anda@gmail.com atau student unud" 
                                               required>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                                        <i class="fas fa-info-circle text-[#8F0B13]"></i>
                                        Kredensial login akan dikirimkan otomatis ke alamat email ini.
                                    </p>
                                    @error('email_mahasiswa')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-submit-maroon w-full text-white font-bold py-3.5 px-6 rounded-xl text-sm flex items-center justify-center gap-2 mt-4 cursor-pointer">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Kirim Kredensial Mahasiswa</span>
                                </button>
                            </form>
                        </div>

                        <!-- Form Dosen -->
                        <div x-show="role === 'dosen'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="role_type" value="dosen">

                                <div>
                                    <label for="nama_dosen" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Nama Lengkap Dosen <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <input type="text" 
                                               id="nama_dosen" 
                                               name="nama_dosen" 
                                               value="{{ old('nama_dosen') }}" 
                                               class="custom-input @error('nama_dosen') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="Contoh: Dr. I Made Sujana, S.T., M.T." 
                                               required>
                                    </div>
                                    @error('nama_dosen')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nuptk_dosen" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        NUPTK / NIDN <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-id-badge"></i>
                                        </div>
                                        <input type="text" 
                                               id="nuptk_dosen" 
                                               name="nuptk_dosen" 
                                               value="{{ old('nuptk_dosen') }}" 
                                               class="custom-input @error('nuptk_dosen') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="Contoh: 0012345678" 
                                               required>
                                    </div>
                                    @error('nuptk_dosen')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email_dosen" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Alamat Gmail Penerima <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <input type="email" 
                                               id="email_dosen" 
                                               name="email_dosen" 
                                               value="{{ old('email_dosen') }}" 
                                               class="custom-input @error('email_dosen') border-rose-500 bg-rose-50 @enderror" 
                                               placeholder="nama.dosen@gmail.com atau unud.ac.id" 
                                               required>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                                        <i class="fas fa-info-circle text-[#8F0B13]"></i>
                                        Kredensial login dosen akan dikirimkan otomatis ke alamat email ini.
                                    </p>
                                    @error('email_dosen')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-submit-maroon w-full text-white font-bold py-3.5 px-6 rounded-xl text-sm flex items-center justify-center gap-2 mt-4 cursor-pointer">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Kirim Kredensial Dosen</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Bottom Nav Links -->
                    <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                        <div>
                            <span>Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="font-bold text-[#8F0B13] hover:underline ml-1">
                                Masuk ke Sistem
                            </a>
                        </div>
                        <div>
                            <span>Lupa password Anda?</span>
                            <a href="{{ route('password.forgot') }}" class="font-bold text-slate-700 hover:text-[#8F0B13] hover:underline ml-1">
                                Reset Password
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
