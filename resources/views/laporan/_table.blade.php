<div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[760px]">
        <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
            <tr>
                <th class="text-center px-4 py-3 w-12">No</th>
                <th class="text-left px-4 py-3">Nama Barang</th>
                <th class="text-left px-4 py-3">Ruangan</th>
                <th class="text-left px-4 py-3">Gedung</th>
                <th class="text-right px-4 py-3">Jumlah</th>
                <th class="text-left px-4 py-3">Kondisi</th>
                <th class="text-right px-4 py-3">Jumlah Harga Perolehan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($preview as $b)
                <tr class="border-t border-[#DEE2E7]">
                    <td class="px-4 py-3 text-center text-[#495465] text-xs font-medium">
                        {{ $loop->iteration + ($preview->currentPage() - 1) * $preview->perPage() }}</td>
                    <td class="px-4 py-3">{{ $b->nama_barang }}</td>
                    <td class="px-4 py-3">{{ $b->ruangan?->nama_ruang ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $b->ruangan?->gedung?->nama_gedung ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">{{ $b->jumlah }} {{ $b->satuan }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            @if ($b->kondisi_baik > 0)
                                <x-ui.badge variant="success">{{ $b->kondisi_baik }} Baik</x-ui.badge>
                            @endif
                            @if ($b->kondisi_rusak_ringan > 0)
                                <x-ui.badge variant="warning">{{ $b->kondisi_rusak_ringan }} RR</x-ui.badge>
                            @endif
                            @if ($b->kondisi_rusak_berat > 0)
                                <x-ui.badge variant="danger">{{ $b->kondisi_rusak_berat }} RB</x-ui.badge>
                            @endif
                            @if ($b->kondisi_baik == 0 && $b->kondisi_rusak_ringan == 0 && $b->kondisi_rusak_berat == 0)
                                <span class="text-[#9EA9B9]">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-[#9EA9B9]">Tidak ada data sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $preview->links() }}</div>