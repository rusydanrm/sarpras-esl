<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        formBaik: 1,
        formRR: 0,
        formRB: 0
    }">

        <!-- Navigation Back Link -->
        <div class="mb-4">
            <a href="{{ route('ruangan.index') }}" 
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#6C7E75] hover:text-[#0A0E2E] bg-white border border-[#E9EFEF] px-3.5 py-2 rounded-xl shadow-2xs hover:shadow-xs transition-all">
                <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                <span>Kembali ke Daftar Ruangan</span>
            </a>
        </div>

        <!-- Room Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <x-heroicon-o-home-modern class="w-4 h-4 stroke-2" />
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0A0E2E]">{{ $ruangan->nama_ruang }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs text-[#6C7E75] font-medium ml-0.5">
                    <span class="px-2.5 py-0.5 rounded-lg bg-white border border-[#E9EFEF] font-semibold text-[#0A0E2E]">
                        {{ $ruangan->gedung->nama_gedung }}
                    </span>
                    <span>•</span>
                    <span>Lantai {{ $ruangan->lantai }}</span>
                    @if ($ruangan->wing)
                        <span>•</span>
                        <span>Wing {{ $ruangan->wing }}</span>
                    @endif
                    @if ($ruangan->kode_ruang)
                        <span>•</span>
                        <span class="font-mono bg-[#F4F6F5] px-2 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]">
                            {{ $ruangan->kode_ruang }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" 
                    @click="modalOpen = true; editData = null; formBaik = 1; formRR = 0; formRB = 0"
                    class="px-4 py-2.5 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                    <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                    <span>Tambah Barang</span>
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

        <!-- Bento Grid Metric Cards ala Spark Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Luas & Dimensi -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Luas & Dimensi</span>
                    <span class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-arrows-pointing-out class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        {{ number_format($ruangan->luas_m2, 2) }} <span class="text-sm font-semibold text-[#6C7E75]">m²</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Panjang {{ $ruangan->panjang_m }} m × Lebar {{ $ruangan->lebar_m }} m
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Barang -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Total Inventaris</span>
                    <span class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        {{ $ruangan->barang->count() }} <span class="text-sm font-semibold text-[#6C7E75]">Jenis</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] mt-1 font-medium">
                        Total Fisik: <strong class="text-[#0A0E2E]">{{ $ruangan->barang->sum('jumlah') }}</strong> Unit
                    </div>
                </div>
            </div>

            <!-- Card 3: Kondisi Barang -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Status Kondisi</span>
                    <span class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-check-badge class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-1.5 mb-2">
                        <span class="badge-table success">{{ $ruangan->barang->sum('kondisi_baik') }} Baik</span>
                        <span class="badge-table warning">{{ $ruangan->barang->sum('kondisi_rusak_ringan') }} RR</span>
                        <span class="badge-table danger">{{ $ruangan->barang->sum('kondisi_rusak_berat') }} RB</span>
                    </div>
                    <div class="text-[11px] text-[#6C7E75]">
                        Kondisi terkini seluruh unit barang
                    </div>
                </div>
            </div>

            <!-- Card 4: Koordinator Ruangan -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Koordinator Ruangan</span>
                    <span class="w-8 h-8 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-[#0A0E2E]">
                        <x-heroicon-o-user-group class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    @if ($ruangan->koordinator->isNotEmpty())
                        <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto">
                            @foreach ($ruangan->koordinator as $k)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#F4F6F5] border border-[#E9EFEF] text-xs font-semibold text-[#0A0E2E]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#ffa916]"></span>
                                    <span>{{ $k->nama }}</span>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-[#9EA9B9] italic">Belum ada koordinator ditetapkan</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section Title -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-[#0A0E2E] tracking-tight">Daftar Barang di Ruangan Ini</h2>
                <p class="text-xs text-[#6C7E75]">Seluruh inventaris sarana dan prasarana yang ditempatkan pada {{ $ruangan->nama_ruang }}</p>
            </div>
        </div>

        <!-- Tabel Barang di Ruangan -->
        <div class="table-card-spark">
            <div class="overflow-x-auto">
                <table class="table-spark-custom">
                    <thead>
                        <tr>
                            <th class="text-center w-12">No</th>
                            <th class="text-left">Nama Barang & Spesifikasi</th>
                            <th class="text-left">Kode & NUP</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-left">Kondisi Unit</th>
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ruangan->barang as $b)
                            @php
                                $initial = strtoupper(mb_substr($b->nama_barang, 0, 1));
                            @endphp
                            <tr>
                                <td class="text-center text-[#6C7E75] text-xs font-semibold">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#0A0E2E]/5 border border-[#E9EFEF] flex items-center justify-center text-xs font-bold text-[#0A0E2E] shrink-0">
                                            {{ $initial }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('rekomendasi.show', $b->barang_id) }}"
                                                class="font-bold text-[#0A0E2E] hover:text-[#ffa916] transition-colors text-sm truncate block"
                                                title="{{ $b->nama_barang }}">
                                                {{ $b->nama_barang }}
                                            </a>
                                            @if ($b->merek_tipe)
                                                <div class="text-xs text-[#6C7E75] truncate max-w-[180px]">
                                                    {{ $b->merek_tipe }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-0.5">
                                        <span class="font-mono text-[11px] bg-[#F4F6F5] px-1.5 py-0.5 rounded text-[#495465] font-semibold border border-[#E9EFEF]/60 w-fit">
                                            {{ $b->kode_barang ?? '-' }}
                                        </span>
                                        @if ($b->nup)
                                            <span class="text-[10px] text-[#9EA9B9]">NUP: {{ $b->nup }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-[#F4F6F5] border border-[#E9EFEF] text-xs font-bold text-[#0A0E2E]">
                                        {{ $b->jumlah }} {{ $b->satuan }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if ($b->kondisi_baik > 0)
                                            <span class="badge-table success">{{ $b->kondisi_baik }} Baik</span>
                                        @endif
                                        @if ($b->kondisi_rusak_ringan > 0)
                                            <span class="badge-table warning">{{ $b->kondisi_rusak_ringan }} RR</span>
                                        @endif
                                        @if ($b->kondisi_rusak_berat > 0)
                                            <span class="badge-table danger">{{ $b->kondisi_rusak_berat }} RB</span>
                                        @endif
                                        @if ($b->kondisi_baik == 0 && $b->kondisi_rusak_ringan == 0 && $b->kondisi_rusak_berat == 0)
                                            <span class="text-xs text-[#9EA9B9]">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="font-bold text-xs text-[#0A0E2E]">
                                        Rp {{ number_format($b->total_harga, 0, ',', '.') }}
                                    </div>
                                    @if ($b->harga_satuan > 0 && $b->jumlah > 1)
                                        <div class="text-[10px] text-[#9EA9B9]">
                                            @ Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Detail Button -->
                                        <a href="{{ route('rekomendasi.show', $b->barang_id) }}"
                                            class="table-btn-action"
                                            title="Detail Barang">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </a>

                                        <!-- Edit Button -->
                                        <button type="button"
                                            class="table-btn-action"
                                            title="Edit Barang"
                                            @click='modalOpen = true; editData = {{ Illuminate\Support\Js::from($b) }}'>
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                        </button>

                                        <!-- Delete Button -->
                                        <x-ui.confirm-delete-form action="{{ route('barang.destroy', $b) }}"
                                            title="Hapus Barang"
                                            message="Barang '{{ $b->nama_barang }}' akan dihapus permanen dari ruangan ini."
                                            buttonClass="table-btn-action delete">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </x-ui.confirm-delete-form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-[#F4F6F5] border border-[#E9EFEF] flex items-center justify-center mx-auto text-[#6C7E75] mb-3">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6" />
                                    </div>
                                    <p class="text-sm font-semibold text-[#0A0E2E]">Belum ada barang di ruangan ini</p>
                                    <p class="text-xs text-[#6C7E75] mt-1">Klik tombol "+ Tambah Barang" di atas untuk mendaftarkan aset baru ke ruangan ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Tambah / Edit Barang di Ruangan ala Spark Admin --}}
        <x-ui.modal open="modalOpen" maxWidth="lg">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#E9EFEF]">
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E] flex items-center justify-center text-[#ffa916] shrink-0 shadow-xs">
                    <x-heroicon-o-cube class="w-5 h-5 stroke-2" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[#0A0E2E] leading-tight" x-text="editData ? 'Edit Data Barang' : 'Tambah Barang ke Ruangan'"></h2>
                    <p class="text-xs text-[#6C7E75] mt-0.5">Penempatan inventaris pada ruangan <strong>{{ $ruangan->nama_ruang }}</strong></p>
                </div>
            </div>

            <form method="POST"
                :action="editData ? `/barang/${editData.barang_id}` : `/ruangan/{{ $ruangan->ruangan_id }}/barang`">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <x-ui.input label="Nama Barang" name="nama_barang" x-bind:value="editData?.nama_barang" required placeholder="Nama lengkap inventaris" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Kode Barang" name="kode_barang" x-bind:value="editData?.kode_barang" placeholder="Contoh: 3.05.01.04.001" />
                        <x-ui.input label="NUP" name="nup" x-bind:value="editData?.nup" placeholder="Nomor Urut Pendaftaran" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Merek / Tipe" name="merek_tipe" x-bind:value="editData?.merek_tipe" placeholder="Contoh: Acer / Daikin 1PK" />
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
                                Total: <span x-text="(parseInt(editData ? editData.kondisi_baik : formBaik)||0) + (parseInt(editData ? editData.kondisi_rusak_ringan : formRR)||0) + (parseInt(editData ? editData.kondisi_rusak_berat : formRB)||0)"></span> Unit
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-[11px] font-semibold text-emerald-700 block mb-1">Baik</label>
                                <input type="number" name="kondisi_baik" min="0"
                                    x-bind:value="editData ? editData.kondisi_baik : formBaik"
                                    x-on:input="if (editData) editData.kondisi_baik = $event.target.value; else formBaik = $event.target.value"
                                    required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-amber-700 block mb-1">Rusak Ringan</label>
                                <input type="number" name="kondisi_rusak_ringan" min="0"
                                    x-bind:value="editData ? editData.kondisi_rusak_ringan : formRR"
                                    x-on:input="if (editData) editData.kondisi_rusak_ringan = $event.target.value; else formRR = $event.target.value"
                                    required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-rose-700 block mb-1">Rusak Berat</label>
                                <input type="number" name="kondisi_rusak_berat" min="0"
                                    x-bind:value="editData ? editData.kondisi_rusak_berat : formRB"
                                    x-on:input="if (editData) editData.kondisi_rusak_berat = $event.target.value; else formRB = $event.target.value"
                                    required
                                    class="w-full px-3 py-2 text-xs bg-white border border-[#E9EFEF] rounded-xl text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 font-semibold" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Satuan" name="satuan" x-bind:value="editData?.satuan" placeholder="unit / buah / set" />
                        <x-ui.input label="Harga Satuan (Rp)" type="number" name="harga_satuan"
                            x-bind:value="editData?.harga_satuan ?? 0" required />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-ui.input label="Sumber Dana" name="sumber_dana" x-bind:value="editData?.sumber_dana" placeholder="BOPTN / RKAT / Hibah" />
                        <x-ui.input label="Jadwal Servis (bulan)" type="number" name="interval_pemeliharaan_bulan"
                            x-bind:value="editData?.interval_pemeliharaan_bulan" placeholder="Contoh AC = 3" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-[#495465] block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="2"
                            class="w-full border border-[#E9EFEF] rounded-xl px-3.5 py-2.5 text-xs text-[#0A0E2E] focus:outline-none focus:border-[#0A0E2E] focus:ring-2 focus:ring-[#ffa916]/30 transition-all font-medium"
                            x-text="editData?.keterangan"
                            placeholder="Catatan khusus atau spesifikasi barang..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5 mt-6 pt-4 border-t border-[#E9EFEF]">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <button type="submit"
                        class="px-5 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Barang
                    </button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>