<x-layouts.public title="{{ $ruangan->nama_ruang }}">
    <div x-data="{
        kondisiAktif: '{{ request('kondisi') ?? '' }}',

        async terapkanFilter() {
            const form = document.getElementById('filterBarang');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('portal.show', $ruangan) }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelBarangWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },

        pilihKondisi(k) {
            this.kondisiAktif = k;
            document.getElementById('inputKondisi').value = k;
            this.terapkanFilter();
        },

        handleTableClick(e) {
            const link = e.target.closest('#tabelBarangWrapper a.page-link, #tabelBarangWrapper nav a');
            if (link && link.href) {
                e.preventDefault();
                fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('tabelBarangWrapper').innerHTML = html;
                        window.history.replaceState({}, '', link.href);
                    });
            }
        }
    }" @click="handleTableClick($event)">

        <!-- Breadcrumb Kembali ala Spark -->
        <div class="mb-5">
            <a href="{{ route('portal.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-[#E9EFEF] text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors shadow-2xs">
                <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                <span>Kembali ke Katalog Ruangan</span>
            </a>
        </div>

        <!-- Hero Room Info Card ala Spark Admin -->
        <div class="bg-white border border-[#E9EFEF] rounded-2xl p-6 sm:p-7 mb-6 shadow-2xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-6 border-b border-[#E9EFEF]">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#6C7E75] mb-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#ffa916]"></span>
                        <span>{{ $ruangan->gedung->nama_gedung }}</span>
                        <span>•</span>
                        <span>Lantai {{ $ruangan->lantai ?? 1 }}</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0E2E] tracking-tight">
                            {{ $ruangan->nama_ruang }}
                        </h1>
                        @if ($ruangan->kode_ruang)
                            <span class="px-2.5 py-1 bg-[#0A0E2E] text-[#ffa916] font-mono text-xs font-bold rounded-lg shadow-2xs">
                                {{ $ruangan->kode_ruang }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 2 Top KPI Stat Cards -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-5 py-3 rounded-2xl bg-[#F8FAF9] border border-[#E9EFEF] flex items-center gap-3.5 min-w-[170px]">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                            <x-heroicon-o-cube class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="text-[11px] text-[#6C7E75] font-semibold uppercase tracking-wider">Item Inventaris</div>
                            <div class="text-lg font-extrabold text-[#0A0E2E]">{{ number_format($totalItem) }} unit</div>
                        </div>
                    </div>

                    <div class="px-5 py-3 rounded-2xl bg-[#F8FAF9] border border-[#E9EFEF] flex items-center gap-3.5 min-w-[200px]">
                        <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shadow-xs">
                            <x-heroicon-o-banknotes class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="text-[11px] text-[#6C7E75] font-semibold uppercase tracking-wider">Total Nilai Aset</div>
                            <div class="text-lg font-extrabold text-[#0A0E2E]">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dimensi Fisik Ruangan -->
            <div class="grid grid-cols-3 gap-4 pt-5 text-xs">
                <div class="bg-[#F8FAF9] p-3.5 rounded-xl border border-[#E9EFEF]">
                    <div class="text-[#6C7E75] font-medium mb-0.5">Panjang Ruangan</div>
                    <div class="text-base font-extrabold text-[#0A0E2E]">{{ $ruangan->panjang_m ?? '-' }} meter</div>
                </div>
                <div class="bg-[#F8FAF9] p-3.5 rounded-xl border border-[#E9EFEF]">
                    <div class="text-[#6C7E75] font-medium mb-0.5">Lebar Ruangan</div>
                    <div class="text-base font-extrabold text-[#0A0E2E]">{{ $ruangan->lebar_m ?? '-' }} meter</div>
                </div>
                <div class="bg-[#F8FAF9] p-3.5 rounded-xl border border-[#E9EFEF]">
                    <div class="text-[#6C7E75] font-medium mb-0.5">Luas Total</div>
                    <div class="text-base font-extrabold text-[#0A0E2E]">{{ number_format($ruangan->luas_m2, 2) }} m²</div>
                </div>
            </div>
        </div>

        <!-- Section Daftar Barang Inventaris -->
        <div class="space-y-4">
            <!-- Header Controls: Title & Condition Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-[#ffa916]" />
                        <span>Daftar Inventaris Sarana & Prasarana</span>
                    </h2>
                    <p class="text-xs text-[#6C7E75]">Katalog barang, kuantitas, nilai perolehan, dan kondisi fisik aset</p>
                </div>

                <!-- Condition Filter Pills -->
                <form id="filterBarang" method="GET" @submit.prevent class="flex items-center gap-2">
                    <input type="hidden" name="kondisi" id="inputKondisi" :value="kondisiAktif">

                    <button type="button" @click="pilihKondisi('')"
                        :class="kondisiAktif === '' 
                            ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shadow-2xs">
                        Semua Kondisi
                    </button>

                    <button type="button" @click="pilihKondisi('Baik')"
                        :class="kondisiAktif === 'Baik' 
                            ? 'bg-emerald-600 text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-emerald-700 hover:bg-emerald-50 font-medium'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shadow-2xs">
                        Kondisi Baik
                    </button>

                    <button type="button" @click="pilihKondisi('Rusak Ringan')"
                        :class="kondisiAktif === 'Rusak Ringan' 
                            ? 'bg-amber-500 text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-amber-700 hover:bg-amber-50 font-medium'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shadow-2xs">
                        Rusak Ringan
                    </button>

                    <button type="button" @click="pilihKondisi('Rusak Berat')"
                        :class="kondisiAktif === 'Rusak Berat' 
                            ? 'bg-rose-600 text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-rose-700 hover:bg-rose-50 font-medium'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shadow-2xs">
                        Rusak Berat
                    </button>
                </form>
            </div>

            <!-- Tabel Barang Wrapper -->
            <div id="tabelBarangWrapper">
                @include('portal._barang_table', ['barang' => $barang])
            </div>
        </div>

    </div>
</x-layouts.public>