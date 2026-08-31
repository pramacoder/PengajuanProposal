@props(['variant' => 'default'])

@php
    $baseClasses = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2';
    
    $variants = [
        'valid' => 'bg-green-100 text-green-800 border border-green-200',
        'rejected' => 'bg-red-100 text-red-800 border border-red-200',
        'revision' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'pending' => 'bg-orange-100 text-orange-800 border border-orange-200',
        'info' => 'bg-blue-100 text-blue-800 border border-blue-200',
        'default' => 'bg-slate-100 text-slate-800 border border-slate-200',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
