<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Proposal PKM')</title>
    <!-- PDF.js - Hanya muat sekali di sini -->
    @vite(['resources/css/app.css', 'resources/css/layout.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { padding-top: var(--navbar-height); }

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
    </style>
    @yield('styles')
    @yield('dosen_styles')
</head>
<body>
    <!-- Navbar -->
    @include('mainlayout.navbar')

    <!-- Sidebar Overlay & Menu -->
    @include('mainlayout.sidebar')

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
    @include('mainlayout.footer-scripts')
    @stack('scripts')
    @yield('scripts')
</body>
</html> 