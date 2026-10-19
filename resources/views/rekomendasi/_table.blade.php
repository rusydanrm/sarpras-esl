<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Barang & Spesifikasi</th>
                    <th class="text-left">Ruangan & Gedung</th>
                    <th class="text-center">Usia Aset</th>
                    <th class="text-left">Kondisi Unit</th>
                    <th class="text-center">Riwayat Servis</th>
                    <th class="text-right">Total Biaya Servis</th>
                    <th class="text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview as $b)
                    @php
                        $usia = $b->usia_tahun;
                        $masaPakai = $b->masa_pakai;
                        $jumlahServis = $b->pemeliharaan->count();
                        $totalBiaya = $b->pemeliharaan->sum('biaya');
                        $servisTerakhir = $b->pemeliharaan->first();
                        $initial = strtoupper(mb_substr($b->nama_barang, 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($preview->currentPage() - 1) * $preview->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('rekomendasi.show', $b->barang_id) }}" 
                                        class="font-bold text-[#0A0E2E] hover:text-[#ffa916] transition-colors text-sm truncate block"
                                        title="{{ $b->nama_barang }}">
                                        {{ $b->nama_barang }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-xs text-[#6C7E75]">
                                        @if ($b->kode_barang)
                                            <span class="font-mono text-[11px] bg-[#F4F6F5] px-1.5 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]/60">
                                                {{ $b->kode_barang }}
                                            </span>
                                        @endif
                                        @if ($b->nup)
                                            <span class="text-[10px] text-[#9EA9B9]">NUP: {{ $b->nup }}</span>
                                        @endif
                                        @if ($b->merek_tipe)
                                            <span class="text-[#6C7E75] truncate max-w-[140px]">• {{ $b->merek_tipe }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($b->ruangan)
                                <a href="{{ route('ruangan.show', $b->ruangan) }}"
                                    class="text-xs font-semibold text-[#0A0E2E] hover:text-[#120F8B] hover:underline block">
                                    {{ $b->ruangan->nama_ruang }}
                                </a>
                                <span class="text-[11px] text-[#6C7E75]">
                                    {{ $b->ruangan->gedung?->nama_gedung ?? '-' }}
                                </span>
                            @else
                                <span class="text-[#9EA9B9] text-xs italic">Tanpa Ruangan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($usia !== null)
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-xs font-bold {{ $masaPakai && $usia >= $masaPakai ? 'text-amber-600' : 'text-[#0A0E2E]' }}">
                                        {{ $usia }} Tahun
                                    </span>
                                    <span class="text-[10px] text-[#9EA9B9]">Std: {{ $masaPakai }} Thn</span>
                                </div>
                            @else
                                <span class="text-[#9EA9B9] text-xs">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if ($b->kondisi_baik > 0)
                                    <span class="badge-table success">{{ $b->kondisi_baik }} Baik</span>
                                @endif
                                @if ($b->kondisi_rusak_ringan > 0)
                                    <span class="badge-table warning">{{ $b->kondisi_rusak_ringan }} RR</span>
                                @endif
                                @if ($b->kondisi_rusak_berat > 0)
                                    <span class="badge-table danger">{{ $b->kondisi_rusak_berat }} RB</span>
                                @endif
                                @if ($b->kondisi_baik == 0 && $b->kondisi_rusak_ringan == 0 && $b->kondisi_rusak_berat == 0)
                                    <span class="text-xs text-[#9EA9B9]">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if ($jumlahServis > 0)
                                <div class="inline-flex flex-col items-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] text-xs font-bold text-[#0A0E2E]">
                                        {{ $jumlahServis }}x Servis
                                    </span>
                                    @if ($servisTerakhir?->tanggal_tindakan)
                                        <span class="text-[10px] text-[#6C7E75] mt-0.5">
                                            {{ $servisTerakhir->tanggal_tindakan->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-[#9EA9B9]">Belum pernah</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($totalBiaya > 0)
                                <span class="font-bold text-xs text-[#0A0E2E]">
                                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xs text-[#9EA9B9]">Rp 0</span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <a href="{{ route('rekomendasi.show', $b->barang_id) }}"
                                class="table-btn-action"
                                title="Lihat Detail & Riwayat MR">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
                            </div>
                            <p class="text-sm font-semibold text-[#0A0E2E]">Tidak ada data inventaris ditemukan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau filter monitoring di atas.</p>
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