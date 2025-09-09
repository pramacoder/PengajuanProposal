<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Proposal PKM')</title>
    <!-- PDF.js - Hanya muat sekali di sini -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8b3a3a;
            --primary-dark: #6d2d2d;
            --sidebar-width: 280px;
            --navbar-height: 70px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding-top: var(--navbar-height);
        }

        /* Navbar Styles */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            height: var(--navbar-height);
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #f8f9fa !important;
            transform: translateY(-1px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            min-width: 20px;
            text-align: center;
        }

        /* Notification Styles */
        .notification-icon-wrapper {
            position: relative;
            display: inline-block;
        }

        .notification-icon {
            filter: brightness(0) invert(1);
            transition: all 0.3s ease;
        }

        .notification-icon:hover {
            transform: scale(1.1);
        }

        .notification-dropdown {
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border-radius: 12px;
            padding: 0;
        }

        .notification-dropdown .dropdown-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #dee2e6;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
        }

        .notification-item.unread:hover {
            background-color: #bbdefb;
        }

        .notification-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .notification-icon-small {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon-small.success {
            background-color: #d4edda;
            color: #155724;
        }

        .notification-icon-small.warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .notification-icon-small.info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .notification-icon-small.danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .notification-text {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: #212529;
            font-size: 0.9rem;
        }

        .notification-message {
            color: #6c757d;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .notification-time {
            color: #adb5bd;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }

        .notification-actions .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .empty-notifications {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .empty-notifications i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* User Profile Dropdown Styles */
        .user-profile-dropdown {
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border-radius: 12px;
            padding: 0;
        }

        .user-profile-dropdown .dropdown-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #dee2e6;
        }

        .user-info-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 0.5rem;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .user-avatar.primary { background-color: #007bff; }
        .user-avatar.success { background-color: #28a745; }
        .user-avatar.info { background-color: #17a2b8; }
        .user-avatar.warning { background-color: #ffc107; }
        .user-avatar.secondary { background-color: #6c757d; }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }

        .user-info {
            color: #6c757d;
            font-size: 0.8rem;
            line-height: 1.3;
        }

        .profile-menu-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            border-radius: 6px;
            margin: 0 0.5rem;
        }

        .profile-menu-item:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }

        .profile-menu-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.75rem;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 1.5rem 0;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            color: white;
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 2rem;
            padding: 0 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu > li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 0 25px 25px 0;
            margin-right: 1rem;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255,255,255,0.15);
            border-right: 3px solid white;
            transform: translateX(5px);
        }

        .sidebar-menu .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-menu .submenu.show {
            max-height: 300px;
        }

        .sidebar-menu .submenu a {
            padding-left: 2.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
            margin-right: 1.5rem;
        }

        .sidebar-menu .submenu a:hover {
            opacity: 1;
            background-color: rgba(255,255,255,0.1);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s ease;
        }

        /* Card Styles */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        /* Modal Styles */
        .modal-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        /* Button Styles */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 58, 58, 0.4);
        }

        /* Sidebar menu visibility control */
        .sidebar-menu .submenu li.hidden {
            display: none !important;
        }
        
        /* Hide menu items based on user type using CSS */
        @if(Auth::guard('mahasiswa')->check())
        .menu-dosen, .menu-reviewer, .menu-operator {
            display: none !important;
        }
        @elseif(Auth::guard('dosen')->check())
        .menu-mahasiswa, .menu-reviewer, .menu-operator {
            display: none !important;
        }
        @elseif(Auth::guard('reviewer')->check())
        .menu-mahasiswa, .menu-dosen, .menu-operator {
            display: none !important;
        }
        @elseif(Auth::guard('operator')->check())
        .menu-mahasiswa, .menu-dosen, .menu-reviewer {
            display: none !important;
        }
        @else
        .menu-mahasiswa, .menu-dosen, .menu-reviewer, .menu-operator {
            display: none !important;
        }
        @endif
        
        /* Debug styling */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .debug-info strong {
            color: #495057;
        }
        
        .debug-info small {
            color: #6c757d;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
        }

        .toast-custom {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid var(--primary-color);
        }
    </style>
    @yield('styles')
    @yield('dosen_styles')
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
                <i class="fas fa-graduation-cap me-2"></i>
                Pengajuan Proposal
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <!-- Notifications -->
                <div class="nav-item dropdown me-3 position-relative">
                    <a class="nav-link dropdown-toggle d-flex align-items-center position-relative" href="#" data-bs-toggle="dropdown" id="notificationDropdown">
                        <div class="notification-icon-wrapper">
                            <img src="{{ asset('ion_notifcations.svg') }}" alt="Notifikasi" class="notification-icon" width="24" height="24">
                            <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px; max-height: 500px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-bell me-2 text-primary"></i>
                                Notifikasi Sistem
                            </h6>
                            <button class="btn btn-sm btn-outline-primary" id="markAllRead">
                                Tandai Semua Dibaca
                            </button>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notificationList">
                            <!-- Notifications will be populated here -->
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="text-center p-2">
                            <a href="#" class="text-decoration-none" id="viewAllNotifications">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
                
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
                    <div class="dropdown-menu dropdown-menu-end user-profile-dropdown" style="min-width: 280px;">
                        <div class="dropdown-header">
                            <i class="fas fa-user-circle me-2"></i>Profil Pengguna
                        </div>
                        
                        <!-- User Info Section -->
                        <div class="user-info-section px-3 py-3">
                            @php
                                $userInfo = \App\Helpers\UserHelper::getUserProfileInfo();
                                $userIcon = \App\Helpers\UserHelper::getUserIcon();
                                $avatarColor = \App\Helpers\UserHelper::getUserAvatarColor();
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="user-avatar {{ $avatarColor }} me-3">
                                    <i class="{{ $userIcon }}"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $userInfo['name'] }}</div>
                                    <div class="user-info">{{ $userInfo['role'] }}</div>
                                    @if($userInfo['email'])
                                        <div class="user-info">{{ $userInfo['email'] }}</div>
                                    @endif
                                    @if($userInfo['phone'])
                                        <div class="user-info">{{ $userInfo['phone'] }}</div>
                                    @endif
                                    @foreach($userInfo['additional_info'] as $label => $value)
                                        @if($value)
                                            <div class="user-info">{{ $label }}: {{ $value }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Action Menu -->
                        @if(Auth::guard('mahasiswa')->check())
                            <a class="dropdown-item profile-menu-item" href="{{ route('mahasiswa.profile') }}">
                                <i class="fas fa-user"></i>Detail Profil
                            </a>
                            <a class="dropdown-item profile-menu-item" href="{{ route('mahasiswa.profile') }}">
                                <i class="fas fa-edit"></i>Edit Profil
                            </a>
                        @elseif(Auth::guard('dosen')->check())
                            <a class="dropdown-item profile-menu-item" href="{{ route('dosen.profile') }}">
                                <i class="fas fa-user"></i>Detail Profil
                            </a>
                            <a class="dropdown-item profile-menu-item" href="{{ route('dosen.profile') }}">
                                <i class="fas fa-edit"></i>Edit Profil
                            </a>
                        @elseif(Auth::guard('reviewer')->check())
                            <a class="dropdown-item profile-menu-item" href="{{ route('reviewer.profile') }}">
                                <i class="fas fa-user"></i>Detail Profil
                            </a>
                            <a class="dropdown-item profile-menu-item" href="{{ route('reviewer.profile') }}">
                                <i class="fas fa-edit"></i>Edit Profil
                            </a>
                        @elseif(Auth::guard('operator')->check())
                            <a class="dropdown-item profile-menu-item" href="{{ route('operator.profile') }}">
                                <i class="fas fa-user"></i>Detail Profil
                            </a>
                            <a class="dropdown-item profile-menu-item" href="{{ route('operator.profile') }}">
                                <i class="fas fa-edit"></i>Edit Profil
                            </a>
                        @endif
                        <a class="dropdown-item profile-menu-item" href="#">
                            <i class="fas fa-key"></i>Ubah Password
                        </a>
                        <a class="dropdown-item profile-menu-item" href="#">
                            <i class="fas fa-cog"></i>Pengaturan
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Logout -->
                        <a class="dropdown-item profile-menu-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-title">
            <i class="fas fa-file-alt me-2"></i>
            PROPOSAL
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="menu-toggle" data-target="pkkOrmawa">
                    <i class="fas fa-users me-2"></i>PKK ORMAWA
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkkOrmawa">
                    @if(Auth::guard('mahasiswa')->check())
                        <li class="menu-mahasiswa"><a href="#"><i class="fas fa-plus me-2"></i>Ajukan Proposal PKK</a></li>
                        <li class="menu-mahasiswa"><a href="#"><i class="fas fa-eye me-2"></i>Lihat Proposal PKK</a></li>
                    @endif
                </ul>
            </li>
            
            <li>
                <a href="#" class="menu-toggle" data-target="pkm" id="pkmMenu">
                    <i class="fas fa-lightbulb me-2"></i>PKM
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkm">
                    @if(Auth::guard('mahasiswa')->check())
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.create') }}" class="@if(request()->routeIs('mahasiswa.proposal.create')) active @endif">
                            <i class="fas fa-plus me-2"></i>Ajukan Proposal
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.index')) active @endif">
                            <i class="fas fa-eye me-2"></i>Lihat Proposal
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.revisi.index') }}" class="@if(request()->routeIs('mahasiswa.revisi.*')) active @endif">
                            <i class="fas fa-edit me-2"></i>Revisi Proposal
                        </a></li>
                    @endif
                    
                    @if(Auth::guard('dosen')->check())
                        <li class="menu-dosen"><a href="{{ route('dosen.validasi.proposal') }}" class="@if(request()->routeIs('dosen.validasi.proposal')) active @endif">
                            <i class="fas fa-clipboard-check me-2"></i>Validasi Proposal
                        </a></li>
                        <li class="menu-dosen"><a href="{{ route('dosen.hasil.review') }}" class="@if(request()->routeIs('dosen.hasil.review')) active @endif">
                            <i class="fas fa-clipboard-list me-2"></i>Hasil Review
                        </a></li>
                        <li class="menu-dosen"><a href="{{ route('dosen.hasil.final') }}" class="@if(request()->routeIs('dosen.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Hasil Final
                        </a></li>
                    @endif
                    
                    @if(Auth::guard('reviewer')->check())
                        <li class="menu-reviewer"><a href="{{ route('reviewer.dashboard') }}" class="@if(request()->routeIs('reviewer.dashboard')) active @endif">
                            <i class="fas fa-home me-2"></i>Beranda
                        </a></li>
                        <li class="menu-reviewer"><a href="{{ route('reviewer.review.administratif') }}" class="@if(request()->routeIs('reviewer.review.administratif')) active @endif">
                            <i class="fas fa-clipboard-check me-2"></i>Review Administratif
                        </a></li>
                        <li class="menu-reviewer"><a href="{{ route('reviewer.review.substantif') }}" class="@if(request()->routeIs('reviewer.review.substantif')) active @endif">
                            <i class="fas fa-user-check me-2"></i>Review Substantif
                        </a></li>
                    @endif
                    
                    @if(Auth::guard('operator')->check())
                        <li class="menu-operator"><a href="{{ route('operator.pilih.reviewer') }}" class="@if(request()->routeIs('operator.pilih.reviewer')) active @endif">
                            <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.ruang.kontrol') }}" class="@if(request()->routeIs('operator.ruang.kontrol')) active @endif">
                            <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.hasil.final') }}" class="@if(request()->routeIs('operator.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Hasil Final
                        </a></li>
                    @endif
                </ul>
            </li>
            
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>



    <!-- Review Modal -->
    @if(Auth::guard('reviewer')->check() || Auth::guard('operator')->check())
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="reviewModalTitle">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Hasil Review
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        // Close sidebar when overlay is clicked
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Menu Toggle
        document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-target');
                const submenu = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down, .fa-chevron-up');
                
                submenu.classList.toggle('show');
                
                if (submenu.classList.contains('show')) {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-up');
                } else {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                }
            });
        });

        // Show Review Modal
        function showReviewModal(type) {
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            const title = document.getElementById('reviewModalTitle');
            const body = document.getElementById('reviewModalBody');
            
            if (type === 'administratif') {
                title.innerHTML = '<i class="fas fa-clipboard-check me-2"></i>Hasil Review Administratif';
                body.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Hasil Review Kesalahan Administratif</strong>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Kesalahan yang Ditemukan:
                            </h6>
                            <ol class="mb-3">
                                <li>Margin tidak sesuai dengan ketentuan (harus 4-4-3-3 cm)</li>
                                <li>Penulisan judul tidak sesuai format yang ditentukan</li>
                                <li>Font yang digunakan tidak sesuai standar (harus Times New Roman)</li>
                                <li>Spasi antar paragraf tidak konsisten</li>
                            </ol>
                            <div class="alert alert-info">
                                <i class="fas fa-edit text-info me-2"></i>
                                <strong>Catatan:</strong><br>
                                Berikan revisi sesuai kesalahan administratif yang ditemukan agar proposal memungkinkan untuk lolos ke tahap review substantif. Semangat!!
                            </div>
                        </div>
                    </div>
                `;
            } else {
                title.innerHTML = '<i class="fas fa-user-check me-2"></i>Hasil Review Subtantif';
                body.innerHTML = `
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-user me-2"></i>
                            <strong>Reviewer 1 - Dr. Sari Widyastuti, M.Kom</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-edit text-warning me-2"></i>
                                <strong>Catatan:</strong><br>
                                Isi proposal sudah baik secara substantif, akan tetapi ada beberapa penggunaan AI yang masih terdeteksi dalam penulisan. Mohon untuk menulis ulang bagian tersebut dengan bahasa yang lebih natural.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nilai: 85/100</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-warning">Perlu Revisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-user me-2"></i>
                            <strong>Reviewer 2 - Prof. Dr. Budi Santoso, M.T</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="fas fa-edit text-warning me-2"></i>
                                <strong>Catatan:</strong><br>
                                Proposal memiliki inovasi yang menarik dan metodologi yang jelas. Namun perlu perbaikan pada bagian analisis data dan kesimpulan.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nilai: 90/100</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="badge bg-success">Disetujui</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            modal.show();
        }

        // Action Sidebar Toggle (for lihat proposal page)
        function toggleActionSidebar() {
            const actionSidebar = document.getElementById('actionSidebar');
            actionSidebar.classList.toggle('show');
        }

        // Show Toast Notification
        function showToast(message, type = 'info', duration = 3000) {
            const toastContainer = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast toast-custom show`;
            toast.style.cssText = `
                min-width: 300px;
                margin-bottom: 10px;
            `;
            
            const icon = type === 'success' ? 'check-circle' : 
                        type === 'error' ? 'times-circle' : 
                        type === 'warning' ? 'exclamation-triangle' : 'info-circle';
            
            const color = type === 'success' ? '#28a745' : 
                         type === 'error' ? '#dc3545' : 
                         type === 'warning' ? '#ffc107' : '#17a2b8';
            
            toast.innerHTML = `
                <div class="toast-header">
                    <i class="fas fa-${icon} me-2" style="color: ${color}"></i>
                    <strong class="me-auto">Notifikasi</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto remove after duration
            setTimeout(() => {
                if (toast && toast.parentNode) {
                    toast.remove();
                }
            }, duration);
        }

        // Notification System
        class NotificationSystem {
            constructor() {
                this.notifications = [];
                this.unreadCount = 0;
                this.init();
            }

            init() {
                this.loadNotifications();
                this.setupEventListeners();
                this.startPolling();
            }

            setupEventListeners() {
                try {
                    // Mark all as read
                    const markAllReadBtn = document.getElementById('markAllRead');
                    if (markAllReadBtn) {
                        markAllReadBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.markAllAsRead();
                        });
                    }

                    // View all notifications
                    const viewAllBtn = document.getElementById('viewAllNotifications');
                    if (viewAllBtn) {
                        viewAllBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.showAllNotifications();
                        });
                    }

                    // Close dropdown when clicking outside
                    document.addEventListener('click', (e) => {
                        const dropdown = document.querySelector('.notification-dropdown');
                        const toggle = document.getElementById('notificationDropdown');
                        
                        if (!dropdown?.contains(e.target) && !toggle?.contains(e.target)) {
                            const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                            if (bsDropdown) {
                                bsDropdown.hide();
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error setting up notification event listeners:', error);
                }
            }

            async loadNotifications() {
                try {
                    // Deteksi user type dan gunakan route yang sesuai
                    let notificationRoute = '';
                    
                    // Cek apakah ada route yang sesuai dengan user type
                    if (window.location.pathname.includes('/dosen/')) {
                        notificationRoute = '{{ route("dosen.notifications.get") }}';
                    } else if (window.location.pathname.includes('/mahasiswa/')) {
                        notificationRoute = '{{ route("mahasiswa.notifications.get") }}';
                    } else if (window.location.pathname.includes('/reviewer/')) {
                        notificationRoute = '{{ route("reviewer.notifications.get") }}';
                    } else if (window.location.pathname.includes('/operator/')) {
                        notificationRoute = '{{ route("operator.notifications.get") }}';
                    } else {
                        // Fallback jika tidak ada route yang cocok
                        console.warn('No notification route found for current path');
                        this.loadSampleNotifications();
                        return;
                    }
                    
                    const response = await fetch(notificationRoute, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.updateNotificationCount();
                        this.renderNotifications();
                    } else {
                        console.error('Failed to load notifications');
                        // Fallback to sample notifications
                        this.loadSampleNotifications();
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                    // Fallback to sample notifications
                    this.loadSampleNotifications();
                }
            }

            loadSampleNotifications() {
                // Fallback notifications when API is not available
                this.notifications = [
                    {
                        id: 1,
                        type: 'success',
                        title: 'Proposal Disetujui',
                        message: 'Proposal PKM Anda telah disetujui dan lolos ke tahap review substantif.',
                        time: '2 jam yang lalu',
                        unread: true,
                        actions: ['Lihat Detail', 'Download']
                    },
                    {
                        id: 2,
                        type: 'warning',
                        title: 'Perlu Revisi',
                        message: 'Proposal PKM Anda memerlukan revisi pada bagian metodologi penelitian.',
                        time: '1 hari yang lalu',
                        unread: true,
                        actions: ['Lihat Detail', 'Revisi']
                    },
                    {
                        id: 3,
                        type: 'info',
                        title: 'Status Berubah',
                        message: 'Status proposal Anda telah berubah dari "Draft" menjadi "Under Review".',
                        time: '3 hari yang lalu',
                        unread: false,
                        actions: ['Lihat Detail']
                    },
                    {
                        id: 4,
                        type: 'danger',
                        title: 'Proposal Ditolak',
                        message: 'Mohon maaf, proposal PKM Anda tidak dapat diproses karena tidak memenuhi syarat administratif.',
                        time: '1 minggu yang lalu',
                        unread: false,
                        actions: ['Lihat Detail', 'Ajukan Ulang']
                    }
                ];

                this.updateNotificationCount();
                this.renderNotifications();
            }

            renderNotifications() {
                try {
                    const container = document.getElementById('notificationList');
                    if (!container) {
                        console.warn('Notification container not found');
                        return;
                    }

                    if (this.notifications.length === 0) {
                        container.innerHTML = `
                            <div class="empty-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <p>Tidak ada notifikasi</p>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = this.notifications.map(notification => `
                        <div class="notification-item ${notification.unread ? 'unread' : ''}" data-id="${notification.id}">
                            <div class="notification-content">
                                <div class="notification-icon-small ${notification.type}">
                                    <i class="fas fa-${this.getIconForType(notification.type)}"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">${notification.title}</div>
                                    <div class="notification-message">${notification.message}</div>
                                    <div class="notification-time">${notification.time}</div>
                                    <div class="notification-actions">
                                        ${notification.actions.map(action => `
                                            <button class="btn btn-sm btn-outline-${this.getButtonStyle(notification.type)}" 
                                                    onclick="notificationSystem.handleAction(${notification.id}, '${action}')">
                                                ${action}
                                            </button>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Add click event to mark as read
                    container.querySelectorAll('.notification-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const id = parseInt(item.dataset.id);
                            this.markAsRead(id);
                        });
                    });
                } catch (error) {
                    console.error('Error rendering notifications:', error);
                    // Fallback to simple display
                    const container = document.getElementById('notificationList');
                    if (container) {
                        container.innerHTML = `
                            <div class="empty-notifications">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Gagal memuat notifikasi</p>
                            </div>
                        `;
                    }
                }
            }

            getIconForType(type) {
                const icons = {
                    success: 'check-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle',
                    danger: 'times-circle'
                };
                return icons[type] || 'info-circle';
            }

            getButtonStyle(type) {
                const styles = {
                    success: 'success',
                    warning: 'warning',
                    info: 'primary',
                    danger: 'danger'
                };
                return styles[type] || 'primary';
            }

            updateNotificationCount() {
                try {
                    this.unreadCount = this.notifications.filter(n => n.unread).length;
                    const badge = document.getElementById('notificationCount');
                    
                    if (badge) {
                        if (this.unreadCount > 0) {
                            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Error updating notification count:', error);
                }
            }

            async markAsRead(id) {
                try {
                    // Deteksi user type dan gunakan route yang sesuai
                    let markReadRoute = '';
                    
                    if (window.location.pathname.includes('/dosen/')) {
                        markReadRoute = '{{ route("dosen.notifications.mark-read") }}';
                    } else if (window.location.pathname.includes('/mahasiswa/')) {
                        markReadRoute = '{{ route("mahasiswa.notifications.mark-read") }}';
                    } else if (window.location.pathname.includes('/reviewer/')) {
                        markReadRoute = '{{ route("reviewer.notifications.mark-read") }}';
                    } else if (window.location.pathname.includes('/operator/')) {
                        markReadRoute = '{{ route("operator.notifications.mark-read") }}';
                    } else {
                        console.warn('No mark-read route found for current path');
                        return;
                    }
                    
                    const response = await fetch(markReadRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ notification_id: id }),
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        const notification = this.notifications.find(n => n.id === id);
                        if (notification && notification.unread) {
                            notification.unread = false;
                            this.updateNotificationCount();
                            this.renderNotifications();
                        }
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                    // Fallback to local update
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification && notification.unread) {
                        notification.unread = false;
                        this.updateNotificationCount();
                        this.renderNotifications();
                    }
                }
            }

            async markAllAsRead() {
                try {
                    // Deteksi user type dan gunakan route yang sesuai
                    let markAllReadRoute = '';
                    
                    if (window.location.pathname.includes('/dosen/')) {
                        markAllReadRoute = '{{ route("dosen.notifications.mark-all-read") }}';
                    } else if (window.location.pathname.includes('/mahasiswa/')) {
                        markAllReadRoute = '{{ route("mahasiswa.notifications.mark-all-read") }}';
                    } else if (window.location.pathname.includes('/reviewer/')) {
                        markAllReadRoute = '{{ route("reviewer.notifications.mark-all-read") }}';
                    } else if (window.location.pathname.includes('/operator/')) {
                        markAllReadRoute = '{{ route("operator.notifications.mark-all-read") }}';
                    } else {
                        console.warn('No mark-all-read route found for current path');
                        return;
                    }
                    
                    const response = await fetch(markAllReadRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    });

                    if (response.ok) {
                        this.notifications.forEach(n => n.unread = false);
                        this.updateNotificationCount();
                        this.renderNotifications();
                        
                        // Show success message
                        if (typeof showToast === 'function') {
                            showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
                        }
                    }
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                    // Fallback to local update
                    this.notifications.forEach(n => n.unread = false);
                    this.updateNotificationCount();
                    this.renderNotifications();
                    
                    if (typeof showToast === 'function') {
                        showToast('Semua notifikasi telah ditandai sebagai dibaca', 'success');
                    }
                }
            }

            handleAction(id, action) {
                try {
                    const notification = this.notifications.find(n => n.id === id);
                    if (!notification) {
                        console.warn(`Notification with id ${id} not found`);
                        return;
                    }

                    // Handle different actions
                    switch (action) {
                        case 'Lihat Detail':
                            this.showNotificationDetail(notification);
                            break;
                        case 'Download':
                            this.downloadDocument(notification);
                            break;
                        case 'Revisi':
                            this.openRevisionForm(notification);
                            break;
                        case 'Ajukan Ulang':
                            this.resubmitProposal(notification);
                            break;
                        default:
                            console.log(`Action: ${action} for notification ${id}`);
                    }
                } catch (error) {
                    console.error('Error handling notification action:', error);
                }
            }

            showNotificationDetail(notification) {
                // Show modal or navigate to detail page
                if (typeof showToast === 'function') {
                    showToast(`Membuka detail: ${notification.title}`, 'info');
                }
            }

            downloadDocument(notification) {
                if (typeof showToast === 'function') {
                    showToast('Mengunduh dokumen...', 'info');
                }
            }

            openRevisionForm(notification) {
                if (typeof showToast === 'function') {
                    showToast('Membuka form revisi...', 'info');
                }
            }

            resubmitProposal(notification) {
                if (typeof showToast === 'function') {
                    showToast('Membuka form pengajuan ulang...', 'info');
                }
            }

            startPolling() {
                try {
                    // Poll for new notifications every 30 seconds
                    this.pollingInterval = setInterval(() => {
                        this.checkForNewNotifications();
                    }, 30000);
                } catch (error) {
                    console.error('Error starting notification polling:', error);
                }
            }

            checkForNewNotifications() {
                try {
                    // Simulate checking for new notifications
                    // In real implementation, this would make an AJAX call to the server
                    const hasNewNotifications = Math.random() > 0.8; // 20% chance of new notification
                    
                    if (hasNewNotifications) {
                        this.addNewNotification();
                    }
                } catch (error) {
                    console.error('Error checking for new notifications:', error);
                }
            }

            addNewNotification() {
                try {
                    const newNotification = {
                        id: Date.now(),
                        type: 'info',
                        title: 'Notifikasi Baru',
                        message: 'Ada pembaruan status proposal yang perlu Anda periksa.',
                        time: 'Baru saja',
                        unread: true,
                        actions: ['Lihat Detail']
                    };

                    this.notifications.unshift(newNotification);
                    this.updateNotificationCount();
                    this.renderNotifications();

                    // Show toast notification
                    if (typeof showToast === 'function') {
                        showToast('Anda memiliki notifikasi baru', 'info');
                    }
                } catch (error) {
                    console.error('Error adding new notification:', error);
                }
            }

            showAllNotifications() {
                // Navigate to notifications page or show all in modal
                if (typeof showToast === 'function') {
                    showToast('Membuka halaman semua notifikasi', 'info');
                }
            }

            // Cleanup function
            cleanup() {
                try {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                } catch (error) {
                    console.error('Error cleaning up notification system:', error);
                }
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Layout loaded successfully');
            
            // Initialize notification system only once
            if (!window.notificationSystem) {
                try {
                    window.notificationSystem = new NotificationSystem();
                    console.log('Notification system initialized successfully');
                } catch (error) {
                    console.error('Failed to initialize notification system:', error);
                }
            } else {
                console.log('Notification system already exists');
            }
        });

        // Cleanup notification system when page unloads
        window.addEventListener('beforeunload', function() {
            if (window.notificationSystem) {
                window.notificationSystem.cleanup();
            }
        });

        // Load custom PDF viewer script
        const pdfViewerScript = document.createElement('script');
        pdfViewerScript.src = '/js/pdf-viewer.js';
        pdfViewerScript.onload = function() {
            console.log('PDF Viewer script loaded successfully');
        };
        pdfViewerScript.onerror = function() {
            console.error('Failed to load PDF Viewer script');
        };
        document.head.appendChild(pdfViewerScript);
    </script>
    @yield('scripts')
</body>
</html> 