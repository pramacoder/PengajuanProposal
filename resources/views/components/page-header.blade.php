@props([
    'title' => 'Dashboard',
    'subtitle' => null,
    'description' => null,
    'icon' => null,
    'badge' => null,
    'badgeClass' => 'bg-secondary text-white',
    'showBackButton' => false,
    'backUrl' => null,
    'backText' => 'Kembali',
    'variant' => 'enterprise',
    'actions' => null,
])

@if($variant === 'centered')
<div class="d-flex justify-content-center align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" style="width: 80px; height: 80px; object-fit: contain;">
        </div>
        <div class="text-center">
            <h2 class="mb-0 fw-bold">{{ $title }}</h2>
            <h4 class="text-muted mb-0">{{ $subtitle ?? 'UNIVERSITAS UDAYANA' }}</h4>
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
@else
<div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header-left">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($showBackButton && $backUrl)
                <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary me-1" title="{{ $backText }}">
                    <i class="fas fa-arrow-left"></i>
                </a>
            @endif
            @if($icon)
                <div class="page-header-icon me-1">
                    <i class="fas {{ $icon }}"></i>
                </div>
            @endif
            <h1 class="h4 fw-bold mb-0 page-header-title" style="color: var(--text-900);">{{ $title }}</h1>
            @if($badge)
                <span class="badge {{ $badgeClass }} ms-2">{{ $badge }}</span>
            @endif
        </div>
        @if($subtitle || $description)
            <p class="text-muted mb-0 mt-1 page-header-subtitle" style="font-size: 0.875rem;">
                {{ $subtitle ?? $description }}
            </p>
        @endif
    </div>
    @if(isset($actions))
        <div class="page-header-actions d-flex align-items-center gap-2">
            {{ $actions }}
        </div>
    @elseif(!empty($slot) && $slot->isNotEmpty())
        <div class="page-header-actions d-flex align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
@endif

