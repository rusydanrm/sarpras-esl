<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Barang</th>
                    <th class="text-left">Ruangan & Gedung</th>
                    <th class="text-left">Kondisi Unit</th>
                    <th class="text-center">Usia</th>
                    <th class="text-left">Pemeliharaan</th>
                    <th class="text-center w-20">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview as $b)
                    @php
                        $usia = $b->usia_tahun;
                        $masaPakai = $b->masa_pakai;
                        $jumlahServis = $b->pemeliharaan->count();
                        $totalBiaya = $b->pemeliharaan->sum('biaya');
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($preview->currentPage() - 1) * $preview->perPage() }}
                        </td>
                        <td>
                            <div class="min-w-0 max-w-[240px]">
                                <a href="{{ route('rekomendasi.show', $b->barang_id) }}" 
                                    class="font-bold text-[#0A0E2E] hover:text-[#ffa916] transition-colors text-xs sm:text-sm truncate block"
                                    title="{{ $b->nama_barang }}">
                                    {{ $b->nama_barang }}
                                </a>
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
                                    <a href="{{ route('ruangan.show', $b->ruangan) }}"
                                        class="text-xs font-semibold text-[#0A0E2E] hover:text-[#120F8B] hover:underline block truncate"
                                        title="{{ $b->ruangan->nama_ruang }}">
                                        {{ $b->ruangan->nama_ruang }}
                                    </a>
                                    <span class="text-[11px] text-[#6C7E75] block truncate">
                                        {{ $b->ruangan->gedung?->nama_gedung ?? '-' }}
                                    </span>
                                @else
                                    <span class="text-[#9EA9B9] text-xs italic">Tanpa Ruangan</span>
                                @endif
                            </div>
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
                            @if ($usia !== null)
                                <span class="text-xs font-semibold {{ $masaPakai && $usia >= $masaPakai ? 'text-amber-600 font-bold' : 'text-[#495465]' }}">
                                    {{ $usia }} Thn
                                </span>
                            @else
                                <span class="text-[#9EA9B9] text-xs">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumlahServis > 0)
                                <div>
                                    <span class="text-xs font-bold text-[#0A0E2E]">{{ $jumlahServis }}x Servis</span>
                                    @if ($totalBiaya > 0)
                                        <span class="text-[11px] text-[#6C7E75] block mt-0.5 font-medium">
                                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-[#9EA9B9]">-</span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <a href="{{ route('rekomendasi.show', $b->barang_id) }}"
                                class="table-btn-action"
                                title="Lihat Detail & Riwayat Servis">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
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