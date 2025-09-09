<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Proposal PKM - Operator')</title>
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
                PROPOSAL PT
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
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                        <div class="dropdown-header">
                            <i class="fas fa-user-circle me-2"></i>Profil Operator
                        </div>
                        
                        <!-- User Info Section -->
                        <div class="px-3 py-3">
                            @php
                                $userInfo = \App\Helpers\UserHelper::getUserProfileInfo();
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-3 me-3">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $userInfo['name'] }}</div>
                                    <div class="text-muted">{{ $userInfo['role'] }}</div>
                                    @if($userInfo['email'])
                                        <div class="text-muted small">{{ $userInfo['email'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Action Menu -->
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user me-2"></i>Detail Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Logout -->
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
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
            PROPOSAL PT
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="menu-toggle" data-target="pkkOrmawa">
                    <i class="fas fa-users me-2"></i>PKK ORMAWA
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkkOrmawa">
                    <li><a href="#"><i class="fas fa-plus me-2"></i>Ajukan Proposal PKK</a></li>
                    <li><a href="#"><i class="fas fa-eye me-2"></i>Lihat Proposal PKK</a></li>
                </ul>
            </li>
            
            <li>
                <a href="#" class="menu-toggle" data-target="pkm" id="pkmMenu">
                    <i class="fas fa-lightbulb me-2"></i>PKM
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu" id="pkm">
                    <li><a href="{{ route('operator.dashboard') }}" class="@if(request()->routeIs('operator.dashboard')) active @endif">
                        <i class="fas fa-home me-2"></i>Beranda
                    </a></li>
                    <li><a href="{{ route('operator.pilih.reviewer') }}" class="@if(request()->routeIs('operator.pilih.reviewer')) active @endif">
                        <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                    </a></li>
                    <li><a href="{{ route('operator.ruang.kontrol') }}" class="@if(request()->routeIs('operator.ruang.kontrol')) active @endif">
                        <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                    </a></li>
                    <li><a href="{{ route('operator.hasil.final') }}" class="@if(request()->routeIs('operator.hasil.final')) active @endif">
                        <i class="fas fa-trophy me-2"></i>Hasil Final
                    </a></li>
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

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Operator layout loaded successfully');
        });
    </script>
    @yield('scripts')
</body>
</html>

