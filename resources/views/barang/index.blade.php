<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        tambahOpen: false,
        kategoriAktif: '{{ request('jenis_id', '') }}',
        pilihKategori(id) {
            this.kategoriAktif = id;
            document.getElementById('inputJenisId').value = id;
            this.terapkanFilter();
        },
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('barang.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('inputCari').value = '';
            document.getElementById('selectGedung').value = '';
            document.getElementById('selectKondisi').value = '';
            this.pilihKategori('');
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
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Data Barang</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Daftar seluruh inventaris sarana dan prasarana Departemen ESL</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span>Total Aset: <strong class="text-[#0A0E2E] font-bold">{{ number_format($totalSemuaBarang, 0, ',', '.') }}</strong> Unit</span>
                </div>
                @if (in_array(auth()->user()->role, ['admin', 'dosen_tendik']))
                    <button type="button" @click="tambahOpen = true"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                        <span>Tambah Barang</span>
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

        {{-- Category Tabs / Pills ala Spark Admin --}}
        <div x-data="{
                isDown: false,
                startX: 0,
                scrollLeft: 0,
                dragged: false,
                handleMouseDown(e) {
                    this.isDown = true;
                    this.dragged = false;
                    this.startX = e.pageX - $el.offsetLeft;
                    this.scrollLeft = $el.scrollLeft;
                },
                handleMouseLeave() { this.isDown = false; },
                handleMouseUp() { this.isDown = false; },
                handleMouseMove(e) {
                    if (!this.isDown) return;
                    this.dragged = true;
                    const x = e.pageX - $el.offsetLeft;
                    const walk = (x - this.startX) * 1.5;
                    $el.scrollLeft = this.scrollLeft - walk;
                },
                handleWheel(e) {
                    if (Math.abs(e.deltaX) < Math.abs(e.deltaY)) {
                        $el.scrollLeft += e.deltaY;
                        e.preventDefault();
                    }
                }
            }"
            @mousedown="handleMouseDown($event)"
            @mouseleave="handleMouseLeave()"
            @mouseup="handleMouseUp()"
            @mousemove="handleMouseMove($event)"
            @wheel="handleWheel($event)"
            class="mb-4 overflow-x-auto pb-1 -mx-1 px-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none cursor-grab active:cursor-grabbing">
            <div class="flex items-center gap-2 min-w-max p-0.5">
                <button type="button"
                    @click="if (!dragged) pilihKategori('')"
                    :class="kategoriAktif === '' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Semua Kategori</span>
                    <span :class="kategoriAktif === '' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $totalSemuaBarang }}
                    </span>
                </button>

                @foreach ($kategoriTabs as $kat)
                    <button type="button"
                        @click="if (!dragged) pilihKategori('{{ $kat->jenis_id }}')"
                        :class="kategoriAktif == '{{ $kat->jenis_id }}' 
                            ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                        class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                        <span>{{ $kat->nama_jenis }}</span>
                        <span :class="kategoriAktif == '{{ $kat->jenis_id }}' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                            class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                            {{ $kat->total_unit }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Filter Toolbar ala Spark Admin Header Control -->
        <div class="bg-white rounded-2xl border border-[#E9EFEF] p-3.5 sm:p-4 mb-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <input type="hidden" name="jenis_id" id="inputJenisId" value="{{ request('jenis_id') }}">
                
                <!-- Search Input Box -->
                <div class="relative flex-1 max-w-md">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-[#6C7E75] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input type="text" name="cari" id="inputCari" value="{{ request('cari') }}" 
                        placeholder="Cari nama barang, kode, merek..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-10 pr-4 py-2.5 text-xs bg-[#FDFDFD] border border-[#E9EFEF] rounded-xl text-[#0A0E2E] placeholder-[#9EA9B9] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" />
                </div>

                <!-- Dropdown Filters -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="w-full sm:w-44">
                        <select name="gedung_id" id="selectGedung" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Gedung</option>
                            @foreach ($gedungList as $g)
                                <option value="{{ $g->gedung_id }}" @selected(request('gedung_id') == $g->gedung_id)>{{ $g->nama_gedung }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40">
                        <select name="kondisi" id="selectKondisi" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Kondisi</option>
                            @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k)
                                <option value="{{ $k }}" @selected(request('kondisi') == $k)>{{ $k }}</option>
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
            @include('barang._table', ['barang' => $barang])
        </div>

        <p class="text-xs text-[#9EA9B9] mt-3">Untuk menambah barang ke ruangan spesifik secara cepat, bisa juga dilakukan melalui halaman Inventaris Ruangan.</p>

        {{-- Modal Edit Barang ala Spark Admin --}}
        <x-ui.modal open="modalOpen" maxWidth="lg">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E] shrink-0">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-[#0A0E2E]" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Edit Data Barang</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Perbarui spesifikasi, kondisi, atau nilai perolehan barang</p>
                </div>
            </div>

            <form method="POST" :action="editData ? `/barang/${editData.barang_id}` : ''">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Kode Barang" name="kode_barang" x-bind:value="editData?.kode_barang" placeholder="Contoh: 3.05.01.04.001" />
                        <x-ui.input label="NUP" name="nup" x-bind:value="editData?.nup" placeholder="Nomor Urut Pendaftaran" />
                    </div>

                    <x-ui.input label="Nama Barang" name="nama_barang" x-bind:value="editData?.nama_barang" required placeholder="Nama lengkap inventaris" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Merek / Tipe" name="merek_tipe" x-bind:value="editData?.merek_tipe" placeholder="Contoh: Asus Vivobook / Daikin 1PK" />
                        <x-ui.select label="Kategori / Jenis" name="jenis_id">
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($jenisList as $j)
                                <option value="{{ $j->jenis_id }}" x-bind:selected="editData && editData.jenis_id == {{ $j->jenis_id }}">{{ $j->nama_jenis }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <x-ui.select label="Tahun Perolehan" name="tahun_perolehan">
                        <option value="">- Pilih Tahun -</option>
                        @for ($y = date('Y'); $y >= 1990; $y--)
                            <option value="{{ $y }}" x-bind:selected="editData && editData.tahun_perolehan == {{ $y }}">{{ $y }}</option>
                        @endfor
                    </x-ui.select>

                    <!-- Kondisi & Unit Group -->
                    <div class="border border-[#E9EFEF] rounded-2xl p-4 bg-[#F8FAF9]">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-bold text-[#0A0E2E]">Kondisi & Jumlah Unit</label>
                            <span class="text-xs bg-[#0A0E2E] text-white px-2.5 py-0.5 rounded-full font-bold">
                                Total: <span x-text="(parseInt(editData?.kondisi_baik)||0) + (parseInt(editData?.kondisi_rusak_ringan)||0) + (parseInt(editData?.kondisi_rusak_berat)||0)"></span> Unit
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-700 block mb-1">Baik</label>
                                <input type="number" name="kondisi_baik" min="0" x-bind:value="editData?.kondisi_baik ?? 0"
                                    x-on:input="if (editData) editData.kondisi_baik = $event.target.value" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-amber-700 block mb-1">Rusak Ringan</label>
                                <input type="number" name="kondisi_rusak_ringan" min="0" x-bind:value="editData?.kondisi_rusak_ringan ?? 0"
                                    x-on:input="if (editData) editData.kondisi_rusak_ringan = $event.target.value" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-rose-700 block mb-1">Rusak Berat</label>
                                <input type="number" name="kondisi_rusak_berat" min="0" x-bind:value="editData?.kondisi_rusak_berat ?? 0"
                                    x-on:input="if (editData) editData.kondisi_rusak_berat = $event.target.value" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Satuan" name="satuan" x-bind:value="editData?.satuan" placeholder="unit / buah / set" />
                        <x-ui.input label="Harga Satuan (Rp)" type="number" name="harga_satuan" x-bind:value="editData?.harga_satuan" required />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Sumber Dana" name="sumber_dana" x-bind:value="editData?.sumber_dana" placeholder="BOPTN / RKAT / Hibah" />
                        <x-ui.input label="Jadwal Servis (bulan)" type="number" name="interval_pemeliharaan_bulan"
                            x-bind:value="editData?.interval_pemeliharaan_bulan"
                            placeholder="Contoh: 3 untuk AC tiap 3 bln" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="2" 
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" 
                            x-text="editData?.keterangan"
                            placeholder="Catatan khusus atau spesifikasi teknis barang..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Modal Tambah Barang ala Spark Admin --}}
        <x-ui.modal open="tambahOpen" maxWidth="lg">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-plus class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Tambah Barang Baru</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Daftarkan inventaris baru ke sistem sarana prasarana</p>
                </div>
            </div>

            <form method="POST" action="{{ route('barang.store-global') }}" x-data="{ baik: 1, rr: 0, rb: 0 }">
                @csrf
                <div class="space-y-4">
                    <x-ui.select label="Penempatan Ruangan" name="ruangan_id" required>
                        <option value="">- Pilih Ruangan Tujuan -</option>
                        @foreach ($ruanganList as $r)
                            <option value="{{ $r->ruangan_id }}">{{ $r->nama_ruang }} ({{ $r->gedung->nama_gedung }})</option>
                        @endforeach
                    </x-ui.select>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Kode Barang" name="kode_barang" placeholder="Contoh: 3.05.01.04.001" />
                        <x-ui.input label="NUP" name="nup" placeholder="Nomor Urut Pendaftaran" />
                    </div>

                    <x-ui.input label="Nama Barang" name="nama_barang" required placeholder="Nama lengkap barang inventaris" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Merek / Tipe" name="merek_tipe" placeholder="Contoh: Asus Vivobook / Daikin 1PK" />
                        <x-ui.select label="Kategori / Jenis" name="jenis_id">
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($jenisList as $j)
                                <option value="{{ $j->jenis_id }}">{{ $j->nama_jenis }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <x-ui.select label="Tahun Perolehan" name="tahun_perolehan">
                        <option value="">- Pilih Tahun -</option>
                        @for ($y = date('Y'); $y >= 1990; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </x-ui.select>

                    <!-- Kondisi & Unit Group -->
                    <div class="border border-[#E9EFEF] rounded-2xl p-4 bg-[#F8FAF9]">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-xs font-bold text-[#0A0E2E]">Kondisi & Jumlah Unit</label>
                            <span class="text-xs bg-[#0A0E2E] text-white px-2.5 py-0.5 rounded-full font-bold">
                                Total: <span x-text="(parseInt(baik)||0) + (parseInt(rr)||0) + (parseInt(rb)||0)"></span> Unit
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-700 block mb-1">Baik</label>
                                <input type="number" name="kondisi_baik" min="0" x-model="baik" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-amber-700 block mb-1">Rusak Ringan</label>
                                <input type="number" name="kondisi_rusak_ringan" min="0" x-model="rr" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-rose-700 block mb-1">Rusak Berat</label>
                                <input type="number" name="kondisi_rusak_berat" min="0" x-model="rb" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Satuan" name="satuan" placeholder="unit / buah / set" />
                        <x-ui.input label="Harga Satuan (Rp)" type="number" name="harga_satuan" value="0" required />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Sumber Dana" name="sumber_dana" placeholder="BOPTN / RKAT / Hibah" />
                        <x-ui.input label="Jadwal Servis (bulan)" type="number" name="interval_pemeliharaan_bulan"
                            placeholder="Contoh: 3 untuk AC tiap 3 bln" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="2" 
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium" 
                            placeholder="Catatan khusus atau rincian spesifikasi barang..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="tambahOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Barang
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>