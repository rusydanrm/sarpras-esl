<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($ruanganList as $r)
        <a href="{{ route('portal.show', $r) }}"
            class="bg-white border border-[#DEE2E7] rounded-xl p-5 hover:shadow-md transition-shadow block">
            <div class="flex justify-between items-start mb-1">
                <h3 class="font-semibold text-[#181B21]">{{ $r->nama_ruang }}</h3>
                @if ($r->kode_ruang)
                    <span
                        class="text-[10px] bg-[#F7F8F9] text-[#495465] px-2 py-0.5 rounded-full shrink-0 ml-2">{{ $r->kode_ruang }}</span>
                @endif
            </div>
            <p class="text-xs text-[#9EA9B9] mb-4">{{ $r->gedung->nama_gedung }}</p>
            <div class="flex justify-between items-end border-t border-[#DEE2E7] pt-3">
                <div>
                    <div class="text-[10px] text-[#9EA9B9] uppercase">Total Nilai Aset</div>
                    <div class="font-bold text-[#120F8B]">Rp
                        {{ number_format($r->barang_sum_total_harga ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-[#9EA9B9] uppercase">Item</div>
                    <div class="font-semibold">{{ $r->barang_count ?? 0 }}</div>
                </div>
            </div>
        </a>
    @empty
        <p class="col-span-full text-center text-[#9EA9B9] py-10">Tidak ada ruangan ditemukan.</p>
    @endforelse
</div>

<div class="mt-6">{{ $ruanganList->links() }}</div>