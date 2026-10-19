<x-layouts.app>
    <div x-data="{ modalOpen: {{ $errors->any() ? 'true' : 'false' }}, editData: null }">

        <h1 class="text-2xl font-bold mb-6">Master Data</h1>

        <div class="flex gap-1 mb-6 border-b border-[#DEE2E7] overflow-x-auto">
            @foreach ($daftarTab as $key => $k)
                <a href="{{ route('master-data.index', ['tab' => $key]) }}"
                    class="px-4 py-2 text-sm whitespace-nowrap {{ $tab === $key ? 'border-b-2 border-[#120F8B] text-[#120F8B] font-semibold' : 'text-[#495465]' }}">
                    {{ $k['label'] }}
                </a>
            @endforeach
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

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            <h2 class="font-semibold">{{ $konfig['label'] }}</h2>
            <x-ui.button @click="modalOpen = true; editData = null">+ Tambah {{ $konfig['label'] }}</x-ui.button>
        </div>

        <div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
            <table class="w-full text-sm min-w-[420px]">
                <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
                    <tr>
                        <th class="text-center px-4 py-3 w-12">No</th>
                        <th class="text-left px-4 py-3">Nama</th>
                        @if ($tab === 'jenis-barang')
                        <th class="text-left px-4 py-3">Masa Pakai</th>@endif
                        @if ($tab === 'vendor')
                        <th class="text-left px-4 py-3">Kontak</th>@endif
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr class="border-t border-[#DEE2E7]">
                            <td class="px-4 py-3 text-center text-[#495465] text-xs font-medium">
                                {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                            <td class="px-4 py-3 text-left">{{ $item->{$konfig['kolom']} }}</td>
                            @if ($tab === 'jenis-barang')
                            <td class="px-4 py-3 text-left">{{ $item->masa_pakai_tahun ?? 5 }} Tahun</td>@endif
                            @if ($tab === 'vendor')
                            <td class="px-4 py-3 text-left">{{ $item->kontak ?? '-' }}</td>@endif
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="flex justify-center gap-1.5">
                                    <x-ui.button type="button" variant="outline" size="sm"
                                        @click='modalOpen = true; editData = {{ Illuminate\Support\Js::from($item) }}'>
                                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" /> Edit
                                    </x-ui.button>
                                    <x-ui.confirm-delete-form
                                        action="{{ route('master-data.destroy', [$tab, $item->getKey()]) }}"
                                        title="Hapus {{ $konfig['label'] }}" message="Data ini akan dihapus permanen.">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" /> Hapus
                                    </x-ui.confirm-delete-form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($tab, ['vendor', 'jenis-barang']) ? 4 : 3 }}"
                                class="px-4 py-6 text-center text-[#9EA9B9]">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $data->links() }}</div>

        <x-ui.modal open="modalOpen" maxWidth="sm">
            <h2 class="text-lg font-bold mb-4"
                x-text="editData ? 'Edit {{ $konfig['label'] }}' : 'Tambah {{ $konfig['label'] }}'"></h2>

            <form method="POST"
                :action="editData ? `/master-data/{{ $tab }}/${editData.{{ $konfig['model']::make()->getKeyName() }}}` : '/master-data/{{ $tab }}'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-3">
                    <x-ui.input label="Nama" name="{{ $konfig['kolom'] }}"
                        x-bind:value="editData ? editData['{{ $konfig['kolom'] }}'] : ''" required />
                    @if ($tab === 'jenis-barang')
                        <x-ui.input type="number" label="Masa Pakai Standar (Tahun)" name="masa_pakai_tahun"
                            x-bind:value="editData ? editData.masa_pakai_tahun : 5" min="1" max="50" required />
                    @endif
                    @if ($tab === 'vendor')
                        <x-ui.input label="Kontak" name="kontak" x-bind:value="editData?.kontak" />
                    @endif
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>