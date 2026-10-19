@props(['variant' => 'primary', 'type' => 'button', 'size' => 'md'])

@php
    $style = match ($variant) {
        'primary' => 'bg-[#120F8B] text-white hover:bg-[#0A0E2E] shadow-sm shadow-[#120F8B]/20 border border-transparent focus:ring-[#120F8B]/20',
        'accent' => 'bg-[#ffa916] text-[#0A0E2E] font-bold hover:bg-[#ffb738] shadow-sm shadow-[#ffa916]/25 border border-transparent focus:ring-[#ffa916]/30',
        'outline' => 'bg-white border border-[#E9EFEF] text-slate-700 hover:bg-[#F4F6F5] hover:border-slate-300 shadow-[0_2px_8px_rgba(11,19,15,0.02)] focus:ring-slate-200',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 shadow-sm shadow-rose-600/20 border border-transparent focus:ring-rose-500/20',
        'danger-light' => 'bg-rose-50 border border-rose-200/80 text-rose-700 hover:bg-rose-100/70 focus:ring-rose-500/20',
        'ghost' => 'bg-transparent text-[#120F8B] hover:bg-[#120F8B]/5 border border-transparent focus:ring-[#120F8B]/20',
        'excel' => 'bg-[#1D6F42] text-white hover:bg-[#185C37] shadow-sm shadow-emerald-900/20 border border-transparent focus:ring-emerald-600/20',
        'pdf' => 'bg-[#C8102E] text-white hover:bg-[#A50D26] shadow-sm shadow-rose-900/20 border border-transparent focus:ring-rose-600/20',
        default => 'bg-[#120F8B] text-white',
    };
    $sizing = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs rounded-lg',
        'lg' => 'px-6 py-3 text-base rounded-xl',
        default => 'px-4 py-2.5 text-sm rounded-xl',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$sizing font-semibold transition-all duration-150 ease-out active:scale-[0.98] $style disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 inline-flex items-center justify-center gap-2 cursor-pointer focus:outline-none focus:ring-2"]) }}>
    {{ $slot }}
</button>