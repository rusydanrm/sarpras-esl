<x-layouts.app>
    <h1 class="text-2xl font-bold mb-6">Laporan</h1>

    <div x-data="{
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('laporan.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        }
    }">

        <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap gap-3 mb-4">
            <div class="w-full sm:w-48">
                <x-ui.select name="gedung_id" x-on:change="terapkanFilter()">
                    <option value="">Semua Gedung</option>
                    @foreach ($gedungList as $g)
                        <option value="{{ $g->gedung_id }}" @selected(($filter['gedung_id'] ?? '') == $g->gedung_id)>
                            {{ $g->nama_gedung }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="w-full sm:w-48">
                <x-ui.select name="kondisi" x-on:change="terapkanFilter()">
                    <option value="">Semua Kondisi</option>
                    @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k)
                        <option value="{{ $k }}" @selected(($filter['kondisi'] ?? '') == $k)>{{ $k }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="w-full sm:w-48">
                <x-ui.input name="sumber_dana" value="{{ $filter['sumber_dana'] ?? '' }}" placeholder="Sumber dana..." x-on:input.debounce.400ms="terapkanFilter()" />
            </div>
        </form>

        <div class="flex flex-col sm:flex-row gap-2 mb-6">
            <a href="{{ route('laporan.excel', $filter) }}">
                <x-ui.button variant="excel">
                    <x-heroicon-o-table-cells class="w-4 h-4" /> Ekspor Excel
                </x-ui.button>
            </a>
            <a href="{{ route('laporan.pdf', $filter) }}">
                <x-ui.button variant="pdf">
                    <x-heroicon-o-document-text class="w-4 h-4" /> Ekspor PDF
                </x-ui.button>
            </a>
        </div>

        <div id="tabelWrapper">
            @include('laporan._table', ['preview' => $preview])
        </div>

    </div>
</x-layouts.app>