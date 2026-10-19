<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Barang & Spesifikasi</th>
                    <th class="text-left">Ruangan & Gedung</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-left">Kondisi Unit</th>
                    <th class="text-right">Harga Perolehan</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $b)
                    @php
                        $bolehEdit = auth()->user()->role === 'admin'
                            || (auth()->user()->role === 'dosen_tendik' && $b->ruangan?->dikoordinasiOleh(auth()->id()));
                        $initial = strtoupper(mb_substr($b->nama_barang, 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($barang->currentPage() - 1) * $barang->perPage() }}
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
                                    <div class="flex flex-wrap items-center gap-2 mt-0.5 text-xs text-[#6C7E75]">
                                        <span class="font-mono text-[11px] bg-[#F4F6F5] px-1.5 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]/60">
                                            {{ $b->kode_barang ?? '-' }}
                                        </span>
                                        @if ($b->merek_tipe)
                                            <span class="truncate max-w-[150px] text-[#6C7E75]">• {{ $b->merek_tipe }}</span>
                                        @endif
                                        @if ($b->nup)
                                            <span class="text-[10px] text-[#9EA9B9]">NUP: {{ $b->nup }}</span>
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
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-[#F4F6F5] border border-[#E9EFEF] text-xs font-bold text-[#0A0E2E]">
                                {{ $b->jumlah }} {{ $b->satuan }}
                            </span>
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
                        <td class="text-right">
                            <div class="font-bold text-xs text-[#0A0E2E]">
                                Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                            </div>
                            @if ($b->harga_satuan > 0 && $b->jumlah > 1)
                                <div class="text-[10px] text-[#9EA9B9]">
                                    @ Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Detail / Rekomendasi Button -->
                                <a href="{{ route('rekomendasi.show', $b->barang_id) }}"
                                    class="table-btn-action"
                                    title="Lihat Detail Barang">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>

                                @if ($bolehEdit)
                                    <!-- Edit Button -->
                                    <button type="button"
                                        class="table-btn-action"
                                        title="Edit Data Barang"
                                        @click='modalOpen = true; editData = {{ Illuminate\Support\Js::from($b) }}'>
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                    </button>

                                    <!-- Delete Button -->
                                    <x-ui.confirm-delete-form action="{{ route('barang.destroy', $b) }}"
                                        title="Hapus Barang"
                                        message="Barang '{{ $b->nama_barang }}' akan dihapus permanen dari data inventaris."
                                        buttonClass="table-btn-action delete">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </x-ui.confirm-delete-form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                <x-heroicon-o-inbox class="w-6 h-6" />
                            </div>
                            <p class="text-sm font-semibold text-[#0A0E2E]">Tidak ada data barang ditemukan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau filter kategori di atas.</p>
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