<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Nama {{ $konfig['label'] }}</th>
                    @if ($tab === 'gedung')
                        <th class="text-center">Ruangan Terkait</th>
                    @elseif ($tab === 'jenis-barang')
                        <th class="text-center">Masa Pakai Standar</th>
                        <th class="text-center">Total Inventaris</th>
                    @elseif ($tab === 'vendor')
                        <th class="text-left">Kontak Rekanan</th>
                        <th class="text-center">Riwayat Servis</th>
                    @elseif ($tab === 'unit')
                        <th class="text-center">Anggota Staf / Pengguna</th>
                    @endif
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    @php
                        $nama = $item->{$konfig['kolom']};
                        $initial = strtoupper(mb_substr($nama, 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-[#0A0E2E] text-sm truncate max-w-xs" title="{{ $nama }}">
                                        {{ $nama }}
                                    </div>
                                    <div class="text-[11px] text-[#6C7E75]">
                                        ID Referensi: #{{ $item->getKey() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        @if ($tab === 'gedung')
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F4F6F5] text-[#0A0E2E] border border-[#E9EFEF]">
                                    <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-[#6C7E75]" />
                                    {{ $item->ruangan_count ?? 0 }} Ruangan
                                </span>
                            </td>
                        @elseif ($tab === 'jenis-barang')
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    {{ $item->masa_pakai_tahun ?? 5 }} Tahun
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F4F6F5] text-[#0A0E2E] border border-[#E9EFEF]">
                                    <x-heroicon-o-archive-box class="w-3.5 h-3.5 text-[#6C7E75]" />
                                    {{ $item->barang_count ?? 0 }} Aset
                                </span>
                            </td>
                        @elseif ($tab === 'vendor')
                            <td>
                                @if ($item->kontak)
                                    <div class="flex items-center gap-1.5 text-xs text-[#0A0E2E] font-medium">
                                        <x-heroicon-o-phone class="w-3.5 h-3.5 text-[#6C7E75]" />
                                        <span>{{ $item->kontak }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-[#9EA9B9] italic">Belum ada kontak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                    <x-heroicon-o-wrench-screwdriver class="w-3.5 h-3.5" />
                                    {{ $item->pemeliharaan_count ?? 0 }} Servis
                                </span>
                            </td>
                        @elseif ($tab === 'unit')
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F4F6F5] text-[#0A0E2E] border border-[#E9EFEF]">
                                    <x-heroicon-o-users class="w-3.5 h-3.5 text-[#6C7E75]" />
                                    {{ $item->users_count ?? 0 }} Pengguna
                                </span>
                            </td>
                        @endif

                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                    @click='editMaster({{ Illuminate\Support\Js::from($item) }})'
                                    class="table-btn-action"
                                    title="Edit {{ $konfig['label'] }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                <x-ui.confirm-delete-form
                                    action="{{ route('master-data.destroy', [$tab, $item->getKey()]) }}"
                                    title="Hapus {{ $konfig['label'] }}"
                                    message="Data '{{ $nama }}' akan dihapus permanen dari referensi sistem.">
                                    <button type="submit" class="table-btn-action delete" title="Hapus {{ $konfig['label'] }}">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </x-ui.confirm-delete-form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ in_array($tab, ['vendor', 'jenis-barang']) ? 5 : 4 }}" class="text-center py-12">
                            <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#6C7E75] mx-auto mb-3">
                                <x-heroicon-o-circle-stack class="w-6 h-6" />
                            </div>
                            <p class="font-bold text-sm text-[#0A0E2E]">Belum ada data {{ strtolower($konfig['label']) }}</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Gunakan tombol Tambah di atas untuk mendaftarkan data rujukan baru.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">
    {{ $data->links() }}
</div>
