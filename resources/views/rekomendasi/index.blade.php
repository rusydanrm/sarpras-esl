<x-layouts.app>
    <div x-data="{
        catatModalOpen: {{ $errors->any() ? 'true' : 'false' }},
        bukaCatatServis(barangId = null) {
            this.catatModalOpen = true;
            if (barangId) {
                this.$nextTick(() => {
                    const el = document.getElementById('barang_id_select');
                    if (el) {
                        el.value = barangId;
                    }
                });
            }
        },
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('rekomendasi.index') }}?' + params;
            
            document.getElementById('btnEksporExcel').href = '{{ route('rekomendasi.excel') }}?' + params;
            document.getElementById('btnEksporPdf').href = '{{ route('rekomendasi.pdf') }}?' + params;

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('inputCari').value = '';
            document.getElementById('selectGedung').value = '';
            document.getElementById('selectJenis').value = '';
            document.getElementById('selectKondisi').value = '';
            document.getElementById('selectUsia').value = '';
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path
                                d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Maintenance & Repair (MR)</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Sistem monitoring kelayakan kondisi aset, riwayat servis,
                    dan pencatatan pemeliharaan ESL</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if (in_array(auth()->user()->role, ['admin', 'teknisi']))
                    <button type="button" @click="bukaCatatServis()"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                        <span>Catat Pemeliharaan</span>
                    </button>
                @endif
                <a id="btnEksporExcel" href="{{ route('rekomendasi.excel', $filter) }}">
                    <x-ui.button variant="excel">
                        <x-heroicon-o-table-cells class="w-4 h-4" /> Ekspor Excel
                    </x-ui.button>
                </a>
                <a id="btnEksporPdf" href="{{ route('rekomendasi.pdf', $filter) }}">
                    <x-ui.button variant="pdf">
                        <x-heroicon-o-document-text class="w-4 h-4" /> Ekspor PDF
                    </x-ui.button>
                </a>
            </div>
        </div>

        <!-- Success & Error Alerts -->
        @if (session('success'))
            <div
                class="mb-5 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 text-sm text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div
                class="mb-5 p-4 bg-rose-50 border border-rose-200/80 rounded-2xl flex items-start gap-3 text-sm text-rose-800 shadow-2xs">
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

        <!-- Bento Grid Metric Cards ala Spark Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Total Unit -->
            <div
                class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Total Unit Aset</span>
                    <span
                        class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        {{ number_format($totalUnit, 0, ',', '.') }} <span
                            class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Seluruh inventaris aktif Departemen ESL
                    </div>
                </div>
            </div>

            <!-- Card 2: Kondisi Baik -->
            <div
                class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-emerald-700 font-bold">Kondisi Baik</span>
                    <span
                        class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ number_format($totalBaik, 0, ',', '.') }} <span
                            class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Aset dalam kondisi optimal & prima
                    </div>
                </div>
            </div>

            <!-- Card 3: Perlu Servis / Perbaikan -->
            <div
                class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-amber-700 font-bold">Perlu Perbaikan (MR)</span>
                    <span
                        class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-amber-600 tracking-tight">
                        {{ number_format($totalPerluPerbaikan, 0, ',', '.') }} <span
                            class="text-sm font-semibold text-[#6C7E75]">Unit</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Akumulasi Rusak Ringan & Rusak Berat
                    </div>
                </div>
            </div>

            <!-- Card 4: Total Biaya Servis -->
            <div
                class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Total Biaya Servis</span>
                    <span
                        class="w-8 h-8 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shadow-2xs">
                        <x-heroicon-o-banknotes class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Akumulasi pengeluaran pemeliharaan
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar ala Spark Admin Header Control -->
        <div
            class="bg-white rounded-2xl border border-[#E9EFEF] p-3.5 sm:p-4 mb-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <form id="filterForm" method="GET" @submit.prevent
                class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-sm">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 text-[#6C7E75] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input type="text" name="q" id="inputCari" value="{{ $filter['q'] ?? '' }}"
                        placeholder="Cari nama barang, kode, NUP..." x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-10 pr-4 py-2.5 text-xs bg-[#FDFDFD] border border-[#E9EFEF] rounded-xl text-[#0A0E2E] placeholder-[#9EA9B9] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" />
                </div>

                <!-- Dropdown Filters -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="w-full sm:w-40">
                        <select name="gedung_id" id="selectGedung" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Gedung</option>
                            @foreach ($gedungList as $g)
                                <option value="{{ $g->gedung_id }}" @selected(($filter['gedung_id'] ?? '') == $g->gedung_id)>
                                    {{ $g->nama_gedung }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40">
                        <select name="jenis_id" id="selectJenis" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach ($jenisList as $j)
                                <option value="{{ $j->jenis_id }}" @selected(($filter['jenis_id'] ?? '') == $j->jenis_id)>
                                    {{ $j->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40">
                        <select name="kondisi" id="selectKondisi" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" @selected(($filter['kondisi'] ?? '') === 'Baik')>Baik</option>
                            <option value="Rusak Ringan" @selected(($filter['kondisi'] ?? '') === 'Rusak Ringan')>Rusak
                                Ringan</option>
                            <option value="Rusak Berat" @selected(($filter['kondisi'] ?? '') === 'Rusak Berat')>Rusak
                                Berat</option>
                            <option value="Perlu Perbaikan" @selected(($filter['kondisi'] ?? '') === 'Perlu Perbaikan')>
                                Perlu Servis (RR & RB)</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-36">
                        <select name="usia_min" id="selectUsia" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Usia</option>
                            <option value="4" @selected(($filter['usia_min'] ?? '') == '4')>Usia ≥ 4 Thn</option>
                            <option value="5" @selected(($filter['usia_min'] ?? '') == '5')>Usia ≥ 5 Thn</option>
                            <option value="8" @selected(($filter['usia_min'] ?? '') == '8')>Usia ≥ 8 Thn</option>
                        </select>
                    </div>

                    <button type="button" @click="resetFilter()" title="Reset Filter"
                        class="px-3.5 py-2.5 text-xs text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl font-medium transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                        <span class="hidden md:inline">Reset</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Monitoring MR Container (AJAX Target) -->
        <div id="tabelWrapper" @click="handleTableClick($event)">
            @include('rekomendasi._table', ['preview' => $preview])
        </div>

        {{-- Modal Catat Pemeliharaan / Servis ala Spark Admin --}}
        <x-ui.modal open="catatModalOpen" maxWidth="lg" zIndex="z-[60]">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div
                    class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Catat Pemeliharaan / Servis</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Dokumentasikan tindakan perbaikan, servis rutin, atau
                        penggantian aset</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pemeliharaan.store') }}">
                @csrf
                <div class="space-y-4">
                    @if (isset($pengajuanSelesai) && $pengajuanSelesai->count())
                        <div class="p-3 bg-[#F8FAF9] border border-[#E9EFEF] rounded-xl">
                            <label class="text-xs font-bold text-[#0A0E2E] block mb-1.5">Tindak Lanjuti dari Pengajuan
                                (Opsional)</label>
                            <select id="pilih_pengajuan"
                                class="w-full border border-[#E9EFEF] rounded-xl px-3 py-2 text-xs bg-white text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-medium"
                                onchange="isiDariPengajuan(this)">
                                <option value="">- Catat manual -</option>
                                @foreach ($pengajuanSelesai as $pg)
                                    <option value="{{ $pg->pengajuan_id }}" data-barang="{{ $pg->barang_id }}"
                                        data-jenis="{{ $pg->jenis_pengajuan === 'Pemeliharaan' ? 'Pemeliharaan Rutin' : $pg->jenis_pengajuan }}">
                                        #{{ $pg->pengajuan_id }} - {{ $pg->barang->nama_barang ?? '-' }}
                                        ({{ $pg->jenis_pengajuan }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="pengajuan_id" id="pengajuan_id_input">
                        </div>
                    @endif

                    <x-ui.select label="Barang yang Diservis" name="barang_id" id="barang_id_select" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->barang_id }}">{{ $b->nama_barang }}
                                ({{ $b->ruangan?->nama_ruang ?? 'Tanpa Ruangan' }})</option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select label="Jenis Tindakan" name="jenis_tindakan" id="jenis_tindakan_select" required>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                        <option value="Penggantian">Penggantian</option>
                    </x-ui.select>

                    <x-ui.input label="Uraian Kerusakan / Masalah" name="kerusakan"
                        placeholder="Contoh: Modul power terbakar, ganti filter AC, kabel putus" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.select label="Petugas Teknisi Internal" name="teknisi_id">
                            <option value="">- Pilih Teknisi -</option>
                            @foreach ($teknisiList as $t)
                                <option value="{{ $t->user_id }}">{{ $t->nama }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.select label="Vendor / Servis Eksternal" name="vendor_id">
                            <option value="">- Tidak ada (Internal) -</option>
                            @foreach ($vendorList as $v)
                                <option value="{{ $v->vendor_id }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Biaya Pemeliharaan (Rp)" type="number" name="biaya" min="0"
                            placeholder="0" />
                        <x-ui.input label="Tanggal Tindakan" type="date" name="tanggal_tindakan"
                            value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan / Hasil
                            Pemeriksaan</label>
                        <textarea name="keterangan" rows="2"
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium"
                            placeholder="Catatan hasil perbaikan, garansi servis, atau suku cadang yang diganti..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="catatModalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Riwayat Servis
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>

    <script>
        function isiDariPengajuan(el) {
            const opt = el.options[el.selectedIndex];
            document.getElementById('pengajuan_id_input').value = el.value;
            if (el.value) {
                document.getElementById('barang_id_select').value = opt.dataset.barang;
                document.getElementById('jenis_tindakan_select').value = opt.dataset.jenis;
            }
        }
    </script>
</x-layouts.app>