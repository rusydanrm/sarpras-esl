<x-layouts.app>
    <div x-data="{
        servisModalOpen: {{ $errors->hasAny(['tanggal_servis', 'jenis_servis', 'biaya']) ? 'true' : 'false' }},
        editModalOpen: {{ $errors->hasAny(['nama_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'interval_servis_bulan']) ? 'true' : 'false' }},
        editData: {{ Illuminate\Support\Js::from($kendaraan) }}
    }">
        <!-- Breadcrumb Kembali ala Spark -->
        <div class="mb-4">
            <a href="{{ route('transportasi.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-[#E9EFEF] text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors shadow-2xs">
                <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                <span>Kembali ke Transportasi</span>
            </a>
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

        <!-- Hero Card Detail Kendaraan ala Spark Admin -->
        <div class="bg-white border border-[#E9EFEF] rounded-2xl p-5 sm:p-6 mb-6 shadow-2xs">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-[#6C7E75] mb-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#ffa916]"></span>
                        <span>Departemen Ekonomi Sumberdaya dan Lingkungan (ESL)</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">{{ $kendaraan->nama_kendaraan }}</h1>
                        <span class="px-2.5 py-1 bg-[#0A0E2E] text-[#ffa916] font-mono text-xs font-bold rounded-lg shadow-2xs">
                            {{ $kendaraan->plat_nomor }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                        <span class="px-2.5 py-1 rounded-lg bg-[#F4F6F5] border border-[#E9EFEF] font-semibold text-[#0A0E2E]">
                            {{ $kendaraan->jenis_kendaraan }}
                        </span>

                        @if ($kendaraan->merek_tipe)
                            <span class="text-[#6C7E75]">•</span>
                            <span class="text-[#0A0E2E] font-medium">{{ $kendaraan->merek_tipe }}</span>
                        @endif

                        <span class="text-[#6C7E75]">•</span>
                        <!-- Status Operasional Badge -->
                        @if ($kendaraan->status === 'Tersedia')
                            <span class="badge-table success">
                                <span class="badge-table-dot"></span>
                                <span>Tersedia</span>
                            </span>
                        @elseif ($kendaraan->status === 'Digunakan')
                            <span class="badge-table warning">
                                <span class="badge-table-dot"></span>
                                <span>Digunakan</span>
                            </span>
                        @else
                            <span class="badge-table danger">
                                <span class="badge-table-dot"></span>
                                <span>Dalam Perawatan</span>
                            </span>
                        @endif

                        <!-- Kondisi Badge -->
                        <span class="text-[#6C7E75]">•</span>
                        @if ($kendaraan->kondisi === 'Baik')
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Kondisi: Baik
                            </span>
                        @elseif ($kendaraan->kondisi === 'Rusak Ringan')
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Kondisi: Rusak Ringan
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Kondisi: Rusak Berat
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Button Group -->
                <div class="flex flex-wrap items-center gap-2.5">
                    @if (in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']))
                        <button type="button" @click="servisModalOpen = true"
                            class="px-4 py-2 bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(255,169,22,0.25)] flex items-center gap-1.5 transition-all cursor-pointer">
                            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 stroke-2" />
                            <span>Catat Servis</span>
                        </button>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <button type="button" @click="editModalOpen = true"
                            class="px-3.5 py-2 bg-white hover:bg-[#F4F6F5] text-[#0A0E2E] border border-[#E9EFEF] text-xs font-semibold rounded-xl shadow-2xs flex items-center gap-1.5 transition-colors cursor-pointer">
                            <x-heroicon-o-pencil class="w-4 h-4 text-[#6C7E75]" />
                            <span>Edit Kendaraan</span>
                        </button>
                    @endif

                    <a href="{{ route('laporan.pdf') }}" target="_blank"
                        class="px-3.5 py-2 bg-[#C8102E] hover:bg-[#A50D26] text-white text-xs font-semibold rounded-xl shadow-2xs flex items-center gap-1.5 transition-colors">
                        <x-heroicon-o-document-text class="w-4 h-4" />
                        <span>Ekspor PDF</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2 Kolom Grid Layout ala Spark Admin -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-sm">

            <!-- Kolom Kiri: Informasi Teknis & Valuasi (7 col) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Spesifikasi & Identitas Teknis -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-information-circle class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Spesifikasi & Identitas Teknis</h3>
                        </div>
                        @if (auth()->user()->role === 'admin')
                            <button type="button" @click="editModalOpen = true"
                                class="text-xs font-semibold text-[#ffa916] hover:underline flex items-center gap-1 cursor-pointer">
                                <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                        @endif
                    </div>
                    <div class="p-4 sm:p-5 text-xs divide-y divide-[#E9EFEF]">
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Nama Kendaraan</span>
                            <span class="col-span-3 font-bold text-[#0A0E2E] text-sm">{{ $kendaraan->nama_kendaraan }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Nomor Polisi (Plat)</span>
                            <span class="col-span-3 font-mono font-bold text-[#0A0E2E] text-sm">{{ $kendaraan->plat_nomor }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Jenis Kendaraan</span>
                            <span class="col-span-3 font-medium text-[#0A0E2E]">{{ $kendaraan->jenis_kendaraan }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Model Kendaraan</span>
                            <span class="col-span-3 text-[#0A0E2E]">{{ $kendaraan->model_kendaraan ?: '-' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Merek / Tipe / Spesifikasi</span>
                            <span class="col-span-3 text-[#0A0E2E]">{{ $kendaraan->merek_tipe ?: '-' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Kode Barang & NUP</span>
                            <span class="col-span-3 font-mono text-[#0A0E2E]">{{ $kendaraan->kode_barang ?: '-' }} / {{ $kendaraan->nup ?: '-' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Nomor Rangka Mesin</span>
                            <span class="col-span-3 font-mono text-[#0A0E2E]">{{ $kendaraan->no_rangka ?: '-' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Nomor Blok Mesin</span>
                            <span class="col-span-3 font-mono text-[#0A0E2E]">{{ $kendaraan->no_mesin ?: '-' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Penanggung Jawab / Driver</span>
                            <span class="col-span-3 font-bold text-[#0A0E2E]">{{ $kendaraan->penanggung_jawab ?: 'Belum ditentukan' }}</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Catatan Keterangan</span>
                            <span class="col-span-3 text-[#6C7E75]">{{ $kendaraan->keterangan ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Valuasi & Perolehan Aset -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Data Valuasi & Perolehan Aset</h3>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-4">
                            <div class="bg-[#F8FAF9] p-3 rounded-xl border border-[#E9EFEF]">
                                <div class="text-[11px] text-[#6C7E75] font-medium">Tahun Perolehan</div>
                                <div class="text-base font-extrabold text-[#0A0E2E] mt-0.5">{{ $kendaraan->tahun_perolehan ?: '-' }}</div>
                                @if ($kendaraan->usia_teks)
                                    <div class="text-[10px] text-rose-600 font-bold mt-0.5">Umur: {{ $kendaraan->usia_teks }}</div>
                                @endif
                            </div>
                            <div class="bg-[#F8FAF9] p-3 rounded-xl border border-[#E9EFEF]">
                                <div class="text-[11px] text-[#6C7E75] font-medium">Sumber Dana</div>
                                <div class="text-base font-extrabold text-[#0A0E2E] mt-0.5">{{ $kendaraan->sumber_dana ?: '-' }}</div>
                                <div class="text-[10px] text-[#6C7E75] mt-0.5">Masa: {{ $kendaraan->masa_manfaat ?: 7 }} Thn</div>
                            </div>
                            <div class="bg-[#F8FAF9] p-3 rounded-xl border border-[#E9EFEF]">
                                <div class="text-[11px] text-[#6C7E75] font-medium">Harga Perolehan</div>
                                <div class="text-sm font-extrabold text-[#0A0E2E] mt-0.5">
                                    Rp {{ number_format($kendaraan->harga_perolehan ?: 0, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-[#6C7E75] mt-0.5">Nilai awal</div>
                            </div>
                            <div class="bg-[#F8FAF9] p-3 rounded-xl border border-[#E9EFEF]">
                                <div class="text-[11px] text-[#6C7E75] font-medium">Nilai Buku Saat Ini</div>
                                <div class="text-sm font-extrabold text-emerald-600 mt-0.5">
                                    Rp {{ number_format($kendaraan->nilai_buku ?: 0, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-[#6C7E75] mt-0.5">Pasca penyusutan</div>
                            </div>
                        </div>

                        <div class="text-xs text-[#6C7E75] bg-[#F4F6F5] p-3 rounded-xl border border-[#E9EFEF] flex items-center justify-between">
                            <span>Akumulasi Penyusutan Aset:</span>
                            <span class="font-bold text-[#0A0E2E]">Rp {{ number_format($kendaraan->akumulasi_penyusutan ?: 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pajak STNK Tahunan -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl p-5 shadow-2xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-identification class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Status Pajak STNK Tahunan</h3>
                        </div>
                        @if ($kendaraan->status_pajak)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                @if ($kendaraan->status_pajak['variant'] === 'danger') bg-rose-50 text-rose-700 border border-rose-200
                                @elseif ($kendaraan->status_pajak['variant'] === 'warning') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-emerald-50 text-emerald-700 border border-emerald-200 @endif">
                                {{ $kendaraan->status_pajak['badge'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between p-3.5 bg-[#F8FAF9] rounded-xl border border-[#E9EFEF] text-xs">
                        <div>
                            <span class="text-[#6C7E75]">Jatuh Tempo Pajak:</span>
                            <span class="font-bold text-[#0A0E2E] ml-1">
                                {{ $kendaraan->tanggal_pajak_stnk ? date('d/m/Y', strtotime($kendaraan->tanggal_pajak_stnk)) : 'Belum diatur' }}
                            </span>
                        </div>
                        @if ($kendaraan->stnk_countdown)
                            <div class="font-bold text-xs {{ str_contains($kendaraan->stnk_countdown, 'Lewat') ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $kendaraan->stnk_countdown }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Foto Fisik, Servis & Riwayat (5 col) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card Foto Kendaraan -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-camera class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Foto Fisik Kendaraan</h3>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col items-center justify-center bg-[#F8FAF9]/50 min-h-[220px]">
                        @if ($kendaraan->foto_url)
                            <img src="{{ asset($kendaraan->foto_url) }}" alt="{{ $kendaraan->nama_kendaraan }}"
                                class="max-h-60 w-auto rounded-xl object-contain border border-[#E9EFEF] shadow-xs" />
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-[#6C7E75]">
                                <x-heroicon-o-truck class="w-14 h-14 mb-2 opacity-30 text-[#0A0E2E]" />
                                <span class="text-xs font-semibold text-[#0A0E2E]">Belum ada foto fisik</span>
                                @if (auth()->user()->role === 'admin')
                                    <button type="button" @click="editModalOpen = true" class="text-xs text-[#ffa916] hover:underline font-bold mt-1.5 cursor-pointer">
                                        + Unggah Foto Kendaraan
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Jadwal Servis Rutin -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-calendar-days class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Jadwal Servis Berkala</h3>
                        </div>
                        @if ($kendaraan->status_servis['status'] === 'Aman')
                            <span class="badge-table success">
                                <span class="badge-table-dot"></span>
                                <span>{{ $kendaraan->status_servis['badge'] }}</span>
                            </span>
                        @elseif ($kendaraan->status_servis['status'] === 'Mendekati Servis')
                            <span class="badge-table warning">
                                <span class="badge-table-dot"></span>
                                <span>{{ $kendaraan->status_servis['badge'] }}</span>
                            </span>
                        @elseif ($kendaraan->status_servis['status'] === 'Jatuh Tempo')
                            <span class="badge-table danger">
                                <span class="badge-table-dot"></span>
                                <span>{{ $kendaraan->status_servis['badge'] }}</span>
                            </span>
                        @else
                            <span class="badge-table info">
                                <span class="badge-table-dot"></span>
                                <span>{{ $kendaraan->status_servis['badge'] }}</span>
                            </span>
                        @endif
                    </div>
                    <div class="p-4 sm:p-5 text-xs divide-y divide-[#E9EFEF]">
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Interval Servis</span>
                            <span class="col-span-3 font-bold text-[#0A0E2E]">Setiap {{ $kendaraan->interval_servis_bulan ?: 6 }} Bulan</span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Servis Terakhir</span>
                            <span class="col-span-3 text-[#0A0E2E]">
                                {{ $kendaraan->tanggal_servis_terakhir ? date('d/m/Y', strtotime($kendaraan->tanggal_servis_terakhir)) : 'Belum pernah' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 py-2">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Status Servis</span>
                            <span class="col-span-3 text-[#6C7E75]">
                                {{ $kendaraan->status_servis['keterangan'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Riwayat Servis & Perbaikan Bengkel -->
                <div class="bg-white border border-[#E9EFEF] rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                                <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-[#0A0E2E]">Riwayat Servis Bengkel</h3>
                        </div>
                        @if (in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']))
                            <button type="button" @click="servisModalOpen = true"
                                class="px-2.5 py-1 bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] text-xs font-bold rounded-lg shadow-2xs flex items-center gap-1 transition-colors cursor-pointer">
                                <span>+</span> Catat Servis
                            </button>
                        @endif
                    </div>
                    <div class="p-4">
                        @if ($kendaraan->servis && $kendaraan->servis->count() > 0)
                            <div class="overflow-x-auto max-h-72 overflow-y-auto">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-[#F8FAF9] text-[#6C7E75] text-[11px] font-bold uppercase tracking-wider border-b border-[#E9EFEF]">
                                        <tr>
                                            <th class="px-3 py-2">Tgl</th>
                                            <th class="px-3 py-2">Tindakan / Bengkel</th>
                                            <th class="px-3 py-2 text-right">Biaya (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E9EFEF] text-[11px]">
                                        @foreach ($kendaraan->servis as $s)
                                            <tr class="hover:bg-[#F4F6F5]/50 transition-colors">
                                                <td class="px-3 py-2.5 text-[#6C7E75] whitespace-nowrap align-top font-medium">
                                                    {{ $s->tanggal_servis ? $s->tanggal_servis->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-3 py-2.5">
                                                    <div class="font-bold text-[#0A0E2E]">{{ $s->jenis_servis ?: 'Servis' }}</div>
                                                    @if ($s->odometer_km)
                                                        <div class="text-[10px] text-[#6C7E75] mt-0.5">{{ number_format($s->odometer_km, 0, ',', '.') }} KM</div>
                                                    @endif
                                                    @if ($s->bengkel_pelaksana || $s->catatan)
                                                        <div class="text-[10px] text-[#6C7E75] mt-0.5">
                                                            {{ $s->bengkel_pelaksana ?: $s->catatan }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-bold text-[#0A0E2E] whitespace-nowrap align-top">
                                                    {{ number_format($s->biaya ?: 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 pt-3 border-t border-[#E9EFEF] flex justify-between items-center text-xs font-bold text-[#0A0E2E]">
                                <span class="text-[#6C7E75]">Total Akumulasi Biaya:</span>
                                <span class="text-sm text-[#0A0E2E] font-extrabold">Rp {{ number_format($kendaraan->servis->sum('biaya'), 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="py-10 text-center text-[#6C7E75] text-xs">
                                <div class="w-10 h-10 rounded-xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-2">
                                    <x-heroicon-o-wrench class="w-5 h-5 opacity-40" />
                                </div>
                                <p class="font-semibold text-[#0A0E2E]">Belum ada riwayat servis tercatat</p>
                                <p class="text-[11px] text-[#6C7E75] mt-0.5">Gunakan tombol "+ Catat Servis" di atas untuk mencatat servis bengkel.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Catat Servis ala Spark Admin -->
        <x-ui.modal open="servisModalOpen" maxWidth="md" zIndex="z-[60]">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]">Catat Servis Kendaraan</h2>
                        <p class="text-xs text-[#6C7E75]">{{ $kendaraan->nama_kendaraan }} ({{ $kendaraan->plat_nomor }})</p>
                    </div>
                </div>
                <button type="button" @click="servisModalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST" action="{{ route('transportasi.servis', $kendaraan->kendaraan_id) }}">
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
                        <x-ui.input label="Bengkel Pelaksana" name="bengkel_pelaksana" placeholder="Nama bengkel" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#495465] mb-1">Catatan Pekerjaan / Suku Cadang</label>
                        <textarea name="catatan" rows="2"
                            class="w-full text-xs rounded-xl border border-[#E9EFEF] px-3.5 py-2.5 text-[#0A0E2E] focus:outline-hidden focus:border-[#ffa916] bg-white placeholder-[#6C7E75]"
                            placeholder="Rincian perbaikan atau suku cadang diganti..."></textarea>
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

        <!-- Modal Edit Kendaraan ala Spark Admin -->
        <x-ui.modal open="editModalOpen" maxWidth="2xl" zIndex="z-[60]">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                        <x-heroicon-o-pencil class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]">Edit Data Kendaraan Dinas</h2>
                        <p class="text-xs text-[#6C7E75]">Perbarui identitas, perolehan, atau jadwal operasional</p>
                    </div>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('transportasi.update', $kendaraan->kendaraan_id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                    <!-- Section 1: Identitas Kendaraan -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">1</span>
                            <span>Identitas Kendaraan</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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
                                    class="w-full text-xs text-[#0A0E2E] file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#0A0E2E] file:text-[#ffa916] hover:file:bg-[#120F8B] border border-[#E9EFEF] rounded-xl cursor-pointer bg-white p-1" />
                                <span class="text-[11px] text-[#6C7E75] mt-1 block">Format: JPG, PNG, WEBP (Maksimal 3MB). Kosongkan jika tidak ingin mengubah foto.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Nilai & Perolehan -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">2</span>
                            <span>Nilai & Data Perolehan</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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

                    <!-- Section 3: Operasional & Servis -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] mb-3 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">3</span>
                            <span>Operasional & Servis Berkala</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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
                                    class="w-full text-xs rounded-xl border border-[#E9EFEF] px-3.5 py-2.5 text-[#0A0E2E] focus:outline-hidden focus:border-[#ffa916] bg-white placeholder-[#6C7E75]">{{ $kendaraan->keterangan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-5 pt-3.5 border-t border-[#E9EFEF]">
                    <button type="button" @click="editModalOpen = false"
                        class="px-4 py-2 bg-white border border-[#E9EFEF] hover:bg-[#F4F6F5] text-[#495465] font-semibold text-xs rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>
