<div class="table-card-spark">
    <div class="overflow-x-auto">
        <table class="table-spark-custom">
            <thead>
                <tr>
                    <th class="text-center w-12">No</th>
                    <th class="text-left">Ruangan & Gedung</th>
                    <th class="text-left">Posisi & Lantai</th>
                    <th class="text-left">Dimensi & Luas</th>
                    <th class="text-left">Koordinator</th>
                    <th class="text-center">Total Aset</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ruangan as $r)
                    @php 
                        $bolehEdit = auth()->user()->role === 'admin' || $r->dikoordinasiOleh(auth()->id()); 
                        $initial = strtoupper(mb_substr($r->nama_ruang, 0, 1));
                    @endphp
                    <tr>
                        <td class="text-center text-[#6C7E75] text-xs font-semibold">
                            {{ $loop->iteration + ($ruangan->currentPage() - 1) * $ruangan->perPage() }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                    {{ $initial }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('ruangan.show', $r) }}"
                                        class="font-bold text-[#0A0E2E] hover:text-[#ffa916] transition-colors text-sm truncate block"
                                        title="{{ $r->nama_ruang }}">
                                        {{ $r->nama_ruang }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-0.5 text-xs text-[#6C7E75]">
                                        <span class="font-mono text-[11px] bg-[#F4F6F5] px-1.5 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]/60">
                                            {{ $r->kode_ruang ?? '-' }}
                                        </span>
                                        <span class="text-[#6C7E75]">• {{ $r->gedung->nama_gedung }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-xs font-semibold text-[#0A0E2E]">
                                Lantai {{ $r->lantai }}
                            </div>
                            <div class="text-[11px] text-[#6C7E75]">
                                Wing {{ $r->wing ?? '-' }} {{ $r->lokasi ? '· ' . $r->lokasi : '' }}
                            </div>
                        </td>
                        <td>
                            <div class="text-xs font-bold text-[#0A0E2E]">
                                {{ number_format($r->luas_m2, 2) }} m²
                            </div>
                            <div class="text-[11px] text-[#6C7E75]">
                                {{ $r->panjang_m }} × {{ $r->lebar_m }} m
                            </div>
                        </td>
                        <td>
                            @if ($r->koordinator->isNotEmpty())
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @foreach ($r->koordinator as $k)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-[#F4F6F5] border border-[#E9EFEF] text-[11px] font-medium text-[#495465]" title="{{ $k->nama }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#120F8B]"></span>
                                            <span class="truncate max-w-[120px]">{{ $k->nama }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[#9EA9B9] text-xs italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] text-xs font-bold text-[#0A0E2E]">
                                {{ $r->barang_count ?? 0 }} Barang
                            </span>
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Detail Button -->
                                <a href="{{ route('ruangan.show', $r) }}"
                                    class="table-btn-action"
                                    title="Buka Ruangan">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </a>

                                @if ($bolehEdit)
                                    @php
                                        $dataEdit = $r->toArray();
                                        $dataEdit['koordinator_ids'] = $r->koordinator->pluck('user_id');
                                    @endphp
                                    <!-- Edit Button -->
                                    <button type="button"
                                        class="table-btn-action"
                                        title="Edit Ruangan"
                                        @click='modalOpen = true; editData = {{ Illuminate\Support\Js::from($dataEdit) }}'>
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                    </button>
                                @endif

                                @if (auth()->user()->role === 'admin')
                                    <!-- Delete Button -->
                                    <x-ui.confirm-delete-form action="{{ route('ruangan.destroy', $r) }}"
                                        title="Hapus Ruangan"
                                        message="Ruangan '{{ $r->nama_ruang }}' beserta seluruh data barang di dalamnya akan dihapus permanen."
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
                                <x-heroicon-o-home-modern class="w-6 h-6" />
                            </div>
                            <p class="text-sm font-semibold text-[#0A0E2E]">Tidak ada data ruangan ditemukan</p>
                            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau filter gedung.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="p-4 sm:p-5 border-t border-[#E9EFEF] bg-white">
        {{ $ruangan->links() }}
    </div>
</div>
