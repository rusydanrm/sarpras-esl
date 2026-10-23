<x-layouts.app title="Pratinjau Hasil Impor Data">
    <div class="space-y-6">

        <!-- Page Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Pratinjau & Validasi Data Impor</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Periksa hasil pembacaan berkas sebelum data disimpan secara permanen ke dalam basis data inventaris</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('impor.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] shadow-2xs transition-all cursor-pointer">
                    <x-heroicon-o-arrow-left class="w-4 h-4 text-[#6C7E75]" />
                    <span>Kembali / Unggah Ulang</span>
                </a>
            </div>
        </div>

        <!-- 3 Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-[#E9EFEF] rounded-2xl p-4 shadow-2xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-semibold text-[#6C7E75] uppercase tracking-wider">Total Baris Diproses</div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] mt-1">{{ count($valid) + count($gagal) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#0A0E2E]/5 text-[#0A0E2E] flex items-center justify-center font-bold">
                    <x-heroicon-o-queue-list class="w-5 h-5 text-[#ffa916]" />
                </div>
            </div>

            <div class="bg-emerald-50/50 border border-emerald-200/80 rounded-2xl p-4 shadow-2xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Baris Valid & Siap Simpan</div>
                    <div class="text-2xl font-extrabold text-emerald-700 mt-1">{{ count($valid) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
            </div>

            <div class="bg-rose-50/50 border border-rose-200/80 rounded-2xl p-4 shadow-2xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Baris Ditolak / Gagal</div>
                    <div class="text-2xl font-extrabold text-rose-700 mt-1">{{ count($gagal) }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    <x-heroicon-o-x-circle class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- DAFTAR BARIS GAGAL (JIKA ADA) -->
        @if (count($gagal))
            <div class="bg-white border border-rose-200 rounded-2xl p-6 shadow-2xs">
                <div class="flex items-center gap-2 mb-4 text-rose-700">
                    <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0" />
                    <div>
                        <h2 class="text-sm font-bold text-rose-900">Baris Tidak Memenuhi Syarat ({{ count($gagal) }} Data Ditolak)</h2>
                        <p class="text-[11px] text-rose-600 mt-0.5">Baris-baris ini akan otomatis dilewati dan tidak akan dimasukkan ke dalam database.</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-rose-100">
                    <table class="w-full text-xs text-left min-w-[650px]">
                        <thead class="bg-rose-50/70 text-rose-800 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-16">Baris</th>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3">Penyebab Kegagalan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100">
                            @foreach ($gagal as $g)
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="px-4 py-3 text-center font-bold text-rose-700 bg-rose-50/40">
                                        #{{ $g['baris'] }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-[#0A0E2E]">
                                        {{ $g['nama_barang'] ?: '(Nama barang kosong)' }}
                                    </td>
                                    <td class="px-4 py-3 text-rose-600 font-medium">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            {{ $g['alasan'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- DAFTAR BARIS VALID -->
        <div class="bg-white border border-[#E9EFEF] rounded-2xl p-6 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-check-badge class="w-5 h-5 text-emerald-600" />
                    <h2 class="text-sm font-bold text-[#0A0E2E]">Data Valid Terverifikasi ({{ count($valid) }} Item Siap Masuk Inventaris)</h2>
                </div>
                <div class="text-[11px] text-[#6C7E75]">
                    Menampilkan seluruh baris yang berhasil diparsing
                </div>
            </div>

            @if (count($valid))
                <div class="overflow-x-auto rounded-xl border border-[#E9EFEF]">
                    <table class="w-full text-xs text-left min-w-[750px]">
                        <thead class="bg-[#F4F6F5] text-[#6C7E75] uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3">Merek / Tipe</th>
                                <th class="px-4 py-3">Ruangan</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3">Kondisi Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E9EFEF]">
                            @foreach ($valid as $v)
                                <tr class="hover:bg-[#F7F8F9]/80 transition-colors">
                                    <td class="px-4 py-3 text-center font-medium text-[#6C7E75] text-[11px]">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-[#0A0E2E]">{{ $v['nama_barang'] }}</div>
                                        @if (!empty($v['kode_barang']) || !empty($v['nup']))
                                            <div class="text-[10px] text-[#6C7E75] mt-0.5">
                                                {{ $v['kode_barang'] ?: '-' }} • NUP: {{ $v['nup'] ?: '-' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-[#0A0E2E]">
                                        {{ $v['merek_tipe'] ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#0A0E2E]/5 text-[#0A0E2E] font-semibold text-[11px] border border-[#E9EFEF]">
                                            <x-heroicon-o-building-office-2 class="w-3 h-3 text-[#ffa916]" />
                                            {{ $v['kode_ruang'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-[#0A0E2E]">
                                        {{ $v['jumlah'] }} <span class="font-normal text-[10px] text-[#6C7E75]">{{ $v['satuan'] ?: 'Unit' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5 text-[10px]">
                                            @if (($v['kondisi_baik'] ?? 0) > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                                    {{ $v['kondisi_baik'] }} Baik
                                                </span>
                                            @endif
                                            @if (($v['kondisi_rusak_ringan'] ?? 0) > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold border border-amber-200">
                                                    {{ $v['kondisi_rusak_ringan'] }} Rusak Ringan
                                                </span>
                                            @endif
                                            @if (($v['kondisi_rusak_berat'] ?? 0) > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                                    {{ $v['kondisi_rusak_berat'] }} Rusak Berat
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-rose-600 bg-rose-50/50 rounded-xl border border-rose-100">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8 mx-auto mb-2 text-rose-500" />
                    <div class="font-bold text-sm">Tidak ada baris valid yang dapat disimpan.</div>
                    <div class="text-xs text-rose-600 mt-1">Silakan sesuaikan format berkas atau baris teks Anda, lalu unggah kembali.</div>
                </div>
            @endif
        </div>

        <!-- Sticky / Clear Action Bar -->
        <div class="bg-white border border-[#E9EFEF] rounded-2xl p-4.5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="text-xs text-[#6C7E75] flex items-center gap-2">
                <x-heroicon-o-information-circle class="w-4 h-4 text-[#ffa916]" />
                <span>Tekan <strong>Konfirmasi & Simpan</strong> untuk mendaftarkan {{ count($valid) }} data ke basis data aset sarpras.</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('impor.index') }}"
                    class="px-4 py-2.5 rounded-xl border border-[#E9EFEF] text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors cursor-pointer text-center">
                    Batalkan & Kembali
                </a>

                @if (count($valid))
                    <form method="POST" action="{{ route('impor.simpan') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#0A0E2E] text-white hover:bg-[#120F8B] text-xs font-bold transition-all shadow-sm shadow-[#0A0E2E]/15 cursor-pointer">
                            <x-heroicon-o-check class="w-4 h-4 text-[#ffa916]" />
                            <span>Konfirmasi & Simpan ({{ count($valid) }} Data)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>