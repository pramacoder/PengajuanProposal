<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISKA - Sistem Pengajuan Proposal')</title>
    <!-- PDF.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
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
<body class="bg-navy-50 text-slate-800 antialiased selection:bg-navy-200 selection:text-navy-900 flex h-screen overflow-hidden">
    
    <!-- Sidebar (Left) -->
    @include('mainlayout.sidebar')

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f8fafc]">
        
        <!-- Navbar (Top) -->
        @include('mainlayout.navbar')

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <x-flash-messages />
            @yield('content')
        </main>
        
    </div>



    @if(auth()->check() && in_array(auth()->user()->role, ['reviewer', 'operator', 'pimpinan_pt']))
    <div id="reviewModal" class="fixed inset-0 z-[60] hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-navy-950/50 backdrop-blur-sm transition-opacity"></div>
        <!-- Dialog -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200">
                    <div class="bg-navy-900 px-6 py-4 flex justify-between items-center">
                        <h5 class="text-lg font-semibold text-white flex items-center gap-2" id="reviewModalTitle">
                            <i class="fas fa-clipboard-list text-navy-200"></i> Hasil Review
                        </h5>
                        <button type="button" class="text-navy-200 hover:text-white transition-colors" onclick="document.getElementById('reviewModal').classList.add('hidden')">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="px-6 py-5" id="reviewModalBody">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[70] flex flex-col gap-2 pointer-events-none"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html> 