<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Spark Key Icon Badge -->
        <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center mx-auto mb-3.5 shadow-xs">
            <x-heroicon-o-key class="w-6 h-6" />
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A0E2E] tracking-tight">Lupa Kata Sandi?</h1>
        <p class="text-xs text-[#6C7E75] font-medium mt-1 leading-relaxed">
            Masukkan alamat email resmi Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-[#0A0E2E] mb-1.5">
                Alamat Email IPB <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#6C7E75]">
                    <x-heroicon-o-envelope class="w-4 h-4" />
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
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

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Kirim Tautan Reset Sandi</span>
                <x-heroicon-o-paper-airplane class="w-4 h-4 stroke-2" />
            </button>
        </div>

        <!-- Back to Login Link -->
        <div class="pt-4 border-t border-[#E9EFEF] text-center text-xs text-[#6C7E75]">
            Ingat kata sandi akun Anda?
            <a href="{{ route('login') }}" class="text-[#0A0E2E] font-bold hover:text-[#ffa916] underline ms-1 transition-colors">
                Kembali ke halaman masuk
            </a>
        </div>
    </form>
</x-guest-layout>
