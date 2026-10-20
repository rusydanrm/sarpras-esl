<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($ruanganList as $r)
        <a href="{{ route('portal.show', $r) }}"
            class="group relative bg-white border border-[#E9EFEF] rounded-2xl p-5 hover:border-[#ffa916]/70 hover:shadow-[0_8px_25px_rgba(10,14,46,0.06)] hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between overflow-hidden shadow-2xs cursor-pointer">
            
            <div>
                <!-- Top Row: Icon & Building Badge -->
                <div class="flex items-center justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-[#0A0E2E]/5 text-[#0A0E2E] group-hover:bg-[#0A0E2E] group-hover:text-[#ffa916] transition-colors flex items-center justify-center shrink-0">
                        <x-heroicon-o-home-modern class="w-5 h-5 stroke-2" />
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-[#F4F6F5] border border-[#E9EFEF] text-[#6C7E75] truncate max-w-[170px]" title="{{ $r->gedung->nama_gedung }}">
                        {{ $r->gedung->nama_gedung }}
                    </span>
                </div>

                <!-- Middle: Room Title & Code -->
                <div class="mt-4 mb-2">
                    <h3 class="font-bold text-[#0A0E2E] text-base group-hover:text-[#ffa916] transition-colors line-clamp-1" title="{{ $r->nama_ruang }}">
                        {{ $r->nama_ruang }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        @if ($r->kode_ruang)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-[#0A0E2E] text-[#ffa916] shadow-2xs">
                                {{ $r->kode_ruang }}
                            </span>
                        @endif

                        <span class="text-xs text-[#6C7E75]">
                            Lantai {{ $r->lantai ?? 1 }}
                        </span>

                        @if ($r->luas_m2)
                            <span class="text-[#E9EFEF]">•</span>
                            <span class="text-xs text-[#6C7E75] font-medium">
                                {{ number_format($r->luas_m2, 1) }} m²
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Valuation & Item Count Pill -->
            <div class="border-t border-[#E9EFEF] pt-3.5 mt-4 flex items-center justify-between">
                <div>
                    <div class="text-[10px] text-[#6C7E75] uppercase tracking-wider font-bold">Total Nilai Aset</div>
                    <div class="font-extrabold text-[#0A0E2E] text-sm mt-0.5">
                        Rp {{ number_format($r->barang_sum_total_harga ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#F4F6F5] border border-[#E9EFEF] text-[#0A0E2E] group-hover:bg-[#0A0E2E] group-hover:text-[#ffa916] transition-colors">
                        {{ $r->barang_count ?? 0 }} unit
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-[#F4F6F5] group-hover:bg-[#ffa916] text-[#6C7E75] group-hover:text-[#0A0E2E] flex items-center justify-center transition-colors">
                        <x-heroicon-o-chevron-right class="w-3.5 h-3.5 stroke-2" />
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full py-16 px-4 text-center bg-white rounded-2xl border border-[#E9EFEF] shadow-2xs">
            <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                <x-heroicon-o-building-office-2 class="w-6 h-6 opacity-40" />
            </div>
            <p class="text-sm font-bold text-[#0A0E2E]">Tidak ada ruangan ditemukan</p>
            <p class="text-xs text-[#6C7E75] mt-1">Coba sesuaikan kata kunci pencarian atau pilih tab gedung lainnya.</p>
        </div>
    @endforelse
</div>

<!-- Table / Grid Pagination -->
<div class="mt-6">
    {{ $ruanganList->links() }}
</div>