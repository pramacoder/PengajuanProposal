@props([
    'items' => [],
])

@if(!empty($items))
    <nav aria-label="breadcrumb" class="app-breadcrumb">
        <ol class="breadcrumb mb-0">
            @foreach($items as $item)
                @php
                    $label = $item['label'] ?? '';
                    $url = $item['url'] ?? null;
                    $active = (bool) ($item['active'] ?? false);
                @endphp
                @if($active || !$url)
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
