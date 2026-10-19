<x-layouts.app>
    <div x-data="{ modalOpen: {{ $errors->any() ? 'true' : 'false' }} }">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
            <h1 class="text-2xl font-bold">Pemeliharaan</h1>
            @if (in_array(auth()->user()->role, ['admin', 'teknisi']))
                <x-ui.button @click="modalOpen = true">+ Catat Pemeliharaan</x-ui.button>
            @endif
        </div>

        @if (session('success'))
            <x-ui.badge variant="success" class="block mb-4 !rounded-lg px-3 py-2">{{ session('success') }}</x-ui.badge>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-[#FEF7F7] text-[#EF4444] rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
                    <tr>
                        <th class="text-center px-4 py-3 w-12">No</th>
                        <th class="text-center px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Barang</th>
                        <th class="text-left px-4 py-3">Ruangan</th>
                        <th class="text-left px-4 py-3">Jenis Tindakan</th>
                        <th class="text-left px-4 py-3">Kerusakan</th>
                        <th class="text-left px-4 py-3">Petugas/Vendor</th>
                        <th class="text-right px-4 py-3">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemeliharaan as $p)
                        <tr class="border-t border-[#DEE2E7]">
                            <td class="px-4 py-3 text-center text-[#495465] text-xs font-medium">
                                {{ $loop->iteration + ($pemeliharaan->currentPage() - 1) * $pemeliharaan->perPage() }}</td>
                            <td class="px-4 py-3 text-center">{{ $p->tanggal_tindakan->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $p->barang->nama_barang }}</td>
                            <td class="px-4 py-3">{{ $p->barang?->ruangan?->nama_ruang ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->jenis_tindakan }}</td>
                            <td class="px-4 py-3">{{ $p->kerusakan ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->teknisi->nama ?? $p->vendor->nama_vendor ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                {{ $p->biaya ? 'Rp ' . number_format($p->biaya, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-[#9EA9B9]">Belum ada riwayat pemeliharaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pemeliharaan->links() }}</div>

        <x-ui.modal open="modalOpen" maxWidth="lg">
            <h2 class="text-lg font-bold mb-4">Catat Pemeliharaan</h2>
            <form method="POST" action="{{ route('pemeliharaan.store') }}">
                @csrf
                <div class="space-y-3">
                    @if ($pengajuanSelesai->count())
                        <div>
                            <label class="text-xs font-medium text-[#495465] block mb-1">Dari Pengajuan (opsional)</label>
                            <select id="pilih_pengajuan" class="w-full border border-[#DEE2E7] rounded-lg px-3 py-2 text-sm"
                                onchange="isiDariPengajuan(this)">
                                <option value="">- Catat manual -</option>
                                @foreach ($pengajuanSelesai as $pg)
                                    <option value="{{ $pg->pengajuan_id }}" data-barang="{{ $pg->barang_id }}"
                                        data-jenis="{{ $pg->jenis_pengajuan === 'Pemeliharaan' ? 'Pemeliharaan Rutin' : $pg->jenis_pengajuan }}">
                                        #{{ $pg->pengajuan_id }} - {{ $pg->barang->nama_barang ?? '-' }}
                                        ({{ $pg->jenis_pengajuan }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="pengajuan_id" id="pengajuan_id_input">
                        </div>
                    @endif

                    <x-ui.select label="Barang" name="barang_id" id="barang_id_select" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach ($barangList as $b)
                            <option value="{{ $b->barang_id }}">{{ $b->nama_barang }} ({{ $b->ruangan?->nama_ruang ?? '-' }})
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select label="Jenis Tindakan" name="jenis_tindakan" id="jenis_tindakan_select" required>
                        <option value="Perbaikan">Perbaikan</option>
                        <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                        <option value="Penggantian">Penggantian</option>
                    </x-ui.select>

                    <x-ui.input label="Kerusakan" name="kerusakan" placeholder="Contoh: motor pemanas" />

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.select label="Teknisi" name="teknisi_id">
                            <option value="">- Pilih -</option>
                            @foreach ($teknisiList as $t)
                                <option value="{{ $t->user_id }}">{{ $t->nama }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.select label="Vendor Eksternal" name="vendor_id">
                            <option value="">- Tidak ada -</option>
                            @foreach ($vendorList as $v)
                                <option value="{{ $v->vendor_id }}">{{ $v->nama_vendor }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input label="Biaya (Rp)" type="number" name="biaya" min="0" placeholder="0" />
                        <x-ui.input label="Tanggal Tindakan" type="date" name="tanggal_tindakan"
                            value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div>
                        <label class="text-xs font-medium text-[#495465] block mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2"
                            class="w-full border border-[#DEE2E7] rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    </div>

    <script>
        function isiDariPengajuan(el) {
            const opt = el.options[el.selectedIndex];
            document.getElementById('pengajuan_id_input').value = el.value;
            if (el.value) {
                document.getElementById('barang_id_select').value = opt.dataset.barang;
                document.getElementById('jenis_tindakan_select').value = opt.dataset.jenis;
            }
        }
    </script>
</x-layouts.app>