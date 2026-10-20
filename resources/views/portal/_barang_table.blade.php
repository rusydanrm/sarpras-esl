<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Barang & Identitas</th>
                    <th class="text-left">Merek / Tipe</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-right">Kuantitas</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Total Perolehan</th>
                    <th class="text-left">Sumber Dana</th>
                    <th class="text-left">Kondisi Fisik</th>
                    <th class="text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $b)
                    @php
                        $initial = strtoupper(mb_substr($b->nama_barang, 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}
                        </td>

                        <!-- Barang & Identitas -->
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-extrabold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0E2E] text-sm truncate max-w-[220px]" title="{{ $b->nama_barang }}">
                                        {{ $b->nama_barang }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        @if ($b->kode_barang)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-[#0A0E2E] text-[#ffa916]">
                                                {{ $b->kode_barang }}
                                            </span>
                                        @endif
                                        @if ($b->nup)
                                            <span class="text-xs text-[#6C7E75] font-mono">
                                                NUP: {{ $b->nup }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Merek & Tipe -->
                        <td>
                            <span class="text-xs text-[#0A0E2E] font-medium">
                                {{ $b->merek_tipe ?: '-' }}
                            </span>
                        </td>

                        <!-- Tahun Perolehan -->
                        <td class="text-center">
                            <span class="text-xs text-[#0A0E2E] font-semibold">
                                {{ $b->tahun_perolehan ?: '-' }}
                            </span>
                        </td>

                        <!-- Kuantitas -->
                        <td class="text-right">
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-[#F4F6F5] border border-[#E9EFEF] text-[#0A0E2E]">
                                {{ $b->jumlah }} {{ $b->satuan }}
                            </span>
                        </td>

                        <!-- Harga Satuan -->
                        <td class="text-right text-xs text-[#6C7E75]">
                            Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
                        </td>

                        <!-- Total Perolehan -->
                        <td class="text-right text-xs font-bold text-[#0A0E2E]">
                            Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                        </td>

                        <!-- Sumber Dana -->
                        <td>
                            <span class="text-xs text-[#6C7E75]">
                                {{ $b->sumber_dana ?: '-' }}
                            </span>
                        </td>

                        <!-- Kondisi Fisik -->
                        <td>
                            <div class="flex flex-wrap gap-1.5">
                                @if ($b->kondisi_baik > 0)
                                    <span class="badge-table success">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $b->kondisi_baik }} Baik</span>
                                    </span>
                                @endif
                                @if ($b->kondisi_rusak_ringan > 0)
                                    <span class="badge-table warning">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $b->kondisi_rusak_ringan }} RR</span>
                                    </span>
                                @endif
                                @if ($b->kondisi_rusak_berat > 0)
                                    <span class="badge-table danger">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $b->kondisi_rusak_berat }} RB</span>
                                    </span>
                                @endif
                                @if ($b->kondisi_baik == 0 && $b->kondisi_rusak_ringan == 0 && $b->kondisi_rusak_berat == 0)
                                    <span class="text-xs text-[#6C7E75]">-</span>
                                @endif
                            </div>
                        </td>

                        <!-- Keterangan -->
                        <td class="max-w-[180px]">
                            <span class="text-xs text-[#6C7E75] truncate block" title="{{ $b->keterangan }}">
                                {{ $b->keterangan ?: '-' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                <x-heroicon-o-inbox class="w-6 h-6 opacity-40" />
                            </div>
                            <p class="text-sm font-bold text-[#0A0E2E]">Belum ada barang di ruangan ini</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan filter kondisi barang di atas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $barang->links() }}
    </div>
</div>