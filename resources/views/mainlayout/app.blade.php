<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Proposal PKM')</title>
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

        /* Action Sidebar */
        .action-sidebar {
            position: fixed;
            right: 0;
            top: var(--navbar-height);
            width: 320px;
            height: calc(100vh - var(--navbar-height));
            background-color: white;
            box-shadow: -4px 0 12px rgba(0,0,0,0.15);
            padding: 1.5rem;
            z-index: 1001;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            border-radius: 12px 0 0 12px;
        }

        .action-sidebar.show {
            transform: translateX(0);
        }

        .action-sidebar h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .action-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
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

            .action-sidebar {
                width: 100%;
                border-radius: 0;
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
                PHIPROSAL
            </a>
            
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
                <!-- Notifications -->
                <div class="nav-item dropdown me-3 position-relative">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="notification-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <h6 class="dropdown-header bg-light">
                            <i class="fas fa-bell me-2"></i>Notifikasi
                        </h6>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <i class="fas fa-file-alt text-primary me-2 mt-1"></i>
                                <div>
                                    <strong>Proposal sedang direview</strong>
                                    <br><small class="text-muted">2 jam yang lalu</small>
                                </div>
                            </div>
                        </a>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <i class="fas fa-edit text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Ada revisi dari reviewer</strong>
                                    <br><small class="text-muted">1 hari yang lalu</small>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center text-primary" href="#">
                            <i class="fas fa-eye me-2"></i>Lihat semua notifikasi
                        </a>
                    </div>
                </div>
                
                <!-- User Profile -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-2">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <span class="fw-bold">{{ Auth::user()->name ?? 'Nama Mahasiswa' }}</span>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header">
                            <i class="fas fa-user-circle me-2"></i>Profil Pengguna
                        </div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i>Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
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
            PROPOSAL
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
                <a href="#" class="menu-toggle active" data-target="pkm">
                    <i class="fas fa-lightbulb me-2"></i>PKM
                    <i class="fas fa-chevron-down float-end mt-1"></i>
                </a>
                <ul class="submenu show" id="pkm">
                    <li><a href="{{ route('mahasiswa.proposal.create') }}" class="@if(request()->routeIs('mahasiswa.proposal.create')) active @endif">
                        <i class="fas fa-plus me-2"></i>Ajukan Proposal
                    </a></li>
                    <li><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.index')) active @endif">
                        <i class="fas fa-eye me-2"></i>Lihat Proposal
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

    <!-- Action Sidebar -->
    <div class="action-sidebar" id="actionSidebar">
        <h5>
            <i class="fas fa-tools me-2"></i>
            AKSI
        </h5>
        <div class="d-grid gap-2">
            <button class="btn action-btn" onclick="showReviewModal('administratif')">
                <i class="fas fa-clipboard-check me-2"></i>
                Hasil Review Administratif
            </button>
            <button class="btn action-btn" onclick="showReviewModal('subtantif')">
                <i class="fas fa-user-check me-2"></i>
                Hasil Review Subtantif
            </button>
        </div>
    </div>

    <!-- Review Modal -->
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

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Layout loaded successfully');
        });
    </script>
    @yield('scripts')
</body>
</html> 