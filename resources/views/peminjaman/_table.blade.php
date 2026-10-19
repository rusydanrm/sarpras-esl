<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Objek & Jenis</th>
                    <th class="text-left">Peminjam</th>
                    <th class="text-left">Rentang Jadwal</th>
                    <th class="text-left">Keperluan</th>
                    <th class="text-left">Status</th>
                    <th class="text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $p)
                    @php
                        $initial = strtoupper(mb_substr($p->peminjam->nama ?? 'U', 0, 1));
                        $isRuangan = $p->jenis_peminjaman === 'Ruangan';
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 border
                                    @if ($isRuangan) bg-blue-50 border-blue-200/70 text-blue-700 @else bg-purple-50 border-purple-200/70 text-purple-700 @endif">
                                    @if ($isRuangan)
                                        <x-heroicon-o-home-modern class="w-4 h-4" />
                                    @else
                                        <x-heroicon-o-cube class="w-4 h-4" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0E2E] text-sm truncate" title="{{ $p->nama_objek }}">
                                        {{ $p->nama_objek }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 text-xs text-[#6C7E75]">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold
                                            @if ($isRuangan) bg-blue-50 text-blue-700 @else bg-purple-50 text-purple-700 @endif">
                                            {{ $p->jenis_peminjaman }}
                                        </span>
                                        @if ($isRuangan && $p->ruangan?->gedung)
                                            <span>• {{ $p->ruangan->gedung->nama_gedung }}</span>
                                        @elseif (!$isRuangan && $p->barang?->ruangan)
                                            <span>• di {{ $p->barang->ruangan->nama_ruang }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="font-semibold text-[#0A0E2E] text-xs truncate max-w-[150px]" title="{{ $p->peminjam->nama }}">
                                    {{ $p->peminjam->nama }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-xs">
                                <div class="font-bold text-[#0A0E2E] flex items-center gap-1.5">
                                    <x-heroicon-o-calendar class="w-3.5 h-3.5 text-[#9EA9B9]" />
                                    <span>{{ $p->tanggal_mulai->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="text-[11px] text-[#6C7E75] mt-0.5 flex items-center gap-1.5">
                                    <span class="text-[#9EA9B9]">s.d.</span>
                                    <span>{{ $p->tanggal_selesai->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-xs">
                            <p class="text-xs text-[#495465] line-clamp-2 leading-relaxed" title="{{ $p->keterangan }}">
                                {{ $p->keterangan }}
                            </p>
                        </td>
                        <td>
                            @if ($p->status === 'Diajukan')
                                <span class="badge-table warning">Diajukan</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Menunggu Persetujuan</span>
                            @elseif ($p->status === 'Disetujui')
                                <span class="badge-table success">Disetujui</span>
                                <span class="text-[10px] text-emerald-600 block mt-0.5 font-medium">Siap Digunakan</span>
                            @elseif ($p->status === 'Dikembalikan')
                                <span class="badge-table accent">Dikembalikan</span>
                                <span class="text-[10px] text-[#6C7E75] block mt-0.5">Selesai</span>
                            @elseif ($p->status === 'Ditolak')
                                <span class="badge-table danger">Ditolak</span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Detail Button (Eye) -->
                                <button type="button"
                                    class="table-btn-action"
                                    title="Lihat Detail Peminjaman"
                                    @click='bukaDetail({{ Illuminate\Support\Js::from($p) }})'>
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                @if ($p->status === 'Diajukan' && in_array(auth()->user()->role, ['pimpinan', 'admin']))
                                    <!-- Setujui Button -->
                                    <form method="POST" action="{{ route('peminjaman.setujui', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <!-- Tolak Button -->
                                    <button type="button"
                                        class="table-btn-action delete"
                                        title="Tolak Peminjaman"
                                        @click="bukaTolakModal({{ $p->peminjaman_id }})">
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                @endif

                                @if ($p->status === 'Disetujui' && auth()->user()->role === 'admin')
                                    <!-- Tandai Dikembalikan Button -->
                                    <form method="POST" action="{{ route('peminjaman.kembalikan', $p) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <x-heroicon-o-arrow-path-rounded-square class="w-3.5 h-3.5 text-[#ffa916]" />
                                            <span>Kembalikan</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                <x-heroicon-o-calendar-days class="w-6 h-6" />
                            </div>
                            <p class="text-sm font-semibold text-[#0A0E2E]">Tidak ada data peminjaman ditemukan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Belum ada permohonan peminjaman dengan filter status atau kata kunci yang dipilih.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $peminjaman->links() }}
    </div>
</div>