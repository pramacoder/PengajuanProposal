<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Proposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* ── Full-screen background ── */
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
            gap: 2rem;
        }

        /* Desktop: side-by-side */
        @media (min-width: 1024px) {
            .page-bg {
                justify-content: space-between;
                padding: 2rem 8% 2rem 10%;
            }
        }

        /* ── Dark gradient overlay ── */
        .page-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(10, 15, 25, 0.82) 0%,
                rgba(20, 32, 48, 0.72) 55%,
                rgba(40, 10, 18, 0.65) 100%
            );
            z-index: 0;
        }

        /* ── Left branding block ── */
        .left-brand {
            display: none; /* hidden on mobile */
            position: relative;
            z-index: 1;
            flex: 1;
            max-width: 480px;
            color: white;
        }

        @media (min-width: 1024px) {
            .left-brand { display: block; }
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.85);
            margin-bottom: 1.5rem;
        }

        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 1rem;
            letter-spacing: -0.02em;
        }

        .brand-title span {
            background: linear-gradient(135deg, #fff 30%, rgba(255,200,200,0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.7;
            max-width: 380px;
            margin: 0 0 2rem;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .brand-feature-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
        }

        .brand-feature-item::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #8F0B13;
            flex-shrink: 0;
        }

        /* ── Glass login card ── */
        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border-radius: 24px;
            padding: 2rem 1.5rem;
            width: 100%;
            max-width: 420px;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-top: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.35),
                0 32px 64px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255,255,255,0.25);
            animation: floatIn 0.55s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .login-card { padding: 2.25rem 2rem; }
        }

        /* Maroon top accent */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #380F17 0%, #8F0B13 60%, rgba(143,11,19,0) 100%);
            border-radius: 24px 24px 0 0;
        }

        @keyframes floatIn {
            from { opacity: 0; transform: translateY(32px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Card typography (white on glass) ── */
        .card-label {
            color: rgba(255,255,255,0.75);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-title-text {
            color: #fff;
        }

        .card-subtitle-text {
            color: rgba(255,255,255,0.6);
        }

        /* ── Glass inputs ── */
        .input-field {
            background: rgba(255,255,255,0.12) !important;
            border: 1px solid rgba(255,255,255,0.22) !important;
            color: #fff !important;
            transition: all 0.2s ease;
        }

        .input-field::placeholder {
            color: rgba(255,255,255,0.4) !important;
        }

        .input-field:hover {
            background: rgba(255,255,255,0.18) !important;
        }

        .input-field:focus {
            outline: none !important;
            border-color: rgba(143,11,19,0.8) !important;
            box-shadow: 0 0 0 3px rgba(143,11,19,0.2) !important;
            background: rgba(255,255,255,0.2) !important;
        }

        /* Input date icon color fix */
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(0.5); }

        /* Eye icon in glass card */
        .eye-btn { color: rgba(255,255,255,0.5) !important; }

        /* ── Divider ── */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0.75rem 0;
            color: rgba(255,255,255,0.4);
            font-size: 0.75rem;
        }
        .form-divider::before, .form-divider::after {
            content: ''; flex: 1;
            height: 1px; background: rgba(255,255,255,0.15);
        }

        /* ── Buttons ── */
        .btn-login {
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-login:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 20px rgba(56,15,23,0.4);
        }
        .btn-login:active { transform: translateY(0) scale(0.99); }

        .btn-secondary {
            background: rgba(255,255,255,0.12) !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2) !important;
            transform: translateY(-1px);
        }

        /* ── Label helpers ── */
        .helper-text { color: rgba(255,255,255,0.4); }

        /* ── Bottom watermark ── */
        .page-brand {
            position: fixed;
            bottom: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="page-bg">

        <!-- LEFT: Branding block -->
        <div class="left-brand">
            <div class="brand-badge">
                <img src="{{ asset('UNUDLOGO.png') }}" alt="" style="width:16px;height:16px;object-fit:contain;opacity:0.8;">
                Universitas Udayana
            </div>

            <h1 class="brand-title">
                <span>Sistem Pengajuan<br>Proposal PKM</span>
            </h1>

            <p class="brand-desc">
                Platform digital terintegrasi untuk pengelolaan proposal Program Kreativitas Mahasiswa (PKM) — dari pengajuan, review, hingga penilaian akhir, semua dalam satu sistem.
            </p>

            <div class="brand-features">
                <div class="brand-feature-item">Pengajuan proposal secara digital tanpa kertas</div>
                <div class="brand-feature-item">Review administratif & substantif terstruktur</div>
                <div class="brand-feature-item">Penilaian multi-fase oleh reviewer terverifikasi</div>
                <div class="brand-feature-item">Notifikasi real-time setiap perubahan status</div>
            </div>
        </div>

        <!-- RIGHT: Glass Login Card -->
        <div class="login-card">

            <!-- Card header -->
            <div class="flex items-center gap-3 mb-5">
                <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#380F17,#8F0B13);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(56,15,23,0.5);">
                    <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD" style="width:28px;height:28px;object-fit:contain;">
                </div>
                <div>
                    <h2 class="font-bold leading-tight card-title-text" style="font-size:1.1rem;margin:0;">Selamat Datang!</h2>
                    <p class="card-subtitle-text" style="font-size:0.72rem;margin:0;">Masuk ke Sistem PKM Universitas Udayana</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl" style="background:rgba(220,38,38,0.15);border:1px solid rgba(220,38,38,0.3);">
                    <div class="flex gap-2 items-start">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:#FCA5A5;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold" style="color:#FCA5A5;">Terjadi kesalahan</p>
                            <ul class="list-disc pl-4 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-xs" style="color:#FCA5A5;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-3 rounded-xl" style="background:rgba(5,150,105,0.15);border:1px solid rgba(5,150,105,0.3);">
                    <p class="text-xs" style="color:#6EE7B7;">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="block card-label mb-1.5">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="input-field block w-full px-3.5 py-2.5 rounded-xl text-sm"
                           placeholder="nama@student.unud.ac.id"
                           required>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block card-label mb-1.5">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                               id="password"
                               name="password"
                               class="input-field block w-full px-3.5 py-2.5 rounded-xl text-sm"
                               placeholder="••••••••"
                               required>
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="eye-btn absolute inset-y-0 right-0 pr-3.5 flex items-center">
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit"
                        class="btn-login w-full text-white py-2.5 px-4 rounded-xl text-sm font-bold tracking-wide focus:outline-none mb-1"
                        style="background:linear-gradient(135deg, #380F17 0%, #8F0B13 100%); box-shadow: 0 4px 14px rgba(143,11,19,0.5);"
                        onmouseover="this.style.background='linear-gradient(135deg,#2a0a10 0%,#6e0910 100%)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #380F17 0%, #8F0B13 100%)'">
                    Log In
                </button>
            </form>

            <!-- Divider -->
            <div class="form-divider">atau</div>

            <!-- Cari Akun -->
            <p class="text-xs text-center mb-1.5 helper-text">Butuh kredensial login?</p>
            <a href="{{ route('register') }}"
               class="btn-login btn-secondary flex items-center justify-center w-full px-3 py-2.5 rounded-xl text-sm font-semibold mb-2">
                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Cari Akun
            </a>

            <!-- Reset Password -->
            <p class="text-xs text-center mb-1.5 helper-text">Lupa password Anda?</p>
            <a href="{{ route('password.forgot') }}"
               class="btn-login btn-secondary flex items-center justify-center w-full px-3 py-2.5 rounded-xl text-sm font-semibold">
                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Reset Password
            </a>

        </div>{{-- /login-card --}}

        <!-- Bottom watermark -->
        <div class="page-brand">
            <img src="{{ asset('UNUDLOGO.png') }}" alt="" style="width:16px;height:16px;object-fit:contain;opacity:0.5;">
            <span>Universitas Udayana — Sistem PKM 2025</span>
        </div>

    </div>
</body>
</html>
