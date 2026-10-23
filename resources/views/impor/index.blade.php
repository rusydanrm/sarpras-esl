<x-layouts.app title="Smart Impor Data Barang">
    <div x-data="{
        mode: 'file',
        fileName: '',
        fileSize: '',
        isDragging: false,
        pastedText: '',
        get rowCount() {
            if (!this.pastedText.trim()) return 0;
            return this.pastedText.trim().split('\n').filter(line => line.trim().length > 0).length;
        },
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
            } else {
                this.fileName = '';
                this.fileSize = '';
            }
        },
        handleDrop(e) {
            this.isDragging = false;
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
            }
        }
    }">

        <!-- Page Header ala Spark Admin -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                    </svg>
                </span>
                <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Smart Impor Excel</h1>
            </div>
            <p class="text-xs text-[#6C7E75] font-medium">Migrasi data inventaris cepat melalui berkas spreadsheet resmi atau tempel langsung dari lembar kerja Excel (format DIR/DBR)</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                </div>
                <div class="text-xs text-rose-800 space-y-1">
                    <div class="font-bold text-sm">Gagal Melakukan Impor:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="text-xs font-semibold text-emerald-900">{{ session('success') }}</div>
            </div>
        @endif



        <!-- Mode Selector Segmented Tabs (Spark Admin Style) -->
        <div class="bg-white border border-[#E9EFEF] p-1.5 rounded-2xl inline-flex gap-1.5 shadow-2xs mb-6">
            <button type="button" @click="mode = 'file'"
                :class="mode === 'file' ? 'bg-[#0A0E2E] text-white shadow-xs font-bold' : 'text-[#6C7E75] hover:text-[#0A0E2E] font-medium'"
                class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer">
                <span :class="mode === 'file' ? 'text-[#ffa916]' : 'text-[#6C7E75]'">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                </span>
                <span>Unggah Berkas Spreadsheet (.xlsx)</span>
            </button>
            <button type="button" @click="mode = 'tempel'"
                :class="mode === 'tempel' ? 'bg-[#0A0E2E] text-white shadow-xs font-bold' : 'text-[#6C7E75] hover:text-[#0A0E2E] font-medium'"
                class="px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 transition-all cursor-pointer">
                <span :class="mode === 'tempel' ? 'text-[#ffa916]' : 'text-[#6C7E75]'">
                    <x-heroicon-o-clipboard-document-list class="w-4 h-4" />
                </span>
                <span>Tempel Langsung dari Clipboard Excel</span>
            </button>
        </div>

        <!-- MODE 1: UNGGAH BERKAS EXCEL -->
        <div x-show="mode === 'file'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white border border-[#E9EFEF] rounded-2xl p-6 md:p-8 shadow-2xs">
                <div class="max-w-2xl mx-auto">
                    <!-- Informative Banner -->
                    <div class="flex items-center justify-between p-4 bg-[#F4F6F5] border border-[#E9EFEF] rounded-xl mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#1D6F42]/10 text-[#1D6F42] flex items-center justify-center font-bold text-sm">
                                XLS
                            </div>
                            <div>
                                <div class="text-xs font-bold text-[#0A0E2E]">Gunakan Berkas Excel Sesuai Format</div>
                                <div class="text-[11px] text-[#6C7E75]">Pastikan susunan kolom atau gunakan templat resmi yang telah disediakan</div>
                            </div>
                        </div>
                        <a href="{{ route('impor.template') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#E9EFEF] text-[11px] font-bold text-[#1D6F42] hover:bg-[#1D6F42]/5 transition-colors">
                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            <span>Unduh Templat</span>
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form method="POST" action="{{ route('impor.preview') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Drag and Drop Dropzone -->
                        <div class="relative border-2 border-dashed rounded-2xl p-8 sm:p-12 text-center transition-all"
                            :class="isDragging ? 'border-[#ffa916] bg-[#ffa916]/5 scale-[0.99]' : 'border-[#DEE2E7] hover:border-[#120F8B] bg-[#F7F8F9]/50'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)">
                            
                            <input type="file" name="file" accept=".xlsx,.xls" required
                                x-ref="fileInput"
                                @change="handleFileSelect($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <!-- Visual State: Not Selected -->
                            <div x-show="!fileName" class="space-y-3 pointer-events-none">
                                <div class="w-14 h-14 rounded-2xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center mx-auto shadow-sm">
                                    <x-heroicon-o-cloud-arrow-up class="w-7 h-7" />
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-[#0A0E2E]">Seret & Jatuhkan berkas Excel di sini</div>
                                    <div class="text-xs text-[#6C7E75] mt-1">atau <span class="text-[#120F8B] font-semibold underline cursor-pointer">Pilih berkas dari komputer</span></div>
                                </div>
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-white border border-[#E9EFEF] text-[10px] text-[#6C7E75] font-semibold">
                                    <span>Format: .XLSX, .XLS</span>
                                    <span>•</span>
                                    <span>Maksimal: 5 MB</span>
                                </div>
                            </div>

                            <!-- Visual State: File Selected -->
                            <div x-show="fileName" x-cloak class="space-y-3 pointer-events-none">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mx-auto shadow-sm">
                                    <x-heroicon-o-document-check class="w-7 h-7" />
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-[#0A0E2E]" x-text="fileName"></div>
                                    <div class="text-xs text-[#6C7E75] mt-0.5" x-text="fileSize"></div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                    <x-heroicon-o-check class="w-3.5 h-3.5" /> Berkas Siap Diunggah
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-sm shadow-[#0A0E2E]/15 cursor-pointer">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-[#ffa916]" />
                                <span>Unggah & Buka Pratinjau</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODE 2: TEMPEL DARI CLIPBOARD EXCEL -->
        <div x-show="mode === 'tempel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white border border-[#E9EFEF] rounded-2xl p-6 md:p-8 shadow-2xs">
                
                <!-- Step by step visual instructions -->
                <div class="bg-[#F4F6F5] border border-[#E9EFEF] rounded-2xl p-5 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-5 h-5 rounded-full bg-[#0A0E2E] text-[#ffa916] text-[10px] font-extrabold flex items-center justify-center">i</span>
                        <div class="text-xs font-bold text-[#0A0E2E]">Panduan Tempel Data Langsung (Format DIR / DBR):</div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="bg-white p-3 rounded-xl border border-[#E9EFEF]">
                            <div class="text-[10px] font-bold text-[#ffa916] uppercase tracking-wider mb-1">Langkah 1</div>
                            <div class="text-xs font-semibold text-[#0A0E2E]">Pilih Ruangan Tujuan</div>
                            <div class="text-[10px] text-[#6C7E75] mt-0.5">Tentukan ruangan tempat barang ditempatkan</div>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-[#E9EFEF]">
                            <div class="text-[10px] font-bold text-[#ffa916] uppercase tracking-wider mb-1">Langkah 2</div>
                            <div class="text-xs font-semibold text-[#0A0E2E]">Blok di Excel & Copy</div>
                            <div class="text-[10px] text-[#6C7E75] mt-0.5">Blok baris isi data (tanpa header), tekan <kbd class="px-1 bg-slate-100 rounded text-[9px] font-mono">Ctrl+C</kbd></div>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-[#E9EFEF]">
                            <div class="text-[10px] font-bold text-[#ffa916] uppercase tracking-wider mb-1">Langkah 3</div>
                            <div class="text-xs font-semibold text-[#0A0E2E]">Tempel (*Paste*) di Sini</div>
                            <div class="text-[10px] text-[#6C7E75] mt-0.5">Klik area teks di bawah lalu tekan <kbd class="px-1 bg-slate-100 rounded text-[9px] font-mono">Ctrl+V</kbd></div>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-[#E9EFEF]">
                            <div class="text-[10px] font-bold text-[#ffa916] uppercase tracking-wider mb-1">Langkah 4</div>
                            <div class="text-xs font-semibold text-[#0A0E2E]">Periksa Pratinjau</div>
                            <div class="text-[10px] text-[#6C7E75] mt-0.5">Sistem memetakan kolom dan kondisi B/RR/RB otomatis</div>
                        </div>
                    </div>

                    <!-- Column Legend Tag Pills -->
                    <div class="mt-4 pt-3 border-t border-[#E9EFEF]/80 flex flex-wrap items-center gap-1.5 text-[10px] text-[#6C7E75]">
                        <span class="font-bold text-[#0A0E2E]">Urutan Kolom DIR/DBR:</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Kode Barang</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Nama Barang</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">NUP</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Merek/Tipe</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Tahun</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Harga Satuan</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Jumlah</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Satuan</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Sumber Dana</span>
                        <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded border border-emerald-200">B</span>
                        <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 rounded border border-amber-200">RR</span>
                        <span class="px-1.5 py-0.5 bg-rose-50 text-rose-700 rounded border border-rose-200">RB</span>
                        <span class="px-1.5 py-0.5 bg-white rounded border border-[#E9EFEF]">Ket.</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('impor.tempel') }}">
                    @csrf
                    
                    <!-- Ruangan Selector -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                            Ruangan Tujuan Penempatan Barang <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative max-w-xl">
                            <select name="ruangan_id" required
                                class="w-full bg-[#F7F8F9] border border-[#DEE2E7] rounded-xl px-4 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#ffa916] focus:bg-white transition-colors cursor-pointer">
                                <option value="">-- Pilih Ruangan Inventaris --</option>
                                @foreach ($ruanganList as $r)
                                    <option value="{{ $r->ruangan_id }}">
                                        {{ $r->nama_ruang }} ({{ $r->gedung->nama_gedung ?? '-' }}) — Lantai {{ $r->lantai ?? '1' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-[11px] text-[#6C7E75] mt-1">Seluruh baris data yang Anda tempel di bawah akan didaftarkan ke ruangan yang dipilih.</p>
                    </div>

                    <!-- Textarea Clipboard -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-[#0A0E2E]">
                                Tempelkan Seluruh Baris Data Tabel di Sini <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-bold text-[#6C7E75] bg-[#F4F6F5] px-2.5 py-0.5 rounded-full border border-[#E9EFEF]">
                                <span x-text="rowCount" class="text-[#0A0E2E] font-extrabold"></span> baris data terdeteksi
                            </span>
                        </div>
                        <div class="relative">
                            <textarea name="data_tempel" rows="10" required
                                x-model="pastedText"
                                class="w-full bg-[#F7F8F9] border border-[#DEE2E7] rounded-2xl px-4 py-3 text-xs font-mono text-[#0A0E2E] placeholder-slate-400 focus:outline-none focus:border-[#ffa916] focus:bg-white transition-colors leading-relaxed shadow-inner"
                                placeholder="Klik di sini, lalu tekan Ctrl + V untuk menempelkan data yang telah Anda copy dari lembar kerja Excel..."></textarea>
                        </div>
                        <div class="flex items-center justify-between mt-1 text-[11px] text-[#6C7E75]">
                            <span>Kolom 'Jumlah Harga' dan 'Penguasaan' akan diabaikan otomatis oleh parser cerdas.</span>
                            <button type="button" @click="pastedText = ''" x-show="pastedText" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                Bersihkan Kolom Teks
                            </button>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-sm shadow-[#0A0E2E]/15 cursor-pointer">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-[#ffa916]" />
                            <span>Buka Pratinjau & Validasi Data</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Import Logs Section (Spark Admin Style) -->
        @if (isset($recentLogs) && count($recentLogs) > 0)
            <div class="mt-8 bg-white border border-[#E9EFEF] rounded-2xl p-6 shadow-2xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4 text-[#ffa916]" />
                        <h2 class="text-sm font-bold text-[#0A0E2E]">Riwayat Impor Terakhir</h2>
                    </div>
                    <span class="text-[11px] text-[#6C7E75] font-medium">5 aktivitas impor inventaris terkini</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left min-w-[550px]">
                        <thead class="bg-[#F4F6F5] text-[#6C7E75] uppercase text-[10px] font-bold tracking-wider rounded-xl">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Nama Berkas / Sumber</th>
                                <th class="px-4 py-3">Pengunggah</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3 text-center">Data Sukses</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E9EFEF]">
                            @foreach ($recentLogs as $log)
                                <tr class="hover:bg-[#F7F8F9]/80 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-[#0A0E2E] flex items-center gap-2">
                                        <x-heroicon-o-document-text class="w-4 h-4 text-[#1D6F42] shrink-0" />
                                        <span class="truncate max-w-xs">{{ $log->nama_file }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-[#6C7E75]">
                                        {{ $log->user->nama ?? 'Sistem' }}
                                    </td>
                                    <td class="px-4 py-3 text-[#6C7E75]">
                                        {{ \Carbon\Carbon::parse($log->tanggal_upload)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                            +{{ $log->jumlah_baris_sukses }} item
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($log->status === 'sukses')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sukses
                                            </span>
                                        @elseif ($log->status === 'sebagian')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sebagian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Gagal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>