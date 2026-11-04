<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Nama Barang & Identitas</th>
                    <th class="text-left">Ruangan & Gedung</th>
                    <th class="text-center">Kuantitas</th>
                    <th class="text-left">Kondisi Fisik</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Total Perolehan</th>
                    <th class="text-left">Sumber & Tahun</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview as $b)
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($preview->currentPage() - 1) * $preview->perPage() }}
                        </td>
                        <td>
                            <div class="min-w-0 max-w-[240px]">
                                <div class="font-bold text-[#0A0E2E] text-xs sm:text-sm truncate" title="{{ $b->nama_barang }}">
                                    {{ $b->nama_barang }}
                                </div>
                                @if ($b->kode_barang)
                                    <span class="text-[11px] font-mono text-[#6C7E75] block mt-0.5">
                                        {{ $b->kode_barang }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="max-w-[210px] xl:max-w-[260px]">
                                @if ($b->ruangan)
                                    <div class="font-semibold text-xs text-[#0A0E2E] truncate" title="{{ $b->ruangan->nama_ruang }}">{{ $b->ruangan->nama_ruang }}</div>
                                    <div class="text-[11px] text-[#6C7E75] truncate">{{ $b->ruangan->gedung?->nama_gedung ?? '-' }}</div>
                                @else
                                    <span class="text-[#9EA9B9] text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center font-bold text-xs text-[#0A0E2E]">
                            {{ number_format($b->jumlah, 0, ',', '.') }} <span class="text-[11px] font-normal text-[#6C7E75]">{{ $b->satuan ?? 'Unit' }}</span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @if ($b->kondisi_baik > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $b->kondisi_baik }} Baik
                                    </span>
                                @endif
                                @if ($b->kondisi_rusak_ringan > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $b->kondisi_rusak_ringan }} RR
                                    </span>
                                @endif
                                @if ($b->kondisi_rusak_berat > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $b->kondisi_rusak_berat }} RB
                                    </span>
                                @endif
                                @if ($b->kondisi_baik == 0 && $b->kondisi_rusak_ringan == 0 && $b->kondisi_rusak_berat == 0)
                                    <span class="text-[#9EA9B9] text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-right text-xs font-medium text-[#495465]">
                            {{ $b->harga_satuan ? 'Rp ' . number_format($b->harga_satuan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right text-xs font-bold text-[#0A0E2E]">
                            Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                        </td>
                        <td>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs font-semibold text-[#0A0E2E]">{{ $b->sumber_dana ?: '-' }}</span>
                                <span class="text-[11px] text-[#6C7E75]">Thn: {{ $b->tahun_perolehan ?: '-' }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#6C7E75] mx-auto mb-3">
                                <x-heroicon-o-document-magnifying-glass class="w-6 h-6" />
                            </div>
                            <p class="font-bold text-sm text-[#0A0E2E]">Tidak ada data inventaris</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau kombinasi filter Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $preview->links() }}
    </div>
</div>