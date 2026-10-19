<x-layouts.public title="Detail Ruangan">
    <a href="{{ route('portal.index') }}" class="text-xs text-[#495465] hover:underline">← Kembali ke Daftar Ruangan</a>
    <p class="text-xs text-[#9EA9B9] mt-2 mb-4">{{ $ruangan->gedung->nama_gedung }} / Lantai {{ $ruangan->lantai }} /
        {{ $ruangan->nama_ruang }}</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <x-ui.card class="lg:col-span-2">
            <h1 class="text-xl font-bold">{{ $ruangan->nama_ruang }}</h1>
            <p class="text-xs text-[#9EA9B9] mb-4"># {{ $ruangan->kode_ruang ?? '-' }} &middot; {{ $ruangan->gedung->nama_gedung }}</p>
            <div class="grid grid-cols-3 gap-4 border-t border-[#DEE2E7] pt-5">
                <div>
                    <div class="text-sm text-[#9EA9B9] mb-1">Panjang</div>
                    <div class="text-xl font-semibold">{{ $ruangan->panjang_m }} m</div>
                </div>
                <div>
                    <div class="text-sm text-[#9EA9B9] mb-1">Lebar</div>
                    <div class="text-xl font-semibold">{{ $ruangan->lebar_m }} m</div>
                </div>
                <div>
                    <div class="text-sm text-[#9EA9B9] mb-1">Luas</div>
                    <div class="text-xl font-semibold">{{ number_format($ruangan->luas_m2, 2) }} m²</div>
                </div>
            </div>
        </x-ui.card>

        <div class="flex flex-col gap-4">
            <x-ui.card>
                <div class="text-xs text-[#9EA9B9]">Total Item Inventaris</div>
                <div class="text-2xl font-bold text-[#120F8B]">{{ number_format($totalItem) }} unit</div>
            </x-ui.card>
            <x-ui.card>
                <div class="text-xs text-[#9EA9B9]">Total Nilai Aset</div>
                <div class="text-2xl font-bold text-[#120F8B]">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </x-ui.card>
        </div>
    </div>

    <div x-data="{
        async terapkanFilter() {
            const form = document.getElementById('filterBarang');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('portal.show', $ruangan) }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelBarangWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        }
    }">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-3">
            <h2 class="font-bold text-lg">Daftar Barang</h2>
            <form id="filterBarang" method="GET" @submit.prevent class="w-full sm:w-48">
                <x-ui.select name="kondisi" x-on:change="terapkanFilter()">
                    <option value="">Semua Kondisi</option>
                    @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k)
                        <option value="{{ $k }}" @selected(request('kondisi') == $k)>{{ $k }}</option>
                    @endforeach
                </x-ui.select>
            </form>
        </div>

        <div id="tabelBarangWrapper">
            @include('portal._barang_table', ['barang' => $barang])
        </div>
    </div>
</x-layouts.public>