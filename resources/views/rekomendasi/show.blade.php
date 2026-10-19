<x-layouts.app>
    <div x-data="{
        catatModalOpen: {{ $errors->hasAny(['jenis_tindakan', 'tanggal_tindakan', 'biaya']) ? 'true' : 'false' }},
        editModalOpen: {{ $errors->hasAny(['nama_barang', 'ruangan_id', 'jumlah', 'harga_satuan']) ? 'true' : 'false' }},
        editData: {{ Illuminate\Support\Js::from($barang) }}
    }">
        {{-- Link Navigasi Kembali --}}
        <div class="mb-4">
            <a href="{{ route('rekomendasi.index') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#6C7E75] hover:text-[#0A0E2E] bg-white border border-[#E9EFEF] px-3.5 py-2 rounded-xl shadow-2xs hover:shadow-xs transition-all">
                <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                <span>Kembali ke Maintenance & Repair (MR)</span>
            </a>
        </div>

        {{-- Flash Messages --}}
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

        {{-- Header Bar ala Spark Admin --}}
        <div class="bg-white border border-[#E9EFEF] rounded-2xl p-5 mb-6 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 stroke-2" />
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A0E2E]">{{ $barang->nama_barang }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-[#6C7E75] font-medium ml-0.5 mt-2">
                        <span class="px-2.5 py-0.5 rounded-lg bg-[#F4F6F5] border border-[#E9EFEF] font-semibold text-[#0A0E2E]">
                            {{ $barang->ruangan?->nama_ruang ?? 'Tanpa Ruangan' }}
                        </span>
                        @if ($barang->ruangan?->gedung)
                            <span>•</span>
                            <span>{{ $barang->ruangan->gedung->nama_gedung }}</span>
                        @endif
                        @if ($barang->kode_barang)
                            <span>•</span>
                            <span class="font-mono bg-[#F4F6F5] px-2 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]">
                                {{ $barang->kode_barang }}
                            </span>
                        @endif
                        @if ($barang->nup)
                            <span>•</span>
                            <span class="text-[#9EA9B9]">NUP: {{ $barang->nup }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    @if (in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']))
                        <button type="button" @click="catatModalOpen = true"
                            class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 stroke-2" />
                            <span>Catat Servis</span>
                        </button>
                    @endif
                    <a href="{{ route('laporan.pdf') }}" target="_blank">
                        <x-ui.button variant="pdf">
                            <x-heroicon-o-document-text class="w-4 h-4" /> Ekspor PDF
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>

        {{-- 2 Kolom Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-sm">
            
            {{-- Kolom Kiri: Informasi Inventaris Barang (7 col) --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white border border-[#E9EFEF] rounded-2xl shadow-[0_2px_12px_rgba(10,14,46,0.03)] overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between bg-[#F8FAF9]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#ffa916]"></span>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E]">Spesifikasi & Identitas Aset</h2>
                        </div>
                        @if (in_array(auth()->user()->role, ['admin', 'dosen_tendik']))
                            <button type="button" @click="editModalOpen = true"
                                class="px-3 py-1.5 bg-white text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] text-xs font-semibold rounded-xl shadow-2xs flex items-center gap-1 transition-all cursor-pointer">
                                <x-heroicon-o-pencil class="w-3.5 h-3.5" />
                                <span>Edit Data</span>
                            </button>
                        @endif
                    </div>

                    <div class="p-4 sm:p-6 text-xs divide-y divide-[#E9EFEF]/60">
                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Nama Barang</span>
                            <span class="col-span-3 font-bold text-[#0A0E2E] text-sm">{{ $barang->nama_barang }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Kode Barang</span>
                            <span class="col-span-3 font-mono font-semibold text-[#0A0E2E]">{{ $barang->kode_barang ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">NUP (Nomor Urut Pendaftaran)</span>
                            <span class="col-span-3 font-mono font-semibold text-[#0A0E2E]">{{ $barang->nup ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Kategori / Jenis</span>
                            <span class="col-span-3 font-semibold text-[#0A0E2E]">
                                {{ $barang->jenis?->nama_jenis ?: '-' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Merek / Tipe / Spesifikasi</span>
                            <span class="col-span-3 text-[#0A0E2E] font-medium">{{ $barang->merek_tipe ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Tahun Perolehan & Usia</span>
                            <div class="col-span-3 text-[#0A0E2E] flex items-center gap-2">
                                <span class="font-bold">{{ $barang->tahun_perolehan ?: '-' }}</span>
                                @if ($barang->usia_teks)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200/60">
                                        {{ $barang->usia_teks }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Sumber Dana</span>
                            <span class="col-span-3 text-[#0A0E2E] font-medium">{{ $barang->sumber_dana ?: '-' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Jumlah Unit & Satuan</span>
                            <span class="col-span-3 font-bold text-[#0A0E2E]">{{ $barang->jumlah }} {{ $barang->satuan ?: 'Unit' }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Harga Satuan</span>
                            <span class="col-span-3 font-semibold text-[#0A0E2E]">Rp {{ number_format($barang->harga_satuan, 0, ',', '.') }}</span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Total Nilai Perolehan</span>
                            <span class="col-span-3 font-extrabold text-[#0A0E2E] text-sm">
                                Rp {{ number_format($barang->total_harga ?: ($barang->harga_satuan * $barang->jumlah), 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Rincian Kondisi Fisik</span>
                            <div class="col-span-3 flex flex-wrap gap-1.5">
                                @if ($barang->kondisi_baik > 0)
                                    <span class="badge-table success">{{ $barang->kondisi_baik }} Baik</span>
                                @endif
                                @if ($barang->kondisi_rusak_ringan > 0)
                                    <span class="badge-table warning">{{ $barang->kondisi_rusak_ringan }} Rusak Ringan</span>
                                @endif
                                @if ($barang->kondisi_rusak_berat > 0)
                                    <span class="badge-table danger">{{ $barang->kondisi_rusak_berat }} Rusak Berat</span>
                                @endif
                                @if ($barang->kondisi_baik == 0 && $barang->kondisi_rusak_ringan == 0 && $barang->kondisi_rusak_berat == 0)
                                    <span class="text-[#9EA9B9]">-</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Ruangan Penempatan</span>
                            <span class="col-span-3">
                                @if ($barang->ruangan)
                                    <a href="{{ route('ruangan.show', $barang->ruangan) }}" class="text-[#0A0E2E] hover:text-[#120F8B] hover:underline font-bold">
                                        {{ $barang->ruangan->nama_ruang }}{{ $barang->ruangan->gedung ? ' (' . $barang->ruangan->gedung->nama_gedung . ')' : '' }}
                                    </a>
                                @else
                                    <span class="text-[#9EA9B9] italic">Belum ditentukan</span>
                                @endif
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Koordinator Ruangan</span>
                            <span class="col-span-3 text-[#0A0E2E] font-medium">
                                {{ $barang->ruangan?->koordinator?->pluck('nama')->join(', ') ?: '-' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-2 py-2.5">
                            <span class="col-span-2 text-[#6C7E75] font-semibold">Keterangan Tambahan</span>
                            <span class="col-span-3 text-[#495465] leading-relaxed">{{ $barang->keterangan ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Jadwal Pemeliharaan & Riwayat Servis (5 col) --}}
            <div class="lg:col-span-5 space-y-6">
                
                {{-- Kartu 1: Jadwal Pemeliharaan Rutin --}}
                <div class="bg-white border border-[#E9EFEF] rounded-2xl shadow-[0_2px_12px_rgba(10,14,46,0.03)] overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between bg-[#F8FAF9]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E]">Jadwal Pemeliharaan Rutin</h2>
                        </div>
                        <x-heroicon-o-calendar-days class="w-4 h-4 text-[#6C7E75]" />
                    </div>

                    <div class="p-4 sm:p-6 text-xs space-y-3.5">
                        <div class="flex items-center justify-between py-1 border-b border-[#E9EFEF]/60">
                            <span class="text-[#6C7E75] font-medium">Interval Rutin</span>
                            <span class="font-bold text-[#0A0E2E]">
                                {{ $barang->interval_pemeliharaan_bulan ? "Setiap {$barang->interval_pemeliharaan_bulan} Bulan" : 'Tidak perlu rutin' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-[#E9EFEF]/60">
                            <span class="text-[#6C7E75] font-medium">Servis Terakhir</span>
                            <span class="font-bold text-[#0A0E2E]">
                                {{ $barang->tanggal_pemeliharaan_terakhir ? $barang->tanggal_pemeliharaan_terakhir->format('d/m/Y') : 'Belum pernah' }}
                            </span>
                        </div>

                        <div class="py-1 border-b border-[#E9EFEF]/60">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[#6C7E75] font-medium">Status Jadwal</span>
                                @php $srv = $barang->status_servis; @endphp
                                @if ($srv['variant'] === 'danger')
                                    <span class="badge-table danger">{{ $srv['badge'] }}</span>
                                @elseif ($srv['variant'] === 'warning')
                                    <span class="badge-table warning">{{ $srv['badge'] }}</span>
                                @elseif ($srv['variant'] === 'success')
                                    <span class="badge-table success">{{ $srv['badge'] }}</span>
                                @else
                                    <span class="text-[#9EA9B9]">{{ $srv['badge'] }}</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-[#6C7E75]">{{ $srv['keterangan'] }}</div>
                        </div>

                        <div class="flex items-center justify-between py-1">
                            <span class="text-[#6C7E75] font-medium">Kondisi Fisik</span>
                            <span>
                                @if ($barang->kondisi_rusak_berat > 0)
                                    <span class="badge-table danger">Perlu Tindakan RB</span>
                                @elseif ($barang->kondisi_rusak_ringan > 0)
                                    <span class="badge-table warning">Perlu Servis RR</span>
                                @else
                                    <span class="badge-table success">Kondisi Baik & Siap Pakai</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kartu 2: Riwayat Servis & Perbaikan --}}
                <div class="bg-white border border-[#E9EFEF] rounded-2xl shadow-[0_2px_12px_rgba(10,14,46,0.03)] overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E9EFEF] flex items-center justify-between bg-[#F8FAF9]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#120F8B]"></span>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E]">Riwayat Perbaikan & Servis</h2>
                        </div>
                        @if (in_array(auth()->user()->role, ['admin', 'teknisi', 'dosen_tendik']))
                            <button type="button" @click="catatModalOpen = true"
                                class="px-3 py-1.5 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center gap-1 cursor-pointer">
                                <x-heroicon-o-plus class="w-3.5 h-3.5 stroke-2" />
                                <span>Catat Servis</span>
                            </button>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5">
                        @if ($barang->pemeliharaan && $barang->pemeliharaan->count() > 0)
                            <div class="overflow-x-auto max-h-72 overflow-y-auto">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-[#F8FAF9] text-[#6C7E75] text-[11px] uppercase tracking-wider border-b border-[#E9EFEF]">
                                        <tr>
                                            <th class="px-3 py-2 font-bold">Tanggal</th>
                                            <th class="px-3 py-2 font-bold">Tindakan & Kerusakan</th>
                                            <th class="px-3 py-2 font-bold text-right">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E9EFEF]/60 text-xs">
                                        @foreach ($barang->pemeliharaan as $p)
                                            <tr class="hover:bg-[#F4F6F5]/50 transition-colors">
                                                <td class="px-3 py-2.5 text-[#6C7E75] whitespace-nowrap align-top font-medium">
                                                    {{ $p->tanggal_tindakan ? $p->tanggal_tindakan->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-3 py-2.5">
                                                    <div class="font-bold text-[#0A0E2E]">{{ $p->jenis_tindakan ?: 'Pemeliharaan' }}</div>
                                                    @if ($p->kerusakan)
                                                        <div class="text-[11px] text-[#495465] mt-0.5">{{ $p->kerusakan }}</div>
                                                    @endif
                                                    @if ($p->keterangan)
                                                        <div class="text-[10px] text-[#9EA9B9] mt-0.5 italic">{{ $p->keterangan }}</div>
                                                    @endif
                                                    @if ($p->teknisi || $p->vendor)
                                                        <div class="text-[10px] text-[#120F8B] font-semibold mt-1">
                                                            Pelaksana: {{ $p->teknisi?->nama ?: ($p->vendor?->nama_vendor ?: '-') }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-bold text-[#0A0E2E] whitespace-nowrap align-top">
                                                    Rp {{ number_format($p->biaya ?: 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 pt-3 border-t border-[#E9EFEF] flex justify-between items-center text-xs font-bold text-[#0A0E2E]">
                                <span class="text-[#6C7E75]">Total Pengeluaran Servis:</span>
                                <span class="text-sm font-extrabold text-[#0A0E2E]">Rp {{ number_format($barang->pemeliharaan->sum('biaya'), 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="py-8 text-center text-[#9EA9B9] text-xs">
                                <x-heroicon-o-wrench-screwdriver class="w-8 h-8 text-[#9EA9B9] mx-auto mb-2 opacity-50" />
                                Belum ada riwayat perbaikan atau servis yang tercatat untuk barang ini.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Modal Catat Pemeliharaan / Servis Cepat --}}
        <x-ui.modal open="catatModalOpen" maxWidth="lg" zIndex="z-[60]">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Catat Pemeliharaan / Servis</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">{{ $barang->nama_barang }} ({{ $barang->ruangan?->nama_ruang ?? 'Tanpa Ruangan' }})</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pemeliharaan.store') }}">
                @csrf
                <input type="hidden" name="barang_id" value="{{ $barang->barang_id }}">

                <div class="space-y-4">
                    <x-ui.select label="Jenis Tindakan" name="jenis_tindakan" required>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                        <option value="Penggantian">Penggantian</option>
                    </x-ui.select>

                    <x-ui.input label="Uraian Masalah / Kerusakan" name="kerusakan" placeholder="Contoh: Modul power rusak, ganti filter AC, kabel putus" />

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
                        <x-ui.input label="Biaya Pemeliharaan (Rp)" type="number" name="biaya" min="0" placeholder="0" />
                        <x-ui.input label="Tanggal Tindakan" type="date" name="tanggal_tindakan" value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan / Hasil Pemeriksaan</label>
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

        {{-- Modal Edit Barang --}}
        <x-ui.modal open="editModalOpen" maxWidth="2xl" zIndex="z-[60]">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-pencil class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight">Edit Data Barang</h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Perbarui spesifikasi, nilai perolehan, atau lokasi ruangan</p>
                </div>
            </div>

            <form method="POST" action="{{ route('barang.update', $barang->barang_id) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="sm:col-span-2">
                            <x-ui.input label="Nama Barang" name="nama_barang" value="{{ $barang->nama_barang }}" required />
                        </div>
                        <div>
                            <x-ui.input label="Kode Barang" name="kode_barang" value="{{ $barang->kode_barang }}" />
                        </div>
                        <div>
                            <x-ui.input label="NUP" name="nup" value="{{ $barang->nup }}" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.select label="Ruangan Penempatan" name="ruangan_id" required>
                                <option value="">- Pilih Ruangan -</option>
                                @foreach ($ruanganList as $r)
                                    <option value="{{ $r->ruangan_id }}" @selected($barang->ruangan_id == $r->ruangan_id)>{{ $r->nama_ruang }} ({{ $r->gedung?->nama_gedung }})</option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <div>
                            <x-ui.select label="Kategori / Jenis" name="jenis_id">
                                <option value="">- Otomatis Deteksi -</option>
                                @foreach ($jenisList as $j)
                                    <option value="{{ $j->jenis_id }}" @selected($barang->jenis_id == $j->jenis_id)>{{ $j->nama_jenis }}</option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <div>
                            <x-ui.input label="Merek / Tipe" name="merek_tipe" value="{{ $barang->merek_tipe }}" />
                        </div>
                        <div>
                            <x-ui.input label="Tahun Perolehan" type="number" name="tahun_perolehan" value="{{ $barang->tahun_perolehan }}" />
                        </div>
                        <div>
                            <x-ui.input label="Harga Satuan (Rp)" type="number" name="harga_satuan" value="{{ $barang->harga_satuan }}" />
                        </div>
                        <div>
                            <x-ui.input label="Sumber Dana" name="sumber_dana" value="{{ $barang->sumber_dana }}" placeholder="Contoh: DM, BOPTN" />
                        </div>
                        <div>
                            <x-ui.input label="Jumlah Unit" type="number" name="jumlah" value="{{ $barang->jumlah }}" min="1" required />
                        </div>
                        <div>
                            <x-ui.input label="Satuan" name="satuan" value="{{ $barang->satuan }}" placeholder="Unit / Buah / Set" required />
                        </div>
                        <div>
                            <x-ui.input label="Kondisi Baik" type="number" name="kondisi_baik" value="{{ $barang->kondisi_baik }}" min="0" />
                        </div>
                        <div>
                            <x-ui.input label="Kondisi Rusak Ringan" type="number" name="kondisi_rusak_ringan" value="{{ $barang->kondisi_rusak_ringan }}" min="0" />
                        </div>
                        <div>
                            <x-ui.input label="Kondisi Rusak Berat" type="number" name="kondisi_rusak_berat" value="{{ $barang->kondisi_rusak_berat }}" min="0" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-ui.input label="Interval Pemeliharaan Rutin (Bulan)" type="number" name="interval_pemeliharaan_bulan"
                                value="{{ $barang->interval_pemeliharaan_bulan }}" min="1" max="60"
                                placeholder="Contoh: 3 atau 6 (kosongkan jika tidak perlu jadwal rutin)" />
                            <p class="text-[11px] text-[#9EA9B9] mt-1">Rekomendasi: AC (3 bln), Proyektor/PC/Printer (6 bln). Kosongkan untuk barang non-elektronik.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-[#495465] block mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2"
                                class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-medium">{{ $barang->keterangan }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="editModalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>
