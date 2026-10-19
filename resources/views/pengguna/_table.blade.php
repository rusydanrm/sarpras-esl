<div class="bg-white border border-[#DEE2E7] rounded-xl overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
        <thead class="bg-[#F7F8F9] text-[#495465] text-xs uppercase">
            <tr>
                <th class="text-center px-4 py-3 w-12">No</th>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Unit</th>
                <th class="text-left px-4 py-3">Role</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-center px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengguna as $p)
                <tr class="border-t border-[#DEE2E7] {{ $p->status === 'pending' ? 'bg-[#FFFBF4]' : '' }}">
                    <td class="px-4 py-3 text-center text-[#495465] text-xs font-medium">{{ $loop->iteration + ($pengguna->currentPage() - 1) * $pengguna->perPage() }}</td>
                    <td class="px-4 py-3">{{ $p->nama }}</td>
                    <td class="px-4 py-3">{{ $p->email }}</td>
                    <td class="px-4 py-3">{{ $p->unit->nama_unit ?? '-' }}</td>
                    <td class="px-4 py-3">
                        {{ $p->role ? ucfirst(str_replace('_', '/', $p->role)) : '- Belum ditentukan -' }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge
                            :variant="match ($p->status) { 'aktif' => 'success', 'pending' => 'warning', 'nonaktif' => 'danger'}">{{ ucfirst($p->status) }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="flex justify-center gap-1.5">
                            <x-ui.button type="button" variant="outline" size="sm" @click='modalOpen = true; editData = {{ Illuminate\Support\Js::from($p) }}'>
                                <x-heroicon-o-pencil-square class="w-3.5 h-3.5" /> {{ $p->status === 'pending' ? 'Tinjau' : 'Edit' }}
                            </x-ui.button>
                            @if ($p->user_id !== auth()->id())
                                <x-ui.confirm-delete-form action="{{ route('pengguna.destroy', $p) }}" title="Hapus Pengguna"
                                    message="Akun '{{ $p->nama }}' akan dihapus permanen dan tidak bisa login lagi.">
                                    <x-heroicon-o-trash class="w-3.5 h-3.5" /> Hapus
                                </x-ui.confirm-delete-form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pengguna->links() }}</div>
