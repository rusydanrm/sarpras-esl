<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Kendaraan & Nomor Polisi</th>
                    <th class="text-left">Jenis</th>
                    <th class="text-left">Status Operasional</th>
                    <th class="text-left">Jadwal Servis</th>
                    <th class="text-left">Pajak STNK</th>
                    <th class="text-center w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kendaraanList as $k)
                    @php
                        $statusServis = $k->status_servis;
                        $statusPajak = $k->status_pajak;
                        $isAdmin = auth()->user()->role === 'admin';
                        $canServis = in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']);
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($kendaraanList->currentPage() - 1) * $kendaraanList->perPage() }}
                        </td>

                        <!-- Kendaraan & Plat -->
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($k->foto_url)
                                    <img src="{{ asset($k->foto_url) }}" alt="{{ $k->nama_kendaraan }}"
                                        class="w-11 h-11 object-cover rounded-xl border border-[#E9EFEF] shadow-2xs shrink-0" />
                                @else
                                    <div class="w-11 h-11 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E] shrink-0">
                                        <x-heroicon-o-truck class="w-6 h-6 opacity-75" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('transportasi.show', $k->kendaraan_id) }}"
                                        class="font-bold text-[#0A0E2E] text-sm hover:text-[#ffa916] transition-colors block truncate"
                                        title="{{ $k->nama_kendaraan }}">
                                        {{ $k->nama_kendaraan }}
                                    </a>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-[#0A0E2E] text-[#ffa916] shadow-2xs">
                                            {{ $k->plat_nomor }}
                                        </span>
                                        @if ($k->merek_tipe || $k->tahun_perolehan)
                                            <span class="text-xs text-[#6C7E75] truncate max-w-[140px]">
                                                {{ $k->merek_tipe ?? '' }} {{ $k->tahun_perolehan ? '(' . $k->tahun_perolehan . ')' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Jenis Angkutan -->
                        <td>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#F4F6F5] border border-[#E9EFEF] text-[#0A0E2E]">
                                @if ($k->jenis_kendaraan === 'Motor')
                                    Motor
                                @elseif ($k->jenis_kendaraan === 'Bus/Minibus')
                                    Minibus / Bus
                                @elseif ($k->jenis_kendaraan === 'Mobil')
                                    Mobil
                                @else
                                    {{ $k->jenis_kendaraan }}
                                @endif
                            </span>
                        </td>

                        <!-- Status Operasional -->
                        <td>
                            @if ($k->status === 'Tersedia')
                                <span class="badge-table success">
                                    <span class="badge-table-dot"></span>
                                    <span>Tersedia</span>
                                </span>
                            @elseif ($k->status === 'Digunakan')
                                <span class="badge-table warning">
                                    <span class="badge-table-dot"></span>
                                    <span>Digunakan</span>
                                </span>
                            @else
                                <span class="badge-table danger">
                                    <span class="badge-table-dot"></span>
                                    <span>Dalam Perawatan</span>
                                </span>
                            @endif
                        </td>

                        <!-- Jadwal Servis Berkala -->
                        <td>
                            <div class="flex flex-col gap-0.5">
                                @if ($statusServis['status'] === 'Aman')
                                    <span class="badge-table success w-fit">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $statusServis['badge'] }}</span>
                                    </span>
                                @elseif ($statusServis['status'] === 'Mendekati Servis')
                                    <span class="badge-table warning w-fit">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $statusServis['badge'] }}</span>
                                    </span>
                                @elseif ($statusServis['status'] === 'Jatuh Tempo')
                                    <span class="badge-table danger w-fit">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $statusServis['badge'] }}</span>
                                    </span>
                                @else
                                    <span class="badge-table info w-fit">
                                        <span class="badge-table-dot"></span>
                                        <span>{{ $statusServis['badge'] }}</span>
                                    </span>
                                @endif
                                <span class="text-[11px] text-[#6C7E75] mt-0.5">
                                    @if ($k->tanggal_servis_terakhir)
                                        Servis: {{ $k->tanggal_servis_terakhir->format('d/m/Y') }}
                                    @else
                                        Belum pernah servis
                                    @endif
                                </span>
                            </div>
                        </td>

                        <!-- Pajak STNK -->
                        <td>
                            @if ($k->tanggal_pajak_stnk)
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-semibold text-[#0A0E2E]">
                                        {{ $k->tanggal_pajak_stnk->format('d/m/Y') }}
                                    </span>
                                    @if ($statusPajak)
                                        <span class="text-[10px] font-bold 
                                            @if ($statusPajak['variant'] === 'danger') text-rose-600 
                                            @elseif ($statusPajak['variant'] === 'warning') text-amber-600 
                                            @else text-emerald-600 @endif">
                                            {{ $statusPajak['keterangan'] }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-[#6C7E75]">-</span>
                            @endif
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Detail Page -->
                                <a href="{{ route('transportasi.show', $k->kendaraan_id) }}"
                                    class="table-btn-action"
                                    title="Lihat Detail & Spesifikasi">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>

                                <!-- Catat Servis -->
                                @if ($canServis)
                                    <button type="button"
                                        @click="bukaCatatServis({{ Illuminate\Support\Js::from($k) }})"
                                        class="table-btn-action hover:!border-amber-400 hover:!bg-amber-500 hover:!text-white"
                                        title="Catat Servis Bengkel">
                                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-amber-600 group-hover:text-white" />
                                    </button>
                                @endif

                                <!-- Riwayat Servis -->
                                <button type="button"
                                    @click="bukaRiwayat({{ $k->kendaraan_id }})"
                                    class="table-btn-action"
                                    title="Riwayat Servis Kendaraan">
                                    <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                                </button>

                                <!-- Edit & Hapus (Admin) -->
                                @if ($isAdmin)
                                    <button type="button"
                                        @click="modalOpen = true; editData = {{ Illuminate\Support\Js::from($k) }}"
                                        class="table-btn-action"
                                        title="Edit Kendaraan">
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                    </button>

                                    <x-ui.confirm-delete-form
                                        action="{{ route('transportasi.destroy', $k) }}"
                                        title="Hapus Kendaraan"
                                        message="Kendaraan '{{ $k->nama_kendaraan }}' ({{ $k->plat_nomor }}) akan dihapus permanen beserta riwayat servisnya."
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
                                <x-heroicon-o-truck class="w-6 h-6 opacity-40" />
                            </div>
                            <p class="text-sm font-bold text-[#0A0E2E]">Belum ada data kendaraan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba ubah kata kunci pencarian atau tambahkan kendaraan baru ke sistem.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $kendaraanList->links() }}
    </div>
</div>
