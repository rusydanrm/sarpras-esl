<x-layouts.app title="Impor Berhasil">
    <div class="max-w-xl mx-auto py-12">
        <div class="bg-white border border-[#E9EFEF] rounded-3xl p-8 sm:p-10 text-center shadow-sm relative overflow-hidden">
            <!-- Ambient Spark Glow -->
            <div class="absolute -top-16 -right-16 w-40 h-40 bg-[#ffa916]/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-[#0A0E2E]/5 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Success Icon Badge -->
            <div class="relative w-20 h-20 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-sm">
                <x-heroicon-o-check-badge class="w-10 h-10" />
                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-[#ffa916] text-[#0A0E2E] flex items-center justify-center text-xs font-black shadow-xs">
                    ✓
                </span>
            </div>

            <!-- Title & Description -->
            <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E] mb-2">Impor Data Berhasil Disimpan!</h1>
            <p class="text-xs text-[#6C7E75] max-w-md mx-auto leading-relaxed">
                Seluruh data barang inventaris telah berhasil divalidasi dan dicatat ke dalam basis data Departemen ESL IPB University.
            </p>

            <!-- Quantity Counter Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold my-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ $jumlah }} Item Barang Baru Terdaftar</span>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ route('barang.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-sm shadow-[#0A0E2E]/15 cursor-pointer">
                    <x-heroicon-o-archive-box class="w-4 h-4 text-[#ffa916]" />
                    <span>Buka Data Barang</span>
                </a>

                <a href="{{ route('impor.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-[#E9EFEF] text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors cursor-pointer">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4 text-[#6C7E75]" />
                    <span>Impor Data Lainnya</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>