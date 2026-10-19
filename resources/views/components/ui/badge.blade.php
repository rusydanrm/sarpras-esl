@props(['variant' => 'default'])

@php
    $style = match ($variant) {
        'success' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
        'warning' => 'bg-[#ffa916]/10 text-[#b45309] border border-[#ffa916]/25',
        'danger' => 'bg-rose-50 text-rose-700 border border-rose-200/60',
        'primary' => 'bg-[#120F8B]/10 text-[#120F8B] border border-[#120F8B]/20',
        'accent' => 'bg-[#ffa916]/15 text-[#92400E] border border-[#ffa916]/30',
        default => 'bg-slate-100 text-slate-700 border border-slate-200/60',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-0.5 rounded-full text-xs font-semibold tracking-wide inline-flex items-center gap-1.5 shadow-[0_1px_2px_rgba(0,0,0,0.02)] $style"]) }}>
    {{ $slot }}
</span>