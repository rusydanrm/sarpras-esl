@props(['action', 'title' => 'Hapus Data', 'message' => 'Tindakan ini tidak dapat dibatalkan.', 'buttonClass' => null])

<form method="POST" action="{{ $action }}" x-data="{ open: false }" class="inline">
    @csrf
    @method('DELETE')

    <button type="button" @click="open = true"
        class="{{ $buttonClass ?? 'px-3 py-1.5 text-xs rounded-xl font-medium transition-all bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 inline-flex items-center gap-1.5 border border-rose-200/60 active:scale-95' }}">
        {{ $slot }}
    </button>

    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 bg-[#0A0E2E]/60 backdrop-blur-md flex items-center justify-center z-50 p-4"
        @click.self="open = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 transform transition-all border border-[#E9EFEF] text-left whitespace-normal">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                </div>
                <h3 class="font-bold text-base text-slate-900 leading-tight whitespace-normal">{{ $title }}</h3>
            </div>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed whitespace-normal break-words">{{ $message }}</p>
            <div class="flex justify-end gap-2.5">
                <x-ui.button type="button" variant="outline" size="sm" @click="open = false">Batal</x-ui.button>
                <x-ui.button type="submit" variant="danger" size="sm">Ya, Hapus</x-ui.button>
            </div>
        </div>
    </div>
</form>