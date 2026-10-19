<x-layouts.app>
    <div x-data="{
        servisModalOpen: {{ $errors->hasAny(['tanggal_servis', 'jenis_servis', 'biaya']) ? 'true' : 'false' }},
        editModalOpen: {{ $errors->hasAny(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'interval_servis_bulan']) ? 'true' : 'false' }},
        editData: {{ Illuminate\Support\Js::from($kendaraan) }}
    }">
        {{-- Link Navigasi Kembali --}}
        <div class="mb-3">
            <a href="{{ route('transportasi.index') }}" class="text-xs text-[#495465] hover:text-[#120F8B] hover:underline inline-flex items-center gap-1 font-medium">
                <x-heroicon-o-arrow-left class="w-3.5 h-3.5" /> Kembali ke Transportasi
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <x-ui.badge variant="success" class="block mb-4 !rounded-lg px-3 py-2 text-sm font-medium">
                {{ session('success') }}
            </x-ui.badge>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-[#FEF7F7] text-[#EF4444] rounded-lg text-sm border border-[#EF4444]/20">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Bar --}}
        <div class="bg-white border border-[#DEE2E7] rounded-xl p-4 sm:p-5 mb-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="text-xs text-[#495465] flex items-center gap-1">
                        <span>Unit</span> <span class="font-semibold text-[#181B21]">Departemen Ekonomi Sumberdaya dan Lingkungan (ESL)</span>
                    </div>
                    <h1 class="text-2xl font-bold text-[#181B21] mt-0.5">{{ $kendaraan->nama_kendaraan }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('transportasi.index') }}"
                        class="px-3.5 py-1.5 bg-white hover:bg-[#F7F8F9] text-[#495465] hover:text-[#181B21] border border-[#DEE2E7] text-xs font-semibold rounded-lg shadow-sm flex items-center gap-1.5 transition-colors">
                        <x-heroicon-o-arrow-uturn-left class="w-3.5 h-3.5" /> Kembali
                    </a>
                    <a href="{{ route('laporan.pdf') }}" target="_blank"
                        class="px-3.5 py-1.5 bg-[#C8102E] hover:bg-[#A50D26] text-white text-xs font-semibold rounded-lg shadow-sm flex items-center gap-1.5 transition-colors">
                        <x-heroicon-o-document-text class="w-3.5 h-3.5" /> Export PDF
                    </a>
                </div>
            </div>
            <div class="mt-3 p-2.5 bg-[#F7F8F9] border border-[#DEE2E7] rounded-lg text-xs text-[#495465] flex flex-wrap items-center gap-2">
                <span>Nomor Polisi:</span>
                <span class="font-bold text-[#181B21] bg-white px-2 py-0.5 rounded border border-[#DEE2E7]">{{ $kendaraan->plat_nomor }}</span>
                <span class="text-[#9EA9B9]">|</span>
                <span>Jenis: <strong class="text-[#181B21]">{{ $kendaraan->jenis_kendaraan }}</strong></span>
                @if ($kendaraan->merek_tipe)
                    <span class="text-[#9EA9B9]">|</span>
                    <span>Tipe: <strong class="text-[#181B21]">{{ $kendaraan->merek_tipe }}</strong></span>
                @endif
                <span class="text-[#9EA9B9]">|</span>
                <span>Status:</span>
                @if ($kendaraan->status === 'Tersedia')
                    <x-ui.badge variant="success">Tersedia</x-ui.badge>
                @elseif ($kendaraan->status === 'Digunakan')
                    <x-ui.badge variant="warning">Digunakan</x-ui.badge>
                @else
                    <x-ui.badge variant="danger">Dalam Perawatan</x-ui.badge>
                @endif
            </div>
        </div>

        {{-- 2 Kolom Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 text-sm">
            
            {{-- Kolom Kiri: Informasi Kendaraan (7 col) --}}
            <div class="lg:col-span-7 space-y-5">
                <div class="border border-[#DEE2E7] rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-[#120F8B] px-4 py-2.5 flex items-center justify-between text-white">
                        <span class="text-xs font-bold uppercase tracking-wider">Informasi Kendaraan</span>
                        @if (auth()->user()->role === 'admin')
                            <button type="button" @click="editModalOpen = true"
                                class="px-2.5 py-1 bg-white text-[#120F8B] hover:bg-[#F7F8F9] text-xs font-semibold rounded shadow-sm flex items-center gap-1 transition-colors cursor-pointer">
                                <x-heroicon-o-pencil-square class="w-3.5 h-3.5" /> Edit Data
                            </button>
                        @endif
                    </div>
                    <div class="p-4 sm:p-5 text-xs space-y-2.5">
                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Nama Kendaraan</span>
                            <span class="col-span-3 font-semibold text-[#181B21] text-sm">{{ $kendaraan->nama_kendaraan }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Nomor Polisi (Plat)</span>
                            <span class="col-span-3 font-bold text-[#181B21] font-mono text-sm">{{ $kendaraan->plat_nomor }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Jenis Angkutan</span>
                            <span class="col-span-3 font-medium text-[#181B21]">{{ $kendaraan->jenis_kendaraan }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Model Kendaraan</span>
                            <span class="col-span-3 text-[#181B21]">{{ $kendaraan->model_kendaraan ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Merek / Tipe / Spesifikasi</span>
                            <span class="col-span-3 text-[#181B21]">{{ $kendaraan->merek_tipe ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Kode Barang & NUP</span>
                            <span class="col-span-3 font-mono text-[#181B21]">{{ $kendaraan->kode_barang ?: '-' }} / {{ $kendaraan->nup ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">No. Rangka</span>
                            <span class="col-span-3 font-mono text-[#181B21]">{{ $kendaraan->no_rangka ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">No. Mesin</span>
                            <span class="col-span-3 font-mono text-[#181B21]">{{ $kendaraan->no_mesin ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Tahun Perolehan</span>
                            <div class="col-span-3 text-[#181B21]">
                                <span class="font-semibold">{{ $kendaraan->tahun_perolehan ?: '-' }}</span>
                                @if ($kendaraan->usia_teks)
                                    <span class="text-[#EF4444] font-medium ml-1.5">(Umur {{ $kendaraan->usia_teks }})</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Harga Perolehan</span>
                            <span class="col-span-3 font-semibold text-[#181B21]">Rp {{ number_format($kendaraan->harga_perolehan ?: 0, 0, ',', '.') }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Sumber Dana</span>
                            <span class="col-span-3 text-[#181B21]">{{ $kendaraan->sumber_dana ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Kondisi Fisik</span>
                            <div class="col-span-3">
                                @if ($kendaraan->kondisi === 'Baik')
                                    <x-ui.badge variant="success">Baik</x-ui.badge>
                                @elseif ($kendaraan->kondisi === 'Rusak Ringan')
                                    <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                                @else
                                    <x-ui.badge variant="danger">Rusak Berat</x-ui.badge>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Status Operasional</span>
                            <div class="col-span-3">
                                @if ($kendaraan->status === 'Tersedia')
                                    <x-ui.badge variant="success">Tersedia & Siap Pakai</x-ui.badge>
                                @elseif ($kendaraan->status === 'Digunakan')
                                    <x-ui.badge variant="warning">Sedang Digunakan</x-ui.badge>
                                @else
                                    <x-ui.badge variant="danger">Dalam Perawatan / Bengkel</x-ui.badge>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Penanggung Jawab / Driver</span>
                            <span class="col-span-3 font-semibold text-[#181B21]">{{ $kendaraan->penanggung_jawab ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Pajak STNK Tahunan</span>
                            <div class="col-span-3">
                                <div class="font-semibold text-[#181B21]">
                                    {{ $kendaraan->tanggal_pajak_stnk ? date('d/m/Y', strtotime($kendaraan->tanggal_pajak_stnk)) : '-' }}
                                </div>
                                @if ($kendaraan->stnk_countdown)
                                    <div class="text-[11px] font-medium mt-0.5 {{ str_contains($kendaraan->stnk_countdown, 'Lewat') ? 'text-[#EF4444]' : 'text-[#120F8B]' }}">
                                        {{ $kendaraan->stnk_countdown }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1.5">
                            <span class="col-span-2 text-[#495465] font-medium">Keterangan / Catatan</span>
                            <span class="col-span-3 text-[#495465]">{{ $kendaraan->keterangan ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Foto Fisik, Jadwal Servis & Riwayat Perbaikan (5 col) --}}
            <div class="lg:col-span-5 space-y-5">
                
                {{-- Kartu 1: Foto Kendaraan --}}
                <div class="border border-[#DEE2E7] rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-[#120F8B] px-4 py-2.5 text-white flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider">Foto Fisik Kendaraan</span>
                        <x-heroicon-o-camera class="w-4 h-4 text-white/80" />
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center bg-[#F7F8F9]/40 min-h-[200px]">
                        @if ($kendaraan->foto_url)
                            <img src="{{ asset($kendaraan->foto_url) }}" alt="{{ $kendaraan->nama_kendaraan }}"
                                class="max-h-56 w-auto rounded-lg object-contain border border-[#DEE2E7] shadow-xs" />
                        @else
                            <div class="flex flex-col items-center justify-center py-6 text-[#9EA9B9]">
                                <x-heroicon-o-truck class="w-16 h-16 mb-2 opacity-30" />
                                <span class="text-xs font-semibold text-[#495465]">Belum ada foto fisik</span>
                                @if (auth()->user()->role === 'admin')
                                    <button type="button" @click="editModalOpen = true" class="text-[11px] text-[#120F8B] hover:underline font-medium mt-1">
                                        + Unggah Foto Kendaraan
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kartu 2: Jadwal Servis Rutin --}}
                <div class="border border-[#DEE2E7] rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-[#120F8B] px-4 py-2.5 text-white flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider">Jadwal Servis Rutin</span>
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-white/80" />
                    </div>
                    <div class="p-4 sm:p-5 text-xs space-y-2.5">
                        <div class="grid grid-cols-5 gap-2 py-1 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Interval Rutin</span>
                            <span class="col-span-3 font-semibold text-[#181B21]">
                                Setiap {{ $kendaraan->interval_servis_bulan ?: 6 }} Bulan
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1 border-b border-gray-100">
                            <span class="col-span-2 text-[#495465] font-medium">Servis Terakhir</span>
                            <span class="col-span-3 text-[#181B21]">
                                {{ $kendaraan->tanggal_servis_terakhir ? date('d/m/Y', strtotime($kendaraan->tanggal_servis_terakhir)) : 'Belum pernah' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-1">
                            <span class="col-span-2 text-[#495465] font-medium">Status Servis</span>
                            <span class="col-span-3">
                                <x-ui.badge :variant="$kendaraan->status_servis['variant']">
                                    {{ $kendaraan->status_servis['badge'] }}
                                </x-ui.badge>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kartu 3: Riwayat Servis & Perbaikan --}}
                <div class="border border-[#DEE2E7] rounded-xl overflow-hidden bg-white shadow-sm">
                    <div class="bg-[#120F8B] px-4 py-2.5 flex items-center justify-between text-white">
                        <span class="text-xs font-bold uppercase tracking-wider">Riwayat Servis Kendaraan</span>
                        @if (in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']))
                            <button type="button" @click="servisModalOpen = true"
                                class="px-2.5 py-1 bg-white text-[#120F8B] hover:bg-[#F7F8F9] text-xs font-semibold rounded shadow-sm flex items-center gap-1 transition-colors cursor-pointer">
                                <span>+</span> Catat Servis
                            </button>
                        @endif
                    </div>
                    <div class="p-3">
                        @if ($kendaraan->servis && $kendaraan->servis->count() > 0)
                            <div>
                                <div class="overflow-x-auto max-h-72 overflow-y-auto">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-[#F7F8F9] text-[#495465] text-[11px] border-b border-[#DEE2E7]">
                                            <tr>
                                                <th class="px-2.5 py-1.5 font-semibold">Tgl</th>
                                                <th class="px-2.5 py-1.5 font-semibold">Tindakan / Bengkel</th>
                                                <th class="px-2.5 py-1.5 font-semibold text-right">Biaya (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-[11px]">
                                            @foreach ($kendaraan->servis as $s)
                                                <tr class="hover:bg-[#F7F8F9]/50">
                                                    <td class="px-2.5 py-2 text-[#495465] whitespace-nowrap align-top">
                                                        {{ $s->tanggal_servis ? $s->tanggal_servis->format('d/m/Y') : '-' }}
                                                    </td>
                                                    <td class="px-2.5 py-2">
                                                        <div class="font-semibold text-[#181B21]">{{ $s->jenis_servis ?: 'Servis' }}</div>
                                                        @if ($s->odometer_km)
                                                            <div class="text-[11px] text-[#495465] mt-0.5">{{ number_format($s->odometer_km, 0, ',', '.') }} KM</div>
                                                        @endif
                                                        @if ($s->bengkel_pelaksana || $s->catatan)
                                                            <div class="text-[10px] text-[#9EA9B9] mt-0.5">
                                                                {{ $s->bengkel_pelaksana ?: $s->catatan }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-2.5 py-2 text-right font-semibold text-[#181B21] whitespace-nowrap align-top">
                                                        {{ number_format($s->biaya ?: 0, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-[#DEE2E7] flex justify-between items-center text-xs font-bold text-[#181B21]">
                                    <span class="text-[#495465]">Total Biaya Servis:</span>
                                    <span class="text-sm text-[#120F8B]">Rp {{ number_format($kendaraan->servis->sum('biaya'), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="py-8 text-center text-[#9EA9B9] text-xs">
                                Belum ada riwayat servis tercatat untuk kendaraan ini.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Modal Catat Servis Cepat --}}
        <x-ui.modal open="servisModalOpen" maxWidth="md" zIndex="z-[60]">
            <h2 class="text-lg font-bold mb-1 text-[#181B21]">Catat Servis Kendaraan</h2>
            <p class="text-xs text-[#495465] mb-4">{{ $kendaraan->nama_kendaraan }} ({{ $kendaraan->plat_nomor }})</p>

            <form method="POST" action="{{ route('transportasi.servis', $kendaraan->kendaraan_id) }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <x-ui.input type="date" label="Tanggal Servis" name="tanggal_servis" value="{{ date('Y-m-d') }}" required />
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
                            <x-ui.input type="number" label="Kilometer (Odometer)" name="odometer_km" placeholder="Contoh: 45000" min="0" />
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
                            placeholder="Detail perbaikan / suku cadang diganti..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <x-ui.button type="button" variant="outline" @click="servisModalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>

        {{-- Modal Edit Kendaraan --}}
        <x-ui.modal open="editModalOpen" maxWidth="2xl" zIndex="z-[60]">
            <h2 class="text-lg font-bold mb-4 text-[#181B21]">Edit Kendaraan</h2>

            <form method="POST" enctype="multipart/form-data" action="{{ route('transportasi.update', $kendaraan->kendaraan_id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    {{-- Section 1: Identitas Utama --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#120F8B] border-b border-[#DEE2E7] pb-1 mb-3">
                            1. Identitas Kendaraan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <x-ui.input label="Nama Kendaraan" name="nama_kendaraan"
                                     value="{{ $kendaraan->nama_kendaraan }}" required />
                            </div>

                            <div>
                                <x-ui.input label="Nomor Polisi (Plat)" name="plat_nomor"
                                    value="{{ $kendaraan->plat_nomor }}" required />
                            </div>

                            <div>
                                <x-ui.select label="Jenis Angkutan" name="jenis_kendaraan" required>
                                    <option value="Mobil" @selected($kendaraan->jenis_kendaraan === 'Mobil')>Mobil</option>
                                    <option value="Bus/Minibus" @selected($kendaraan->jenis_kendaraan === 'Bus/Minibus')>Minibus / Bus</option>
                                    <option value="Motor" @selected($kendaraan->jenis_kendaraan === 'Motor')>Motor</option>
                                    <option value="Lainnya" @selected($kendaraan->jenis_kendaraan === 'Lainnya')>Lainnya</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.input label="Kode Barang" name="kode_barang"
                                    value="{{ $kendaraan->kode_barang }}" />
                            </div>

                            <div>
                                <x-ui.input label="NUP" name="nup"
                                    value="{{ $kendaraan->nup }}" />
                            </div>

                            <div>
                                <x-ui.input label="Model Kendaraan" name="model_kendaraan"
                                    value="{{ $kendaraan->model_kendaraan }}" placeholder="Contoh: MPV, Sedan, Bebek" />
                            </div>

                            <div>
                                <x-ui.input label="Merek / Tipe / Spesifikasi" name="merek_tipe"
                                    value="{{ $kendaraan->merek_tipe }}" />
                            </div>

                            <div>
                                <x-ui.input label="No. Rangka" name="no_rangka"
                                    value="{{ $kendaraan->no_rangka }}" />
                            </div>

                            <div>
                                <x-ui.input label="No. Mesin" name="no_mesin"
                                    value="{{ $kendaraan->no_mesin }}" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-[#495465] mb-1">Unggah / Ganti Foto Kendaraan</label>
                                <input type="file" name="foto" accept="image/*"
                                    class="w-full text-xs text-[#181B21] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#120F8B] file:text-white hover:file:bg-[#0D0B63] border border-[#DEE2E7] rounded-lg cursor-pointer bg-white p-1" />
                                <span class="text-[11px] text-[#9EA9B9] mt-0.5 block">Format: JPG, PNG, WEBP (Maksimal 3MB). Kosongkan jika tidak ingin mengubah foto.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Nilai & Perolehan --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#120F8B] border-b border-[#DEE2E7] pb-1 mb-3">
                            2. Nilai & Perolehan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <x-ui.input type="number" label="Tahun Perolehan" name="tahun_perolehan"
                                    value="{{ $kendaraan->tahun_perolehan }}" />
                            </div>

                            <div>
                                <x-ui.input label="Sumber Dana" name="sumber_dana"
                                    value="{{ $kendaraan->sumber_dana ?: 'DM' }}" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Harga Perolehan (Rp)" name="harga_perolehan"
                                    value="{{ $kendaraan->harga_perolehan }}" min="0" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Masa Manfaat (Tahun)" name="masa_manfaat"
                                    value="{{ $kendaraan->masa_manfaat ?: 7 }}" min="1" max="50" />
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
                                    <option value="Baik" @selected($kendaraan->kondisi === 'Baik')>Baik</option>
                                    <option value="Rusak Ringan" @selected($kendaraan->kondisi === 'Rusak Ringan')>Rusak Ringan</option>
                                    <option value="Rusak Berat" @selected($kendaraan->kondisi === 'Rusak Berat')>Rusak Berat</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.select label="Status Operasional" name="status" required>
                                    <option value="Tersedia" @selected($kendaraan->status === 'Tersedia')>Tersedia</option>
                                    <option value="Digunakan" @selected($kendaraan->status === 'Digunakan')>Digunakan</option>
                                    <option value="Dalam Perawatan" @selected($kendaraan->status === 'Dalam Perawatan')>Dalam Perawatan</option>
                                </x-ui.select>
                            </div>

                            <div>
                                <x-ui.input label="Penanggung Jawab / Driver" name="penanggung_jawab"
                                    value="{{ $kendaraan->penanggung_jawab }}" />
                            </div>

                            <div>
                                <x-ui.input type="number" label="Interval Servis (Bulan)" name="interval_servis_bulan"
                                    value="{{ $kendaraan->interval_servis_bulan ?: 6 }}" min="1" max="36" required />
                            </div>

                            <div>
                                <x-ui.input type="date" label="Tanggal Servis Terakhir" name="tanggal_servis_terakhir"
                                    value="{{ $kendaraan->tanggal_servis_terakhir ? date('Y-m-d', strtotime($kendaraan->tanggal_servis_terakhir)) : '' }}" />
                            </div>

                            <div>
                                <x-ui.input type="date" label="Pajak STNK Tahunan" name="tanggal_pajak_stnk"
                                    value="{{ $kendaraan->tanggal_pajak_stnk ? date('Y-m-d', strtotime($kendaraan->tanggal_pajak_stnk)) : '' }}" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-[#495465] mb-1">Keterangan Tambahan</label>
                                <textarea name="keterangan" rows="2"
                                    class="w-full text-sm rounded-lg border border-[#DEE2E7] px-3 py-2 text-[#181B21] focus:outline-none focus:border-[#120F8B]">{{ $kendaraan->keterangan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-[#DEE2E7]">
                    <x-ui.button type="button" variant="outline" @click="editModalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>
