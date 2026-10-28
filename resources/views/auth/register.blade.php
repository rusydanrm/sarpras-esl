<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Spark User Plus Icon Badge -->
        <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center mx-auto mb-3.5 shadow-xs">
            <x-heroicon-o-user-plus class="w-6 h-6" />
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A0E2E] tracking-tight">Pendaftaran Akun Baru</h1>
        <p class="text-xs text-[#6C7E75] font-medium mt-1">Daftarkan akun staf/civitas untuk mengakses layanan sarpras ESL</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="nama" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                Nama Lengkap & Gelar <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                    <x-heroicon-o-user class="w-4 h-4" />
                </div>
                <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus autocomplete="name"
                    placeholder="Contoh: Dr. Budi Santoso, M.Si"
                    class="w-full pl-10 pr-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs sm:text-sm font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden {{ $errors->has('nama') ? '!border-rose-400 !bg-rose-50/30' : '' }}">
            </div>
            @error('nama')
                <p class="text-[11px] text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                Alamat Email IPB / Instansi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                    <x-heroicon-o-envelope class="w-4 h-4" />
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="nama@apps.ipb.ac.id"
                    class="w-full pl-10 pr-3.5 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs sm:text-sm font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden {{ $errors->has('email') ? '!border-rose-400 !bg-rose-50/30' : '' }}">
            </div>
            @error('email')
                <p class="text-[11px] text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Kata Sandi -->
        <div x-data="{ showPass: false }">
            <label for="password" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                Kata Sandi (Minimal 8 Karakter) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                </div>
                <input id="password" :type="showPass ? 'text' : 'password'" name="password" required autocomplete="new-password"
                    placeholder="Minimal 8 karakter..."
                    class="w-full pl-10 pr-10 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs sm:text-sm font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden {{ $errors->has('password') ? '!border-rose-400 !bg-rose-50/30' : '' }}">
                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6C7E75] hover:text-[#0A0E2E] cursor-pointer">
                    <x-heroicon-o-eye x-show="!showPass" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="showPass" class="w-4 h-4" x-cloak />
                </button>
            </div>
            @error('password')
                <p class="text-[11px] text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div x-data="{ showConfirm: false }">
            <label for="password_confirmation" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                </div>
                <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru..."
                    class="w-full pl-10 pr-10 py-2.5 bg-[#F4F6F5] border border-transparent focus:border-[#ffa916] focus:bg-white rounded-xl text-xs sm:text-sm font-medium text-[#0A0E2E] placeholder-[#6C7E75] transition-all outline-hidden {{ $errors->has('password_confirmation') ? '!border-rose-400 !bg-rose-50/30' : '' }}">
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#6C7E75] hover:text-[#0A0E2E] cursor-pointer">
                    <x-heroicon-o-eye x-show="!showConfirm" class="w-4 h-4" />
                    <x-heroicon-o-eye-slash x-show="showConfirm" class="w-4 h-4" x-cloak />
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-[11px] text-rose-600 font-medium mt-1.5 flex items-center gap-1">
                    <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <!-- Info Card: Status Pending -->
        <div class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-2xl text-xs text-amber-900 flex items-start gap-3">
            <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <x-heroicon-o-shield-check class="w-4 h-4" />
            </div>
            <div class="text-[11px] leading-relaxed">
                Akun yang didaftarkan akan berstatus <strong>Pending</strong> terlebih dahulu untuk diverifikasi dan ditentukan peran aksesnya oleh Administrator Sarpras.
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button type="submit"
                class="w-full bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Daftarkan Akun Civitas</span>
                <x-heroicon-o-arrow-right class="w-4 h-4 stroke-2" />
            </button>
        </div>

        <!-- Login Link -->
        <div class="pt-4 border-t border-[#E9EFEF] text-center text-xs text-[#6C7E75]">
            Sudah memiliki akun terdaftar?
            <a href="{{ route('login') }}" class="text-[#0A0E2E] font-bold hover:text-[#ffa916] underline ms-1 transition-colors">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
