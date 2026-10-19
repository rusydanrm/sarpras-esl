<x-layouts.app>
    <div class="text-center py-10">
        <div
            class="w-16 h-16 rounded-full bg-[#F0FEF9] text-[#10B981] flex items-center justify-center mx-auto text-3xl mb-4">
            ✓</div>
        <h1 class="text-xl font-bold">Impor Berhasil</h1>
        <p class="text-sm text-[#495465] mt-2">{{ $jumlah }} data barang berhasil disimpan.</p>
        <a href="{{ route('barang.index') }}"><x-ui.button class="mt-5">Lihat Data Barang</x-ui.button></a>
    </div>
</x-layouts.app>