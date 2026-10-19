<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-[#181B21]">Masuk ke Akun</h1>
        <p class="text-xs text-[#495465] mt-1">Gunakan akun Anda untuk mengakses sistem sarpras</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="text-xs font-medium text-[#495465] block mb-1.5">Alamat Email <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                placeholder="nama@apps.ipb.ac.id"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('email') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('email')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-xs font-medium text-[#495465]">Password <span class="text-[#EF4444] font-bold">*</span></label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-[#120F8B] hover:underline font-medium">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('password') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('password')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-1">
            <input id="remember_me" type="checkbox" name="remember"
                class="w-4 h-4 rounded border-[#DEE2E7] text-[#120F8B] focus:ring-[#120F8B]/20 accent-[#120F8B]">
            <label for="remember_me" class="ms-2 text-xs text-[#495465] cursor-pointer select-none">
                Ingat saya di perangkat ini
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#120F8B] hover:bg-[#0D0B63] text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-all shadow-md shadow-[#120F8B]/15 hover:shadow-lg hover:shadow-[#120F8B]/25 active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Masuk</span>
                <x-heroicon-o-arrow-right class="w-4 h-4" />
            </button>
        </div>

        <!-- Register Link -->
        <div class="pt-3 border-t border-[#DEE2E7] text-center text-xs text-[#495465]">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="text-[#120F8B] font-semibold hover:underline ms-0.5">
                Daftar sekarang
            </a>
        </div>
    </form>
</x-guest-layout>
