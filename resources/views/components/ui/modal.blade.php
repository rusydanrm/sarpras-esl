@props(['open' => 'modalOpen', 'title' => '', 'maxWidth' => 'md', 'zIndex' => 'z-50'])

@php
    $width = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'full' => 'max-w-full',
        default => 'max-w-md',
    };
@endphp

<div x-show="{{ $open }}" x-cloak
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-[#0A0E2E]/60 backdrop-blur-md flex items-center justify-center {{ $zIndex }} p-4">
    
    <div x-show="{{ $open }}"
        x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        @click.away="{{ $open }} = false"
        class="bg-white rounded-2xl w-full {{ $width }} p-6 max-h-[90vh] overflow-y-auto whitespace-normal text-left shadow-2xl border border-[#E9EFEF]">
        
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-[#E9EFEF]">
            @if ($title)
                <h2 class="text-base font-bold text-slate-900 -tracking-tight">{{ $title }}</h2>
            @else
                <div></div>
            @endif
            <button @click="{{ $open }} = false" type="button"
                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-[#F4F6F5] transition-all duration-100 active:scale-95">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        
        {{ $slot }}
    </div>
</div>