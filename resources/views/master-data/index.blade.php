<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        tabAktif: '{{ $tab }}',
        editMaster(item) {
            this.editData = item;
            this.modalOpen = true;
        },
        tambahMaster() {
            this.editData = null;
            this.modalOpen = true;
        },
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('master-data.index') }}?tab={{ $tab }}&' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            const inputQ = document.getElementById('inputCari');
            if (inputQ) inputQ.value = '';
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
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Master Data Referensi</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Pusat pengaturan data rujukan gedung, kategori aset, vendor rekanan, dan unit bagian ESL</p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="tambahMaster()"
                    class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                    <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                    <span>Tambah {{ $konfig['label'] }}</span>
                </button>
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

        <!-- 4 Bento Metric Cards ala Spark Admin (1 for each Master Entity) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach ($daftarTab as $key => $meta)
                @php
                    $isAktif = ($tab === $key);
                @endphp
                <a href="{{ route('master-data.index', ['tab' => $key]) }}"
                    class="rounded-2xl border p-4 sm:p-5 transition-all duration-200 block group shadow-[0_2px_12px_rgba(10,14,46,0.03)]
                        {{ $isAktif 
                            ? 'bg-white border-[#ffa916] ring-2 ring-[#ffa916]/30 shadow-md scale-[1.01]' 
                            : 'bg-white border-[#E9EFEF] hover:border-slate-300 hover:shadow-sm' }}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold {{ $isAktif ? 'text-[#0A0E2E]' : 'text-[#6C7E75]' }}">
                            {{ $meta['label'] }}
                        </span>
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors
                            {{ $isAktif ? 'bg-[#0A0E2E] text-[#ffa916]' : 'bg-[#0A0E2E]/5 text-[#0A0E2E] group-hover:bg-[#0A0E2E]/10' }}">
                            <x-dynamic-component :component="'heroicon-o-' . ($meta['icon'] ?? 'circle-stack')" class="w-4 h-4" />
                        </span>
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                            {{ $tabCounts[$key] ?? 0 }}
                            <span class="text-xs font-semibold text-[#6C7E75]">Data</span>
                        </div>
                        <div class="text-[11px] text-[#6C7E75] truncate mt-1">
                            {{ $meta['deskripsi'] ?? 'Data referensi sistem' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Segmented Tab Navigation ala Spark Admin -->
        <div class="mb-5 overflow-x-auto pb-1 -mx-1 px-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none">
            <div class="flex items-center gap-2 min-w-max p-0.5">
                @foreach ($daftarTab as $key => $k)
                    @php
                        $isCurrent = ($tab === $key);
                    @endphp
                    <a href="{{ route('master-data.index', ['tab' => $key]) }}"
                        class="px-4 py-2.5 rounded-xl text-xs transition-all flex items-center gap-2.5 shadow-2xs
                            {{ $isCurrent 
                                ? 'bg-[#0A0E2E] text-white font-bold shadow-xs' 
                                : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium' }}">
                        <x-dynamic-component :component="'heroicon-o-' . ($k['icon'] ?? 'circle-stack')" 
                            class="w-4 h-4 {{ $isCurrent ? 'text-[#ffa916]' : 'text-[#6C7E75]' }}" />
                        <span>{{ $k['label'] }}</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full font-bold
                            {{ $isCurrent ? 'bg-[#ffa916] text-[#0A0E2E]' : 'bg-[#F4F6F5] text-[#6C7E75]' }}">
                            {{ $tabCounts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filter & Search Toolbar ala Spark Admin -->
        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-[#E9EFEF] shadow-2xs mb-5">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Search Input Box -->
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text" id="inputCari" name="q" value="{{ request('q') }}"
                        placeholder="Cari {{ strtolower($konfig['label']) }}..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-9 pr-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <!-- Tombol Reset -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="resetFilter()"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-semibold text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] transition-colors flex items-center gap-1.5 cursor-pointer">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                        <span>Reset</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Data Spark AJAX Wrapper -->
        <div id="tabelWrapper">
            @include('master-data._table', ['tab' => $tab, 'data' => $data, 'konfig' => $konfig])
        </div>

        <!-- Modal Tambah / Edit Master Data ala Spark Admin -->
        <x-ui.modal open="modalOpen" maxWidth="md">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                        <x-dynamic-component :component="'heroicon-o-' . ($konfig['icon'] ?? 'circle-stack')" class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]"
                            x-text="editData ? 'Edit ' + '{{ $konfig['label'] }}' : 'Tambah ' + '{{ $konfig['label'] }}' + ' Baru'">
                        </h2>
                        <p class="text-xs text-[#6C7E75]"
                            x-text="editData ? 'Perbarui informasi data referensi ini' : 'Tambahkan entitas rujukan baru ke sistem'">
                        </p>
                    </div>
                </div>
                <button type="button" @click="modalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST"
                :action="editData ? `/master-data/{{ $tab }}/${editData.{{ $konfig['model']::make()->getKeyName() }}}` : '/master-data/{{ $tab }}'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF] space-y-3.5">
                        <div>
                            <x-ui.input label="Nama {{ $konfig['label'] }}" name="{{ $konfig['kolom'] }}"
                                x-bind:value="editData ? editData['{{ $konfig['kolom'] }}'] : ''"
                                placeholder="Masukkan nama {{ strtolower($konfig['label']) }}..." required />
                        </div>

                        @if ($tab === 'jenis-barang')
                            <div>
                                <x-ui.input type="number" label="Masa Pakai Standar (Tahun)" name="masa_pakai_tahun"
                                    x-bind:value="editData ? editData.masa_pakai_tahun : 5" min="1" max="50"
                                    placeholder="Contoh: 5" required />
                                <p class="text-[11px] text-[#6C7E75] mt-1">Digunakan untuk acuan perhitungan rekomendasi pemeliharaan/peremajaan aset.</p>
                            </div>
                        @endif

                        @if ($tab === 'vendor')
                            <div>
                                <x-ui.input label="Kontak / No. Telepon / Email" name="kontak"
                                    x-bind:value="editData ? editData.kontak : ''"
                                    placeholder="Contoh: 0812-3456-7890 / info@vendor.co.id" />
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-[#E9EFEF]">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-[#495465] hover:bg-[#F4F6F5] border border-[#DEE2E7] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Data
                    </button>
                </div>
            </form>
        </x-ui.modal>

    </div>
</x-layouts.app>