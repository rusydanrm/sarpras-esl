<x-layouts.app>
    <div x-data="{
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('laporan.index') }}?' + params;

            const btnExcel = document.getElementById('btnEksporExcel');
            if (btnExcel) btnExcel.href = '{{ route('laporan.excel') }}?' + params;

            const btnPdf = document.getElementById('btnEksporPdf');
            if (btnPdf) btnPdf.href = '{{ route('laporan.pdf') }}?' + params;

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('inputCari').value = '';
            document.getElementById('selectGedung').value = '';
            document.getElementById('selectKondisi').value = '';
            document.getElementById('selectTahun').value = '';
            document.getElementById('inputSumberDana').value = '';
            this.terapkanFilter();
        },
        handleTableClick(e) {
            const link = e.target.closest('#tabelWrapper a.page-link, #tabelWrapper nav a');
            if (link && link.href) {
                e.preventDefault();
                fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('tabelWrapper').innerHTML = html;
                        window.history.replaceState({}, '', link.href);
                    });
            }
        }
    }" @click="handleTableClick($event)">

        <!-- Page Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Laporan & Rekapitulasi</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Rekapitulasi inventaris barang, monitoring kondisi aset, serta ekspor dokumen resmi DIR/DBR</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a id="btnEksporExcel" href="{{ route('laporan.excel', $filter) }}">
                    <x-ui.button variant="excel">
                        <x-heroicon-o-table-cells class="w-4 h-4" /> Ekspor Excel
                    </x-ui.button>
                </a>
                <a id="btnEksporPdf" href="{{ route('laporan.pdf', $filter) }}">
                    <x-ui.button variant="pdf">
                        <x-heroicon-o-document-arrow-down class="w-4 h-4" /> Ekspor PDF
                    </x-ui.button>
                </a>
            </div>
        </div>

        <!-- 4 Bento Metric Cards ala Spark Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Alert Dark Navy Spark Card (Valuasi Aset) -->
            <div class="alert-spark-card">
                <!-- Inline SVG geometric decoration (Amber 6-pointed star with rounded caps) -->
                <svg class="alert-spark-bg-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(50,50)">
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(60)" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(120)" />
                    </g>
                </svg>

                <div class="relative z-10">
                    <span class="alert-spark-badge">Valuasi Total Aset</span>
                    <div class="alert-spark-date">{{ now()->format('d M Y') }} • ESL IPB</div>
                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight">
                        Rp {{ number_format($totalNilaiPerolehan, 0, ',', '.') }}
                    </div>
                </div>

                <div class="relative z-10 text-[11px] text-slate-300 font-medium flex items-center gap-1.5 mt-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#ffa916]"></span>
                    <span>{{ number_format($totalAset, 0, ',', '.') }} jenis barang terdata</span>
                </div>
            </div>

            <!-- Card 2: Total Fisik Unit -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Total Fisik Unit</span>
                    <span class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        {{ number_format($totalUnit, 0, ',', '.') }} <span class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] font-medium mt-1">
                        Tersebar di seluruh ruangan ESL
                    </div>
                </div>
            </div>

            <!-- Card 3: Kondisi Baik -->
            @php
                $rasioBaik = $totalUnit > 0 ? round(($totalBaik / $totalUnit) * 100, 1) : 0;
            @endphp
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Kondisi Siap Pakai</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ number_format($totalBaik, 0, ',', '.') }} <span class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-bold text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $rasioBaik }}% dalam kondisi prima</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Perlu Penanganan (Rusak Ringan & Berat) -->
            @php
                $rasioRusak = $totalUnit > 0 ? round(($totalRusak / $totalUnit) * 100, 1) : 0;
            @endphp
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Perlu Penanganan</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-amber-600 tracking-tight">
                        {{ number_format($totalRusak, 0, ',', '.') }} <span class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-bold text-amber-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>{{ $rasioRusak }}% rusak ringan / berat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar ala Spark Admin -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E9EFEF] shadow-2xs mb-5">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text" id="inputCari" name="q" value="{{ $filter['q'] ?? '' }}"
                        placeholder="Cari nama barang, kode aset, merek..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <!-- Filter Gedung -->
                <div class="w-full sm:w-44">
                    <select id="selectGedung" name="gedung_id" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Gedung</option>
                        @foreach ($gedungList as $g)
                            <option value="{{ $g->gedung_id }}" @selected(($filter['gedung_id'] ?? '') == $g->gedung_id)>
                                {{ $g->nama_gedung }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kondisi -->
                <div class="w-full sm:w-40">
                    <select id="selectKondisi" name="kondisi" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Kondisi</option>
                        @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k)
                            <option value="{{ $k }}" @selected(($filter['kondisi'] ?? '') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tahun Perolehan -->
                <div class="w-full sm:w-36">
                    <select id="selectTahun" name="tahun" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $thn)
                            <option value="{{ $thn }}" @selected(($filter['tahun'] ?? '') == $thn)>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sumber Dana -->
                <div class="w-full sm:w-44">
                    <input type="text" id="inputSumberDana" name="sumber_dana" value="{{ $filter['sumber_dana'] ?? '' }}"
                        placeholder="Sumber dana..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full px-3 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <!-- Tombol Reset -->
                <button type="button" @click="resetFilter()"
                    class="px-3 py-2.5 rounded-xl text-xs font-semibold text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] transition-colors flex items-center gap-1.5 cursor-pointer">
                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                    <span>Reset</span>
                </button>
            </form>
        </div>

        <!-- Tabel Data Spark AJAX Wrapper -->
        <div id="tabelWrapper">
            @include('laporan._table', ['preview' => $preview])
        </div>

    </div>
</x-layouts.app>