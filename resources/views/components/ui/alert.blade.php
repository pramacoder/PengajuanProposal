@props([
    'type' => 'info',
    'icon' => null,
    'dismissible' => false,
])

@php
    $bgColor = match($type) {
        'success' => 'bg-emerald-50',
        'danger' => 'bg-red-50',
        'warning' => 'bg-amber-50',
        'info' => 'bg-blue-50',
        default => 'bg-slate-50',
    };

    $borderColor = match($type) {
        'success' => 'border-emerald-200',
        'danger' => 'border-red-200',
        'warning' => 'border-amber-200',
        'info' => 'border-blue-200',
        default => 'border-slate-200',
    };

    $textColor = match($type) {
        'success' => 'text-emerald-800',
        'danger' => 'text-red-800',
        'warning' => 'text-amber-800',
        'info' => 'text-blue-800',
        default => 'text-slate-800',
    };

    $iconColor = match($type) {
        'success' => 'text-emerald-500',
        'danger' => 'text-red-500',
        'warning' => 'text-amber-500',
        'info' => 'text-blue-500',
        default => 'text-slate-500',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex p-4 rounded-lg border $bgColor $borderColor $textColor mb-4"]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $iconColor }} text-lg mt-0.5 mr-3 flex-shrink-0"></i>
    @endif
    
    <div class="flex-1">
        {{ $slot }}
    </div>

    @if($dismissible)
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-slate-400 hover:text-slate-900 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-slate-200 transition-colors" data-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>
