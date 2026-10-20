<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Proposal PKM - Universitas Udayana</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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

        [x-cloak] {
            display: none !important;
        }

        /* ── Aura Gradient: "Rose Gold" (Light Theme) ── */
        /* Base color on BODY - blend modes composite against this */
        body {
            background-color: #faf8f2;
            color: #1e293b;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .aura-bg {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            /* NO background-color - layers blend against body */
        }

        /* Layer 1 - multiply */
        .aura-layer-1 {
            position: fixed;
            inset: 0;
            background: linear-gradient(rgba(0,0,0,0) 0%, rgba(255,183,178,0.12) 28%, rgb(255,255,255) 18%, rgb(255,145,140) 68%, rgb(200,120,115) 100%);
            mix-blend-mode: multiply;
            filter: blur(90px);
            transform: translateZ(0);
            will-change: transform;
            pointer-events: none;
            z-index: 0;
        }

        /* Layer 2 - multiply */
        .aura-layer-2 {
            position: fixed;
            inset: 0;
            background: linear-gradient(rgba(0,0,0,0) 0%, rgba(255,183,178,0.22) 34%, rgb(255,255,255) 66%, rgb(255,145,140) 82%, rgb(200,120,115) 100%);
            mix-blend-mode: multiply;
            filter: blur(90px);
            transform: translateZ(0);
            will-change: transform;
            pointer-events: none;
            z-index: 0;
        }

        @media (min-width: 768px) {
            .aura-layer-1, .aura-layer-2 {
                filter: blur(130px);
            }
        }

        /* ── Headline Gradient Shimmer (BIAsia/gradient-shimmer implementation) ── */
        .gradient-shimmer-headline {
            display: inline-block;
            background: linear-gradient(
                105deg,
                #0f172a 0%,
                #380F17 22%,
                #8F0B13 38%,
                #E11D48 48%,
                #F59E0B 52%,
                #F43F5E 58%,
                #8F0B13 68%,
                #380F17 80%,
                #0f172a 100%
            );
            background-size: 260% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShimmerSweep 4.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes gradientShimmerSweep {
            0% {
                background-position: 120% 50%;
            }
            100% {
                background-position: -80% 50%;
            }
        }

        /* ── Sticky Navbar Blur (Light Theme) ── */
        .glass-navbar {
            background: rgba(250, 248, 242, 0.86);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(244, 114, 182, 0.25);
            box-shadow: 0 4px 20px -2px rgba(143, 11, 19, 0.05);
        }

        /* ── Glass Cards (Light Luxury Rose-Gold Theme) ── */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(244, 114, 182, 0.22);
            box-shadow: 0 4px 20px -2px rgba(143, 11, 19, 0.04), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(225, 29, 72, 0.45);
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -10px rgba(143, 11, 19, 0.12), 0 6px 15px -3px rgba(0, 0, 0, 0.04);
        }

        /* ── Slide-over Login Drawer Panel (Light Glass Luxury) ── */
        .login-drawer-panel {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(32px) saturate(180%);
            -webkit-backdrop-filter: blur(32px) saturate(180%);
            border-left: 1px solid rgba(244, 63, 94, 0.25);
            box-shadow: -15px 0 50px rgba(143, 11, 19, 0.15);
        }

        .login-drawer-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #380F17 0%, #8F0B13 60%, #F59E0B 100%);
        }

        /* ── Form Inputs Inside Login Card ── */
        .input-field {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            transition: all 0.2s ease;
        }

        .input-field::placeholder {
            color: #94a3b8 !important;
        }

        .input-field:focus {
            outline: none !important;
            border-color: #8F0B13 !important;
            box-shadow: 0 0 0 3px rgba(143, 11, 19, 0.2) !important;
            background: #ffffff !important;
        }

        /* ── Button Gradients ── */
        .btn-maroon {
            background: linear-gradient(135deg, #380F17 0%, #8F0B13 100%);
            box-shadow: 0 4px 14px rgba(143, 11, 19, 0.35);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-maroon:hover {
            background: linear-gradient(135deg, #2a0a10 0%, #6e0910 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(143, 11, 19, 0.5);
        }

        .btn-maroon:active {
            transform: translateY(0);
        }
    </style>
</head>

<body x-data="{ 
    loginDrawerOpen: {{ $errors->any() || request()->has('signin') ? 'true' : 'false' }}, 
    mobileMenuOpen: false,
    showPassword: false,
    activeFaq: 1,
    activeAlurStep: 1
}">

    <!-- =========================================================================
         AURA GRADIENT "ROSE GOLD" BACKGROUND WRAPPER
         ========================================================================= -->
    <div class="aura-bg">
        <!-- Layer 1 & Layer 2 Multiply Blend Modes -->
        <div class="aura-layer-1" aria-hidden="true"></div>
        <div class="aura-layer-2" aria-hidden="true"></div>

        <!-- Real Page Content Sitting Above Layers -->
        <div style="position: relative; z-index: 1;">

            <!-- =========================================================================
                 STICKY NAVIGATION BAR
                 ========================================================================= -->
            <header class="fixed top-0 left-0 right-0 z-40 glass-navbar">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-20">
                        
                        <!-- Logo & Brand -->
                        <a href="#hero" class="flex items-center gap-3 group">
                            <img src="{{ asset('UNUDLOGO.png') }}" alt="Logo Udayana" 
                                 class="w-11 h-11 object-contain drop-shadow transition-transform group-hover:scale-105">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-rose-800">Universitas Udayana</span>
                                    <span class="hidden sm:inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300">SIM-PKM</span>
                                </div>
                                <span class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight block">
                                    Pengajuan Proposal PKM
                                </span>
                            </div>
                        </a>

                        <!-- Desktop Nav Items -->
                        <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold text-slate-700">
                            <a href="#hero" class="hover:text-rose-800 transition-colors">Beranda</a>
                            <a href="#linimasa" class="hover:text-rose-800 transition-colors">Linimasa</a>
                            <a href="#skim" class="hover:text-rose-800 transition-colors">Skim PKM</a>
                            <a href="#alur" class="hover:text-rose-800 transition-colors">Alur Seleksi</a>
                            <a href="#statistik" class="hover:text-rose-800 transition-colors">Statistik</a>
                            <a href="#faq" class="hover:text-rose-800 transition-colors">Panduan & FAQ</a>
                        </nav>

                        <!-- Actions: Request Account & Sign In Button -->
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('register') }}" 
                               class="text-xs font-bold text-slate-700 hover:text-rose-800 px-3.5 py-2 rounded-xl hover:bg-rose-100/50 transition-all">
                                <i class="fas fa-search me-1 text-rose-600"></i>Cari / Request Akun
                            </a>
                            
                            <button type="button" 
                                    @click="loginDrawerOpen = true"
                                    class="btn-maroon text-white font-extrabold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer shadow-md">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Sign In</span>
                            </button>
                        </div>

                        <!-- Mobile Hamburger Button -->
                        <div class="flex sm:hidden items-center gap-2">
                            <button type="button" 
                                    @click="loginDrawerOpen = true"
                                    class="btn-maroon text-white font-bold text-xs px-3 py-2 rounded-lg flex items-center gap-1.5 shadow">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </button>
                            
                            <button type="button" 
                                    @click="mobileMenuOpen = !mobileMenuOpen"
                                    class="text-slate-700 hover:text-slate-900 p-2 rounded-lg bg-white/70 border border-slate-200">
                                <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Mobile Navigation Menu Dropdown -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="sm:hidden border-t border-rose-200/80 bg-[#faf8f2]/95 backdrop-blur-md px-4 pt-3 pb-5 space-y-2 text-sm font-semibold">
                    <a href="#hero" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Beranda</a>
                    <a href="#linimasa" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Linimasa & Jadwal</a>
                    <a href="#skim" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Skim PKM</a>
                    <a href="#alur" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Alur Seleksi</a>
                    <a href="#statistik" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Statistik</a>
                    <a href="#faq" @click="mobileMenuOpen = false" class="block py-2 text-slate-700 hover:text-rose-800">Panduan & FAQ</a>
                    <div class="pt-3 border-t border-rose-200/80 flex flex-col gap-2">
                        <a href="{{ route('register') }}" class="block text-center py-2.5 rounded-lg bg-rose-100/70 text-rose-900 text-xs font-bold">
                            Cari / Request Kredensial
                        </a>
                    </div>
                </div>
            </header>


            <!-- =========================================================================
                 SLIDE-OVER DRAWER KANAN (LOGIN CARD OVERLAY)
                 ========================================================================= -->
            <div x-show="loginDrawerOpen" 
                 x-cloak
                 class="relative z-50" 
                 aria-labelledby="slide-over-title" 
                 role="dialog" 
                 aria-modal="true">
                
                <!-- Backdrop Blur Overlay -->
                <div x-show="loginDrawerOpen"
                     x-transition:enter="ease-in-out duration-400"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in-out duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="loginDrawerOpen = false"
                     class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-6 sm:pl-10">
                            
                            <!-- Slide-in Panel Content -->
                            <div x-show="loginDrawerOpen"
                                 x-transition:enter="transform transition ease-in-out duration-400 sm:duration-500"
                                 x-transition:enter-start="translate-x-full"
                                 x-transition:enter-end="translate-x-0"
                                 x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                                 x-transition:leave-start="translate-x-0"
                                 x-transition:leave-end="translate-x-full"
                                 class="pointer-events-auto w-screen max-w-md login-drawer-panel p-6 sm:p-8 flex flex-col justify-between overflow-y-auto">
                                
                                <div>
                                    <!-- Drawer Top Header & Close Button -->
                                    <div class="flex items-center justify-between pb-5 border-b border-slate-200 mb-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-800 to-rose-950 border border-red-500/30 flex items-center justify-center p-1.5 shadow">
                                                <img src="{{ asset('UNUDLOGO.png') }}" alt="" class="w-full h-full object-contain">
                                            </div>
                                            <div>
                                                <h3 class="text-base font-black text-slate-900 leading-tight">Selamat Datang!</h3>
                                                <p class="text-xs text-slate-500">Masuk ke Sistem PKM Universitas Udayana</p>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click="loginDrawerOpen = false" 
                                                class="rounded-xl p-2 text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                                            <i class="fas fa-times text-lg"></i>
                                        </button>
                                    </div>

                                    <!-- Session Flash Messages -->
                                    @if(session('success'))
                                        <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs flex items-start gap-2.5">
                                            <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
                                            <span>{{ session('success') }}</span>
                                        </div>
                                    @endif

                                    @if(session('error'))
                                        <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs flex items-start gap-2.5">
                                            <i class="fas fa-exclamation-circle text-rose-600 mt-0.5"></i>
                                            <span>{{ session('error') }}</span>
                                        </div>
                                    @endif

                                    @if($errors->any())
                                        <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-300 text-rose-800 text-xs">
                                            <div class="font-bold mb-1 flex items-center gap-1.5">
                                                <i class="fas fa-exclamation-triangle text-rose-600"></i>
                                                <span>Gagal Masuk</span>
                                            </div>
                                            <ul class="list-disc pl-4 space-y-1">
                                                @foreach($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Login Form -->
                                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                                        @csrf

                                        <!-- Email Input -->
                                        <div>
                                            <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5 uppercase tracking-wider">
                                                Email Akun <span class="text-rose-600">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fas fa-envelope text-sm"></i>
                                                </div>
                                                <input type="email" 
                                                       id="email" 
                                                       name="email" 
                                                       value="{{ old('email') }}"
                                                       class="input-field block w-full pl-10 pr-3.5 py-2.5 rounded-xl text-sm font-medium"
                                                       placeholder="nama@student.unud.ac.id" 
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Password Input -->
                                        <div>
                                            <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5 uppercase tracking-wider">
                                                Password <span class="text-rose-600">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                    <i class="fas fa-lock text-sm"></i>
                                                </div>
                                                <input :type="showPassword ? 'text' : 'password'" 
                                                       id="password" 
                                                       name="password" 
                                                       class="input-field block w-full pl-10 pr-10 py-2.5 rounded-xl text-sm font-medium" 
                                                       placeholder="••••••••"
                                                       required>
                                                <button type="button" 
                                                        @click="showPassword = !showPassword"
                                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700">
                                                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Log In Button -->
                                        <button type="submit"
                                                class="btn-maroon w-full text-white py-3 px-4 rounded-xl text-sm font-extrabold tracking-wide focus:outline-none flex items-center justify-center gap-2 mt-2 cursor-pointer shadow-md">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Log In</span>
                                        </button>
                                    </form>

                                    <!-- Divider -->
                                    <div class="relative my-6 text-center">
                                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                                        <span class="relative bg-white px-3 text-xs text-slate-500 uppercase tracking-widest font-bold">atau bantuan</span>
                                    </div>

                                    <!-- Quick Action Buttons -->
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-[11px] text-slate-500 mb-1 text-center font-medium">Belum punya kredensial akun?</p>
                                            <a href="{{ route('register') }}"
                                               class="flex items-center justify-center w-full px-3 py-2.5 rounded-xl text-xs font-bold bg-slate-50 hover:bg-rose-50/70 border border-slate-200 hover:border-rose-300 text-slate-800 transition-colors shadow-sm">
                                                <i class="fas fa-key text-rose-600 me-2"></i>
                                                <span>Cari / Request Kredensial</span>
                                            </a>
                                        </div>

                                        <div>
                                            <p class="text-[11px] text-slate-500 mb-1 text-center font-medium">Lupa kata sandi Anda?</p>
                                            <a href="{{ route('password.forgot') }}"
                                               class="flex items-center justify-center w-full px-3 py-2.5 rounded-xl text-xs font-bold bg-slate-50 hover:bg-rose-50/70 border border-slate-200 hover:border-rose-300 text-slate-800 transition-colors shadow-sm">
                                                <i class="fas fa-unlock-alt text-amber-600 me-2"></i>
                                                <span>Reset Password Akun</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Drawer Footer -->
                                <div class="pt-6 mt-6 border-t border-slate-200 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                                    <img src="{{ asset('UNUDLOGO.png') }}" alt="" class="w-4 h-4 object-contain opacity-70">
                                    <span>Universitas Udayana © {{ date('Y') }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- =========================================================================
                 SECTION 1: HERO BANNER (AURA ROSE GOLD & GRADIENT SHIMMER HEADLINE)
                 ========================================================================= -->
            <section id="hero" class="pt-36 pb-20 px-4 sm:px-6 lg:px-8 flex items-center relative">
                <div class="max-w-7xl mx-auto w-full relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        
                        <!-- Left Hero Headline & Description -->
                        <div class="lg:col-span-8 space-y-6">
                            
                            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-rose-100/90 border border-rose-300/80 text-rose-900 text-xs font-extrabold uppercase tracking-wider shadow-sm">
                                <i class="fas fa-trophy text-amber-600"></i>
                                <span>Program Kreativitas Mahasiswa {{ date('Y') }}/{{ date('Y')+1 }}</span>
                            </div>

                            <!-- Headline with BIAsia/gradient-shimmer effect -->
                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.12]">
                                Wujudkan Inovasi, <br>
                                <span class="gradient-shimmer-headline">
                                    Raih Prestasi di PIMNAS
                                </span>
                            </h1>

                            <p class="text-base sm:text-lg text-slate-700 max-w-2xl leading-relaxed font-medium">
                                Sistem informasi digital terintegrasi Universitas Udayana untuk manajemen pengajuan, pendampingan dosen, evaluasi berjenjang oleh reviewer, serta seleksi internal proposal Program Kreativitas Mahasiswa (PKM).
                            </p>

                            <!-- Hero Action Buttons -->
                            <div class="flex flex-wrap items-center gap-4 pt-2">
                                <button type="button" 
                                        @click="loginDrawerOpen = true"
                                        class="btn-maroon text-white font-extrabold text-sm px-7 py-3.5 rounded-xl flex items-center gap-2.5 shadow-lg cursor-pointer">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Masuk ke Akun (Sign In)</span>
                                </button>

                                <a href="#skim" 
                                   class="px-6 py-3.5 rounded-xl text-sm font-bold text-slate-800 bg-white/90 hover:bg-white border border-slate-300/80 shadow-sm hover:border-rose-400 transition-all flex items-center gap-2">
                                    <i class="fas fa-layer-group text-rose-600"></i>
                                    <span>Pelajari 8 Skim PKM</span>
                                </a>

                                <a href="{{ route('register') }}" 
                                   class="px-5 py-3.5 rounded-xl text-sm font-bold text-slate-700 hover:text-rose-800 hover:bg-white/60 transition-colors flex items-center gap-2">
                                    <i class="fas fa-key text-slate-500"></i>
                                    <span>Request Kredensial</span>
                                </a>
                            </div>

                            <!-- Highlight Badges -->
                            <div class="pt-6 grid grid-cols-2 sm:grid-cols-3 gap-4 border-t border-rose-200/80 max-w-xl text-xs font-semibold text-slate-700">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-check-double text-xs"></i>
                                    </div>
                                    <span>Double-Blind Review</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </div>
                                    <span>Paperless Submission</span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-rose-100 border border-rose-300 text-rose-800 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-shield-alt text-xs"></i>
                                    </div>
                                    <span>SSO Terintegrasi</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Quick Interactive Summary Card -->
                        <div class="lg:col-span-4">
                            <div class="glass-card rounded-3xl p-6 sm:p-7 relative overflow-hidden border border-rose-200/80 shadow-xl">
                                <div class="flex items-center justify-between mb-5 pb-3 border-b border-rose-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Status Sistem Aktif</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-500">{{ date('d M Y') }}</span>
                                </div>

                                <h4 class="text-lg font-black text-slate-900 mb-2">Portal Resmi PKM Udayana</h4>
                                <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                                    Akses mudah untuk seluruh civitas akademika Universitas Udayana dalam mengelola proposal riset, kewirausahaan, dan pengabdian.
                                </p>

                                <!-- Role Access Pill Grid -->
                                <div class="space-y-2.5 mb-6 text-xs">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                                        <span class="flex items-center gap-2 text-slate-800 font-semibold">
                                            <i class="fas fa-user-graduate text-rose-600"></i> Mahasiswa Pengusul
                                        </span>
                                        <span class="text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold text-[10px]">Aktif</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                                        <span class="flex items-center gap-2 text-slate-800 font-semibold">
                                            <i class="fas fa-chalkboard-teacher text-amber-600"></i> Dosen Pendamping
                                        </span>
                                        <span class="text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold text-[10px]">Aktif</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                                        <span class="flex items-center gap-2 text-slate-800 font-semibold">
                                            <i class="fas fa-user-check text-blue-600"></i> Reviewer Ahli
                                        </span>
                                        <span class="text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold text-[10px]">Aktif</span>
                                    </div>
                                </div>

                                <button type="button" 
                                        @click="loginDrawerOpen = true"
                                        class="w-full py-3.5 rounded-xl bg-slate-900 text-white hover:bg-rose-900 font-extrabold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all cursor-pointer">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>Buka Form Login</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- =========================================================================
                 SECTION 2: JADWAL & LINIMASA RUANG KONTROL
                 ========================================================================= -->
            <section id="linimasa" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-rose-200/60 relative">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-800 bg-rose-100 border border-rose-300 px-4 py-1.5 rounded-full inline-block mb-3">
                            Linimasa & Jadwal Seleksi
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            Fase Pelaksanaan PKM Saat Ini
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                            Setiap tahapan seleksi PKM dijalankan secara terkoordinasi melalui Ruang Kontrol Pimpinan & Operator Universitas Udayana.
                        </p>
                    </div>

                    <!-- 4 Phase Timeline Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Fase 1: Pendaftaran -->
                        @php
                            $isFase1Active = isset($ruangKontrol) && $ruangKontrol->status_pendaftaran === 'terbuka';
                        @endphp
                        <div class="glass-card rounded-2xl p-6 relative flex flex-col justify-between {{ $isFase1Active ? 'border-emerald-500/80 ring-2 ring-emerald-500/20' : '' }}">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-rose-50 text-rose-900 border border-rose-200">Fase 1</span>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $isFase1Active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $isFase1Active ? 'DIBUKA' : 'DITUTUP' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">Pendaftaran Proposal</h3>
                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Pengajuan draf proposal PKM oleh tim mahasiswa serta persetujuan resmi oleh dosen pendamping.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
                                <i class="far fa-calendar-alt text-rose-600"></i>
                                <span>
                                    @if(isset($ruangKontrol) && $ruangKontrol->tanggal_pendaftaran_mulai)
                                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') }}
                                    @else
                                        Mengikuti Jadwal Resmi
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Fase 2: Review -->
                        @php
                            $isFase2Active = isset($ruangKontrol) && $ruangKontrol->status_review === 'terbuka';
                        @endphp
                        <div class="glass-card rounded-2xl p-6 relative flex flex-col justify-between {{ $isFase2Active ? 'border-emerald-500/80 ring-2 ring-emerald-500/20' : '' }}">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-rose-50 text-rose-900 border border-rose-200">Fase 2</span>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $isFase2Active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $isFase2Active ? 'DIBUKA' : 'DITUTUP' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">Review Proposal</h3>
                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Penilaian administratif dan substantif independen oleh reviewer ahli yang ditugaskan universitas.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
                                <i class="far fa-calendar-alt text-rose-600"></i>
                                <span>
                                    @if(isset($ruangKontrol) && $ruangKontrol->tanggal_review_mulai)
                                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_review_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_review_selesai)->format('d M Y') }}
                                    @else
                                        Tahap Evaluasi Ahli
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Fase 3: Perbaikan & Revisi -->
                        @php
                            $isFase3Active = isset($ruangKontrol) && $ruangKontrol->status_perbaikan === 'terbuka';
                        @endphp
                        <div class="glass-card rounded-2xl p-6 relative flex flex-col justify-between {{ $isFase3Active ? 'border-emerald-500/80 ring-2 ring-emerald-500/20' : '' }}">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-rose-50 text-rose-900 border border-rose-200">Fase 3</span>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $isFase3Active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $isFase3Active ? 'DIBUKA' : 'DITUTUP' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">Perbaikan & Revisi</h3>
                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Penyempurnaan proposal oleh tim mahasiswa berdasarkan masukan dan catatan reviewer.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
                                <i class="far fa-calendar-alt text-rose-600"></i>
                                <span>
                                    @if(isset($ruangKontrol) && $ruangKontrol->tanggal_perbaikan_mulai)
                                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                                    @else
                                        Tahap Penyempurnaan
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Fase 4: Penilaian Akhir -->
                        @php
                            $isFase4Active = isset($ruangKontrol) && $ruangKontrol->status_penilaian_akhir === 'terbuka';
                        @endphp
                        <div class="glass-card rounded-2xl p-6 relative flex flex-col justify-between {{ $isFase4Active ? 'border-emerald-500/80 ring-2 ring-emerald-500/20' : '' }}">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-rose-50 text-rose-900 border border-rose-200">Fase 4</span>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider {{ $isFase4Active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $isFase4Active ? 'DIBUKA' : 'DITUTUP' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">Penilaian Akhir & Lolos</h3>
                                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                    Penetapan hasil semi final & final oleh Pimpinan PT untuk rekomendasi unggah ke Simbelmawa.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-2">
                                <i class="far fa-calendar-alt text-rose-600"></i>
                                <span>
                                    @if(isset($ruangKontrol) && $ruangKontrol->tanggal_penilaian_akhir_mulai)
                                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_selesai)->format('d M Y') }}
                                    @else
                                        Seleksi Akhir Belmawa
                                    @endif
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- =========================================================================
                 SECTION 3: SKIM PKM YANG DIDUKUNG
                 ========================================================================= -->
            <section id="skim" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-rose-200/60 relative">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-800 bg-rose-100 border border-rose-300 px-4 py-1.5 rounded-full inline-block mb-3">
                            Klasifikasi Bidang
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            8 Skim Program Kreativitas Mahasiswa
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                            Pilih bidang skim yang sesuai dengan keahlian, minat riset, dan fokus karya inovasi kelompok Anda.
                        </p>
                    </div>

                    <!-- Skim Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- 1. PKM-RE -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-rose-100 border border-rose-300 flex items-center justify-center text-rose-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-atom"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-rose-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-RE (Riset Eksakta)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Penelitian ilmiah untuk membuktikan hipotesis, fenomena sains, teknologi rekayasa, kedokteran, dan ilmu hayati.
                                </p>
                            </div>
                        </div>

                        <!-- 2. PKM-RSH -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-purple-100 border border-purple-300 flex items-center justify-center text-purple-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-purple-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-RSH (Sosial Humaniora)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Riset fenomena sosial masyarakat, kearifan budaya lokal Bali, hukum, ekonomi, dan psikologi perilaku.
                                </p>
                            </div>
                        </div>

                        <!-- 3. PKM-K -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-K (Kewirausahaan)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Pengembangan produk atau jasa komersial inovatif berbasis sains dan teknologi yang siap bersaing di pasar.
                                </p>
                            </div>
                        </div>

                        <!-- 4. PKM-PM -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-hands-helping"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-PM (Pengabdian Masy.)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Bantuan IPTEK aplikatif untuk mitra non-produktif seperti panti asuhan, sekolah inklusi, desa, dan kelompok sosial.
                                </p>
                            </div>
                        </div>

                        <!-- 5. PKM-PI -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center text-blue-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-blue-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-PI (Penerapan IPTEK)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Solusi teknologi untuk meningkatkan efisiensi dan profitabilitas mitra usaha mikro, kecil, atau menengah (UMKM).
                                </p>
                            </div>
                        </div>

                        <!-- 6. PKM-KC -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-cyan-100 border border-cyan-300 flex items-center justify-center text-cyan-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-cyan-700 uppercase tracking-wider">Pendanaan</span>
                                    <span class="text-slate-500 text-xs">• Max 8 Juta</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-KC (Karsa Cipta)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Penciptaan karya desain fungsional, prototipe rekayasa, atau perangkat lunak berbasis karsa inovatif.
                                </p>
                            </div>
                        </div>

                        <!-- 7. PKM-AI -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-teal-100 border border-teal-300 flex items-center justify-center text-teal-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-teal-700 uppercase tracking-wider">Insentif</span>
                                    <span class="text-slate-500 text-xs">• Karya Selesai</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-AI (Artikel Ilmiah)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Penyusunan artikel ilmiah terstruktur dari hasil kegiatan akademik mandiri (misal magang, KKN, atau penelitian).
                                </p>
                            </div>
                        </div>

                        <!-- 8. PKM-GFT -->
                        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-rose-100 border border-rose-300 flex items-center justify-center text-rose-700 text-xl mb-4 shadow-sm">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs font-extrabold text-rose-700 uppercase tracking-wider">Insentif</span>
                                    <span class="text-slate-500 text-xs">• Gagasan Visioner</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">PKM-GFT (Gagasan Futuristik)</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Karya tulis visioner untuk merespons isu global strategis jangka panjang dan Tujuan Pembangunan Berkelanjutan (SDGs).
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- =========================================================================
                 SECTION 4: ALUR & TAHAPAN PENGAJUAN (SERPENTINE WAVY ROADMAP)
                 ========================================================================= -->
            <section id="alur" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-rose-200/60 relative overflow-hidden">
                <div class="max-w-7xl mx-auto relative z-10">
                    
                    <!-- Section Header -->
                    <div class="text-center max-w-3xl mx-auto mb-14">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-800 bg-rose-100 border border-rose-300 px-4 py-1.5 rounded-full inline-flex items-center gap-2 mb-3">
                            <i class="fas fa-route text-rose-600"></i>
                            <span>Roadmap Seleksi & Kurasi</span>
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            Alur Seleksi Proposal PKM Udayana
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                            Klik pada setiap <span class="text-rose-800 font-extrabold">lingkaran tahapan</span> untuk membuka kejelasan detail, aktor yang terlibat, dokumen yang dibutuhkan, serta kunci kelolosannya.
                        </p>
                    </div>

                    <!-- Serpentine Wavy Roadmap -->
                    <div class="overflow-x-auto pb-6 pt-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                        <div class="min-w-[960px] max-w-5xl mx-auto relative h-[370px] select-none">
                            
                            <!-- SVG S-Wave Curves & Dashed Connectors -->
                            <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 1000 370" fill="none" preserveAspectRatio="none">
                                <defs>
                                    <filter id="glowRoseLight" x="-30%" y="-30%" width="160%" height="160%">
                                        <feDropShadow dx="0" dy="0" stdDeviation="6" flood-color="#E11D48" flood-opacity="0.6"/>
                                    </filter>
                                </defs>

                                <!-- Dashed Connecting Lines (Diagonals between node arcs) -->
                                <!-- Dash 1 to 2: UP-RIGHT -->
                                <line x1="150" y1="214" x2="250" y2="156" stroke="#94a3b8" stroke-width="3" stroke-dasharray="6,6" stroke-linecap="round" />
                                <!-- Dash 2 to 3: DOWN-RIGHT -->
                                <line x1="350" y1="156" x2="450" y2="214" stroke="#94a3b8" stroke-width="3" stroke-dasharray="6,6" stroke-linecap="round" />
                                <!-- Dash 3 to 4: UP-RIGHT -->
                                <line x1="550" y1="214" x2="650" y2="156" stroke="#94a3b8" stroke-width="3" stroke-dasharray="6,6" stroke-linecap="round" />
                                <!-- Dash 4 to 5: DOWN-RIGHT -->
                                <line x1="750" y1="156" x2="850" y2="214" stroke="#94a3b8" stroke-width="3" stroke-dasharray="6,6" stroke-linecap="round" />

                                <!-- Arc 1: Under Node 1 (Center 100, 185) -->
                                <path d="M 50 214 A 58 58 0 0 0 150 214" 
                                      :stroke="activeAlurStep === 1 ? '#E11D48' : '#1E3A5F'" 
                                      :filter="activeAlurStep === 1 ? 'url(#glowRoseLight)' : ''"
                                      :stroke-width="activeAlurStep === 1 ? '13' : '10'" 
                                      stroke-linecap="round" 
                                      class="transition-all duration-300" />

                                <!-- Arc 2: Over Node 2 (Center 300, 185) -->
                                <path d="M 250 156 A 58 58 0 0 1 350 156" 
                                      :stroke="activeAlurStep === 2 ? '#E11D48' : '#204A7D'" 
                                      :filter="activeAlurStep === 2 ? 'url(#glowRoseLight)' : ''"
                                      :stroke-width="activeAlurStep === 2 ? '13' : '10'" 
                                      stroke-linecap="round" 
                                      class="transition-all duration-300" />

                                <!-- Arc 3: Under Node 3 (Center 500, 185) -->
                                <path d="M 450 214 A 58 58 0 0 0 550 214" 
                                      :stroke="activeAlurStep === 3 ? '#E11D48' : '#3A6B9B'" 
                                      :filter="activeAlurStep === 3 ? 'url(#glowRoseLight)' : ''"
                                      :stroke-width="activeAlurStep === 3 ? '13' : '10'" 
                                      stroke-linecap="round" 
                                      class="transition-all duration-300" />

                                <!-- Arc 4: Over Node 4 (Center 700, 185) -->
                                <path d="M 650 156 A 58 58 0 0 1 750 156" 
                                      :stroke="activeAlurStep === 4 ? '#E11D48' : '#5386B4'" 
                                      :filter="activeAlurStep === 4 ? 'url(#glowRoseLight)' : ''"
                                      :stroke-width="activeAlurStep === 4 ? '13' : '10'" 
                                      stroke-linecap="round" 
                                      class="transition-all duration-300" />

                                <!-- Arc 5: Under Node 5 (Center 900, 185) -->
                                <path d="M 850 214 A 58 58 0 0 0 950 214" 
                                      :stroke="activeAlurStep === 5 ? '#E11D48' : '#78A6CC'" 
                                      :filter="activeAlurStep === 5 ? 'url(#glowRoseLight)' : ''"
                                      :stroke-width="activeAlurStep === 5 ? '13' : '10'" 
                                      stroke-linecap="round" 
                                      class="transition-all duration-300" />
                            </svg>

                            <!-- Node 1: Bentuk Tim & Registrasi -->
                            <button type="button" 
                                    @click="activeAlurStep = 1"
                                    class="absolute rounded-full flex flex-col items-center justify-center transition-all duration-300 group z-20 cursor-pointer focus:outline-none"
                                    :class="activeAlurStep === 1 
                                        ? 'ring-4 ring-rose-500 shadow-[0_0_32px_rgba(225,29,72,0.45)] scale-110 bg-white' 
                                        : 'bg-white hover:scale-105 shadow-xl hover:shadow-2xl border border-slate-200'"
                                    style="top: 185px; left: 10%; width: 84px; height: 84px; transform: translate(-50%, -50%);"
                                    title="Klik untuk melihat kejelasan Tahap 1">
                                <i class="fas fa-search text-2xl transition-colors duration-200"
                                   :class="activeAlurStep === 1 ? 'text-rose-600' : 'text-slate-700 group-hover:text-rose-600'"></i>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center shadow"
                                      :class="activeAlurStep === 1 ? 'bg-rose-600 text-white ring-2 ring-white' : 'bg-slate-800 text-slate-100 border border-white/40'">
                                    1
                                </span>
                            </button>
                            <div @click="activeAlurStep = 1" 
                                 class="absolute text-center max-w-[170px] cursor-pointer transition-all duration-300 group z-20"
                                 style="top: 262px; left: 10%; transform: translateX(-50%);">
                                <h4 class="text-sm font-black leading-snug transition-colors"
                                    :class="activeAlurStep === 1 ? 'text-rose-700' : 'text-slate-900 group-hover:text-rose-700'">
                                    Bentuk Tim & Registrasi
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-1 leading-tight line-clamp-3">
                                    Bentuk tim 3-5 mhs & unggah draf ide di portal.
                                </p>
                            </div>

                            <!-- Node 2: Validasi Dosen (Text ABOVE) -->
                            <div @click="activeAlurStep = 2" 
                                 class="absolute text-center max-w-[170px] cursor-pointer transition-all duration-300 group z-20"
                                 style="top: 22px; left: 30%; transform: translateX(-50%);">
                                <h4 class="text-sm font-black leading-snug transition-colors"
                                    :class="activeAlurStep === 2 ? 'text-rose-700' : 'text-slate-900 group-hover:text-rose-700'">
                                    Validasi Dosen
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-1 leading-tight line-clamp-3">
                                    Pemeriksaan substansi & approval digital pembimbing.
                                </p>
                            </div>
                            <button type="button" 
                                    @click="activeAlurStep = 2"
                                    class="absolute rounded-full flex flex-col items-center justify-center transition-all duration-300 group z-20 cursor-pointer focus:outline-none"
                                    :class="activeAlurStep === 2 
                                        ? 'ring-4 ring-rose-500 shadow-[0_0_32px_rgba(225,29,72,0.45)] scale-110 bg-white' 
                                        : 'bg-white hover:scale-105 shadow-xl hover:shadow-2xl border border-slate-200'"
                                    style="top: 185px; left: 30%; width: 84px; height: 84px; transform: translate(-50%, -50%);"
                                    title="Klik untuk melihat kejelasan Tahap 2">
                                <i class="fas fa-file-invoice text-2xl transition-colors duration-200"
                                   :class="activeAlurStep === 2 ? 'text-rose-600' : 'text-slate-700 group-hover:text-rose-600'"></i>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center shadow"
                                      :class="activeAlurStep === 2 ? 'bg-rose-600 text-white ring-2 ring-white' : 'bg-slate-800 text-slate-100 border border-white/40'">
                                    2
                                </span>
                            </button>

                            <!-- Node 3: Review Bertingkat (Text BELOW) -->
                            <button type="button" 
                                    @click="activeAlurStep = 3"
                                    class="absolute rounded-full flex flex-col items-center justify-center transition-all duration-300 group z-20 cursor-pointer focus:outline-none"
                                    :class="activeAlurStep === 3 
                                        ? 'ring-4 ring-rose-500 shadow-[0_0_32px_rgba(225,29,72,0.45)] scale-110 bg-white' 
                                        : 'bg-white hover:scale-105 shadow-xl hover:shadow-2xl border border-slate-200'"
                                    style="top: 185px; left: 50%; width: 84px; height: 84px; transform: translate(-50%, -50%);"
                                    title="Klik untuk melihat kejelasan Tahap 3">
                                <i class="fas fa-users-gear text-2xl transition-colors duration-200"
                                   :class="activeAlurStep === 3 ? 'text-rose-600' : 'text-slate-700 group-hover:text-rose-600'"></i>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center shadow"
                                      :class="activeAlurStep === 3 ? 'bg-rose-600 text-white ring-2 ring-white' : 'bg-slate-800 text-slate-100 border border-white/40'">
                                    3
                                </span>
                            </button>
                            <div @click="activeAlurStep = 3" 
                                 class="absolute text-center max-w-[170px] cursor-pointer transition-all duration-300 group z-20"
                                 style="top: 262px; left: 50%; transform: translateX(-50%);">
                                <h4 class="text-sm font-black leading-snug transition-colors"
                                    :class="activeAlurStep === 3 ? 'text-rose-700' : 'text-slate-900 group-hover:text-rose-700'">
                                    Review Bertingkat
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-1 leading-tight line-clamp-3">
                                    Penilaian administrasi & rubrik substantif Belmawa.
                                </p>
                            </div>

                            <!-- Node 4: Revisi Terpadu (Text ABOVE) -->
                            <div @click="activeAlurStep = 4" 
                                 class="absolute text-center max-w-[170px] cursor-pointer transition-all duration-300 group z-20"
                                 style="top: 22px; left: 70%; transform: translateX(-50%);">
                                <h4 class="text-sm font-black leading-snug transition-colors"
                                    :class="activeAlurStep === 4 ? 'text-rose-700' : 'text-slate-900 group-hover:text-rose-700'">
                                    Revisi Terpadu
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-1 leading-tight line-clamp-3">
                                    Penyempurnaan naskah sesuai catatan reviewer.
                                </p>
                            </div>
                            <button type="button" 
                                    @click="activeAlurStep = 4"
                                    class="absolute rounded-full flex flex-col items-center justify-center transition-all duration-300 group z-20 cursor-pointer focus:outline-none"
                                    :class="activeAlurStep === 4 
                                        ? 'ring-4 ring-rose-500 shadow-[0_0_32px_rgba(225,29,72,0.45)] scale-110 bg-white' 
                                        : 'bg-white hover:scale-105 shadow-xl hover:shadow-2xl border border-slate-200'"
                                    style="top: 185px; left: 70%; width: 84px; height: 84px; transform: translate(-50%, -50%);"
                                    title="Klik untuk melihat kejelasan Tahap 4">
                                <i class="fas fa-file-pen text-2xl transition-colors duration-200"
                                   :class="activeAlurStep === 4 ? 'text-rose-600' : 'text-slate-700 group-hover:text-rose-600'"></i>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center shadow"
                                      :class="activeAlurStep === 4 ? 'bg-rose-600 text-white ring-2 ring-white' : 'bg-slate-800 text-slate-100 border border-white/40'">
                                    4
                                </span>
                            </button>

                            <!-- Node 5: Seleksi & Lolos PT (Text BELOW) -->
                            <button type="button" 
                                    @click="activeAlurStep = 5"
                                    class="absolute rounded-full flex flex-col items-center justify-center transition-all duration-300 group z-20 cursor-pointer focus:outline-none"
                                    :class="activeAlurStep === 5 
                                        ? 'ring-4 ring-rose-500 shadow-[0_0_32px_rgba(225,29,72,0.45)] scale-110 bg-white' 
                                        : 'bg-white hover:scale-105 shadow-xl hover:shadow-2xl border border-slate-200'"
                                    style="top: 185px; left: 90%; width: 84px; height: 84px; transform: translate(-50%, -50%);"
                                    title="Klik untuk melihat kejelasan Tahap 5">
                                <i class="fas fa-trophy text-2xl transition-colors duration-200"
                                   :class="activeAlurStep === 5 ? 'text-rose-600' : 'text-slate-700 group-hover:text-rose-600'"></i>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center shadow"
                                      :class="activeAlurStep === 5 ? 'bg-rose-600 text-white ring-2 ring-white' : 'bg-slate-800 text-slate-100 border border-white/40'">
                                    5
                                </span>
                            </button>
                            <div @click="activeAlurStep = 5" 
                                 class="absolute text-center max-w-[170px] cursor-pointer transition-all duration-300 group z-20"
                                 style="top: 262px; left: 90%; transform: translateX(-50%);">
                                <h4 class="text-sm font-black leading-snug transition-colors"
                                    :class="activeAlurStep === 5 ? 'text-rose-700' : 'text-slate-900 group-hover:text-rose-700'">
                                    Seleksi & Lolos PT
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-1 leading-tight line-clamp-3">
                                    Penetapan kuota unggah Simbelmawa & PIMNAS.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Panel Kejelasan Tahapan (Interaktif) -->
                    <div class="mt-8">
                        
                        <!-- Pill Quick-Switcher -->
                        <div class="flex items-center justify-center gap-2 mb-6 flex-wrap">
                            <span class="text-xs font-bold text-slate-600 me-1.5 flex items-center gap-1.5">
                                <i class="fas fa-hand-pointer text-rose-600"></i> Pilih Kejelasan Tahapan:
                            </span>
                            <button type="button" 
                                    @click="activeAlurStep = 1"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    :class="activeAlurStep === 1 
                                        ? 'bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-md ring-2 ring-rose-400/50' 
                                        : 'bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 shadow-sm'">
                                <span class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center bg-white/20">1</span>
                                <span>Tim & Registrasi</span>
                            </button>
                            <button type="button" 
                                    @click="activeAlurStep = 2"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    :class="activeAlurStep === 2 
                                        ? 'bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-md ring-2 ring-rose-400/50' 
                                        : 'bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 shadow-sm'">
                                <span class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center bg-white/20">2</span>
                                <span>Validasi Dosen</span>
                            </button>
                            <button type="button" 
                                    @click="activeAlurStep = 3"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    :class="activeAlurStep === 3 
                                        ? 'bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-md ring-2 ring-rose-400/50' 
                                        : 'bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 shadow-sm'">
                                <span class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center bg-white/20">3</span>
                                <span>Review Evaluator</span>
                            </button>
                            <button type="button" 
                                    @click="activeAlurStep = 4"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    :class="activeAlurStep === 4 
                                        ? 'bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-md ring-2 ring-rose-400/50' 
                                        : 'bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 shadow-sm'">
                                <span class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center bg-white/20">4</span>
                                <span>Revisi Terpadu</span>
                            </button>
                            <button type="button" 
                                    @click="activeAlurStep = 5"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    :class="activeAlurStep === 5 
                                        ? 'bg-gradient-to-r from-red-600 to-rose-700 text-white shadow-md ring-2 ring-rose-400/50' 
                                        : 'bg-white hover:bg-rose-50 text-slate-700 border border-slate-200 shadow-sm'">
                                <span class="w-4 h-4 rounded-full text-[10px] flex items-center justify-center bg-white/20">5</span>
                                <span>Seleksi & Lolos PT</span>
                            </button>
                        </div>

                        <!-- Explanation Card Container -->
                        <div class="glass-card rounded-3xl p-6 sm:p-8 lg:p-10 border border-rose-200/80 shadow-xl relative overflow-hidden transition-all duration-300">
                            
                            <!-- Top Subtle Gradient Glow Line -->
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500"></div>

                            <!-- DETAIL TAHAP 1: TIM & REGISTRASI -->
                            <div x-show="activeAlurStep === 1"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-6">
                                
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-rose-100">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-rose-600 text-white uppercase tracking-wider">Tahap 01 dari 05</span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-rose-100 text-rose-900 border border-rose-200">Aktor: Tim Mahasiswa Pengusul</span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900">
                                            Pembentukan Tim & Registrasi Proposal Awal
                                        </h3>
                                        <p class="text-sm text-slate-600 font-medium mt-2 max-w-3xl leading-relaxed">
                                            Tahap inisiasi di mana mahasiswa Universitas Udayana membentuk kelompok kerja multidisiplin, merumuskan ide riset inovatif, dan mendaftarkan judul proposal ke dalam SIM-PKM.
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" @click="loginDrawerOpen = true" class="btn-maroon text-white text-xs font-extrabold uppercase tracking-wider px-5 py-3 rounded-xl flex items-center gap-2 shadow">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Mulai Usulkan Proposal</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- 3 Detail Information Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-rose-50/60 rounded-2xl p-5 border border-rose-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-rose-100 border border-rose-300 flex items-center justify-center text-rose-700 shadow-sm">
                                            <i class="fas fa-users text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Ketentuan Keanggotaan Tim</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Terdiri dari 1 Ketua dan 2–4 Anggota mahasiswa aktif D3/D4/S1.</li>
                                            <li>Disarankan kolaborasi lintas Program Studi/Fakultas untuk menambah nilai interdisipliner.</li>
                                            <li>Seluruh anggota wajib memiliki akun SIM-PKM yang terverifikasi.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shadow-sm">
                                            <i class="fas fa-file-arrow-up text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Tindakan di Portal SIM-PKM</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Ketua login dan memilih salah satu dari 8 skim PKM Belmawa.</li>
                                            <li>Menginput data anggota dengan memasukkan NIM masing-masing.</li>
                                            <li>Memilih Dosen Pembimbing ber-NIDN/NUPTK aktif di Universitas Udayana.</li>
                                            <li>Mengunggah draf naskah proposal awal dalam format PDF (maks 5MB).</li>
                                        </ul>
                                    </div>

                                    <div class="bg-emerald-50/60 rounded-2xl p-5 border border-emerald-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-sm">
                                            <i class="fas fa-bullseye text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Kunci Kelolosan Tahap 1</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Judul padat, jelas, tidak menggunakan akronim yang tidak baku.</li>
                                            <li>Kesesuaian topik dengan kriteria skim (contoh: PKM-RE murni riset eksakta).</li>
                                            <li>Proposal harus sudah terunggah sebelum tenggat waktu pendaftaran ditutup.</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Footer Navigation -->
                                <div class="pt-4 flex items-center justify-between text-xs text-slate-500 border-t border-rose-100">
                                    <span class="flex items-center gap-2 font-medium">
                                        <i class="fas fa-info-circle text-rose-600"></i>
                                        Proposal yang terkirim akan otomatis diteruskan ke Dosen Pembimbing.
                                    </span>
                                    <button type="button" @click="activeAlurStep = 2" class="text-rose-800 hover:text-rose-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <span>Lihat Tahap 2: Validasi Dosen</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- DETAIL TAHAP 2: VALIDASI DOSEN -->
                            <div x-show="activeAlurStep === 2"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 style="display: none;"
                                 class="space-y-6">
                                
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-rose-100">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-blue-600 text-white uppercase tracking-wider">Tahap 02 dari 05</span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-blue-100 text-blue-900 border border-blue-200">Aktor: Dosen Pembimbing (NIDN/NUPTK)</span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900">
                                            Bimbingan & Persetujuan Digital Dosen Pendamping
                                        </h3>
                                        <p class="text-sm text-slate-600 font-medium mt-2 max-w-3xl leading-relaxed">
                                            Dosen pembimbing menelaah kelayakan saintifik, orisinalitas riset, serta kesesuaian anggaran sebelum memberikan persetujuan formal digital pada sistem.
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" @click="loginDrawerOpen = true" class="btn-maroon text-white text-xs font-extrabold uppercase tracking-wider px-5 py-3 rounded-xl flex items-center gap-2 shadow">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Login Akun Dosen</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-blue-50/60 rounded-2xl p-5 border border-blue-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center text-blue-700 shadow-sm">
                                            <i class="fas fa-glasses text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Aspek Penelaahan Pembimbing</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Relevansi kepakaran dosen dengan fokus riset tim mahasiswa.</li>
                                            <li>Kesesuaian metode pelaksanaan dengan waktu penelitian (3–4 bulan).</li>
                                            <li>Kelayakan Rencana Anggaran Biaya (RAB) sesuai pagu Belmawa (maks Rp 8-10 Juta).</li>
                                        </ul>
                                    </div>

                                    <div class="bg-cyan-50/60 rounded-2xl p-5 border border-cyan-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-cyan-100 border border-cyan-300 flex items-center justify-center text-cyan-700 shadow-sm">
                                            <i class="fas fa-signature text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Aksi Approval di Dashboard</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Dosen melihat daftar usulan yang menunjuk dirinya sebagai pembimbing.</li>
                                            <li>Dapat memberikan catatan masukan awal kepada ketua tim jika diperlukan revisi.</li>
                                            <li>Mengklik tombol <strong>"Setujui Usulan"</strong> untuk pengesahan formal digital.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-emerald-50/60 rounded-2xl p-5 border border-emerald-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-sm">
                                            <i class="fas fa-check-double text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Prasyarat Lolos ke Reviewer</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Proposal hanya dapat dinilai reviewer jika telah berstatus <strong>"Disetujui Dosen"</strong>.</li>
                                            <li>Persetujuan harus diberikan sebelum batas akhir penutupan pendaftaran.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="pt-4 flex items-center justify-between text-xs text-slate-500 border-t border-rose-100">
                                    <button type="button" @click="activeAlurStep = 1" class="text-slate-700 hover:text-slate-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Tahap 1: Registrasi Tim</span>
                                    </button>
                                    <button type="button" @click="activeAlurStep = 3" class="text-rose-800 hover:text-rose-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <span>Lihat Tahap 3: Review Bertingkat</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- DETAIL TAHAP 3: REVIEW BERTINGKAT -->
                            <div x-show="activeAlurStep === 3"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 style="display: none;"
                                 class="space-y-6">
                                
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-rose-100">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-indigo-600 text-white uppercase tracking-wider">Tahap 03 dari 05</span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-900 border border-indigo-200">Aktor: Tim Reviewer Internal Independen</span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900">
                                            Evaluasi Administratif & Penilaian Substantif Berjenjang
                                        </h3>
                                        <p class="text-sm text-slate-600 font-medium mt-2 max-w-3xl leading-relaxed">
                                            Penilaian ganda secara independen menggunakan format rubrik baku Belmawa untuk memastikan kelayakan administratif, mutu inovasi, dan akurasi metodologi.
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" @click="loginDrawerOpen = true" class="btn-maroon text-white text-xs font-extrabold uppercase tracking-wider px-5 py-3 rounded-xl flex items-center gap-2 shadow">
                                            <i class="fas fa-sign-in-alt"></i>
                                            <span>Login Akun Reviewer</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-indigo-50/60 rounded-2xl p-5 border border-indigo-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-100 border border-indigo-300 flex items-center justify-center text-indigo-700 shadow-sm">
                                            <i class="fas fa-clipboard-list text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Tahap 1: Seleksi Administrasi</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Kesesuaian jumlah halaman inti (maks 10-15 hal sesuai skim).</li>
                                            <li>Format margin (4-3-3-3), jenis font, spasi 1.15, dan penomoran halaman.</li>
                                            <li>Kelengkapan berkas: biodata ketua/anggota/dosen, surat pernyataan ketua.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-purple-50/60 rounded-2xl p-5 border border-purple-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-100 border border-purple-300 flex items-center justify-center text-purple-700 shadow-sm">
                                            <i class="fas fa-brain text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Tahap 2: Rubrik Substantif</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Kebaruan (*novelty*) gagasan inovasi teknologi / solusi sosial.</li>
                                            <li>Ketepatan metode pemecahan masalah dan ketercapaian target luaran.</li>
                                            <li>Potensi keberlanjutan produk, paten, publikasi jurnal, atau dampak kemasyarakatan.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-emerald-50/60 rounded-2xl p-5 border border-emerald-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-sm">
                                            <i class="fas fa-comment-dots text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Output Evaluasi Reviewer</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Skor kuantitatif otomatis terakumulasi dalam sistem database.</li>
                                            <li>Catatan dan masukan kritis terperinci untuk digunakan tim pada tahap revisi.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="pt-4 flex items-center justify-between text-xs text-slate-500 border-t border-rose-100">
                                    <button type="button" @click="activeAlurStep = 2" class="text-slate-700 hover:text-slate-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Tahap 2: Validasi Dosen</span>
                                    </button>
                                    <button type="button" @click="activeAlurStep = 4" class="text-rose-800 hover:text-rose-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <span>Lihat Tahap 4: Revisi Terpadu</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- DETAIL TAHAP 4: REVISI TERPADU -->
                            <div x-show="activeAlurStep === 4"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 style="display: none;"
                                 class="space-y-6">
                                
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-rose-100">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-amber-600 text-white uppercase tracking-wider">Tahap 04 dari 05</span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 border border-amber-200">Aktor: Tim Mahasiswa & Dosen Pembimbing</span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900">
                                            Perbaikan & Penyempurnaan Naskah Terpadu
                                        </h3>
                                        <p class="text-sm text-slate-600 font-medium mt-2 max-w-3xl leading-relaxed">
                                            Tim mahasiswa mempelajari catatan masukan reviewer, memperbaiki dokumen proposal didampingi dosen pembimbing, dan mengunggah kembali naskah versi final ke sistem.
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" @click="loginDrawerOpen = true" class="btn-maroon text-white text-xs font-extrabold uppercase tracking-wider px-5 py-3 rounded-xl flex items-center gap-2 shadow">
                                            <i class="fas fa-upload"></i>
                                            <span>Unggah Berkas Revisi</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shadow-sm">
                                            <i class="fas fa-file-circle-check text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Membedah Catatan Reviewer</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Ketua tim mengunduh lembar hasil penilaian dan komentar reviewer.</li>
                                            <li>Mengidentifikasi poin-poin krusial yang memerlukan penguatan metode / RAB.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-rose-50/60 rounded-2xl p-5 border border-rose-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-rose-100 border border-rose-300 flex items-center justify-center text-rose-700 shadow-sm">
                                            <i class="fas fa-tools text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Pendampingan Perbaikan</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Konsultasi intensif dengan dosen pembimbing terhadap naskah revisi.</li>
                                            <li>Penyusunan matriks tanggapan perbaikan (*response to reviewer notes*).</li>
                                            <li>Memastikan kepatuhan format Belmawa 100% tanpa kesalahan redaksional.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-emerald-50/60 rounded-2xl p-5 border border-emerald-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-sm">
                                            <i class="fas fa-clock text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Batas Waktu Pengunggahan</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Wajib diunggah sebelum tenggat waktu <strong>Fase Perbaikan</strong> ditutup di SIM-PKM.</li>
                                            <li>Proposal yang tidak mengunggah revisi akan dinilai berdasarkan draf awal.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="pt-4 flex items-center justify-between text-xs text-slate-500 border-t border-rose-100">
                                    <button type="button" @click="activeAlurStep = 3" class="text-slate-700 hover:text-slate-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Tahap 3: Review Bertingkat</span>
                                    </button>
                                    <button type="button" @click="activeAlurStep = 5" class="text-rose-800 hover:text-rose-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <span>Lihat Tahap 5: Seleksi Akhir PT</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- DETAIL TAHAP 5: SELEKSI & LOLOS PT -->
                            <div x-show="activeAlurStep === 5"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 style="display: none;"
                                 class="space-y-6">
                                
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-rose-100">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-black px-2.5 py-1 rounded-md bg-emerald-600 text-white uppercase tracking-wider">Tahap 05 dari 05</span>
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-900 border border-emerald-200">Aktor: Pimpinan PT (Wakil Rektor) & Operator SIM-PKM</span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-black text-slate-900">
                                            Penetapan Hasil Final, Kuota Universitas & Menuju PIMNAS
                                        </h3>
                                        <p class="text-sm text-slate-600 font-medium mt-2 max-w-3xl leading-relaxed">
                                            Pleno pimpinan perguruan tinggi untuk menetapkan proposal unggulan terbaik yang berhak mendapatkan akun dan rekomendasi unggah resmi ke portal nasional Simbelmawa Kemendikbudristek.
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="#linimasa" class="btn-maroon text-white text-xs font-extrabold uppercase tracking-wider px-5 py-3 rounded-xl flex items-center gap-2 shadow">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>Cek Jadwal Pengumuman</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="bg-emerald-50/60 rounded-2xl p-5 border border-emerald-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-700 shadow-sm">
                                            <i class="fas fa-ranking-star text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Perangkingan & Kuota PT</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Penyusunan peringkat skor gabungan (skor review + skor revisi final).</li>
                                            <li>Penetapan kuota resmi per skim sesuai kuota PT yang diberikan Belmawa.</li>
                                            <li>Penerbitan Surat Keputusan (SK) Lolos Internal oleh Rektorat.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-cyan-50/60 rounded-2xl p-5 border border-cyan-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-cyan-100 border border-cyan-300 flex items-center justify-center text-cyan-700 shadow-sm">
                                            <i class="fas fa-key text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Pemberian Akun Simbelmawa</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Operator mendaftarkan akun Simbelmawa nasional untuk ketua tim yang lolos.</li>
                                            <li>Ketua tim melakukan unggah berkas akhir ke portal resmi Simbelmawa.</li>
                                            <li>Pemberian lembar pengesahan stempel resmi pimpinan perguruan tinggi.</li>
                                        </ul>
                                    </div>

                                    <div class="bg-amber-50/60 rounded-2xl p-5 border border-amber-200/70 space-y-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-700 shadow-sm">
                                            <i class="fas fa-trophy text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900">Karantina Menuju PIMNAS</h4>
                                        <ul class="text-xs text-slate-600 space-y-1.5 list-disc pl-4 leading-relaxed font-medium">
                                            <li>Fasilitasi bootcamp presentasi dan pemantapan riset oleh tim kemahasiswaan.</li>
                                            <li>Pendanaan hibah pelaksanaan riset dari Kemendikbudristek.</li>
                                            <li>Target akhir: Mendapatkan medali di Pekan Ilmiah Mahasiswa Nasional (PIMNAS).</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="pt-4 flex items-center justify-between text-xs text-slate-500 border-t border-rose-100">
                                    <button type="button" @click="activeAlurStep = 4" class="text-slate-700 hover:text-slate-900 font-bold flex items-center gap-1.5 transition-colors cursor-pointer">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Tahap 4: Revisi Terpadu</span>
                                    </button>
                                    <span class="text-emerald-700 font-bold flex items-center gap-1.5">
                                        <i class="fas fa-flag-checkered"></i>
                                        <span>Tahap Akhir Seleksi Internal Universitas</span>
                                    </span>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </section>


            <!-- =========================================================================
                 SECTION 5: STATISTIK & CALLOUT BANNER
                 ========================================================================= -->
            <section id="statistik" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-rose-200/60 relative">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
                        
                        <div class="glass-card rounded-2xl p-6 text-center">
                            <div class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight mb-2">
                                {{ $stats['total_proposal'] ?? 21 }}+
                            </div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-rose-800">Proposal Terdaftar</span>
                        </div>

                        <div class="glass-card rounded-2xl p-6 text-center">
                            <div class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight mb-2">
                                {{ $stats['total_mahasiswa'] ?? 25 }}+
                            </div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-700">Mahasiswa Terlibat</span>
                        </div>

                        <div class="glass-card rounded-2xl p-6 text-center">
                            <div class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight mb-2">
                                {{ $stats['total_dosen'] ?? 10 }}+
                            </div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700">Dosen Pembimbing</span>
                        </div>

                        <div class="glass-card rounded-2xl p-6 text-center">
                            <div class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight mb-2">
                                {{ $stats['total_fakultas'] ?? 13 }}
                            </div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">Fakultas Terkoneksi</span>
                        </div>

                    </div>

                    <!-- Callout Action Banner -->
                    <div class="rounded-3xl p-8 sm:p-12 relative overflow-hidden bg-gradient-to-r from-red-950 via-rose-900 to-slate-950 text-white shadow-2xl">
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="space-y-3 max-w-2xl text-center md:text-left">
                                <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                                    Siap Memulai Gagasan PKM Anda?
                                </h3>
                                <p class="text-sm text-rose-100/90 leading-relaxed font-medium">
                                    Masuk ke dashboard mahasiswa atau dosen sekarang dan pantau seluruh status validasi serta tahapan review secara langsung.
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0 flex-wrap justify-center">
                                <button type="button" 
                                        @click="loginDrawerOpen = true"
                                        class="bg-white text-slate-900 hover:bg-rose-50 font-extrabold text-xs uppercase tracking-wider px-6 py-3.5 rounded-xl flex items-center gap-2 shadow-lg transition-all cursor-pointer">
                                    <i class="fas fa-sign-in-alt text-rose-700"></i>
                                    <span>Buka Login Drawer</span>
                                </button>
                                <a href="{{ route('register') }}"
                                   class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs px-5 py-3.5 rounded-xl transition-all">
                                    Cari Kredensial
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </section>


            <!-- =========================================================================
                 SECTION 6: FAQ ACCORDION (PANDUAN & BANTUAN)
                 ========================================================================= -->
            <section id="faq" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-rose-200/60 relative">
                <div class="max-w-4xl mx-auto">
                    
                    <div class="text-center mb-16">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-800 bg-rose-100 border border-rose-300 px-4 py-1.5 rounded-full inline-block mb-3">
                            Bantuan & FAQ
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            Pertanyaan yang Sering Diajukan
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 mt-3">
                            Jawaban seputar akses login, pembuatan akun, dan ketentuan teknis pengajuan proposal PKM.
                        </p>
                    </div>

                    <div class="space-y-4">
                        
                        <!-- FAQ 1 -->
                        <div class="glass-card rounded-2xl overflow-hidden">
                            <button type="button" 
                                    @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                    class="w-full p-5 text-left flex items-center justify-between text-sm sm:text-base font-bold text-slate-900 hover:text-rose-800 transition-colors">
                                <span>1. Bagaimana cara mendapatkan akun jika belum terdaftar?</span>
                                <i class="fas text-sm transition-transform duration-200" :class="activeFaq === 1 ? 'fa-chevron-up text-rose-600' : 'fa-chevron-down text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 1" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-rose-100 pt-3 bg-rose-50/30">
                                Mahasiswa dan Dosen Universitas Udayana yang belum menerima akun dapat menggunakan menu <a href="{{ route('register') }}" class="text-rose-700 underline font-bold">Request Kredensial</a> dengan memasukkan NIM/NUPTK serta alamat Gmail aktif. Sistem secara otomatis mencocokkan data dan mengirimkan username beserta kata sandi Anda.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="glass-card rounded-2xl overflow-hidden">
                            <button type="button" 
                                    @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                    class="w-full p-5 text-left flex items-center justify-between text-sm sm:text-base font-bold text-slate-900 hover:text-rose-800 transition-colors">
                                <span>2. Apa yang harus dilakukan jika lupa kata sandi akun?</span>
                                <i class="fas text-sm transition-transform duration-200" :class="activeFaq === 2 ? 'fa-chevron-up text-rose-600' : 'fa-chevron-down text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 2" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-rose-100 pt-3 bg-rose-50/30">
                                Gunakan fitur <a href="{{ route('password.forgot') }}" class="text-rose-700 underline font-bold">Reset Password</a>. Masukkan NIM/NUPTK Anda dan alamat Gmail terdaftar. Sistem akan membuat kata sandi acak baru yang langsung dikirimkan ke kotak masuk email Anda.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="glass-card rounded-2xl overflow-hidden">
                            <button type="button" 
                                    @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                    class="w-full p-5 text-left flex items-center justify-between text-sm sm:text-base font-bold text-slate-900 hover:text-rose-800 transition-colors">
                                <span>3. Berapa batas maksimal usulan dana pada proposal PKM?</span>
                                <i class="fas text-sm transition-transform duration-200" :class="activeFaq === 3 ? 'fa-chevron-up text-rose-600' : 'fa-chevron-down text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 3" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-rose-100 pt-3 bg-rose-50/30">
                                Sesuai panduan Belmawa Kemendikbudristek, dana usulan Belmawa maksimal Rp 8.000.000 dan dana pendamping perguruan tinggi maksimal Rp 2.000.000 (total Rp 10.000.000 untuk skim pendanaan). Khusus skim insentif (PKM-AI dan PKM-GFT), nilai usulan dana adalah Rp 0.
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="glass-card rounded-2xl overflow-hidden">
                            <button type="button" 
                                    @click="activeFaq = (activeFaq === 4 ? null : 4)"
                                    class="w-full p-5 text-left flex items-center justify-between text-sm sm:text-base font-bold text-slate-900 hover:text-rose-800 transition-colors">
                                <span>4. Berapa jumlah anggota kelompok dalam 1 proposal tim?</span>
                                <i class="fas text-sm transition-transform duration-200" :class="activeFaq === 4 ? 'fa-chevron-up text-rose-600' : 'fa-chevron-down text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 4" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-rose-100 pt-3 bg-rose-50/30">
                                Satu tim PKM terdiri dari 1 orang ketua dan 2 hingga 4 orang anggota (total 3 sampai 5 orang mahasiswa aktif). Anggota tim dapat berasal dari program studi atau fakultas yang berbeda (kolaborasi multidisiplin).
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="glass-card rounded-2xl overflow-hidden">
                            <button type="button" 
                                    @click="activeFaq = (activeFaq === 5 ? null : 5)"
                                    class="w-full p-5 text-left flex items-center justify-between text-sm sm:text-base font-bold text-slate-900 hover:text-rose-800 transition-colors">
                                <span>5. Berapa kelompok maksimal yang dapat dibimbing oleh dosen?</span>
                                <i class="fas text-sm transition-transform duration-200" :class="activeFaq === 5 ? 'fa-chevron-up text-rose-600' : 'fa-chevron-down text-slate-400'"></i>
                            </button>
                            <div x-show="activeFaq === 5" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-rose-100 pt-3 bg-rose-50/30">
                                Seorang dosen pendamping diperbolehkan membimbing maksimal hingga 10 kelompok proposal PKM yang disetujui di tingkat perguruan tinggi pada satu periode tahun anggaran.
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- =========================================================================
                 SECTION 7: FOOTER
                 ========================================================================= -->
            <footer class="border-t border-rose-200/80 bg-slate-950 py-12 px-4 sm:px-6 lg:px-8 text-xs text-slate-400">
                <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-800">
                    
                    <div class="md:col-span-5 space-y-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('UNUDLOGO.png') }}" alt="" class="w-8 h-8 object-contain">
                            <span class="text-sm font-black text-white">SIM-PKM Universitas Udayana</span>
                        </div>
                        <p class="text-slate-400 leading-relaxed max-w-sm">
                            Sistem digitalisasi terpadu pengelolaan Program Kreativitas Mahasiswa (PKM) di lingkungan Universitas Udayana, Bali.
                        </p>
                        <div class="text-[11px] text-slate-500">
                            Gedung Rektorat Universitas Udayana, Kampus Bukit Jimbaran, Badung, Bali - Indonesia.
                        </div>
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-white">Navigasi Utama</span>
                        <ul class="space-y-1.5 font-medium">
                            <li><a href="#hero" class="hover:text-rose-400 transition-colors">Beranda</a></li>
                            <li><a href="#linimasa" class="hover:text-rose-400 transition-colors">Linimasa Seleksi</a></li>
                            <li><a href="#skim" class="hover:text-rose-400 transition-colors">8 Skim PKM</a></li>
                            <li><a href="#alur" class="hover:text-rose-400 transition-colors">Alur Pengajuan</a></li>
                            <li><a href="#faq" class="hover:text-rose-400 transition-colors">Pusat Bantuan & FAQ</a></li>
                        </ul>
                    </div>

                    <div class="md:col-span-4 space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-white">Layanan Akun Mandiri</span>
                        <ul class="space-y-1.5 font-medium">
                            <li>
                                <button type="button" @click="loginDrawerOpen = true" class="text-rose-400 hover:text-rose-300 font-bold cursor-pointer">
                                    <i class="fas fa-sign-in-alt me-1"></i>Sign In ke Sistem
                                </button>
                            </li>
                            <li>
                                <a href="{{ route('register') }}" class="hover:text-white transition-colors">
                                    <i class="fas fa-key me-1 text-slate-500"></i>Cari / Request Kredensial
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('password.forgot') }}" class="hover:text-white transition-colors">
                                    <i class="fas fa-unlock-alt me-1 text-slate-500"></i>Reset Password Akun
                                </a>
                            </li>
                        </ul>
                        <div class="pt-3">
                            <span class="text-[11px] text-slate-500 block">Hubungi Bantuan Teknis:</span>
                            <span class="text-slate-300 font-bold">pkm@unud.ac.id</span>
                        </div>
                    </div>

                </div>

                <div class="max-w-7xl mx-auto pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
                    <span>© {{ date('Y') }} Universitas Udayana. All rights reserved.</span>
                    <div class="flex items-center gap-4">
                        <span>Direktorat Kemahasiswaan & Penalaran</span>
                        <span>•</span>
                        <span>Portal SIM-PKM</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

</body>
</html>