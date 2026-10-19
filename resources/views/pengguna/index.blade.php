<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('pengguna.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        }
    }">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold">Kelola Pengguna</h1>
                @if ($jumlahPending > 0)
                    <p class="text-xs text-[#F59E0B] mt-1">{{ $jumlahPending }} akun menunggu persetujuan</p>
                @endif
            </div>
            <x-ui.button @click="modalOpen = true; editData = null">+ Tambah Pengguna</x-ui.button>
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

        <form id="filterForm" method="GET" @submit.prevent class="flex flex-wrap gap-3 mb-4">
            <div class="w-full sm:w-48">
                <x-ui.input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/email..."
                    x-on:input.debounce.400ms="terapkanFilter()" />
            </div>
            <div class="w-full sm:w-48">
                <x-ui.select name="status" x-on:change="terapkanFilter()">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                    <option value="aktif" @selected(request('status') == 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') == 'nonaktif')>Nonaktif</option>
                </x-ui.select>
            </div>
            <div class="w-full sm:w-48">
                <x-ui.select name="role" x-on:change="terapkanFilter()">
                    <option value="">Semua Role</option>
                    @foreach (['admin', 'dosen_tendik', 'teknisi', 'pimpinan'] as $r)
                        <option value="{{ $r }}" @selected(request('role') == $r)>{{ ucfirst(str_replace('_', '/', $r)) }}
                        </option>
                    @endforeach
                </x-ui.select>
            </div>
        </form>

        <div id="tabelWrapper">
            @include('pengguna._table', ['pengguna' => $pengguna])
        </div>

        <x-ui.modal open="modalOpen">
            <h2 class="text-lg font-bold mb-4"
                x-text="editData ? (editData.status === 'pending' ? 'Tinjau Pengguna' : 'Edit Pengguna') : 'Tambah Pengguna'">
            </h2>

            <form method="POST" :action="editData ? `/pengguna/${editData.user_id}` : '/pengguna'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-3">
                    <x-ui.input label="Nama" name="nama" x-bind:value="editData?.nama" required />
                    <x-ui.input label="Email" type="email" name="email" x-bind:value="editData?.email" required />

                    <template x-if="!editData">
                        <x-ui.input label="Kata Sandi" type="password" name="password" minlength="8" required />
                    </template>

                    <x-ui.select label="Unit/Bagian" name="unit_id">
                        <option value="">- Tidak ada -</option>
                        @foreach ($unitList as $u)
                            <option value="{{ $u->unit_id }}"
                                x-bind:selected="editData && editData.unit_id == {{ $u->unit_id }}">{{ $u->nama_unit }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.select label="Role" name="role" required>
                        <option value="">- Pilih -</option>
                        <option value="admin" x-bind:selected="editData && editData.role === 'admin'">Admin</option>
                        <option value="dosen_tendik" x-bind:selected="editData && editData.role === 'dosen_tendik'">
                            Dosen/Tendik</option>
                        <option value="teknisi" x-bind:selected="editData && editData.role === 'teknisi'">Teknisi
                        </option>
                        <option value="pimpinan" x-bind:selected="editData && editData.role === 'pimpinan'">Pimpinan
                        </option>
                    </x-ui.select>

                    <template x-if="editData">
                        <x-ui.select label="Status" name="status" required>
                            <option value="pending" x-bind:selected="editData && editData.status === 'pending'">Pending
                            </option>
                            <option value="aktif" x-bind:selected="editData && editData.status === 'aktif'">Aktif
                            </option>
                            <option value="nonaktif" x-bind:selected="editData && editData.status === 'nonaktif'">
                                Nonaktif</option>
                        </x-ui.select>
                    </template>
                    <template x-if="!editData">
                        <input type="hidden" name="status" value="aktif">
                    </template>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <x-ui.button type="button" variant="outline" @click="modalOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan</x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    </div>
</x-layouts.app>