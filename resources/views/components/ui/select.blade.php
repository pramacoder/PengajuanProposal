@props(['label' => '', 'hint' => '', 'error' => '', 'required' => false, 'options' => []])

<div class="space-y-1.5 w-full">
    @if($label)
        <label class="block text-sm font-medium text-slate-700">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    
    <select {{ $attributes->merge([
        'class' => 'flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent disabled:cursor-not-allowed disabled:opacity-50' . ($error ? ' border-red-500 focus:ring-red-500' : '')
    ]) }}>
        @if($attributes->has('placeholder'))
            <option value="" disabled selected>{{ $attributes->get('placeholder') }}</option>
        @endif
        
        @foreach($options as $option)
            <option value="{{ $option['value'] ?? $option }}" {{ $attributes->get('value') == ($option['value'] ?? $option) ? 'selected' : '' }}>
                {{ $option['label'] ?? $option }}
            </option>
        @endforeach
        
        {{ $slot }}
    </select>
    
    @if($error)
        <p class="text-xs text-red-500">{{ $error }}</p>
    @elseif($hint)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
