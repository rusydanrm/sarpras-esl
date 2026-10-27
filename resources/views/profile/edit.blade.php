<x-layouts.app>
    <div class="space-y-6">

        <!-- Page Header ala Spark Admin -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#0A0E2E] text-[#ffa916] text-xs shadow-xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0A0E2E]">Profil & Pengaturan Akun</h1>
                </div>
                <p class="text-xs text-[#6C7E75] font-medium">Kelola informasi data diri, keamanan kredensial, dan hak akses operasional Anda</p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E9EFEF] rounded-xl text-xs font-semibold text-[#0A0E2E] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Status Akun: <strong class="text-emerald-700 capitalize">{{ $user->status }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('status') === 'profile-updated')
            <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 text-sm text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="font-medium flex-1">Informasi profil Anda berhasil diperbarui.</div>
            </div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-center gap-3 text-sm text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                </div>
                <div class="font-medium flex-1">Kata sandi akun Anda berhasil diperbarui.</div>
            </div>
        @endif

        <!-- Main Layout: 2 Kolom Grid ala Spark Admin -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Kolom Kiri: Hero Identitas Akun & Tips Keamanan -->
            <div class="space-y-6">
                <!-- Hero Identity Card -->
                <div class="bg-white rounded-2xl border border-[#E9EFEF] p-6 shadow-2xs text-center flex flex-col items-center">
                    @php
                        $initial = strtoupper(mb_substr($user->nama, 0, 1));
                    @endphp
                    <div class="w-20 h-20 rounded-2xl bg-[#0A0E2E] text-[#ffa916] border-2 border-[#ffa916]/30 text-2xl font-black flex items-center justify-center shadow-md mb-3.5">
                        {{ $initial }}
                    </div>

                    <h2 class="text-base font-bold text-[#0A0E2E]">{{ $user->nama }}</h2>
                    <p class="text-xs text-[#6C7E75] font-medium mt-0.5">{{ $user->email }}</p>

                    <!-- Role Badge -->
                    <div class="mt-3">
                        @if ($user->role === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#0A0E2E] text-[#ffa916] shadow-2xs">
                                <x-heroicon-o-shield-check class="w-4 h-4" /> Admin Departemen
                            </span>
                        @elseif ($user->role === 'dosen_tendik')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <x-heroicon-o-academic-cap class="w-4 h-4" /> Dosen / Tendik
                            </span>
                        @elseif ($user->role === 'teknisi')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                <x-heroicon-o-wrench-screwdriver class="w-4 h-4" /> Tim Teknisi
                            </span>
                        @elseif ($user->role === 'pimpinan')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <x-heroicon-o-briefcase class="w-4 h-4" /> Pimpinan ESL
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-dashed border-amber-300">
                                Belum Ada Peran
                            </span>
                        @endif
                    </div>

                    <!-- Divider -->
                    <div class="w-full h-px bg-[#E9EFEF] my-5"></div>

                    <!-- Detail Metadata List -->
                    <div class="w-full space-y-3 text-left">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#6C7E75] font-medium">Unit / Divisi:</span>
                            <span class="font-bold text-[#0A0E2E] text-right">{{ $user->unit?->nama_unit ?? 'Departemen ESL' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#6C7E75] font-medium">ID Pengguna:</span>
                            <span class="font-mono font-bold text-[#0A0E2E]">#{{ $user->user_id }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#6C7E75] font-medium">Bergabung:</span>
                            <span class="font-semibold text-[#0A0E2E]">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Ruangan yang Dikoordinasi (Khusus Dosen / Tendik) -->
                    @if ($user->ruanganDikelola->count() > 0)
                        <div class="w-full h-px bg-[#E9EFEF] my-4"></div>
                        <div class="w-full text-left">
                            <div class="text-[11px] font-bold text-[#6C7E75] uppercase tracking-wider mb-2 flex items-center justify-between">
                                <span>Ruangan Koordinasi</span>
                                <span class="bg-[#F4F6F5] px-1.5 py-0.5 rounded text-[#0A0E2E] font-bold text-[10px]">{{ $user->ruanganDikelola->count() }}</span>
                            </div>
                            <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                @foreach ($user->ruanganDikelola as $rg)
                                    <a href="{{ route('ruangan.show', $rg) }}" 
                                        class="block p-2 rounded-xl bg-[#F8FAF9] hover:bg-[#F4F6F5] border border-[#E9EFEF] transition-colors text-xs">
                                        <div class="font-bold text-[#0A0E2E] truncate">{{ $rg->nama_ruang }}</div>
                                        <div class="text-[10px] text-[#6C7E75] truncate">{{ $rg->gedung?->nama_gedung ?? '-' }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Security Awareness Card (Spark Style) -->
                <div class="alert-spark-card !min-h-0 !p-5">
                    <svg class="alert-spark-bg-shape !w-24 !h-24 !-bottom-4 !-right-4" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(50,50)">
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(60)" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(120)" />
                        </g>
                    </svg>

                    <div class="relative z-10 flex items-center gap-2 mb-2">
                        <x-heroicon-o-shield-check class="w-4 h-4 text-[#ffa916]" />
                        <span class="text-xs font-bold text-[#ffa916] uppercase tracking-wider">Keamanan Akun</span>
                    </div>
                    <p class="relative z-10 text-xs text-slate-300 leading-relaxed">
                        Gunakan kombinasi sandi yang kuat dan jangan bagikan kredensial login Anda kepada pihak lain demi menjaga validitas data sarana prasarana departemen.
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan: Form Edit Data Diri & Ganti Password -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Informasi Data Diri -->
                <div class="bg-white rounded-2xl border border-[#E9EFEF] p-5 sm:p-6 shadow-2xs">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-[#E9EFEF]">
                        <div class="w-9 h-9 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                            <x-heroicon-o-identification class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-[#0A0E2E]">Informasi Profil & Data Diri</h3>
                            <p class="text-xs text-[#6C7E75]">Perbarui nama lengkap dan alamat surel resmi civitas akademika</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="inputNama" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="inputNama" name="nama" value="{{ old('nama', $user->nama) }}"
                                class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                required autofocus />
                            @error('nama')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="inputEmail" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                Alamat Email Akun <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="inputEmail" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                required />
                            @error('email')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Hak Akses (Read-Only) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                            <div class="p-3 bg-[#F8FAF9] rounded-xl border border-[#E9EFEF]">
                                <span class="text-[11px] text-[#6C7E75] font-medium block">Hak Akses Sistem:</span>
                                <span class="text-xs font-bold text-[#0A0E2E] uppercase mt-0.5 block">
                                    {{ str_replace('_', '/', $user->role) }}
                                </span>
                            </div>
                            <div class="p-3 bg-[#F8FAF9] rounded-xl border border-[#E9EFEF]">
                                <span class="text-[11px] text-[#6C7E75] font-medium block">Unit / Divisi:</span>
                                <span class="text-xs font-bold text-[#0A0E2E] mt-0.5 block">
                                    {{ $user->unit?->nama_unit ?? 'Departemen ESL' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-[#E9EFEF]">
                            <button type="submit"
                                class="px-5 py-2.5 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                <x-heroicon-o-check class="w-4 h-4 stroke-2" />
                                <span>Simpan Perubahan Profil</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Pembaruan Kata Sandi -->
                <div class="bg-white rounded-2xl border border-[#E9EFEF] p-5 sm:p-6 shadow-2xs">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-[#E9EFEF]">
                        <div class="w-9 h-9 rounded-xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center shrink-0">
                            <x-heroicon-o-key class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-[#0A0E2E]">Pembaruan Kata Sandi</h3>
                            <p class="text-xs text-[#6C7E75]">Gunakan minimal 8 karakter dengan kombinasi angka dan huruf</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="current_password" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                placeholder="Masukkan kata sandi lama Anda..." required autocomplete="current-password" />
                            @if ($errors->updatePassword->has('current_password'))
                                <p class="text-[11px] text-rose-600 mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="password" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                    Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" id="password" name="password" minlength="8"
                                    class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                    placeholder="Minimal 8 karakter..." required autocomplete="new-password" />
                                @if ($errors->updatePassword->has('password'))
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $errors->updatePassword->first('password') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                                    class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                    placeholder="Ulangi kata sandi baru..." required autocomplete="new-password" />
                                @if ($errors->updatePassword->has('password_confirmation'))
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-[#E9EFEF]">
                            <button type="submit"
                                class="px-5 py-2.5 bg-[#0A0E2E] text-white hover:bg-[#120F8B] rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                <x-heroicon-o-lock-closed class="w-4 h-4 text-[#ffa916]" />
                                <span>Perbarui Kata Sandi</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 3: Zona Bahaya / Hapus Akun (Khusus Non-Admin Utama) -->
                @if ($user->role !== 'admin')
                    <div class="bg-rose-50/40 rounded-2xl border border-rose-200 p-5 sm:p-6 shadow-2xs"
                        x-data="{ confirmDeleteOpen: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
                        <div class="flex items-center gap-3 pb-3 mb-3 border-b border-rose-200/60">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-rose-900">Hapus Akun Pengguna</h3>
                                <p class="text-xs text-rose-700">Penghapusan akun bersifat permanen dan tidak dapat dipulihkan</p>
                            </div>
                        </div>

                        <p class="text-xs text-rose-800/90 leading-relaxed mb-4">
                            Jika Anda menghapus akun, seluruh data sesi dan hak akses personal Anda di portal Sarpras ESL akan ditutup permanen. Pastikan Anda telah menyelesaikan seluruh pengajuan atau peminjaman yang tertaut.
                        </p>

                        <button type="button" @click="confirmDeleteOpen = true"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Hapus Akun Saya</span>
                        </button>

                        <!-- Modal Konfirmasi Hapus Akun -->
                        <x-ui.modal open="confirmDeleteOpen" maxWidth="md">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E9EFEF]">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                        <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-rose-900">Konfirmasi Hapus Akun</h2>
                                        <p class="text-xs text-[#6C7E75]">Tindakan ini permanen dan tidak dapat dibatalkan</p>
                                    </div>
                                </div>
                                <button type="button" @click="confirmDeleteOpen = false" class="text-[#6C7E75] hover:text-[#0A0E2E] p-1 rounded-lg">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>

                            <form method="POST" action="{{ route('profile.destroy') }}">
                                @csrf
                                @method('delete')

                                <p class="text-xs text-[#495465] leading-relaxed mb-4">
                                    Masukkan kata sandi Anda saat ini untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen.
                                </p>

                                <div>
                                    <label for="delete_password" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                                        Kata Sandi Pengguna <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" id="delete_password" name="password"
                                        class="w-full px-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-rose-400 focus:bg-white rounded-xl text-xs font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden"
                                        placeholder="Ketikkan kata sandi Anda..." required />
                                    @if ($errors->userDeletion->has('password'))
                                        <p class="text-[11px] text-rose-600 mt-1">{{ $errors->userDeletion->first('password') }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-[#E9EFEF]">
                                    <button type="button" @click="confirmDeleteOpen = false"
                                        class="px-4 py-2 rounded-xl text-xs font-semibold text-[#495465] hover:bg-[#F4F6F5] border border-[#DEE2E7] transition-colors cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer">
                                        Ya, Hapus Akun Permanen
                                    </button>
                                </div>
                            </form>
                        </x-ui.modal>
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-layouts.app>
