@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'badge' => null,
    'badgeClass' => 'bg-secondary text-white',
    'headerClass' => '',
    'bodyClass' => '',
    'actions' => null,
])

<div {{ $attributes->merge(['class' => 'card card-custom']) }}>
    @if($title)
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 {{ $headerClass }}">
            <div class="d-flex align-items-center gap-2">
                @if($icon)
                    <i class="fas {{ $icon }} text-primary me-1"></i>
                @endif
                <div>
                    <h6 class="mb-0 fw-bold card-title-text">{{ $title }}</h6>
                    @if($subtitle)
                        <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $subtitle }}</small>
                    @endif
                </div>
                @if($badge)
                    <span class="badge {{ $badgeClass }} ms-1">{{ $badge }}</span>
                @endif
            </div>
            @if(isset($actions))
                <div class="card-header-actions d-flex align-items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
