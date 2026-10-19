<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-[#181B21]">Atur Ulang Password</h1>
        <p class="text-xs text-[#495465] mt-1">Masukkan password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="text-xs font-medium text-[#495465] block mb-1.5">Alamat Email <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('email') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('email')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-xs font-medium text-[#495465] block mb-1.5">Password Baru <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('password') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('password')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="text-xs font-medium text-[#495465] block mb-1.5">Konfirmasi Password Baru <span class="text-[#EF4444] font-bold">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ketik ulang password"
                class="w-full border border-[#DEE2E7] rounded-xl px-3.5 py-2.5 text-sm text-[#181B21] placeholder-[#9EA9B9] focus:outline-none focus:ring-2 focus:ring-[#120F8B]/20 focus:border-[#120F8B] transition-colors {{ $errors->has('password_confirmation') ? 'border-[#EF4444] bg-[#FEF7F7]/30' : 'bg-white' }}">
            @error('password_confirmation')
                <p class="text-xs text-[#EF4444] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#120F8B] hover:bg-[#0D0B63] text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-all shadow-md shadow-[#120F8B]/15 hover:shadow-lg hover:shadow-[#120F8B]/25 active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Simpan Password Baru</span>
                <x-heroicon-o-check class="w-4 h-4" />
            </button>
        </div>
    </form>
</x-guest-layout>
