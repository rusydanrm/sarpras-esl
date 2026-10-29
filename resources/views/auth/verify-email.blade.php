<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Spark Envelope Icon Badge -->
        <div class="w-12 h-12 rounded-2xl bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center mx-auto mb-3.5 shadow-xs">
            <x-heroicon-o-envelope class="w-6 h-6 stroke-[1.8]" />
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A0E2E] tracking-tight">Verifikasi Alamat Email</h1>
        <p class="text-xs text-[#6C7E75] font-medium mt-1.5 leading-relaxed">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan verifikasi yang telah kami kirimkan ke email Anda.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200/80 rounded-2xl font-medium text-xs text-emerald-800 flex items-start gap-2.5 shadow-xs">
            <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
            <span class="leading-relaxed">Tautan verifikasi baru telah berhasil dikirimkan ke alamat email yang Anda daftarkan. Silakan periksa kotak masuk atau folder spam Anda.</span>
        </div>
    @endif

    <div class="space-y-4 pt-1">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit"
                class="w-full bg-[#ffa916] hover:bg-[#ffb738] text-[#0A0E2E] font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] hover:shadow-[0_6px_16px_rgba(255,169,22,0.35)] active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Kirim Ulang Email Verifikasi</span>
                <x-heroicon-o-arrow-path class="w-4 h-4 stroke-2" />
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-3 border-t border-[#E9EFEF]">
            @csrf

            <button type="submit" class="text-xs text-[#6C7E75] hover:text-rose-600 font-semibold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                <x-heroicon-o-arrow-left-on-rectangle class="w-3.5 h-3.5" />
                <span>Keluar dari Akun (Log Out)</span>
            </button>
        </form>
    </div>
</x-guest-layout>
