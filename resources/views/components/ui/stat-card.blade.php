@props(['title', 'value', 'subtitle' => '', 'icon' => null, 'trend' => null, 'accent' => 'blue'])

@php
    $accents = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'yellow' => 'bg-yellow-50 text-yellow-600',
        'red' => 'bg-red-50 text-red-600',
        'navy' => 'bg-navy-50 text-navy-600',
    ];
    
    $accentClass = $accents[$accent] ?? $accents['blue'];
@endphp

<div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm transition-all hover:shadow-md">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">{{ $title }}</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $value }}</h3>
            
            @if($subtitle || $trend !== null)
                <div class="flex items-center gap-2 mt-2">
                    @if($trend !== null)
                        @if($trend > 0)
                            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-1.5 py-0.5 rounded">
                                <i class="fas fa-arrow-up mr-1 text-[10px]"></i> {{ $trend }}%
                            </span>
                        @elseif($trend < 0)
                            <span class="flex items-center text-xs font-medium text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                <i class="fas fa-arrow-down mr-1 text-[10px]"></i> {{ abs($trend) }}%
                            </span>
                        @else
                            <span class="flex items-center text-xs font-medium text-slate-600 bg-slate-50 px-1.5 py-0.5 rounded">
                                <i class="fas fa-minus mr-1 text-[10px]"></i> 0%
                            </span>
                        @endif
                    @endif
                    
                    @if($subtitle)
                        <p class="text-xs text-slate-400">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
        </div>
        
        @if($icon)
            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $accentClass }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
