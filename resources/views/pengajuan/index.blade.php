<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        detailOpen: false,
        tolakModalOpen: false,
        tolakPengajuanId: null,
        selectedPengajuan: null,
        statusAktif: '{{ request('status', '') }}',
        jenis: '',
        pilihStatus(s) {
            this.statusAktif = s;
            document.getElementById('inputStatus').value = s;
            this.terapkanFilter();
        },
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('pengajuan.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('selectJenis').value = '';
            this.pilihStatus('');
        },
        bukaDetail(data) {
            this.selectedPengajuan = data;
            this.detailOpen = true;
        },
        bukaTolakModal(id) {
            this.tolakPengajuanId = id;
            this.tolakModalOpen = true;
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
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Pengajuan Sarana & Prasarana</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Alur permohonan pengadaan, perbaikan, pemeliharaan, dan penggantian inventaris Departemen ESL</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span>Total: <strong class="text-[#0A0E2E] font-bold">{{ $totalSemua }}</strong> Pengajuan</span>
                </div>
                <button type="button" @click="modalOpen = true"
                    class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                    <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                    <span>Ajukan Baru</span>
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

        {{-- Status Tabs / Pills ala Spark Admin --}}
        <div class="mb-4 overflow-x-auto pb-1 -mx-1 px-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none">
            <div class="flex items-center gap-2 min-w-max p-0.5">
                <button type="button"
                    @click="pilihStatus('')"
                    :class="statusAktif === '' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Semua Status</span>
                    <span :class="statusAktif === '' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $totalSemua }}
                    </span>
                </button>

                @foreach (['Diajukan', 'Diverifikasi', 'Disetujui', 'Diproses', 'Selesai', 'Ditolak'] as $st)
                    <button type="button"
                        @click="pilihStatus('{{ $st }}')"
                        :class="statusAktif === '{{ $st }}' 
                            ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                            : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                        class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                        <span>{{ $st }}</span>
                        <span :class="statusAktif === '{{ $st }}' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                            class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                            {{ $statusCounts[$st] ?? 0 }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Filter Toolbar ala Spark Admin Header Control -->
        <div class="bg-white rounded-2xl border border-[#E9EFEF] p-3.5 sm:p-4 mb-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">

                <!-- Filter Jenis Pengajuan -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="w-full sm:w-56">
                        <select name="jenis" id="selectJenis" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2.5 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#495465] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium cursor-pointer">
                            <option value="">Semua Jenis Pengajuan</option>
                            @foreach (['Pengadaan', 'Perbaikan', 'Pemeliharaan', 'Penggantian'] as $j)
                                <option value="{{ $j }}" @selected(request('jenis') == $j)>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" 
                        @click="resetFilter()"
                        title="Reset Filter"
                        class="px-3.5 py-2.5 text-xs text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl font-medium transition-all inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                        <span>Reset Filter</span>
                    </button>
                </div>

                <div class="text-xs text-[#6C7E75]">
                    <span class="font-medium">Alur:</span> Diajukan → Diverifikasi → Disetujui → Diproses → Selesai
                </div>
            </form>
        </div>

        <!-- Tabel Container (AJAX Target) -->
        <div id="tabelWrapper" @click="handleTableClick($event)">
            @include('pengajuan._table', ['pengajuan' => $pengajuan])
        </div>

        {{-- Modal Ajukan Baru ala Spark Admin --}}
        <x-ui.modal open="modalOpen" maxWidth="lg">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-paper-airplane class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Buat Pengajuan Baru</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Kirimkan permohonan sarana prasarana untuk diverifikasi</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pengajuan.store') }}">
                @csrf
                <div class="space-y-4">
                    <x-ui.select label="Jenis Pengajuan" name="jenis_pengajuan" x-model="jenis" required>
                        <option value="">- Pilih Jenis Pengajuan -</option>
                        <option value="Pengadaan">Pengadaan (Permohonan Aset Baru)</option>
                        <option value="Perbaikan">Perbaikan (Servis Kerusakan)</option>
                        <option value="Pemeliharaan">Pemeliharaan Rutin</option>
                        <option value="Penggantian">Penggantian Aset Rusak Berat</option>
                    </x-ui.select>

                    <div x-show="['Perbaikan','Pemeliharaan','Penggantian'].includes(jenis)" x-cloak x-transition>
                        <x-ui.select label="Barang Terkait" name="barang_id">
                            <option value="">- Pilih Barang yang Diajukan -</option>
                            @foreach ($barangList as $b)
                                <option value="{{ $b->barang_id }}">
                                    {{ $b->nama_barang }} ({{ $b->ruangan?->nama_ruang ?? 'Tanpa Ruangan' }}) - {{ $b->kode_barang ?? 'No Code' }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">
                            Deskripsi / Rincian Kebutuhan <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <textarea name="deskripsi" required rows="4"
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium"
                            placeholder="Jelaskan secara detail spesifikasi kebutuhan pengadaan, gejala kerusakan barang, atau alasan permohonan..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Modal Detail Pengajuan & Riwayat Alur --}}
        <x-ui.modal open="detailOpen" maxWidth="lg">
            <template x-if="selectedPengajuan">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E9EFEF]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-document-magnifying-glass class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#0A0E2E]">Detail Pengajuan</h2>
                                <p class="text-xs text-[#6C7E75]">ID Pengajuan: #PGJ-<span x-text="selectedPengajuan.pengajuan_id"></span></p>
                            </div>
                        </div>
                        <span class="badge-table"
                            :class="{
                                'success': ['Selesai', 'Disetujui'].includes(selectedPengajuan.status),
                                'danger': selectedPengajuan.status === 'Ditolak',
                                'accent': selectedPengajuan.status === 'Diverifikasi',
                                'warning': ['Diajukan', 'Diproses'].includes(selectedPengajuan.status)
                            }"
                            x-text="selectedPengajuan.status">
                        </span>
                    </div>

                    <!-- Workflow Timeline Stepper -->
                    <div class="bg-[#F8FAF9] border border-[#E9EFEF] rounded-2xl p-4 mb-4">
                        <label class="text-[11px] font-bold text-[#6C7E75] uppercase tracking-wider block mb-3">Status Alur Verifikasi</label>
                        <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
                            <div class="p-2 rounded-xl transition-all"
                                :class="['Diajukan', 'Diverifikasi', 'Disetujui', 'Diproses', 'Selesai'].includes(selectedPengajuan.status) ? 'bg-[#0A0E2E] text-white font-bold' : 'bg-white border border-[#E9EFEF] text-[#9EA9B9]'">
                                1. Diajukan
                            </div>
                            <div class="p-2 rounded-xl transition-all"
                                :class="['Diverifikasi', 'Disetujui', 'Diproses', 'Selesai'].includes(selectedPengajuan.status) ? 'bg-[#0A0E2E] text-white font-bold' : (selectedPengajuan.status === 'Ditolak' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-white border border-[#E9EFEF] text-[#9EA9B9]')">
                                2. Diverifikasi
                            </div>
                            <div class="p-2 rounded-xl transition-all"
                                :class="['Disetujui', 'Diproses', 'Selesai'].includes(selectedPengajuan.status) ? 'bg-emerald-600 text-white font-bold' : (selectedPengajuan.status === 'Ditolak' ? 'bg-rose-600 text-white font-bold' : 'bg-white border border-[#E9EFEF] text-[#9EA9B9]')">
                                <span x-text="selectedPengajuan.status === 'Ditolak' ? 'Ditolak' : '3. Disetujui'"></span>
                            </div>
                            <div class="p-2 rounded-xl transition-all"
                                :class="selectedPengajuan.status === 'Selesai' ? 'bg-emerald-600 text-white font-bold' : (selectedPengajuan.status === 'Diproses' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-white border border-[#E9EFEF] text-[#9EA9B9]')">
                                <span x-text="selectedPengajuan.status === 'Diproses' ? 'Diproses' : '4. Selesai'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Fields -->
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-white border border-[#E9EFEF] rounded-xl">
                                <span class="text-[#6C7E75] block text-[11px] mb-0.5">Pemohon</span>
                                <strong class="text-[#0A0E2E] font-bold text-xs" x-text="selectedPengajuan.pemohon?.nama ?? '-'"></strong>
                            </div>
                            <div class="p-3 bg-white border border-[#E9EFEF] rounded-xl">
                                <span class="text-[#6C7E75] block text-[11px] mb-0.5">Jenis Pengajuan</span>
                                <strong class="text-[#0A0E2E] font-bold text-xs" x-text="selectedPengajuan.jenis_pengajuan"></strong>
                            </div>
                        </div>

                        <div class="p-3 bg-white border border-[#E9EFEF] rounded-xl">
                            <span class="text-[#6C7E75] block text-[11px] mb-0.5">Objek yang Diajukan</span>
                            <div class="font-bold text-xs text-[#0A0E2E]" x-text="selectedPengajuan.barang?.nama_barang ?? selectedPengajuan.ruangan?.nama_ruang ?? 'Pengadaan Baru'"></div>
                        </div>

                        <div class="p-3 bg-white border border-[#E9EFEF] rounded-xl">
                            <span class="text-[#6C7E75] block text-[11px] mb-1 font-semibold">Deskripsi Permohonan:</span>
                            <p class="text-[#495465] leading-relaxed whitespace-pre-line" x-text="selectedPengajuan.deskripsi"></p>
                        </div>

                        <template x-if="selectedPengajuan.catatan_verifikasi">
                            <div class="p-3 rounded-xl border"
                                :class="selectedPengajuan.status === 'Ditolak' ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-amber-50 border-amber-200 text-amber-800'">
                                <span class="block text-[11px] font-bold mb-1">Catatan Verifikasi / Penolakan Pimpinan:</span>
                                <p class="text-xs leading-relaxed" x-text="selectedPengajuan.catatan_verifikasi"></p>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end mt-5 pt-3 border-t border-[#E9EFEF]">
                        <x-ui.button type="button" variant="outline" @click="detailOpen = false">Tutup</x-ui.button>
                    </div>
                </div>
            </template>
        </x-ui.modal>

        {{-- Modal Tolak Pengajuan (Menggantikan prompt browser) --}}
        <x-ui.modal open="tolakModalOpen" maxWidth="sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0">
                    <x-heroicon-o-x-circle class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-bold text-base text-[#0A0E2E]">Tolak Pengajuan</h3>
                    <p class="text-xs text-[#6C7E75]">Berikan alasan penolakan kepada pemohon</p>
                </div>
            </div>

            <form method="POST" :action="'/pengajuan/' + tolakPengajuanId + '/tolak'">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="text-xs font-semibold text-[#495465] block mb-1.5">Alasan Penolakan <span class="text-rose-500 font-bold">*</span></label>
                    <textarea name="catatan_verifikasi" required rows="3"
                        class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 font-medium"
                        placeholder="Tuliskan alasan pengajuan ini tidak dapat disetujui..."></textarea>
                </div>
                <div class="flex justify-end gap-2.5">
                    <x-ui.button type="button" variant="outline" size="sm" @click="tolakModalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-4 py-2 bg-rose-600 text-white hover:bg-rose-700 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                        Ya, Tolak Pengajuan
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>