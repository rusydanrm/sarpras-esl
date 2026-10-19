<div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[900px]">
        <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
            <tr>
                <th class="text-center px-4 py-3 w-12">No</th>
                <th class="text-left px-4 py-3">Kode Barang</th>
                <th class="text-left px-4 py-3">Nama Barang</th>
                <th class="text-left px-4 py-3">NUP</th>
                <th class="text-left px-4 py-3">Merek/Tipe</th>
                <th class="text-center px-4 py-3">Tahun</th>
                <th class="text-right px-4 py-3">Jumlah</th>
                <th class="text-right px-4 py-3">Harga Satuan</th>
                <th class="text-right px-4 py-3">Jumlah Harga Perolehan</th>
                <th class="text-left px-4 py-3">Sumber Dana</th>
                <th class="text-left px-4 py-3">Kondisi</th>
                <th class="text-left px-4 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barang as $b)
                <tr class="border-t border-[#DEE2E7]">
                    <td class="px-4 py-3 text-center whitespace-nowrap text-[#495465] text-xs font-medium">{{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $b->kode_barang ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium">{{ $b->nama_barang }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $b->nup ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $b->merek_tipe ?? '-' }}</td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">{{ $b->tahun_perolehan ?? '-' }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">{{ $b->jumlah }} {{ $b->satuan }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap font-medium">Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">{{ $b->sumber_dana ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
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
                    <td class="px-4 py-3 text-center max-w-[180px] truncate" title="{{ $b->keterangan }}">{{ $b->keterangan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="px-4 py-6 text-center text-[#9EA9B9]">Belum ada barang di ruangan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $barang->links() }}</div>