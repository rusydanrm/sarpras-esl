<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->hasAny(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'interval_servis_bulan']) ? 'true' : 'false' }},
        editData: null,
        servisModalOpen: {{ $errors->hasAny(['tanggal_servis', 'jenis_servis', 'biaya']) ? 'true' : 'false' }},
        targetKendaraan: null,
        riwayatModalOpen: false,
        riwayatData: null,
        loadingRiwayat: false,

        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('transportasi.index') }}?' + params;

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },

        bukaCatatServis(kendaraan) {
            this.targetKendaraan = kendaraan;
            this.servisModalOpen = true;
        },

        async bukaRiwayat(kendaraanId) {
            this.riwayatModalOpen = true;
            this.loadingRiwayat = true;
            this.riwayatData = null;

            try {
                const res = await fetch(`/transportasi/${kendaraanId}/riwayat`);
                this.riwayatData = await res.json();
            } catch (e) {
                alert('Gagal memuat riwayat servis.');
            } finally {
                this.loadingRiwayat = false;
            }
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
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Transportasi & Armada Operasional</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Manajemen armada kendaraan dinas, monitoring servis berkala, pajak STNK, dan log perbaikan bengkel</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span>Total: <strong class="text-[#0A0E2E] font-bold">{{ $totalKendaraan }}</strong> Armada</span>
                </div>

                @if (auth()->user()->role === 'admin')
                    <button type="button" @click="modalOpen = true; editData = null"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                        <span>Tambah Kendaraan</span>
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

        <!-- 4 Bento KPI Metric Cards ala Spark Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Total Armada -->
            <div class="bg-white p-5 rounded-2xl border border-[#E9EFEF] shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Total Kendaraan</span>
                    <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-truck class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">{{ $totalKendaraan }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-[#6C7E75]">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#0A0E2E]"></span>
                        <span>Unit armada terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Siap Operasional -->
            <div class="bg-white p-5 rounded-2xl border border-[#E9EFEF] shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Siap Operasional</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-check-badge class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $kendaraanTersedia }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-emerald-700">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ $totalKendaraan > 0 ? round(($kendaraanTersedia / $totalKendaraan) * 100) : 0 }}% armada siap jalan</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Perlu Servis Segera -->
            <div class="bg-white p-5 rounded-2xl border border-[#E9EFEF] shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Perlu Servis Segera</span>
                    <div class="w-9 h-9 rounded-xl @if($kendaraanPerluServis > 0) bg-rose-50 border-rose-200/60 text-rose-600 @else bg-slate-50 border-slate-200 text-slate-500 @endif border flex items-center justify-center">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-extrabold @if($kendaraanPerluServis > 0) text-rose-600 @else text-[#0A0E2E] @endif tracking-tight">
                        {{ $kendaraanPerluServis }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs @if($kendaraanPerluServis > 0) text-rose-700 @else text-[#6C7E75] @endif">
                        <span class="inline-block w-1.5 h-1.5 rounded-full @if($kendaraanPerluServis > 0) bg-rose-500 @else bg-slate-400 @endif"></span>
                        <span>{{ $kendaraanPerluServis > 0 ? 'Jatuh tempo / mendekati' : 'Semua armada terawat baik' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Biaya Servis Tahun Ini -->
            <div class="bg-white p-5 rounded-2xl border border-[#E9EFEF] shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Biaya Servis ({{ date('Y') }})</span>
                    <div class="w-9 h-9 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shadow-xs">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="text-xl font-extrabold text-[#0A0E2E] tracking-tight">
                        Rp {{ number_format($totalBiayaServisTahunIni, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-[#6C7E75]">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#ffa916]"></span>
                        <span>Akumulasi log pemeliharaan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Toolbar Card ala Spark Admin -->
        <div class="bg-white p-4 rounded-2xl border border-[#E9EFEF] shadow-2xs mb-5">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap items-center gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text" name="q" value="{{ $filter['q'] ?? '' }}"
                        placeholder="Cari nama kendaraan, plat nomor, driver..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-9 pr-3.5 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <!-- Filter Jenis -->
                <div class="w-full sm:w-44">
                    <select name="jenis_kendaraan" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Jenis</option>
                        @foreach (['Mobil', 'Motor', 'Bus/Minibus', 'Lainnya'] as $jk)
                            <option value="{{ $jk }}" @selected(($filter['jenis_kendaraan'] ?? '') === $jk)>{{ $jk }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Operasional -->
                <div class="w-full sm:w-44">
                    <select name="status" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Status</option>
                        @foreach (['Tersedia', 'Digunakan', 'Dalam Perawatan'] as $st)
                            <option value="{{ $st }}" @selected(($filter['status'] ?? '') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Servis -->
                <div class="w-full sm:w-48">
                    <select name="status_servis" x-on:change="terapkanFilter()"
                        class="w-full px-3 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                        <option value="">Semua Jadwal Servis</option>
                        <option value="Jatuh Tempo" @selected(($filter['status_servis'] ?? '') === 'Jatuh Tempo')>Jatuh Tempo (Darurat)</option>
                        <option value="Mendekati Servis" @selected(($filter['status_servis'] ?? '') === 'Mendekati Servis')>Mendekati Servis</option>
                        <option value="Aman" @selected(($filter['status_servis'] ?? '') === 'Aman')>Jadwal Aman</option>
                    </select>
                </div>

                <!-- Reset Button -->
                @if (!empty($filter['q']) || !empty($filter['jenis_kendaraan']) || !empty($filter['status']) || !empty($filter['status_servis']))
                    <a href="{{ route('transportasi.index') }}"
                        class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition-colors flex items-center gap-1">
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Data Spark -->
        <div id="tabelWrapper">
            @include('transportasi._table', ['kendaraanList' => $kendaraanList])
        </div>

        <!-- Modal Tambah / Edit Kendaraan ala Spark Admin -->
        <x-ui.modal open="modalOpen" maxWidth="2xl">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                        <x-heroicon-o-truck class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]" x-text="editData ? 'Edit Kendaraan Dinas' : 'Tambah Kendaraan Dinas Baru'"></h2>
                        <p class="text-xs text-[#6C7E75]">Lengkapi spesifikasi, data perolehan, dan jadwal operasional armada</p>
                    </div>
                </div>
                <button type="button" @click="modalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST" enctype="multipart/form-data"
                :action="editData ? `/transportasi/${editData.kendaraan_id}` : '{{ route('transportasi.store') }}'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                    <!-- Section 1: Identitas Utama -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">1</span>
                            <span>Identitas Kendaraan</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="sm:col-span-2">
                                <x-ui.input label="Nama Kendaraan" name="nama_kendaraan"
                                    x-bind:value="editData ? editData.nama_kendaraan : ''"
                                    placeholder="Contoh: Mobil Operasional Departemen" required />
                            </div>

                            <div>
                                <x-ui.input label="Nomor Polisi (Plat)" name="plat_nomor"
                                    x-bind:value="editData ? editData.plat_nomor : ''" placeholder="Contoh: F 1057 JH"
                                    required />
                            </div>

                            <div>
                                <x-ui.select label="Jenis Angkutan" name="jenis_kendaraan" required>
                                    <option value="Mobil" :selected="editData?.jenis_kendaraan === 'Mobil'">Mobil</option>
                                    <option value="Bus/Minibus" :selected="editData?.jenis_kendaraan === 'Bus/Minibus'">Minibus / Bus</option>
                                    <option value="Motor" :selected="editData?.jenis_kendaraan === 'Motor'">Motor</option>
                                    <option value="Lainnya" :selected="editData?.jenis_kendaraan === 'Lainnya'">Lainnya</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.input label="Kode Barang" name="kode_barang"
                                    x-bind:value="editData ? editData.kode_barang : ''" placeholder="Contoh: 3020102003" />
                            </div>

                            <div>
                                <x-ui.input label="NUP" name="nup"
                                    x-bind:value="editData ? editData.nup : ''" placeholder="Contoh: 388" />
                            </div>

                            <div>
                                <x-ui.input label="Model Kendaraan" name="model_kendaraan"
                                    x-bind:value="editData ? editData.model_kendaraan : ''" placeholder="Contoh: MPV, Minibus, Sedan" />
                            </div>

                            <div>
                                <x-ui.input label="Merek / Tipe / Spesifikasi" name="merek_tipe"
                                    x-bind:value="editData ? editData.merek_tipe : ''" placeholder="Contoh: Toyota Avanza 1.3 G" />
                            </div>

                            <div>
                                <x-ui.input label="No. Rangka" name="no_rangka"
                                    x-bind:value="editData ? editData.no_rangka : ''" placeholder="Nomor rangka mesin" />
                            </div>

                            <div>
                                <x-ui.input label="No. Mesin" name="no_mesin"
                                    x-bind:value="editData ? editData.no_mesin : ''" placeholder="Nomor blok mesin" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-[#495465] mb-1">Foto Fisik Kendaraan</label>
                                <input type="file" name="foto" accept="image/*"
                                    class="w-full text-xs text-[#0A0E2E] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A0E2E] file:text-[#ffa916] hover:file:bg-[#120F8B] border border-[#E9EFEF] rounded-xl cursor-pointer bg-white p-1" />
                                <span class="text-[11px] text-[#6C7E75] mt-1 block">Format: JPG, PNG, WEBP (Maksimal 3MB). Kosongkan jika tidak mengganti foto.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Perolehan & Nilai -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">2</span>
                            <span>Data Perolehan & Valuasi</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <x-ui.input type="number" label="Tahun Perolehan" name="tahun_perolehan"
                                    x-bind:value="editData ? editData.tahun_perolehan : ''" placeholder="Contoh: 2019"
                                    min="1980" max="{{ date('Y') + 1 }}" />
                            </div>

                            <div>
                                <x-ui.input label="Sumber Dana" name="sumber_dana"
                                    x-bind:value="editData ? editData.sumber_dana : 'DM'" placeholder="Contoh: DM, BOPTN" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Harga Perolehan (Rp)" name="harga_perolehan"
                                    x-bind:value="editData ? editData.harga_perolehan : ''" placeholder="Contoh: 250000000" min="0" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Masa Manfaat (Tahun)" name="masa_manfaat"
                                    x-bind:value="editData ? (editData.masa_manfaat || 7) : 7" min="1" max="30" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Operasional & Servis -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">3</span>
                            <span>Operasional & Servis Berkala</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <x-ui.select label="Kondisi Fisik" name="kondisi" required>
                                    <option value="Baik" :selected="editData?.kondisi === 'Baik'">B (Baik)</option>
                                    <option value="Rusak Ringan" :selected="editData?.kondisi === 'Rusak Ringan'">RR (Rusak Ringan)</option>
                                    <option value="Rusak Berat" :selected="editData?.kondisi === 'Rusak Berat'">RB (Rusak Berat)</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.select label="Status Operasional" name="status" required>
                                    <option value="Tersedia" :selected="editData?.status === 'Tersedia'">Tersedia</option>
                                    <option value="Digunakan" :selected="editData?.status === 'Digunakan'">Digunakan</option>
                                    <option value="Dalam Perawatan" :selected="editData?.status === 'Dalam Perawatan'">Dalam Perawatan</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.input label="Penanggung Jawab / Driver" name="penanggung_jawab"
                                    x-bind:value="editData ? editData.penanggung_jawab : ''" placeholder="Nama staf driver" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Interval Servis (Bulan)" name="interval_servis_bulan"
                                    x-bind:value="editData ? editData.interval_servis_bulan : 6" min="1" max="36" required />
                            </div>

                            <div>
                                <x-ui.input type="date" label="Tanggal Servis Terakhir" name="tanggal_servis_terakhir"
                                    x-bind:value="editData ? (editData.tanggal_servis_terakhir ? editData.tanggal_servis_terakhir.substring(0,10) : '') : ''" />
                            </div>

                            <div>
                                <x-ui.input type="date" label="Pajak STNK Tahunan" name="tanggal_pajak_stnk"
                                    x-bind:value="editData ? (editData.tanggal_pajak_stnk ? editData.tanggal_pajak_stnk.substring(0,10) : '') : ''" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-[#495465] mb-1">Keterangan Tambahan</label>
                                <textarea name="keterangan" rows="2" x-bind:value="editData ? editData.keterangan : ''"
                                    class="w-full text-xs rounded-xl border border-[#E9EFEF] px-3.5 py-2.5 text-[#0A0E2E] focus:outline-hidden focus:border-[#ffa916] bg-white placeholder-[#6C7E75]"
                                    placeholder="Catatan tambahan kondisi kendaraan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-5 pt-3.5 border-t border-[#E9EFEF]">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 bg-white border border-[#E9EFEF] hover:bg-[#F4F6F5] text-[#495465] font-semibold text-xs rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                        Simpan Kendaraan
                    </button>
                </div>
            </form>
        </x-ui.modal>

        <!-- Modal Catat Servis Cepat ala Spark Admin -->
        <x-ui.modal open="servisModalOpen" maxWidth="md" zIndex="z-[60]">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]">Catat Servis Kendaraan</h2>
                        <p class="text-xs text-[#6C7E75]" x-text="targetKendaraan ? `${targetKendaraan.nama_kendaraan} (${targetKendaraan.plat_nomor})` : ''"></p>
                    </div>
                </div>
                <button type="button" @click="servisModalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST"
                :action="targetKendaraan ? `/transportasi/${targetKendaraan.kendaraan_id}/servis` : '#'">
                @csrf
                <div class="space-y-3.5">
                    <div>
                        <x-ui.input type="date" label="Tanggal Servis" name="tanggal_servis" value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div>
                        <x-ui.select label="Jenis Tindakan Servis" name="jenis_servis" required>
                            <option value="Servis Rutin">Servis Rutin Berkala</option>
                            <option value="Ganti Oli">Ganti Oli & Filter</option>
                            <option value="Perbaikan Mesin">Perbaikan Mesin / Transmisi</option>
                            <option value="Ganti Ban/Aki">Ganti Ban / Aki</option>
                            <option value="Uji Emisi / KIR">Uji Emisi / KIR</option>
                            <option value="Lainnya">Lainnya</option>
                        </x-ui.select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-ui.input type="number" label="Kilometer (Odometer)" name="odometer_km" placeholder="Contoh: 45000" min="0" />
                        </div>
                        <div>
                            <x-ui.input type="number" label="Biaya Servis (Rp)" name="biaya" placeholder="0" min="0" />
                        </div>
                    </div>

                    <div>
                        <x-ui.input label="Bengkel Pelaksana" name="bengkel_pelaksana" placeholder="Nama bengkel resmi / rekanan" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#495465] mb-1">Catatan Pekerjaan / Suku Cadang</label>
                        <textarea name="catatan" rows="2"
                            class="w-full text-xs rounded-xl border border-[#E9EFEF] px-3.5 py-2.5 text-[#0A0E2E] focus:outline-hidden focus:border-[#ffa916] bg-white placeholder-[#6C7E75]"
                            placeholder="Rincian perbaikan atau pergantian sparepart..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-5 pt-3.5 border-t border-[#E9EFEF]">
                    <button type="button" @click="servisModalOpen = false"
                        class="px-4 py-2 bg-white border border-[#E9EFEF] hover:bg-[#F4F6F5] text-[#495465] font-semibold text-xs rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                        Simpan Catatan Servis
                    </button>
                </div>
            </form>
        </x-ui.modal>

        <!-- Modal Riwayat Servis ala Spark Admin -->
        <x-ui.modal open="riwayatModalOpen" maxWidth="lg" zIndex="z-[60]">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                        <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]"
                            x-text="riwayatData ? `Riwayat Servis: ${riwayatData.kendaraan.nama_kendaraan}` : 'Riwayat Servis'">
                        </h2>
                        <p class="text-xs text-[#6C7E75]"
                            x-text="riwayatData ? `Plat: ${riwayatData.kendaraan.plat_nomor} • Total: Rp ${new Intl.NumberFormat('id-ID').format(riwayatData.total_biaya)}` : ''">
                        </p>
                    </div>
                </div>
                <button type="button" @click="riwayatModalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <!-- Loading State -->
            <div x-show="loadingRiwayat" class="py-12 text-center text-[#6C7E75] text-xs">
                <div class="inline-block w-6 h-6 border-2 border-[#0A0E2E] border-t-transparent rounded-full animate-spin mb-2"></div>
                <div>Memuat log riwayat servis...</div>
            </div>

            <!-- Content -->
            <div x-show="!loadingRiwayat">
                <template x-if="riwayatData && riwayatData.riwayat.length === 0">
                    <div class="py-10 text-center text-[#6C7E75]">
                        <div class="w-10 h-10 rounded-xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-2">
                            <x-heroicon-o-document-text class="w-5 h-5 opacity-40" />
                        </div>
                        <p class="text-xs font-semibold text-[#0A0E2E]">Belum ada riwayat servis</p>
                        <p class="text-[11px] text-[#6C7E75] mt-0.5">Catatan pemeliharaan akan muncul setelah servis dicatat.</p>
                    </div>
                </template>

                <template x-if="riwayatData && riwayatData.riwayat.length > 0">
                    <div class="overflow-x-auto max-h-[360px] border border-[#E9EFEF] rounded-2xl bg-white shadow-2xs">
                        <table class="w-full text-xs">
                            <thead class="bg-[#F8FAF9] text-[#6C7E75] font-bold text-[11px] uppercase tracking-wider border-b border-[#E9EFEF]">
                                <tr>
                                    <th class="text-left px-3.5 py-2.5">Tanggal</th>
                                    <th class="text-left px-3.5 py-2.5">Tindakan & Catatan</th>
                                    <th class="text-left px-3.5 py-2.5">Bengkel</th>
                                    <th class="text-right px-3.5 py-2.5">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E9EFEF]">
                                <template x-for="item in riwayatData.riwayat" :key="item.servis_id">
                                    <tr class="hover:bg-[#F4F6F5]/60 transition-colors">
                                        <td class="px-3.5 py-2.5 whitespace-nowrap text-[#0A0E2E] font-medium"
                                            x-text="new Date(item.tanggal_servis).toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit', year:'numeric'})">
                                        </td>
                                        <td class="px-3.5 py-2.5">
                                            <div class="font-bold text-[#0A0E2E]" x-text="item.jenis_servis"></div>
                                            <template x-if="item.catatan">
                                                <div class="text-[11px] text-[#6C7E75] mt-0.5" x-text="item.catatan"></div>
                                            </template>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-[#6C7E75]" x-text="item.bengkel_pelaksana || '-'"></td>
                                        <td class="px-3.5 py-2.5 text-right font-bold text-[#0A0E2E] whitespace-nowrap"
                                            x-text="`Rp ${new Intl.NumberFormat('id-ID').format(item.biaya)}`"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="flex justify-end mt-4 pt-3 border-t border-[#E9EFEF]">
                <button type="button" @click="riwayatModalOpen = false"
                    class="px-4 py-2 bg-[#F4F6F5] hover:bg-[#E9EFEF] text-[#0A0E2E] font-bold text-xs rounded-xl transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </x-ui.modal>

    </div>
</x-layouts.app>