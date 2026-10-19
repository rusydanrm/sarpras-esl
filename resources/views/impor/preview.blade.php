<x-layouts.app>
    <h1 class="text-2xl font-bold mb-2">Pratinjau Impor</h1>
    <p class="text-sm text-[#495465] mb-6">{{ count($valid) }} baris valid, {{ count($gagal) }} baris gagal</p>

    @if (count($gagal))
        <h2 class="font-semibold text-sm mb-2 text-[#EF4444]">Baris Gagal</h2>
        <div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto mb-6">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-[#FEF7F7] text-[#EF4444] text-xs uppercase">
                    <tr>
                        <th class="text-center px-4 py-2">Baris</th>
                        <th class="text-left px-4 py-2">Nama Barang</th>
                        <th class="text-left px-4 py-2">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gagal as $g)
                        <tr class="border-t border-[#DEE2E7] bg-[#FEF7F7]/40">
                            <td class="px-4 py-2 text-center">{{ $g['baris'] }}</td>
                            <td class="px-4 py-2 text-left">{{ $g['nama_barang'] ?: '-' }}</td>
                            <td class="px-4 py-2 text-left">{{ $g['alasan'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2 class="font-semibold text-sm mb-2 text-[#10B981]">Baris Valid</h2>
    <div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-[#F0FEF9] text-[#10B981] text-xs uppercase">
                <tr>
                    <th class="text-center px-4 py-2 w-12">No</th>
                    <th class="text-left px-4 py-2">Nama Barang</th>
                    <th class="text-left px-4 py-2">Merek/Tipe</th>
                    <th class="text-left px-4 py-2">Ruangan</th>
                    <th class="text-right px-4 py-2">Jumlah</th>
                    <th class="text-left px-4 py-2">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($valid as $v)
                    <tr class="border-t border-[#DEE2E7] bg-[#F0FEF9]/40">
                        <td class="px-4 py-2 text-center text-xs font-medium text-[#495465]">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $v['nama_barang'] }}</td>
                        <td class="px-4 py-2">{{ $v['merek_tipe'] }}</td>
                        <td class="px-4 py-2">{{ $v['kode_ruang'] }}</td>
                        <td class="px-4 py-2 text-right">{{ $v['jumlah'] }}</td>
                        <td class="px-4 py-2">
                            <div class="flex flex-wrap gap-1 text-xs">
                                @if (($v['kondisi_baik'] ?? 0) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $v['kondisi_baik'] }} Baik</span>
                                @endif
                                @if (($v['kondisi_rusak_ringan'] ?? 0) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">{{ $v['kondisi_rusak_ringan'] }} RR</span>
                                @endif
                                @if (($v['kondisi_rusak_berat'] ?? 0) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800">{{ $v['kondisi_rusak_berat'] }} RB</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex gap-2 mt-5">
        <a href="{{ route('impor.index') }}"><x-ui.button type="button" variant="outline">Batalkan</x-ui.button></a>
        @if (count($valid))
            <form method="POST" action="{{ route('impor.simpan') }}">
                @csrf
                <x-ui.button type="submit">Konfirmasi & Simpan ({{ count($valid) }} data)</x-ui.button>
            </form>
        @endif
    </div>
</x-layouts.app>