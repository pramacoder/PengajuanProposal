@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'disabled' => false])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium transition-colors rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy-400 disabled:opacity-50 disabled:pointer-events-none';
    
    $variants = [
        'primary' => 'bg-navy-600 text-white hover:bg-navy-700 shadow-sm',
        'secondary' => 'bg-slate-100 text-slate-800 hover:bg-slate-200',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 shadow-sm',
        'ghost' => 'hover:bg-slate-100 text-slate-700',
    ];
    
    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-10 px-4 py-2 text-sm',
        'lg' => 'h-12 px-6 text-base',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) disabled @endif>
    {{ $slot }}
</button>
