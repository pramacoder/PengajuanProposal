@props([
    'title' => null,
    'icon' => null,
    'headerClass' => '',
    'bodyClass' => '',
])

<div {{ $attributes->merge(['class' => 'card card-custom']) }}>
    @if($title)
        <div class="card-header card-header-custom {{ $headerClass }}">
            <h6 class="mb-0">
                @if($icon)
                    <i class="fas {{ $icon }} me-2"></i>
                @endif
                {{ $title }}
            </h6>
        </div>
    @endif
    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
