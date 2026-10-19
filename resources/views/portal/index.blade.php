<x-layouts.public title="Daftar Ruangan">
    <div x-data="{
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('portal.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('gridWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        }
    }">
        <h1 class="text-2xl font-bold mb-1">Data Sarana & Prasarana per Ruangan</h1>
        <p class="text-sm text-[#495465] mb-6">Ikhtisar aset dan inventaris di seluruh ruangan Departemen ESL. Pilih ruangan untuk melihat detail.</p>

        <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap gap-3 mb-6">
            <div class="w-full sm:w-64">
                <x-ui.input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/kode ruangan..."
                            x-on:input.debounce.400ms="terapkanFilter()" />
            </div>
            <div class="w-full sm:w-48">
                <x-ui.select name="gedung_id" x-on:change="terapkanFilter()">
                    <option value="">Semua Gedung</option>
                    @foreach ($gedungList as $g)
                        <option value="{{ $g->gedung_id }}" @selected(request('gedung_id') == $g->gedung_id)>{{ $g->nama_gedung }}</option>
                    @endforeach
                </x-ui.select>
            </div>
        </form>

        <div id="gridWrapper">
            @include('portal._grid', ['ruanganList' => $ruanganList])
        </div>
    </div>
</x-layouts.public>