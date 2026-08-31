@props(['className' => ''])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden ' . $className]) }}>
    {{ $slot }}
</div>
