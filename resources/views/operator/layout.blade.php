<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Proposal PKM - Operator')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-900: #380F17;
            --primary-700: #8F0B13;
            --primary-100: rgba(143,11,19,0.08);
            --bg-soft: #EDEBDD;
            --surface: #FFFFFF;
            --surface-2: #F7F4EF;
            --text-900: #252B2B;
            --text-600: #4C4F54;
            --text-400: #9CA3AF;
            --border: #E2E0D9;
            --border-dark: #D4D0C8;
            --shadow-sm: 0 1px 4px rgba(56,15,23,0.06);
            --shadow: 0 2px 12px rgba(56,15,23,0.08);
            --shadow-md: 0 4px 20px rgba(56,15,23,0.12);
            --shadow-lg: 0 8px 32px rgba(56,15,23,0.16);
            /* Legacy */
            --primary-color: #8F0B13;
            --primary-dark: #380F17;
            --sidebar-width: 270px;
            --navbar-height: 64px;
        }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background-color: var(--bg-soft);
            margin: 0;
            padding-top: var(--navbar-height);
            color: var(--text-900);
            -webkit-font-smoothing: antialiased;
        }
        /* ====== NAVBAR ====== */
        .navbar-custom {
            background: var(--primary-900);
            color: white;
            padding: 0 1.25rem;
            box-shadow: 0 2px 12px rgba(56,15,23,0.18);
            height: var(--navbar-height);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .navbar-brand {
            color: white !important;
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand i {
            background: rgba(255,255,255,0.15);
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.85) !important;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
        }
        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.12);
        }
        /* ====== SIDEBAR ====== */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 1rem 0 2rem;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 16px rgba(56,15,23,0.06);
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border-dark); border-radius: 4px; }
        .sidebar-title {
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            background: var(--primary-900);
            color: white;
            font-weight: 800;
            font-size: 0.9rem;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0 0.75rem;
            margin: 0;
            padding-bottom: 2rem;
        }
        .sidebar-menu > li { margin-bottom: 2px; list-style: none; }
        .sidebar-menu > li::marker { display: none; }
        .sidebar-menu li { list-style: none; }
        .sidebar-menu li::marker { display: none; }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: var(--text-600);
            text-decoration: none;
            padding: 0.6rem 0.875rem;
            transition: all 0.2s ease;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            gap: 0.6rem;
        }
        .sidebar-menu a i {
            width: 18px; text-align: center; font-size: 0.9rem;
            flex-shrink: 0; color: var(--text-400); transition: color 0.2s ease;
        }
        .sidebar-menu a:hover { background: var(--primary-100); color: var(--primary-700); }
        .sidebar-menu a:hover i { color: var(--primary-700); }
        .sidebar-menu a.active {
            background: var(--primary-900);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(56,15,23,0.2);
        }
        .sidebar-menu a.active i { color: rgba(255,255,255,0.85); }
        .sidebar-menu .submenu {
            list-style: none;
            padding: 0 0 0 0.5rem;
            margin: 2px 0 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .sidebar-menu .submenu.show { max-height: 500px; }
        .sidebar-menu .submenu li { list-style: none; }
        .sidebar-menu .submenu a {
            padding: 0.5rem 0.875rem 0.5rem 2.2rem;
            font-size: 0.84rem;
            border-left: 2px solid var(--border);
            border-radius: 0 7px 7px 0;
            margin-left: 0.5rem;
        }
        .sidebar-menu .submenu a:hover { border-left-color: var(--primary-700); }
        .sidebar-menu .submenu a.active {
            background: rgba(143,11,19,0.1);
            color: var(--primary-700);
            font-weight: 700;
            border-left-color: var(--primary-700);
            box-shadow: none;
        }
        .menu-toggle { display: flex; align-items: center; justify-content: space-between; }
        .menu-toggle .fa-chevron-down { transition: transform 0.25s ease; font-size: 0.7rem; color: var(--text-400); margin-left: auto; }
        .menu-toggle.active .fa-chevron-down { transform: rotate(180deg); }
        .sidebar-menu .menu-toggle.active { background: var(--primary-100); color: var(--primary-700); }
        .sidebar-menu .menu-toggle.active i { color: var(--primary-700); }
        /* ====== MAIN CONTENT ====== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.75rem 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s ease;
        }
        /* ====== CARDS ====== */
        .card-custom {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            transition: all 0.25s ease;
            overflow: hidden;
        }
        .card-custom:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .card-header-custom {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            color: var(--text-900);
            border-radius: 14px 14px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-header-custom i, .card-header-custom h5 i { color: var(--primary-700); }
        /* ====== RESPONSIVE ====== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); box-shadow: 4px 0 24px rgba(56,15,23,0.15); }
            .main-content { margin-left: 0; padding: 1rem; }
            .sidebar-overlay {
                position: fixed; top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(56,15,23,0.4);
                z-index: 999; display: none;
            }
            .sidebar-overlay.show { display: block; }
        }
        .toast-container { position: fixed; top: 80px; right: 1.25rem; z-index: 9999; }
        .toast-custom {
            background: var(--surface); border-radius: 10px;
            box-shadow: var(--shadow-lg); border-left: 4px solid var(--primary-700);
        }
        /* User profile dropdown */
        .user-profile-dropdown {
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            border-radius: 12px;
            padding: 0;
        }
        .profile-menu-item {
            padding: 0.6rem 0.875rem;
            transition: all 0.2s ease;
            border-radius: 7px;
            margin: 0 0.375rem;
            color: var(--text-900);
            font-size: 0.875rem;
        }
        .profile-menu-item:hover { background: var(--surface-2); color: var(--primary-700); }
        .profile-menu-item i { width: 18px; text-align: center; margin-right: 0.625rem; color: var(--primary-700); }
        .user-name { font-weight: 700; color: var(--text-900); font-size: 0.9rem; }
        .user-info { color: var(--text-600); font-size: 0.78rem; }
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white; flex-shrink: 0;
        }
        .user-avatar.primary { background: var(--primary-700); }
        .user-avatar.success { background: #059669; }
        .user-avatar.info { background: #0891B2; }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Hamburger Menu -->
            <button class="btn btn-link text-white d-lg-none me-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="#">
                <i class="fas fa-cogs"></i>
                <span>Operator Panel</span>
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <!-- User Profile -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-2">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <span class="fw-bold">
                                {{ \App\Helpers\UserHelper::getCurrentUserName() }}
                            </span>
                        </div>
                    </a>
                    <x-user-profile-dropdown
                        headerTitle="Profil Operator"
                        menuClass="dropdown-menu dropdown-menu-end"
                        menuStyle="min-width: 280px;"
                        menuItemClass="dropdown-item"
                        infoTextClass="text-muted small"
                        logoutFormId="logout-form-operator"
                    />
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-title">
            <div class="sidebar-brand-inner">
                <i class="fas fa-cogs"></i>
                <span>Operator Panel</span>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="menu-toggle" data-target="pkm" id="pkmMenu">
                    <i class="fas fa-lightbulb me-2"></i>PKM
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkm">
                    @php
                        $operatorUser = auth()->user();
                        $isPimpinanPT = $operatorUser && $operatorUser->role === 'pimpinan_pt';
                        $isOperator = $operatorUser && $operatorUser->role === 'operator';
                    @endphp
                    
                    @if($isOperator)
                    <li><a href="{{ route('operator.dashboard') }}" class="@if(request()->routeIs('operator.dashboard')) active @endif">
                        <i class="fas fa-home me-2"></i>Beranda
                    </a></li>
                    <li><a href="{{ route('operator.pilih.reviewer') }}" class="@if(request()->routeIs('operator.pilih.reviewer')) active @endif">
                        <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                    </a></li>
                    <li><a href="{{ route('operator.pilih.reviewer.seleksi') }}" class="@if(request()->routeIs('operator.pilih.reviewer.seleksi')) active @endif">
                        <i class="fas fa-user-check me-2"></i>Pilih Reviewer Seleksi
                    </a></li>
                    <li><a href="{{ route('operator.ruang.kontrol') }}" class="@if(request()->routeIs('operator.ruang.kontrol')) active @endif">
                        <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                    </a></li>
                    <li><a href="{{ route('operator.hasil.semi.final') }}" class="@if(request()->routeIs('operator.hasil.semi.final*')) active @endif">
                        <i class="fas fa-clipboard-check me-2"></i>Hasil Semi Final
                    </a></li>
                    <li><a href="{{ route('operator.hasil.final') }}" class="@if(request()->routeIs('operator.hasil.final*') || request()->routeIs('operator.detail.hasil.final')) active @endif">
                        <i class="fas fa-trophy me-2"></i>Hasil Final
                    </a></li>
                    <li><a href="{{ route('operator.form.penilaian.index') }}" class="@if(request()->routeIs('operator.form.penilaian.*')) active @endif">
                        <i class="fas fa-file-alt me-2"></i>Form Penilaian
                    </a></li>
                    <li><a href="{{ route('operator.laporan.simbelmawa.index') }}" class="@if(request()->routeIs('operator.laporan.simbelmawa.*')) active @endif">
                        <i class="fas fa-chart-bar me-2"></i>Laporan SIMBELMAWA
                    </a></li>
                    <li><a href="{{ route('operator.manage.accounts') }}" class="@if(request()->routeIs('operator.manage.accounts')) active @endif">
                        <i class="fas fa-id-card me-2"></i>Manajemen Akun
                    </a></li>
                    @elseif($isPimpinanPT)
                        <li><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard')) active @endif">
                            <i class="fas fa-home me-2"></i>Beranda
                        </a></li>
                        <li><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard') || request()->routeIs('pimpinan_pt.detail.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Hasil Final
                        </a></li>
                        <li><a href="{{ route('pimpinan_pt.manage.accounts') }}" class="@if(request()->routeIs('pimpinan_pt.manage.accounts*')) active @endif">
                            <i class="fas fa-id-card me-2"></i>Manajemen Akun
                        </a></li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <x-flash-messages />

        @yield('content')
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function applyIndonesianNumberFormatting() {
            if (window.AppUI?.applyIndonesianNumberFormatting) {
                window.AppUI.applyIndonesianNumberFormatting(document);
            }
        }

        function showToast(message, type = 'info', duration = 3000) {
            if (window.AppUI?.showToast) {
                window.AppUI.showToast(message, type, duration);
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Operator layout loaded successfully');
            window.AppUI?.initSidebarInteractions?.();
            applyIndonesianNumberFormatting();
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>

