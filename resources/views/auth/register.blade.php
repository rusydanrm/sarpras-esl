<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-[#181B21]">Pendaftaran Akun Baru</h1>
        <p class="text-xs text-[#495465] mt-1">Daftarkan akun Anda untuk mengakses layanan sarpras</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="nama" class="text-xs font-medium text-[#495465] block mb-1.5">Nama Lengkap <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus autocomplete="name"
                placeholder="Contoh: Budi Santoso, S.Kom"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('nama') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('nama')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="text-xs font-medium text-[#495465] block mb-1.5">Alamat Email (IPB / Instansi) <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                placeholder="nama@apps.ipb.ac.id"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('email') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('email')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-xs font-medium text-[#495465] block mb-1.5">Password <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('password') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('password')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="text-xs font-medium text-[#495465] block mb-1.5">Konfirmasi Password <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ketik ulang password"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('password_confirmation') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('password_confirmation')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Information Note -->
        <div class="p-3 bg-[#EEF2FF] border border-[#DEE2E7] rounded-xl text-xs text-[#495465] flex items-start gap-2.5">
            <x-heroicon-o-information-circle class="w-4 h-4 text-[#120F8B] shrink-0 mt-0.5" />
            <div>Akun baru akan berstatus <strong>Pending</strong> dan menunggu aktivasi serta penugasan role oleh Admin Sarpras.</div>
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button type="submit"
                class="w-full bg-[#120F8B] hover:bg-[#0D0B63] text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-all shadow-md shadow-[#120F8B]/15 hover:shadow-lg hover:shadow-[#120F8B]/25 active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Daftar Akun</span>
                <x-heroicon-o-user-plus class="w-4 h-4" />
            </button>
        </div>

        <!-- Login Link -->
        <div class="pt-3 border-t border-[#DEE2E7] text-center text-xs text-[#495465]">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="text-[#120F8B] font-semibold hover:underline ms-0.5">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
