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
        .notification-badge {
            position: absolute;
            top: -4px; right: -4px;
            background: #EF4444;
            color: white;
            border-radius: 50%;
            padding: 0.15rem 0.4rem;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 18px;
            text-align: center;
            border: 2px solid var(--primary-900);
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

        /* Negative Notification Styles (Penolakan) */
        .notification-item.negative {
            background-color: #fee;
            border-left: 4px solid #dc3545;
            border-radius: 8px;
            padding: 1rem;
            margin: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .notification-item.negative:hover {
            background-color: #fdd;
        }

        .notification-item.negative.unread {
            background-color: #fee;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.1);
        }

        .notification-item.negative.unread:hover {
            background-color: #fdd;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.15);
        }

        .notification-item.negative .notification-icon-small {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .notification-item.negative .notification-title {
            color: #dc3545;
            font-weight: 600;
        }

        .notification-item.negative .notification-message {
            color: #721c24;
            font-weight: 500;
        }

        .notification-item.negative .notification-time {
            color: #a94442;
        }

        .notification-item.negative .catatan-box {
            background-color: #fff;
            border: 1px solid #dc3545;
            border-radius: 6px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            font-style: italic;
            color: #721c24;
        }

        .notification-item.negative .catatan-box strong {
            color: #dc3545;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .notification-item.negative .action-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            margin-top: 0.5rem;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .notification-item.negative .action-button:hover {
            background-color: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
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
        .sidebar::-webkit-scrollbar-thumb:hover { background: var(--primary-700); }

        /* Sidebar header/brand area */
        .sidebar-title {
            padding: 0.75rem 1rem 0.75rem;
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
            letter-spacing: -0.01em;
        }
        .sidebar-brand-inner i {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Section label */
        .sidebar-section-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-400);
            padding: 0.75rem 1.25rem 0.25rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 0.75rem;
            margin: 0;
            padding-bottom: 2rem;
        }
        .sidebar-menu > li {
            margin-bottom: 2px;
            list-style: none;
        }
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
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            color: var(--text-400);
            transition: color 0.2s ease;
        }
        .sidebar-menu a:hover {
            background: var(--primary-100);
            color: var(--primary-700);
        }
        .sidebar-menu a:hover i { color: var(--primary-700); }

        .sidebar-menu a.active {
            background: var(--primary-900);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(56,15,23,0.2);
        }
        .sidebar-menu a.active i { color: rgba(255,255,255,0.85); }

        /* Submenu */
        .sidebar-menu .submenu {
            list-style: none;
            padding: 0 0 0 0.5rem;
            margin: 2px 0 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .sidebar-menu .submenu.show { max-height: 500px; }
        .sidebar-menu .submenu li { margin: 0; list-style: none; }
        .sidebar-menu .submenu li::marker { display: none; }
        .sidebar-menu .submenu a {
            padding: 0.5rem 0.875rem 0.5rem 2.2rem;
            font-size: 0.84rem;
            color: var(--text-600);
            border-radius: 7px;
            border-left: 2px solid var(--border);
            border-radius: 0 7px 7px 0;
            margin-left: 0.5rem;
        }
        .sidebar-menu .submenu a:hover {
            background: var(--primary-100);
            color: var(--primary-700);
            border-left-color: var(--primary-700);
        }
        .sidebar-menu .submenu a.active {
            background: rgba(143,11,19,0.1);
            color: var(--primary-700);
            font-weight: 700;
            border-left-color: var(--primary-700);
            box-shadow: none;
        }
        .sidebar-menu .submenu a.active i { color: var(--primary-700); }

        /* Menu toggle */
        .menu-toggle {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .menu-toggle .fa-chevron-down {
            transition: transform 0.25s ease;
            font-size: 0.7rem;
            color: var(--text-400);
            margin-left: auto;
        }
        .menu-toggle.active .fa-chevron-down { transform: rotate(180deg); }
        .sidebar-menu .menu-toggle.active {
            background: var(--primary-100);
            color: var(--primary-700);
        }
        .sidebar-menu .menu-toggle.active i { color: var(--primary-700); }

        /* ====== MAIN CONTENT ====== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.75rem 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s ease;
        }

        /* Card Styles */
        .card-custom {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            transition: all 0.25s ease;
            overflow: hidden;
        }
        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
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
        .card-header-custom i { color: var(--primary-700); }

        /* Modal */
        .modal-header-custom {
            background: var(--primary-900);
            color: white;
            border-radius: 14px 14px 0 0;
            border-bottom: none;
        }
        /* Button */
        .btn-primary-custom {
            background: var(--primary-700);
            border: none; border-radius: 8px;
            padding: 0.6rem 1.25rem;
            font-weight: 600; color: white;
            transition: all 0.25s ease;
        }
        .btn-primary-custom:hover {
            background: var(--primary-900); color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(56,15,23,0.25);
        }

        /* Sidebar menu visibility control */
        .sidebar-menu .submenu li.hidden {
            display: none !important;
        }
        
        /* Hide menu items based on user type using CSS */
        @if(auth()->check() && auth()->user()->role === 'mahasiswa')
        .menu-dosen, .menu-reviewer, .menu-operator {
            display: none !important;
        }
        @elseif(auth()->check() && auth()->user()->role === 'dosen')
        .menu-mahasiswa, .menu-reviewer, .menu-operator {
            display: none !important;
        }
        @elseif(auth()->check() && auth()->user()->role === 'reviewer')
        .menu-mahasiswa, .menu-dosen, .menu-operator {
            display: none !important;
        }
        @elseif(auth()->check() && in_array(auth()->user()->role, ['operator', 'pimpinan_pt']))
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

        /* ====== RESPONSIVE ====== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); box-shadow: none; }
            .sidebar.show { transform: translateX(0); box-shadow: 4px 0 24px rgba(56,15,23,0.15); }
            .main-content { margin-left: 0; padding: 1rem; }
            .sidebar-overlay {
                position: fixed; top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(56,15,23,0.4);
                z-index: 999; display: none;
                backdrop-filter: blur(2px);
            }
            .sidebar-overlay.show { display: block; }
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

        /* ====== TOAST ====== */
        .toast-container { position: fixed; top: 80px; right: 1.25rem; z-index: 9999; }
        .toast-custom {
            background: var(--surface); border-radius: 10px;
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--primary-700);
        }

        /* ====== USER PROFILE / NOTIFICATION DROPDOWNS ====== */
        .user-profile-dropdown, .notification-dropdown {
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            border-radius: 12px;
            padding: 0;
            background: var(--surface);
        }
        .user-profile-dropdown .dropdown-header,
        .notification-dropdown .dropdown-header {
            background: var(--surface-2);
            padding: 0.875rem 1rem;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid var(--border);
        }
        .profile-menu-item {
            padding: 0.6rem 0.875rem;
            transition: all 0.2s ease;
            border-radius: 7px;
            margin: 0 0.375rem;
            color: var(--text-900);
            font-size: 0.875rem;
        }
        .profile-menu-item:hover {
            background: var(--surface-2);
            color: var(--primary-700);
            transform: translateX(3px);
        }
        .profile-menu-item i {
            width: 18px; text-align: center;
            margin-right: 0.625rem; color: var(--primary-700);
        }
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white; flex-shrink: 0;
        }
        .user-avatar.primary { background: var(--primary-700); }
        .user-avatar.success { background: #059669; }
        .user-avatar.info { background: #0891B2; }
        .user-avatar.warning { background: #D97706; }
        .user-avatar.secondary { background: var(--text-600); }
        .user-name { font-weight: 700; color: var(--text-900); font-size: 0.9rem; }
        .user-info { color: var(--text-600); font-size: 0.78rem; line-height: 1.3; }
        .notification-item {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s ease;
            cursor: pointer;
        }
        .notification-item:hover { background: var(--surface-2); }
        .notification-item.unread {
            background: #EFF6FF;
            border-left: 3px solid #3B82F6;
        }
        .notification-item.negative {
            background: #FEF2F2;
            border-left: 3px solid #EF4444;
        }
        .notification-title { font-weight: 600; margin-bottom: 3px; color: var(--text-900); font-size: 0.875rem; }
        .notification-message { color: var(--text-600); font-size: 0.82rem; line-height: 1.4; margin-bottom: 6px; }
        .notification-time { color: var(--text-400); font-size: 0.72rem; font-weight: 500; }
        .notification-icon-small {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .notification-icon-small.success { background: #D1FAE5; color: #065F46; }
        .notification-icon-small.warning { background: #FEF3C7; color: #92400E; }
        .notification-icon-small.info { background: #DBEAFE; color: #1E40AF; }
        .notification-icon-small.danger { background: #FEE2E2; color: #991B1B; }
        .empty-notifications { text-align: center; padding: 2rem; color: var(--text-400); }
        .empty-notifications i { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5; }
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
                <i class="fas fa-graduation-cap"></i>
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
                    <x-user-profile-dropdown
                        headerTitle="Profil Pengguna"
                        menuClass="dropdown-menu dropdown-menu-end user-profile-dropdown"
                        menuStyle="min-width: 280px;"
                        menuItemClass="dropdown-item profile-menu-item"
                        infoTextClass="user-info"
                        logoutFormId="logout-form-main"
                    />
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="menu-toggle" data-target="pkm" id="pkmMenu">
                    <i class="fas fa-lightbulb me-2"></i>PKM
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkm">
                    @if(auth()->check() && auth()->user()->role === 'mahasiswa')
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.dashboard') }}" class="@if(request()->routeIs('mahasiswa.dashboard')) active @endif">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.create') }}" class="@if(request()->routeIs('mahasiswa.proposal.create')) active @endif">
                            <i class="fas fa-plus me-2"></i>Ajukan Proposal
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.index')) active @endif">
                            <i class="fas fa-eye me-2"></i>Lihat Proposal
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.revisi.index') }}" class="@if(request()->routeIs('mahasiswa.revisi.*')) active @endif">
                            <i class="fas fa-edit me-2"></i>Revisi Proposal
                        </a></li>
                        <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.revisi.akhir*')) active @endif">
                            <i class="fas fa-file-edit me-2"></i>Revisi Akhir
                        </a></li>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->role === 'dosen')
                        <!-- Menu Dosen Pendamping -->
                        <li class="menu-dosen">
                            <a href="#" class="menu-toggle" data-target="pendampingProposal">
                                <i class="fas fa-user-check me-2"></i>Pendamping Proposal
                                <i class="fas fa-chevron-down float-end"></i>
                            </a>
                            <ul class="submenu" id="pendampingProposal">
                                <li><a href="{{ route('dosen.pendamping.dashboard') }}" class="@if(request()->routeIs('dosen.pendamping.dashboard')) active @endif">
                                    <i class="fas fa-user-check me-2"></i>Dashboard Pendamping
                                </a></li>
                                <li><a href="{{ route('dosen.pendamping.proposal.validasi') }}" class="@if(request()->routeIs('dosen.pendamping.proposal.validasi')) active @endif">
                                    <i class="fas fa-clipboard-check me-2"></i>Validasi Proposal
                                </a></li>
                                <li><a href="{{ route('dosen.hasil.review') }}" class="@if(request()->routeIs('dosen.hasil.review')) active @endif">
                                    <i class="fas fa-clipboard-list me-2"></i>Hasil Review
                                </a></li>
                                <li><a href="{{ route('dosen.hasil.final') }}" class="@if(request()->routeIs('dosen.hasil.final')) active @endif">
                                    <i class="fas fa-trophy me-2"></i>Hasil Final
                                </a></li>
                            </ul>
                        </li>

                        <li class="menu-dosen"><a href="{{ route('dosen.pembimbing.dashboard') }}" class="@if(request()->routeIs('dosen.pembimbing.*')) active @endif">
                            <i class="fas fa-user-graduate me-2"></i>Pembimbing
                        </a></li>
                        
                        <!-- Menu Dosen Universitas (Pendamping Universitas) -->
                        <li class="menu-dosen">
                            <a href="#" class="menu-toggle" data-target="dosenUniversitas">
                                <i class="fas fa-user-graduate me-2"></i>Pendamping Univ
                                <i class="fas fa-chevron-down float-end"></i>
                            </a>
                            <ul class="submenu" id="dosenUniversitas">
                                <li><a href="{{ route('dosen.universitas.dashboard') }}" class="@if(request()->routeIs('dosen.universitas.dashboard')) active @endif">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a href="{{ route('dosen.universitas.validasi.akhir') }}" class="@if(request()->routeIs('dosen.universitas.validasi.akhir*')) active @endif">
                                    <i class="fas fa-check-double me-2"></i>Validasi Akhir
                                </a></li>
                            </ul>
                        </li>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->role === 'reviewer')
                        <li class="menu-reviewer"><a href="{{ route('reviewer.dashboard') }}" class="@if(request()->routeIs('reviewer.dashboard')) active @endif">
                            <i class="fas fa-home me-2"></i>Beranda
                        </a></li>
                        <li class="menu-reviewer"><a href="{{ route('reviewer.review.administratif') }}" class="@if(request()->routeIs('reviewer.review.administratif')) active @endif">
                            <i class="fas fa-clipboard-check me-2"></i>Review Administratif
                        </a></li>
                        <li class="menu-reviewer"><a href="{{ route('reviewer.review.substantif') }}" class="@if(request()->routeIs('reviewer.review.substantif')) active @endif">
                            <i class="fas fa-user-check me-2"></i>Review Substantif
                        </a></li>
                        <li class="menu-reviewer"><a href="{{ route('reviewer.review.substantif.seleksi') }}" class="@if(request()->routeIs('reviewer.review.substantif.seleksi')) active @endif">
                            <i class="fas fa-award me-2"></i>Review Substantif Seleksi
                        </a></li>
                    @endif
                    
                    @if(auth()->check() && in_array(auth()->user()->role, ['operator', 'pimpinan_pt']))
                        @php
                            $operatorUser = auth()->user();
                            $isPimpinanPT = $operatorUser->role === 'pimpinan_pt';
                            $isOperator = $operatorUser->role === 'operator';
                        @endphp
                        
                        @if($isOperator)
                        <li class="menu-operator"><a href="{{ route('operator.pilih.reviewer') }}" class="@if(request()->routeIs('operator.pilih.reviewer')) active @endif">
                            <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.pilih.reviewer.seleksi') }}" class="@if(request()->routeIs('operator.pilih.reviewer.seleksi')) active @endif">
                            <i class="fas fa-user-check me-2"></i>Pilih Reviewer Seleksi
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.ruang.kontrol') }}" class="@if(request()->routeIs('operator.ruang.kontrol')) active @endif">
                            <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.hasil.semi.final') }}" class="@if(request()->routeIs('operator.hasil.semi.final*')) active @endif">
                            <i class="fas fa-clipboard-check me-2"></i>Hasil Semi Final
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.hasil.final') }}" class="@if(request()->routeIs('operator.hasil.final*') || request()->routeIs('operator.detail.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Hasil Final
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.form.penilaian.index') }}" class="@if(request()->routeIs('operator.form.penilaian.*')) active @endif">
                            <i class="fas fa-file-alt me-2"></i>Form Penilaian
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('operator.laporan.simbelmawa.index') }}" class="@if(request()->routeIs('operator.laporan.simbelmawa.*')) active @endif">
                            <i class="fas fa-chart-bar me-2"></i>Laporan SIMBELMAWA
                        </a></li>
                        @elseif($isPimpinanPT)
                            <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard')) active @endif">
                                <i class="fas fa-home me-2"></i>Beranda
                            </a></li>
                            <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard') || request()->routeIs('pimpinan_pt.detail.hasil.final')) active @endif">
                                <i class="fas fa-trophy me-2"></i>Hasil Final
                            </a></li>
                            <li class="menu-operator"><a href="{{ route('pimpinan_pt.manage.accounts') }}" class="@if(request()->routeIs('pimpinan_pt.manage.accounts*')) active @endif">
                                <i class="fas fa-id-card me-2"></i>Manajemen Akun
                            </a></li>
                        @endif
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



    <!-- Review Modal -->
    @if(auth()->check() && in_array(auth()->user()->role, ['reviewer', 'operator', 'pimpinan_pt']))
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
        function applyIndonesianNumberFormatting() {
            if (window.AppUI?.applyIndonesianNumberFormatting) {
                window.AppUI.applyIndonesianNumberFormatting(document);
            }
        }

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
            if (window.AppUI?.showToast) {
                window.AppUI.showToast(message, type, duration);
            }
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
                        // Format notifications untuk memastikan semua field ada
                        this.notifications = (data.notifications || []).map(notif => ({
                            ...notif,
                            unread: notif.unread !== undefined ? notif.unread : !notif.read_at,
                            data: notif.data || {},
                            proposal_id: notif.proposal_id || (notif.data?.proposal_id || null)
                        }));
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

                    container.innerHTML = this.notifications.map(notification => {
                        // Cek apakah notifikasi negatif (penolakan)
                        const isNegative = notification.data?.is_negative || false;
                        const hasCatatan = notification.data?.catatan || notification.data?.catatan_final || notification.data?.catatan_operator || notification.data?.catatan_dosen;
                        const proposalId = notification.data?.proposal_id || notification.proposal_id;
                        const action = notification.data?.action;
                        
                        return `
                            <div class="notification-item ${notification.unread ? 'unread' : ''} ${isNegative ? 'negative' : ''}" data-id="${notification.id}">
                                <div class="notification-content">
                                    <div class="notification-icon-small ${notification.type}">
                                        <i class="fas fa-${this.getIconForType(notification.type)}"></i>
                                    </div>
                                    <div class="notification-text">
                                        <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                                        <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                                        ${hasCatatan ? `
                                            <div class="catatan-box">
                                                <strong>Catatan:</strong> ${this.escapeHtml(hasCatatan)}
                                            </div>
                                        ` : ''}
                                        <div class="notification-time">${notification.time || notification.created_at || ''}</div>
                                        ${action && proposalId ? `
                                            <button class="action-button" 
                                                    onclick="event.stopPropagation(); notificationSystem.handleAction(${notification.id}, '${action}', ${proposalId})">
                                                ${this.getActionText(action)}
                                            </button>
                                        ` : notification.actions && notification.actions.length > 0 ? `
                                            <div class="notification-actions">
                                                ${notification.actions.map(action => `
                                                    <button class="btn btn-sm btn-outline-${isNegative ? 'danger' : this.getButtonStyle(notification.type)}" 
                                                            onclick="event.stopPropagation(); notificationSystem.handleAction(${notification.id}, '${action}')">
                                                        ${action}
                                                    </button>
                                                `).join('')}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

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

            handleAction(id, action, proposalId = null) {
                try {
                    const notification = this.notifications.find(n => n.id === id);
                    if (!notification) {
                        console.warn(`Notification with id ${id} not found`);
                        return;
                    }

                    // Jika ada proposalId, gunakan action URL
                    if (proposalId && action) {
                        const url = this.getActionUrl(action, proposalId);
                        if (url && url !== '#') {
                            window.location.href = url;
                            return;
                        }
                    }

                    // Handle different actions (fallback untuk action lama)
                    switch (action) {
                        case 'Lihat Detail':
                        case 'view_proposal':
                            if (proposalId) {
                                window.location.href = this.getActionUrl('view_proposal', proposalId);
                            } else {
                                this.showNotificationDetail(notification);
                            }
                            break;
                        case 'Download':
                            this.downloadDocument(notification);
                            break;
                        case 'Revisi':
                        case 'revisi_proposal':
                        case 'upload_revisi':
                            if (proposalId) {
                                window.location.href = this.getActionUrl('upload_revisi', proposalId);
                            } else {
                                this.openRevisionForm(notification);
                            }
                            break;
                        case 'upload_revisi_akhir':
                            if (proposalId) {
                                window.location.href = this.getActionUrl('upload_revisi_akhir', proposalId);
                            } else {
                                this.openRevisionForm(notification);
                            }
                            break;
                        case 'view_hasil_final':
                            if (proposalId) {
                                window.location.href = this.getActionUrl('view_hasil_final', proposalId);
                            } else {
                                this.showNotificationDetail(notification);
                            }
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

            getActionUrl(action, proposalId) {
                const baseUrl = window.location.origin;
                const routes = {
                    'view_proposal': `/mahasiswa/proposal/${proposalId}`,
                    'revisi_proposal': `/mahasiswa/proposal/${proposalId}/revisi`,
                    'upload_revisi': `/mahasiswa/proposal/${proposalId}/revisi`,
                    'upload_revisi_akhir': `/mahasiswa/proposal/${proposalId}/revisi-akhir`,
                    'view_hasil_final': `/mahasiswa/proposal/${proposalId}`
                };
                return routes[action] || '#';
            }

            getActionText(action) {
                const texts = {
                    'view_proposal': 'Lihat Proposal',
                    'revisi_proposal': 'Revisi Proposal',
                    'upload_revisi': 'Upload Revisi',
                    'upload_revisi_akhir': 'Upload Revisi Akhir',
                    'view_hasil_final': 'Lihat Hasil Final'
                };
                return texts[action] || 'Lihat Detail';
            }

            escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
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
            window.AppUI?.initSidebarInteractions?.();
            applyIndonesianNumberFormatting();
            
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
    @stack('scripts')
    @yield('scripts')
</body>
</html> 