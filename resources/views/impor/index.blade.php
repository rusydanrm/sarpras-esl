<x-layouts.app title="Impor Data">
    <div x-data="{ mode: 'file' }">
        <h1 class="text-2xl font-bold mb-6">Impor Data Barang</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-[#FEF7F7] text-[#EF4444] rounded-lg text-sm">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="flex gap-1 mb-6 border-b border-[#DEE2E7]">
            <button @click="mode = 'file'"
                :class="mode === 'file' ? 'border-b-2 border-[#120F8B] text-[#120F8B] font-semibold' : 'text-[#495465]'"
                class="px-4 py-2 text-sm">
                Unggah File Excel
            </button>
            <button @click="mode = 'tempel'"
                :class="mode === 'tempel' ? 'border-b-2 border-[#120F8B] text-[#120F8B] font-semibold' : 'text-[#495465]'"
                class="px-4 py-2 text-sm">
                Tempel dari Excel
            </button>
        </div>

        <div x-show="mode === 'file'">
            <a href="{{ route('impor.template') }}" class="text-sm text-[#120F8B] underline">Unduh Templat Excel</a>
            <form method="POST" action="{{ route('impor.preview') }}" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="border-2 border-dashed border-[#DEE2E7] rounded-xl p-10 text-center bg-white">
                    <p class="text-sm text-[#495465] mb-3">Pilih file Excel (.xlsx) berisi data barang <span class="text-[#EF4444] font-bold">*</span></p>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="mx-auto text-sm">
                </div>
                <div class="mt-4">
                    <x-ui.button type="submit">Unggah & Pratinjau</x-ui.button>
                </div>
            </form>
        </div>

        {{-- Mode: Tempel dari Excel (baru) --}}
        <div x-show="mode === 'tempel'">
            <div class="bg-white border border-[#DEE2E7] rounded-xl p-5 mb-4">
                <p class="text-sm font-medium mb-2">Cara pakai:</p>
                <ol class="text-sm text-[#495465] list-decimal list-inside space-y-1">
                    <li>Pilih ruangan tujuan di bawah</li>
                    <li>Buka Excel, blok baris data barang (TANPA baris header), tekan <kbd
                            class="px-1 py-0.5 bg-[#F7F8F9] rounded text-xs">Ctrl+C</kbd></li>
                    <li>Klik kotak teks di bawah, tekan <kbd
                            class="px-1 py-0.5 bg-[#F7F8F9] rounded text-xs">Ctrl+V</kbd></li>
                    <li>Urutan kolom di Excel aslimu (sesuai format DIR/DBR): Kode Barang, Nama Barang, NUP, Merek/Tipe, Tahun Perolehan,
                        Harga Satuan, Jumlah, Satuan, Jumlah Harga, Sumber Dana, B, RR, RB, Penguasaan, Ket. — <strong>boleh diblok dan
                            tempel langsung apa adanya</strong>, sistem otomatis abaikan kolom "Jumlah Harga" dan "Penguasaan", dan baca
                        kondisi dari kolom B/RR/RB.</li>
                </ol>
            </div>

            <form method="POST" action="{{ route('impor.tempel') }}">
                @csrf
                <div class="mb-4">
                    <x-ui.select label="Ruangan Tujuan" name="ruangan_id" required>
                        <option value="">- Pilih Ruangan -</option>
                        @foreach ($ruanganList as $r)
                            <option value="{{ $r->ruangan_id }}">{{ $r->nama_ruang }} ({{ $r->gedung->nama_gedung }})
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div>
                    <label class="text-xs font-medium text-[#495465] block mb-1">
                        Tempel Data di Sini <span class="text-[#EF4444] font-bold">*</span>
                    </label>
                    <textarea name="data_tempel" rows="10" required
                        class="w-full border border-[#DEE2E7] rounded-lg px-3 py-2 text-sm font-mono"
                        placeholder="Klik di sini, lalu Ctrl+V untuk tempel data dari Excel..."></textarea>
                </div>
                <div class="mt-4">
                    <x-ui.button type="submit">Pratinjau Data</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>