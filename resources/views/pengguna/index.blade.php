<x-layouts.app>
    <div x-data="{
        modalOpen: {{ $errors->any() ? 'true' : 'false' }},
        editData: null,
        statusAktif: '{{ request('status', '') }}',
        editUser(user) {
            this.editData = user;
            this.modalOpen = true;
        },
        tambahUser() {
            this.editData = null;
            this.modalOpen = true;
        },
        pilihStatus(st) {
            this.statusAktif = st;
            document.getElementById('inputStatus').value = st;
            this.terapkanFilter();
        },
        async terapkanFilter() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = '{{ route('pengguna.index') }}?' + params;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            document.getElementById('tabelWrapper').innerHTML = await res.text();
            window.history.replaceState({}, '', url);
        },
        resetFilter() {
            document.getElementById('inputCari').value = '';
            document.getElementById('selectRole').value = '';
            document.getElementById('selectUnit').value = '';
            this.pilihStatus('');
        },
        handleTableClick(e) {
            const link = e.target.closest('#tabelWrapper a.page-link, #tabelWrapper nav a');
            if (link && link.href) {
                e.preventDefault();
                fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('tabelWrapper').innerHTML = html;
                        window.history.replaceState({}, '', link.href);
                    });
            }
        }
    }" @click="handleTableClick($event)">

        <!-- Page Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Kelola Pengguna</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Manajemen akun civitas, persetujuan pendaftaran staf, dan pengaturan hak akses peran ESL</p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" @click="tambahUser()"
                    class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                    <x-heroicon-o-plus class="w-4 h-4 stroke-2" />
                    <span>Tambah Pengguna</span>
                </button>
            </div>
        </div>

        <!-- Success & Error Alerts -->
        @if (session('success'))
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 text-sm text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 p-4 bg-rose-50 border border-rose-200/80 rounded-2xl flex items-start gap-3 text-sm text-rose-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                </div>
                <div class="flex-1">
                    <h4 class="font-bold mb-1">Terdapat beberapa kesalahan:</h4>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Banner Akun Pending (Jika Ada Akun Menunggu Persetujuan) -->
        @if ($jumlahPending > 0)
            <div class="mb-6 p-4 rounded-2xl bg-amber-50/80 border border-amber-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <x-heroicon-o-bell-alert class="w-5 h-5 animate-bounce" />
                    </div>
                    <div>
                        <div class="text-sm font-bold text-amber-900">
                            {{ $jumlahPending }} Akun Menunggu Persetujuan
                        </div>
                        <div class="text-xs text-amber-700/90 mt-0.5">
                            Terdapat pendaftaran akun baru yang perlu diverifikasi dan diberikan penugasan hak akses oleh Administrator.
                        </div>
                    </div>
                </div>
                <button type="button" @click="pilihStatus('pending')"
                    class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-heroicon-o-eye class="w-4 h-4" />
                    <span>Tinjau Akun Pending</span>
                </button>
            </div>
        @endif

        <!-- 4 Bento Metric Cards ala Spark Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Alert Dark Navy Spark Card (Total Pengguna) -->
            <div class="alert-spark-card">
                <!-- Inline SVG geometric decoration (Amber 6-pointed star with rounded caps) -->
                <svg class="alert-spark-bg-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(50,50)">
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(60)" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(120)" />
                    </g>
                </svg>

                <div class="relative z-10">
                    <span class="alert-spark-badge">Total Pengguna</span>
                    <div class="alert-spark-date">{{ now()->format('d M Y') }} • ESL IPB</div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                        {{ $totalPengguna }} <span class="text-base font-semibold text-slate-300">Akun</span>
                    </div>
                </div>

                <div class="relative z-10 text-[11px] text-slate-300 font-medium flex items-center gap-1.5 mt-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#ffa916]"></span>
                    <span>{{ $totalAktif }} pengguna aktif terverifikasi</span>
                </div>
            </div>

            <!-- Card 2: Menunggu Persetujuan -->
            <div class="rounded-2xl border p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between transition-all
                {{ $jumlahPending > 0 ? 'bg-amber-50/40 border-amber-200' : 'bg-white border-[#E9EFEF]' }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Menunggu Persetujuan</span>
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center
                        {{ $jumlahPending > 0 ? 'bg-amber-100 text-amber-700' : 'bg-[#0A0E2E]/5 text-[#0A0E2E]' }}">
                        <x-heroicon-o-clock class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold tracking-tight {{ $jumlahPending > 0 ? 'text-amber-700' : 'text-[#0A0E2E]' }}">
                        {{ $jumlahPending }} <span class="text-sm font-semibold text-[#6C7E75]">Akun</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] font-medium mt-1">
                        Pendaftaran mandiri staf
                    </div>
                </div>
            </div>

            <!-- Card 3: Akun Aktif -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Akun Aktif</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ $totalAktif }} <span class="text-sm font-semibold text-[#6C7E75]">Akun</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 mt-1.5 text-xs font-bold text-emerald-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Akses operasional siap pakai</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Dosen & Tendik -->
            <div class="bg-white rounded-2xl border border-[#E9EFEF] p-4 sm:p-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)] flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-[#6C7E75]">Dosen & Tendik</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
                        <x-heroicon-o-academic-cap class="w-4 h-4" />
                    </span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-[#0A0E2E] tracking-tight">
                        {{ $roleCounts['dosen_tendik'] ?? 0 }} <span class="text-sm font-semibold text-[#6C7E75]">Akun</span>
                    </div>
                    <div class="text-xs text-[#6C7E75] font-medium mt-1">
                        Koordinator ruang & pemohon
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmented Status Tabs ala Spark Admin -->
        <div class="mb-4 overflow-x-auto pb-1 -mx-1 px-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden select-none">
            <div class="flex items-center gap-2 min-w-max p-0.5">
                <button type="button" @click="pilihStatus('')"
                    :class="statusAktif === '' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Semua Status</span>
                    <span :class="statusAktif === '' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $totalPengguna }}
                    </span>
                </button>

                <button type="button" @click="pilihStatus('pending')"
                    :class="statusAktif === 'pending' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Menunggu Persetujuan</span>
                    <span :class="statusAktif === 'pending' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-amber-100 text-amber-700 font-bold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $jumlahPending }}
                    </span>
                </button>

                <button type="button" @click="pilihStatus('aktif')"
                    :class="statusAktif === 'aktif' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Aktif</span>
                    <span :class="statusAktif === 'aktif' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $totalAktif }}
                    </span>
                </button>

                <button type="button" @click="pilihStatus('nonaktif')"
                    :class="statusAktif === 'nonaktif' 
                        ? 'bg-[#0A0E2E] text-white shadow-xs font-semibold' 
                        : 'bg-white border border-[#E9EFEF] text-[#495465] hover:bg-[#F4F6F5] hover:text-[#0A0E2E] font-medium'"
                    class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-2 cursor-pointer shadow-2xs">
                    <span>Nonaktif</span>
                    <span :class="statusAktif === 'nonaktif' ? 'bg-[#ffa916] text-[#0A0E2E] font-bold' : 'bg-[#F4F6F5] text-[#6C7E75] font-semibold'"
                        class="text-[11px] px-2 py-0.5 rounded-full transition-colors">
                        {{ $totalNonaktif }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Filter Toolbar ala Spark Admin Header Control -->
        <div class="bg-white rounded-2xl border border-[#E9EFEF] p-3.5 sm:p-4 mb-5 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
            <form id="filterForm" method="GET" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">

                <!-- Search Input Box -->
                <div class="relative flex-1 min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input type="text" name="cari" id="inputCari" value="{{ request('cari') }}"
                        placeholder="Cari nama atau email pengguna..."
                        x-on:input.debounce.400ms="terapkanFilter()"
                        class="w-full pl-9 pr-3.5 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden" />
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Filter Role -->
                    <div class="w-full sm:w-44">
                        <select name="role" id="selectRole" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                            <option value="">Semua Role</option>
                            @foreach (['admin', 'dosen_tendik', 'teknisi', 'pimpinan'] as $r)
                                <option value="{{ $r }}" @selected(request('role') === $r)>
                                    {{ ucfirst(str_replace('_', '/', $r)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Unit -->
                    <div class="w-full sm:w-48">
                        <select name="unit_id" id="selectUnit" x-on:change="terapkanFilter()"
                            class="w-full px-3 py-2 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] transition-all outline-hidden cursor-pointer">
                            <option value="">Semua Unit/Bagian</option>
                            @foreach ($unitList as $u)
                                <option value="{{ $u->unit_id }}" @selected(request('unit_id') == $u->unit_id)>
                                    {{ $u->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <button type="button" @click="resetFilter()"
                        class="px-3 py-2 rounded-xl text-xs font-semibold text-[#6C7E75] hover:text-[#0A0E2E] hover:bg-[#F4F6F5] border border-[#E9EFEF] transition-colors flex items-center gap-1 cursor-pointer">
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                        <span>Reset</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Data Spark AJAX Wrapper -->
        <div id="tabelWrapper">
            @include('pengguna._table', ['pengguna' => $pengguna])
        </div>

        <!-- Modal Tambah / Edit Pengguna ala Spark Admin -->
        <x-ui.modal open="modalOpen" maxWidth="lg">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                        <x-heroicon-o-user-group class="w-4 h-4" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#0A0E2E]"
                            x-text="editData ? (editData.status === 'pending' ? 'Tinjau & Setujui Akun' : 'Edit Data Pengguna') : 'Tambah Pengguna Baru'">
                        </h2>
                        <p class="text-xs text-[#6C7E75]"
                            x-text="editData ? 'Perbarui profil, hak akses peran, dan status akun pengguna' : 'Daftarkan akun staf baru dan berikan hak akses operasional'">
                        </p>
                    </div>
                </div>
                <button type="button" @click="modalOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form method="POST" :action="editData ? `/pengguna/${editData.user_id}` : '{{ route('pengguna.store') }}'">
                @csrf
                <template x-if="editData"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    <!-- Section 1: Identitas Akun -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF] space-y-3.5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">1</span>
                            <span>Kredensial & Identitas Akun</span>
                        </h3>

                        <div>
                            <x-ui.input label="Nama Lengkap" name="nama" x-bind:value="editData?.nama"
                                placeholder="Masukkan nama lengkap beserta gelar..." required />
                        </div>

                        <div>
                            <x-ui.input label="Alamat Email" type="email" name="email" x-bind:value="editData?.email"
                                placeholder="nama@apps.ipb.ac.id" required />
                        </div>

                        <template x-if="!editData">
                            <div>
                                <x-ui.input label="Kata Sandi Awal" type="password" name="password" minlength="8"
                                    placeholder="Minimal 8 karakter..." required />
                                <p class="text-[11px] text-[#6C7E75] mt-1">Pengguna dapat mengubah kata sandi mandiri di menu Profil.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Section 2: Penugasan Unit & Hak Akses -->
                    <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF] space-y-3.5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#0A0E2E] flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-md bg-[#0A0E2E] text-[#ffa916] inline-flex items-center justify-center text-[10px]">2</span>
                            <span>Penugasan & Hak Akses Peran</span>
                        </h3>

                        <div>
                            <x-ui.select label="Unit / Divisi Kerja" name="unit_id">
                                <option value="">- Tanpa Unit / Umum -</option>
                                @foreach ($unitList as $u)
                                    <option value="{{ $u->unit_id }}"
                                        x-bind:selected="editData && editData.unit_id == {{ $u->unit_id }}">{{ $u->nama_unit }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div>
                            <x-ui.select label="Hak Akses (Role)" name="role" required>
                                <option value="">- Pilih Role Sistem -</option>
                                <option value="admin" x-bind:selected="editData && editData.role === 'admin'">
                                    Admin (Akses Penuh Seluruh Modul)
                                </option>
                                <option value="dosen_tendik" x-bind:selected="editData && editData.role === 'dosen_tendik'">
                                    Dosen / Tendik (Koordinator Ruangan & Pemohon)
                                </option>
                                <option value="teknisi" x-bind:selected="editData && editData.role === 'teknisi'">
                                    Teknisi (Catat Servis & Pemeliharaan)
                                </option>
                                <option value="pimpinan" x-bind:selected="editData && editData.role === 'pimpinan'">
                                    Pimpinan (Persetujuan Tahap-2 & Monitoring)
                                </option>
                            </x-ui.select>
                        </div>

                        <template x-if="editData">
                            <div>
                                <x-ui.select label="Status Akun" name="status" required>
                                    <option value="pending" x-bind:selected="editData && editData.status === 'pending'">
                                        Pending (Menunggu Persetujuan)
                                    </option>
                                    <option value="aktif" x-bind:selected="editData && editData.status === 'aktif'">
                                        Aktif (Bisa Login & Mengakses Sistem)
                                    </option>
                                    <option value="nonaktif" x-bind:selected="editData && editData.status === 'nonaktif'">
                                        Nonaktif (Akses Login Ditangguhkan)
                                    </option>
                                </x-ui.select>
                            </div>
                        </template>
                        <template x-if="!editData">
                            <input type="hidden" name="status" value="aktif">
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-[#E9EFEF]">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-[#495465] hover:bg-[#F4F6F5] border border-[#DEE2E7] transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-95 cursor-pointer">
                        Simpan Data Pengguna
                    </button>
                </div>
            </form>
        </x-ui.modal>

    </div>
</x-layouts.app>