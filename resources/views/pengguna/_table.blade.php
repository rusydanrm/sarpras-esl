<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Pengguna & Identitas</th>
                    <th class="text-left">Email Akses</th>
                    <th class="text-left">Unit / Divisi</th>
                    <th class="text-left">Hak Akses (Role)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengguna as $p)
                    @php
                        $initial = strtoupper(mb_substr($p->nama, 0, 1));
                        $isPending = ($p->status === 'pending');
                    @endphp
                    <tr class="{{ $isPending ? 'bg-amber-50/30 hover:bg-amber-50/60 border-l-4 border-l-[#ffa916]' : '' }}">
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($pengguna->currentPage() - 1) * $pengguna->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $isPending 
                                        ? 'bg-[#ffa916]/15 text-[#ffa916] border border-[#ffa916]/30' 
                                        : 'bg-[#0A0E2E]/5 text-[#0A0E2E] border border-[#E9EFEF]' }}">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0E2E] text-sm truncate max-w-xs flex items-center gap-1.5" title="{{ $p->nama }}">
                                        <span>{{ $p->nama }}</span>
                                        @if ($p->user_id === auth()->id())
                                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-md bg-[#0A0E2E] text-white">Anda</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-[#6C7E75]">
                                        ID: #{{ $p->user_id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5 text-xs text-[#0A0E2E] font-medium">
                                <x-heroicon-o-envelope class="w-3.5 h-3.5 text-[#6C7E75]" />
                                <span>{{ $p->email }}</span>
                            </div>
                        </td>
                        <td>
                            @if ($p->unit)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F4F6F5] text-[#0A0E2E] border border-[#E9EFEF]">
                                    <x-heroicon-o-building-office class="w-3.5 h-3.5 text-[#6C7E75]" />
                                    {{ $p->unit->nama_unit }}
                                </span>
                            @else
                                <span class="text-xs text-[#9EA9B9]">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($p->role === 'admin')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#0A0E2E] text-[#ffa916] shadow-2xs">
                                    <x-heroicon-o-shield-check class="w-3.5 h-3.5" /> Admin
                                </span>
                            @elseif ($p->role === 'dosen_tendik')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <x-heroicon-o-academic-cap class="w-3.5 h-3.5" /> Dosen / Tendik
                                </span>
                            @elseif ($p->role === 'teknisi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    <x-heroicon-o-wrench-screwdriver class="w-3.5 h-3.5" /> Teknisi
                                </span>
                            @elseif ($p->role === 'pimpinan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <x-heroicon-o-briefcase class="w-3.5 h-3.5" /> Pimpinan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-dashed border-amber-300">
                                    Belum Ditentukan
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($p->status === 'aktif')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                </span>
                            @elseif ($p->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                </span>
                            @elseif ($p->status === 'nonaktif')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if ($isPending)
                                    <button type="button"
                                        @click='editUser({{ Illuminate\Support\Js::from($p) }})'
                                        class="px-2.5 py-1 rounded-xl bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] text-xs font-bold shadow-2xs flex items-center gap-1 transition-all active:scale-95 cursor-pointer">
                                        <x-heroicon-o-check-badge class="w-3.5 h-3.5" />
                                        <span>Tinjau</span>
                                    </button>
                                @else
                                    <button type="button"
                                        @click='editUser({{ Illuminate\Support\Js::from($p) }})'
                                        class="table-btn-action"
                                        title="Edit Pengguna">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </button>
                                @endif

                                @if ($p->user_id !== auth()->id())
                                    <x-ui.confirm-delete-form action="{{ route('pengguna.destroy', $p) }}"
                                        title="Hapus Pengguna"
                                        message="Akun '{{ $p->nama }}' akan dihapus permanen dan tidak bisa login lagi.">
                                        <button type="submit" class="table-btn-action delete" title="Hapus Pengguna">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </x-ui.confirm-delete-form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                            <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#6C7E75] mx-auto mb-3">
                                <x-heroicon-o-users class="w-6 h-6" />
                            </div>
                            <p class="font-bold text-sm text-[#0A0E2E]">Tidak ada data pengguna</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau kombinasi filter Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">
    {{ $pengguna->links() }}
</div>
