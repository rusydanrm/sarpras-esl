<x-layouts.public title="Katalog Ruangan & Fasilitas">
    <div x-data="{
        gedungAktif: '{{ request('gedung_id') ?? '' }}',

        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('portal.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('gridWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },

        pilihGedung(id) {
            this.gedungAktif = id;
            document.getElementById('inputGedung').value = id;
            this.terapkanFilter();
        },

        handleGridClick(e) {
            const link = e.target.closest('#gridWrapper a.page-link, #gridWrapper nav a');
            if (link && link.href) {
                e.preventDefault();
                fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('gridWrapper').innerHTML = html;
                        window.history.replaceState({}, '', link.href);
                    });
            }
        }
    }" @click="handleGridClick($event)">

        <!-- Hero Promo Banner ala Spark Admin -->
        <div class="bg-[#0A0E2E] rounded-3xl p-6 sm:p-8 lg:p-10 text-white relative overflow-hidden shadow-[0_12px_36px_rgba(10,14,46,0.14)] mb-8">
            <!-- Decorative Spark Background Shape -->
            <div class="absolute -right-8 -bottom-8 w-64 h-64 opacity-10 pointer-events-none text-[#ffa916]">
                <svg class="w-full h-full fill-current" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                </svg>
            </div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-xs mb-3.5">
                    <span class="w-2 h-2 rounded-full bg-[#ffa916] animate-pulse"></span>
                    <span>Katalog Terbuka Sarana & Prasarana ESL IPB</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight mb-2.5 text-white">
                    Data Fasilitas, Inventaris & Ruangan
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl font-medium leading-relaxed mb-6">
                    Transparansi pencatatan aset inventaris sarana dan prasarana Departemen Ekonomi Sumberdaya dan Lingkungan, Fakultas Ekonomi dan Manajemen, IPB University.
                </p>

                <!-- Quick Stats Pills -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xs flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#ffa916] text-[#0A0E2E] flex items-center justify-center font-bold text-xs">
                            <x-heroicon-o-home-modern class="w-4 h-4 stroke-2" />
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold">Ruangan</div>
                            <div class="text-sm font-extrabold text-white">{{ $totalRuangan ?? 0 }} Ruang</div>
                        </div>
                    </div>

                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xs flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-400 text-[#0A0E2E] flex items-center justify-center font-bold text-xs">
                            <x-heroicon-o-cube class="w-4 h-4 stroke-2" />
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold">Total Unit</div>
                            <div class="text-sm font-extrabold text-white">{{ number_format($totalBarang ?? 0) }} Unit</div>
                        </div>
                    </div>

                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xs flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-400 text-[#0A0E2E] flex items-center justify-center font-bold text-xs">
                            <x-heroicon-o-banknotes class="w-4 h-4 stroke-2" />
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold">Nilai Aset Terdata</div>
                            <div class="text-sm font-extrabold text-white">Rp {{ number_format($totalNilaiSeluruh ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="mb-6 space-y-4">
            <!-- Hidden Filter Form for AJAX sync -->
            <form id="filterForm" method="GET" @submit.prevent class="hidden">
                <input type="hidden" name="gedung_id" id="inputGedung" :value="gedungAktif">
            </form>

            <!-- Gedung Tabs ala Spark Category Pills -->
            <div class="overflow-x-auto pb-1 -mx-1 px-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none">
                <div class="flex items-center gap-2 min-w-max p-0.5">
                    <button type="button"
                        @click="pilihGedung('')"
                        :class="gedungAktif === '' 
                            ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                        class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                        <span>Semua Gedung</span>
                        <span :class="gedungAktif === '' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                            class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                            {{ $totalRuangan ?? 0 }}
                        </span>
                    </button>

                    @foreach ($gedungList as $g)
                        <button type="button"
                            @click="pilihGedung('{{ $g->gedung_id }}')"
                            :class="gedungAktif == '{{ $g->gedung_id }}' 
                                ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                                : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                            class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                            <span>{{ $g->nama_gedung }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Search Toolbar Card -->
            <div class="bg-white p-4 rounded-2xl border border-[#E9EFEF] shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text" form="filterForm" name="cari" value="{{ request('cari') }}"
                        placeholder="Cari nama ruangan atau kode ruangan (contoh: RK. 2.01)..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <div class="flex items-center gap-2 text-xs text-[#6C7E75]">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#10B981]"></span>
                    <span>Klik kartu untuk menelusuri rincian barang & kondisi</span>
                </div>
            </div>
        </div>

        <!-- Ruangan Cards Grid Container -->
        <div id="gridWrapper">
            @include('portal._grid', ['ruanganList' => $ruanganList])
        </div>

    </div>
</x-layouts.public>