<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-[#181B21]">Verifikasi Email</h1>
        <p class="text-xs text-[#495465] mt-1">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik link yang telah kami kirimkan ke email Anda.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-[#ECFDF5] border border-[#10B981]/20 rounded-xl font-medium text-xs text-[#10B981] flex items-center gap-2">
            <x-heroicon-o-check-circle class="w-4 h-4 text-[#10B981] shrink-0" />
            <span>Link verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.</span>
        </div>
    @endif

    <div class="space-y-4 pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit"
                class="w-full bg-[#120F8B] hover:bg-[#0D0B63] text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-all shadow-md shadow-[#120F8B]/15 hover:shadow-lg hover:shadow-[#120F8B]/25 active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Kirim Ulang Email Verifikasi</span>
                <x-heroicon-o-arrow-path class="w-4 h-4" />
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-2 border-t border-[#DEE2E7]">
            @csrf

            <button type="submit" class="text-xs text-[#EF4444] hover:underline font-medium">
                Keluar (Log Out)
            </button>
        </form>
    </div>
</x-guest-layout>
