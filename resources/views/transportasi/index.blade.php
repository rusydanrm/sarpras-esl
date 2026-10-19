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
        }
    }">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
            <h1 class="text-2xl font-bold">Transportasi</h1>

            @if (auth()->user()->role === 'admin')
                <x-ui.button @click="modalOpen = true; editData = null">+ Tambah Kendaraan</x-ui.button>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <x-ui.badge variant="success" class="block mb-4 !rounded-lg px-3 py-2 text-sm font-medium">
                {{ session('success') }}
            </x-ui.badge>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-[#FEF7F7] text-[#EF4444] rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Ringkasan Simple (Sesuai Dashboard) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-ui.card>
                <div class="text-xs text-[#495465] mb-1">Total Kendaraan</div>
                <div class="text-2xl font-bold text-[#120F8B]">{{ $totalKendaraan }}</div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-xs text-[#495465] mb-1">Siap Operasional</div>
                <div class="text-2xl font-bold text-[#10B981]">{{ $kendaraanTersedia }}</div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-xs text-[#495465] mb-1">Perlu Servis Segera</div>
                <div class="text-2xl font-bold text-[#EF4444]">{{ $kendaraanPerluServis }}</div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-xs text-[#495465] mb-1">Biaya Servis ({{ date('Y') }})</div>
                <div class="text-2xl font-bold text-[#120F8B]">Rp
                    {{ number_format($totalBiayaServisTahunIni, 0, ',', '.') }}</div>
            </x-ui.card>
        </div>

        {{-- Filter Bar --}}
        <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap gap-3 mb-4">
            <div class="w-full sm:w-56">
                <x-ui.input name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama, plat nomor..."
                    x-on:input.debounce.400ms="terapkanFilter()" />
            </div>

            <div class="w-full sm:w-44">
                <x-ui.select name="jenis_kendaraan" x-on:change="terapkanFilter()">
                    <option value="">Semua Jenis</option>
                    @foreach (['Mobil', 'Motor', 'Bus/Minibus', 'Lainnya'] as $jk)
                        <option value="{{ $jk }}" @selected(($filter['jenis_kendaraan'] ?? '') === $jk)>{{ $jk }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div class="w-full sm:w-44">
                <x-ui.select name="status" x-on:change="terapkanFilter()">
                    <option value="">Semua Status</option>
                    @foreach (['Tersedia', 'Digunakan', 'Dalam Perawatan'] as $st)
                        <option value="{{ $st }}" @selected(($filter['status'] ?? '') === $st)>{{ $st }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div class="w-full sm:w-48">
                <x-ui.select name="status_servis" x-on:change="terapkanFilter()">
                    <option value="">Semua Jadwal Servis</option>
                    <option value="Jatuh Tempo" @selected(($filter['status_servis'] ?? '') === 'Jatuh Tempo')>Jatuh Tempo
                    </option>
                    <option value="Mendekati Servis" @selected(($filter['status_servis'] ?? '') === 'Mendekati Servis')>
                        Mendekati Servis</option>
                    <option value="Aman" @selected(($filter['status_servis'] ?? '') === 'Aman')>Aman</option>
                </x-ui.select>
            </div>
        </form>

        {{-- Tabel Data --}}
        <div id="tabelWrapper">
            @include('transportasi._table', ['kendaraanList' => $kendaraanList])
        </div>

        {{-- Modal Tambah / Edit Kendaraan --}}
        <x-ui.modal open="modalOpen" maxWidth="2xl">
            <h2 class="text-lg font-bold mb-4 text-[#181B21]" x-text="editData ? 'Edit Kendaraan' : 'Tambah Kendaraan'">
            </h2>

            <form method="POST" enctype="multipart/form-data"
                :action="editData ? `/transportasi/${editData.kendaraan_id}` : '{{ route('transportasi.store') }}'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    {{-- Section 1: Identitas Utama --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#120F8B] border-b border-[#DEE2E7] pb-1 mb-3">
                            1. Identitas Kendaraan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                    x-bind:value="editData ? editData.model_kendaraan : ''" placeholder="Contoh: Mobil Penumpang / MPV" />
                            </div>

                            <div>
                                <x-ui.input label="Merek / Tipe / Spesifikasi" name="merek_tipe"
                                    x-bind:value="editData ? editData.merek_tipe : ''" placeholder="Contoh: Toyota Avanza 1.3 G" />
                            </div>

                            <div>
                                <x-ui.input label="No. Rangka" name="no_rangka"
                                    x-bind:value="editData ? editData.no_rangka : ''" placeholder="Contoh: MHKM1BA3J..." />
                            </div>

                            <div>
                                <x-ui.input label="No. Mesin" name="no_mesin"
                                    x-bind:value="editData ? editData.no_mesin : ''" placeholder="Contoh: 1NR-FE..." />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-[#495465] mb-1">Foto Fisik Kendaraan</label>
                                <input type="file" name="foto" accept="image/*"
                                    class="w-full text-xs text-[#181B21] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#120F8B] file:text-white hover:file:bg-[#0D0B63] border border-[#DEE2E7] rounded-lg cursor-pointer bg-white p-1" />
                                <span class="text-[11px] text-[#9EA9B9] mt-0.5 block">Format: JPG, PNG, WEBP (Maksimal 3MB). Kosongkan jika tidak mengubah foto.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Perolehan & Nilai --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#120F8B] border-b border-[#DEE2E7] pb-1 mb-3">
                            2. Data Perolehan & Nilai
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

                    {{-- Section 3: Operasional & Servis --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#120F8B] border-b border-[#DEE2E7] pb-1 mb-3">
                            3. Operasional & Servis
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                    x-bind:value="editData ? editData.penanggung_jawab : ''" placeholder="Nama staf / driver" />
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
                                    class="w-full text-sm rounded-lg border border-[#DEE2E7] px-3 py-2 text-[#181B21] focus:outline-none focus:border-[#120F8B]"
                                    placeholder="Informasi catatan kendaraan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-[#DEE2E7]">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Modal Catat Servis Cepat --}}
        <x-ui.modal open="servisModalOpen" maxWidth="md" zIndex="z-[60]">
            <h2 class="text-lg font-bold mb-1 text-[#181B21]">Catat Servis Kendaraan</h2>
            <p class="text-xs text-[#495465] mb-4"
                x-text="targetKendaraan ? `${targetKendaraan.nama_kendaraan} (${targetKendaraan.plat_nomor})` : ''"></p>

            <form method="POST"
                :action="targetKendaraan ? `/transportasi/${targetKendaraan.kendaraan_id}/servis` : '#'">
                @csrf
                <div class="space-y-3">
                    <div>
                        <x-ui.input type="date" label="Tanggal Servis" name="tanggal_servis" value="{{ date('Y-m-d') }}"
                            required />
                    </div>

                    <div>
                        <x-ui.select label="Jenis Servis" name="jenis_servis" required>
                            <option value="Servis Rutin">Servis Rutin</option>
                            <option value="Ganti Oli">Ganti Oli</option>
                            <option value="Perbaikan Mesin">Perbaikan Mesin</option>
                            <option value="Ganti Ban/Aki">Ganti Ban / Aki</option>
                            <option value="Uji Emisi / KIR">Uji Emisi / KIR</option>
                            <option value="Lainnya">Lainnya</option>
                        </x-ui.select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-ui.input type="number" label="Kilometer (Odometer)" name="odometer_km"
                                placeholder="Contoh: 45000" min="0" />
                        </div>
                        <div>
                            <x-ui.input type="number" label="Biaya (Rp)" name="biaya" placeholder="0" min="0" />
                        </div>
                    </div>

                    <div>
                        <x-ui.input label="Bengkel Pelaksana" name="bengkel_pelaksana" placeholder="Nama bengkel" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#495465] mb-1">Catatan</label>
                        <textarea name="catatan" rows="2"
                            class="w-full text-sm rounded-lg border border-[#DEE2E7] px-3 py-2 text-[#181B21] focus:outline-none focus:border-[#120F8B]"
                            placeholder="Detail perbaikan..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <x-ui.button type="button" variant="outline" @click="servisModalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Modal Riwayat Servis --}}
        <x-ui.modal open="riwayatModalOpen" maxWidth="lg" zIndex="z-[60]">
            <div class="flex items-center justify-between mb-4 border-b border-[#DEE2E7] pb-3">
                <div>
                    <h2 class="text-lg font-bold text-[#181B21]"
                        x-text="riwayatData ? `Riwayat Servis: ${riwayatData.kendaraan.nama_kendaraan}` : 'Riwayat Servis'">
                    </h2>
                    <p class="text-xs text-[#495465]"
                        x-text="riwayatData ? `Plat: ${riwayatData.kendaraan.plat_nomor} • Total Biaya: Rp ${new Intl.NumberFormat('id-ID').format(riwayatData.total_biaya)}` : ''">
                    </p>
                </div>
            </div>

            <div x-show="loadingRiwayat" class="py-8 text-center text-[#495465] text-sm">
                Memuat riwayat servis...
            </div>

            <div x-show="!loadingRiwayat">
                <template x-if="riwayatData && riwayatData.riwayat.length === 0">
                    <div class="py-8 text-center text-[#9EA9B9] text-sm">
                        Belum ada catatan riwayat servis.
                    </div>
                </template>

                <template x-if="riwayatData && riwayatData.riwayat.length > 0">
                    <div class="overflow-x-auto max-h-[360px] border border-[#DEE2E7] rounded-xl">
                        <table class="w-full text-xs">
                            <thead class="bg-[#F7F8F9] text-[#495465] uppercase border-b border-[#DEE2E7]">
                                <tr>
                                    <th class="text-left px-3 py-2">Tanggal</th>
                                    <th class="text-left px-3 py-2">Tindakan</th>
                                    <th class="text-left px-3 py-2">Bengkel</th>
                                    <th class="text-right px-3 py-2">Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#DEE2E7]">
                                <template x-for="item in riwayatData.riwayat" :key="item.servis_id">
                                    <tr class="hover:bg-[#F7F8F9]/60">
                                        <td class="px-3 py-2 whitespace-nowrap text-[#181B21]"
                                            x-text="new Date(item.tanggal_servis).toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit', year:'numeric'})">
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="font-medium text-[#181B21]" x-text="item.jenis_servis"></div>
                                            <template x-if="item.catatan">
                                                <div class="text-[11px] text-[#9EA9B9]" x-text="item.catatan"></div>
                                            </template>
                                        </td>
                                        <td class="px-3 py-2 text-[#495465]" x-text="item.bengkel_pelaksana || '-'">
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-[#181B21]"
                                            x-text="`Rp ${new Intl.NumberFormat('id-ID').format(item.biaya)}`"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="flex justify-end mt-4 pt-3 border-t border-[#DEE2E7]">
                <x-ui.button type="button" variant="outline" size="sm"
                    @click="riwayatModalOpen = false">Tutup</x-ui.button>
            </div>
        </x-ui.modal>
    </div>
</x-layouts.app>