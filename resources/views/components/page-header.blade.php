@props([
    'title' => 'Dashboard',
    'subtitle' => 'UNIVERSITAS UDAYANA',
    'description' => null,
    'showBackButton' => false,
    'backUrl' => null,
    'backText' => 'Kembali'
])

<div class="d-flex justify-content-center align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" style="width: 80px; height: 80px; object-fit: contain;">
        </div>
        <div class="text-center">
            <h2 class="mb-0 fw-bold">{{ $title }}</h2>
            <h4 class="text-muted mb-0">{{ $subtitle }}</h4>
            @if($description)
                <p class="text-muted mb-0 mt-2">{{ $description }}</p>
            @endif
            @if($showBackButton && $backUrl)
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm mt-2">
                    <i class="fas fa-arrow-left me-1"></i>{{ $backText }}
                </a>
            @endif
        </div>
    </div>
</div>

@if($showBackButton || $description)
    <hr class="mb-4">
@endif

