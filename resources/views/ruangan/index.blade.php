<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('ruangan.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('inputCari').value = '';
            document.getElementById('selectGedung').value = '';
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
    }">

        <!-- Page Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Inventaris Ruangan</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Kelola daftar ruangan, denah lokasi gedung, dan koordinator ruangan Departemen ESL</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span>Total: <strong class="text-[#0A0E2E] font-bold">{{ $ruangan->total() }}</strong> Ruangan</span>
                </div>
                @if (auth()->user()->role === 'admin')
                    <button type="button" @click="modalOpen = true; editData = null"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                        <span>Tambah Ruangan</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Success & Error Alerts -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 text-sm text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 p-4 bg-rose-50 border border-rose-200/80 rounded-2xl flex items-start gap-3 text-sm text-rose-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                </div>
                <div class="flex-1">
                    <h4 class="font-bold mb-1">Terdapat beberapa kesalahan:</h4>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Filter Toolbar ala Spark Admin Header Control -->
        <div class="bg-white rounded-2xl border border-[#E9EFEF] p-3.5 sm:p-4 mb-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Search Input Box -->
                <div class="relative flex-1 max-w-md">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-[#6C7E75] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input type="text" name="cari" id="inputCari" value="{{ request('cari') }}" 
                        placeholder="Cari nama atau kode ruangan..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-10 pr-4 py-2.5 text-xs bg-[#FDFDFD] border border-[#E9EFEF] rounded-xl text-[#0A0E2E] placeholder-[#9EA9B9] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" />
                </div>

                <!-- Dropdown Gedung & Reset -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="w-full sm:w-52">
                        <select name="gedung_id" id="selectGedung" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Gedung</option>
                            @foreach ($gedungList as $g)
                                <option value="{{ $g->gedung_id }}" @selected(request('gedung_id') == $g->gedung_id)>{{ $g->nama_gedung }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" 
                        @click="resetFilter()"
                        title="Reset Filter"
                        class="px-3.5 py-2.5 text-xs text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl font-medium transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                        <span class="hidden md:inline">Reset</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Container (AJAX Target) -->
        <div id="tabelWrapper" @click="handleTableClick($event)">
            @include('ruangan._table', ['ruangan' => $ruangan])
        </div>

        {{-- Modal Tambah / Edit Ruangan ala Spark Admin --}}
        <x-ui.modal open="modalOpen" maxWidth="lg">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-home-modern class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight" x-text="editData ? 'Edit Data Ruangan' : 'Tambah Ruangan Baru'"></h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5" x-text="editData ? 'Perbarui informasi ruangan dan koordinator' : 'Daftarkan ruangan baru ke sistem inventaris'"></p>
                </div>
            </div>

            <form method="POST" :action="editData ? `/ruangan/${editData.ruangan_id}` : '/ruangan'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    @if (auth()->user()->role === 'admin')
                        <x-ui.select label="Gedung" name="gedung_id" required>
                            @foreach ($gedungList as $g)
                                <option value="{{ $g->gedung_id }}"
                                    x-bind:selected="editData && editData.gedung_id == {{ $g->gedung_id }}">
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Kode Ruang" name="kode_ruang" x-bind:value="editData?.kode_ruang" placeholder="Contoh: R-101 / LAB-ESL" />
                        <x-ui.input label="Nama Ruang" name="nama_ruang" x-bind:value="editData?.nama_ruang" required placeholder="Contoh: Ruang Sidang Utama" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Wing" name="wing" x-bind:value="editData?.wing" placeholder="Wing A / Wing B / dll" />
                        <x-ui.input label="Lantai" type="number" name="lantai" x-bind:value="editData?.lantai ?? 1" placeholder="Lantai gedung (1, 2, ...)" />
                    </div>

                    <x-ui.input label="Lokasi / Catatan Posisi" name="lokasi" x-bind:value="editData?.lokasi" placeholder="Misal: Dekat tangga barat / Koridor utara" />

                    <!-- Dimensi Ruangan -->
                    <div class="border border-[#E9EFEF] rounded-2xl p-4 bg-[#F8FAF9]">
                        <label class="text-xs font-bold text-[#0A0E2E] block mb-2">Dimensi Ruangan (Meter)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <x-ui.input label="Panjang (m)" type="number" step="0.01" name="panjang_m"
                                x-bind:value="editData?.panjang_m ?? 0" placeholder="0" />
                            <x-ui.input label="Lebar (m)" type="number" step="0.01" name="lebar_m"
                                x-bind:value="editData?.lebar_m ?? 0" placeholder="0" />
                        </div>
                        <p class="text-[11px] text-[#6C7E75] mt-2 italic">* Luas ruangan (m²) dihitung otomatis oleh sistem.</p>
                    </div>

                    @if (auth()->user()->role === 'admin')
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-[#0A0E2E]">Koordinator Ruangan</label>
                                <span class="text-[11px] text-[#6C7E75]">Bisa pilih lebih dari satu</span>
                            </div>
                            <select name="koordinator_id[]" multiple size="4"
                                class="w-full border border-[#E9EFEF] rounded-xl px-3 py-2 text-xs bg-white text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-medium">
                                @foreach ($koordinatorList as $k)
                                    <option value="{{ $k->user_id }}"
                                        x-bind:selected="editData && editData.koordinator_ids && editData.koordinator_ids.includes({{ $k->user_id }})">
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-[#9EA9B9] mt-1">Tahan tombol <kbd class="px-1 py-0.5 bg-slate-100 rounded text-[10px] border border-slate-200">Ctrl</kbd> (Windows) atau <kbd class="px-1 py-0.5 bg-slate-100 rounded text-[10px] border border-slate-200">Cmd</kbd> (Mac) untuk memilih beberapa koordinator.</p>
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="2" 
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" 
                            x-text="editData?.keterangan"
                            placeholder="Catatan fungsi ruangan, kapasitas, dll..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Ruangan
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>