@extends('mainlayout.app')

@section('title', 'Dashboard Dosen - Sistem Proposal PKM')

@section('styles')
<style>
    .sidebar-menu .submenu a.active {
        background-color: rgba(255,255,255,0.2);
        border-right: 3px solid white;
        transform: translateX(5px);
    }
    
    .proposal-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .proposal-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .proposal-thumbnail {
        width: 80px;
        height: 100px;
        border: 1px solid #dc3545;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
    }
    
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-valid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-invalid {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-lolos {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .status-tidak-lolos {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-submitted {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    .status-review-administratif {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-review-substantif {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-revisi {
        background-color: #fff3cd;
        color: #856404;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" style="width: 80px; height: 80px; object-fit: contain;">
                    </div>
                    <div class="text-center">
                        <h2 class="mb-0 fw-bold">UNIVERSITAS UDAYANA</h2>
                        <h4 class="text-muted mb-0">@yield('page_title', 'Dashboard Dosen')</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @yield('dosen_content')
</div>
@endsection

@section('scripts')
<script>
    // Override sidebar menu untuk dosen
    document.addEventListener('DOMContentLoaded', function() {
        // Set menu PKM sebagai active
        const pkmMenu = document.querySelector('[data-target="pkm"]');
        if (pkmMenu) {
            pkmMenu.classList.add('active');
        }
        
        // Set submenu yang sesuai sebagai active
        const currentRoute = '{{ request()->route()->getName() }}';
        const submenuItems = document.querySelectorAll('.submenu a');
        
        submenuItems.forEach(item => {
            if (item.href && item.href.includes(currentRoute)) {
                item.classList.add('active');
            }
        });
    });
</script>
@yield('dosen_scripts')
@endsection

@section('dosen_styles')
@yield('dosen_styles')
@endsection
