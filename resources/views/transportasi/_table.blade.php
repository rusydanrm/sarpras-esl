<div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[850px]">
        <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
            <tr>
                <th class="text-center px-4 py-3 w-12">No</th>
                <th class="text-left px-4 py-3">Plat Nomor</th>
                <th class="text-left px-4 py-3">Nama Kendaraan</th>
                <th class="text-left px-4 py-3">Jenis</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Servis Terakhir</th>
                <th class="text-left px-4 py-3">Jadwal Servis</th>
                <th class="text-left px-4 py-3">Pajak STNK</th>
                <th class="text-center px-4 py-3">Aksi</th>
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
                <tr class="border-t border-[#DEE2E7]">
                    <td class="px-4 py-3 text-center text-[#495465] text-xs font-medium">
                        {{ $loop->iteration + ($kendaraanList->currentPage() - 1) * $kendaraanList->perPage() }}
                    </td>
                    <td class="px-4 py-3 font-semibold text-[#181B21]">
                        {{ $k->plat_nomor }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            @if ($k->foto_url)
                                <img src="{{ asset($k->foto_url) }}" alt="{{ $k->nama_kendaraan }}" class="w-9 h-9 object-cover rounded-lg border border-[#DEE2E7] shrink-0" />
                            @else
                                <div class="w-9 h-9 rounded-lg bg-[#F7F8F9] border border-[#DEE2E7] flex items-center justify-center text-[#9EA9B9] shrink-0">
                                    <x-heroicon-o-truck class="w-5 h-5 opacity-40" />
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-[#181B21]">{{ $k->nama_kendaraan }}</div>
                                @if ($k->merek_tipe || $k->tahun_perolehan)
                                    <div class="text-xs text-[#9EA9B9]">
                                        {{ $k->merek_tipe ?? '' }} {{ $k->tahun_perolehan ? '(' . $k->tahun_perolehan . ')' : '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-[#495465]">
                        {{ $k->jenis_kendaraan }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($k->status === 'Tersedia')
                            <x-ui.badge variant="success">Tersedia</x-ui.badge>
                        @elseif ($k->status === 'Digunakan')
                            <x-ui.badge variant="warning">Digunakan</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger">Perawatan</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-[#495465]">
                        {{ $k->tanggal_servis_terakhir ? $k->tanggal_servis_terakhir->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :variant="$statusServis['variant']">
                            {{ $statusServis['badge'] }}
                        </x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-[#495465]">
                        {{ $k->tanggal_pajak_stnk ? $k->tanggal_pajak_stnk->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('transportasi.show', $k->kendaraan_id) }}">
                                <x-ui.button type="button" variant="outline" size="sm">
                                    <x-heroicon-o-eye class="w-3.5 h-3.5" /> Detail
                                </x-ui.button>
                            </a>

                            <div x-data="{
                                dropdownOpen: false,
                                top: 0,
                                left: 0,
                                openDropdown(e) {
                                    const rect = e.currentTarget.getBoundingClientRect();
                                    const dropdownHeight = 175;
                                    const dropdownWidth = 176;

                                    if (window.innerHeight - rect.bottom < dropdownHeight) {
                                        this.top = Math.max(10, rect.top - dropdownHeight);
                                    } else {
                                        this.top = rect.bottom + 4;
                                    }

                                    this.left = Math.max(10, rect.right - dropdownWidth);
                                    this.dropdownOpen = true;
                                }
                            }" class="inline-block">
                                <button type="button" @click="openDropdown($event)"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-[#495465] hover:bg-[#F7F8F9] hover:text-[#181B21] transition-colors focus:outline-none border border-transparent hover:border-[#DEE2E7]"
                                    title="Menu Aksi">
                                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                </button>

                                <template x-teleport="body">
                                    <div x-show="dropdownOpen" x-cloak
                                        @click.outside="dropdownOpen = false"
                                        @scroll.window="dropdownOpen = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        :style="`top: ${top}px; left: ${left}px; position: fixed;`"
                                        class="z-50 w-44 rounded-xl bg-white p-1.5 shadow-xl border border-[#DEE2E7] divide-y divide-[#DEE2E7]">

                                        <div class="py-0.5 space-y-0.5">
                                            <a href="{{ route('transportasi.show', $k->kendaraan_id) }}"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-[#181B21] hover:bg-[#F7F8F9] rounded-lg transition-colors text-left">
                                                <x-heroicon-o-eye class="w-4 h-4 text-[#120F8B]" />
                                                Detail Kendaraan
                                            </a>

                                            @if ($canServis)
                                                <button type="button"
                                                    @click="dropdownOpen = false; bukaCatatServis({{ Illuminate\Support\Js::from($k) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-[#181B21] hover:bg-[#F7F8F9] rounded-lg transition-colors text-left">
                                                    <x-heroicon-o-wrench class="w-4 h-4 text-[#120F8B]" />
                                                    Catat Servis
                                                </button>
                                            @endif

                                        <button type="button"
                                            @click="dropdownOpen = false; bukaRiwayat({{ $k->kendaraan_id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-[#181B21] hover:bg-[#F7F8F9] rounded-lg transition-colors text-left">
                                            <x-heroicon-o-document-text class="w-4 h-4 text-[#495465]" />
                                            Riwayat Servis
                                        </button>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="pt-1 mt-0.5 space-y-0.5">
                                            <button type="button"
                                                @click="dropdownOpen = false; modalOpen = true; editData = {{ Illuminate\Support\Js::from($k) }}"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-[#181B21] hover:bg-[#F7F8F9] rounded-lg transition-colors text-left">
                                                <x-heroicon-o-pencil-square class="w-4 h-4 text-[#495465]" />
                                                Edit Kendaraan
                                            </button>

                                            <div class="w-full" @click="dropdownOpen = false">
                                                <x-ui.confirm-delete-form
                                                    action="{{ route('transportasi.destroy', $k) }}"
                                                    title="Hapus Kendaraan"
                                                    message="Kendaraan '{{ $k->nama_kendaraan }}' ({{ $k->plat_nomor }}) akan dihapus permanen beserta riwayat servisnya."
                                                    buttonClass="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-[#EF4444] hover:bg-[#FEF7F7] rounded-lg transition-colors text-left">
                                                    <x-heroicon-o-trash class="w-4 h-4 text-[#EF4444]" />
                                                    Hapus Kendaraan
                                                </x-ui.confirm-delete-form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-[#9EA9B9]">
                        Belum ada data kendaraan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $kendaraanList->links() }}</div>
