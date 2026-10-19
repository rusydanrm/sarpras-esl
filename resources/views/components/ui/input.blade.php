@props(['label' => null, 'error' => null, 'type' => 'text', 'required' => false])

@php
    $isRequired = $required || $attributes->has('required');
@endphp

<div class="w-full">
    @if ($label)
        <label class="text-xs font-bold text-slate-700 block mb-1.5 uppercase tracking-wider">
            {{ $label }}
            @if ($isRequired)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
    @endif
    <input type="{{ $type }}" {{ $isRequired ? 'required' : '' }} {{ $attributes->merge(['class' => 'w-full bg-white border border-[#E9EFEF] rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#ffa916]/20 focus:border-[#ffa916] transition-all duration-150 shadow-[0_2px_8px_rgba(11,19,15,0.02)]']) }}>
    @if ($error)
        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $error }}</p>
    @endif
</div>