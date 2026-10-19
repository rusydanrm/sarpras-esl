<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Tanggal & Pemohon</th>
                    <th class="text-left">Jenis & Objek</th>
                    <th class="text-left">Deskripsi Kebutuhan</th>
                    <th class="text-left">Status Alur</th>
                    <th class="text-center w-36">Aksi & Alur</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuan as $p)
                    @php
                        $initial = strtoupper(mb_substr($p->pemohon->nama ?? 'U', 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($pengajuan->currentPage() - 1) * $pengajuan->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0E2E] text-sm truncate" title="{{ $p->pemohon->nama }}">
                                        {{ $p->pemohon->nama }}
                                    </div>
                                    <div class="text-xs text-[#6C7E75] flex items-center gap-1.5 mt-0.5">
                                        <x-heroicon-o-calendar class="w-3.5 h-3.5 text-[#9EA9B9]" />
                                        <span>{{ $p->tanggal_pengajuan->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold w-fit
                                    @if ($p->jenis_pengajuan === 'Pengadaan') bg-blue-50 text-blue-700 border border-blue-200/60
                                    @elseif ($p->jenis_pengajuan === 'Perbaikan') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @elseif ($p->jenis_pengajuan === 'Pemeliharaan') bg-emerald-50 text-emerald-700 border border-emerald-200/60
                                    @else bg-purple-50 text-purple-700 border border-purple-200/60
                                    @endif">
                                    {{ $p->jenis_pengajuan }}
                                </span>
                                <div class="text-xs font-semibold text-[#0A0E2E] truncate max-w-[200px]" title="{{ $p->barang?->nama_barang ?? $p->ruangan?->nama_ruang ?? 'Pengadaan Baru' }}">
                                    @if ($p->barang)
                                        <a href="{{ route('rekomendasi.show', $p->barang_id) }}" class="hover:text-[#120F8B] hover:underline">
                                            {{ $p->barang->nama_barang }}
                                        </a>
                                        @if ($p->barang->ruangan)
                                            <span class="text-[10px] text-[#6C7E75] block truncate font-normal">
                                                di {{ $p->barang->ruangan->nama_ruang }}
                                            </span>
                                        @endif
                                    @elseif ($p->ruangan)
                                        <a href="{{ route('ruangan.show', $p->ruangan) }}" class="hover:text-[#120F8B] hover:underline">
                                            {{ $p->ruangan->nama_ruang }}
                                        </a>
                                    @else
                                        <span class="text-[#6C7E75] italic">Usulan Pengadaan</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="max-w-xs">
                            <p class="text-xs text-[#495465] line-clamp-2 leading-relaxed" title="{{ $p->deskripsi }}">
                                {{ $p->deskripsi }}
                            </p>
                        </td>
                        <td>
                            @if ($p->status === 'Diajukan')
                                <span class="badge-table warning">Diajukan</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Menunggu Verifikasi Admin</span>
                            @elseif ($p->status === 'Diverifikasi')
                                <span class="badge-table accent">Diverifikasi</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Menunggu Pimpinan</span>
                            @elseif ($p->status === 'Disetujui')
                                <span class="badge-table success">Disetujui</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Siap Diproses</span>
                            @elseif ($p->status === 'Diproses')
                                <span class="badge-table warning">Diproses</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Dalam Pengerjaan</span>
                            @elseif ($p->status === 'Selesai')
                                <span class="badge-table success">Selesai</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Tuntas</span>
                            @elseif ($p->status === 'Ditolak')
                                <span class="badge-table danger">Ditolak</span>
                                @if ($p->catatan_verifikasi)
                                    <span class="text-[10px] text-rose-600 block mt-0.5 truncate max-w-[150px]" title="{{ $p->catatan_verifikasi }}">
                                        "{{ $p->catatan_verifikasi }}"
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Detail Button (Eye) -->
                                <button type="button"
                                    class="table-btn-action"
                                    title="Lihat Detail & Riwayat Pengajuan"
                                    @click='bukaDetail({{ Illuminate\Support\Js::from($p) }})'>
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                <!-- Action Workflow Form Buttons -->
                                @if ($p->status === 'Diajukan' && auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('pengajuan.verifikasi', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-check-badge class="w-3.5 h-3.5 text-[#ffa916]" />
                                            <span>Verifikasi</span>
                                        </button>
                                    </form>
                                @endif

                                @if ($p->status === 'Diverifikasi' && auth()->user()->role === 'pimpinan')
                                    <form method="POST" action="{{ route('pengajuan.setujui', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                            <span>Setujui</span>
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="table-btn-action delete"
                                        title="Tolak Pengajuan"
                                        @click="bukaTolakModal({{ $p->pengajuan_id }})">
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                @endif

                                @if ($p->status === 'Disetujui' && in_array(auth()->user()->role, ['admin', 'teknisi']))
                                    <form method="POST" action="{{ route('pengajuan.proses', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-play class="w-3.5 h-3.5 text-[#ffa916]" />
                                            <span>Proses</span>
                                        </button>
                                    </form>
                                @endif

                                @if ($p->status === 'Diproses' && in_array(auth()->user()->role, ['admin', 'teknisi']))
                                    <form method="POST" action="{{ route('pengajuan.selesai', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-check class="w-3.5 h-3.5" />
                                            <span>Selesai</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                <x-heroicon-o-document-text class="w-6 h-6" />
                            </div>
                            <p class="text-sm font-semibold text-[#0A0E2E]">Tidak ada pengajuan ditemukan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Belum ada pengajuan dengan filter status atau jenis yang dipilih.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $pengajuan->links() }}
    </div>
</div>
