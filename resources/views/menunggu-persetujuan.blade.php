<x-guest-layout>
    <div class="text-center">
        <!-- Status Icon ala Spark -->
        <div class="w-16 h-16 rounded-2xl bg-amber-50 border-2 border-amber-200 flex items-center justify-center text-amber-600 mx-auto mb-4 relative shadow-2xs">
            <x-heroicon-o-clock class="w-8 h-8 animate-pulse text-[#ffa916]" />
            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#ffa916] border-2 border-white"></span>
        </div>

        <!-- Status Badge -->
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 mb-2.5">
            <span class="w-2 h-2 rounded-full bg-[#ffa916] animate-ping"></span>
            <span>Status: Menunggu Persetujuan</span>
        </span>

        <h1 class="text-xl font-extrabold text-[#0A0E2E] tracking-tight">Akun Sedang Diverifikasi</h1>
        <p class="text-xs text-[#6C7E75] font-medium mt-1.5 leading-relaxed max-w-sm mx-auto">
            Pendaftaran akun Anda telah tercatat di sistem. Administrator Departemen ESL sedang meninjau data Anda untuk penugasan peran dan pengaktifan hak akses operasional.
        </p>

        <!-- Informasi Akun Terdaftar -->
        @auth
            <div class="mt-4 p-3 bg-[#F8FAF9] rounded-xl border border-[#E9EFEF] flex items-center justify-between text-xs text-left">
                <div class="min-w-0 pr-2">
                    <div class="font-bold text-[#0A0E2E] truncate">{{ auth()->user()->nama }}</div>
                    <div class="text-[11px] text-[#6C7E75] truncate">{{ auth()->user()->email }}</div>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 shrink-0">
                    Pending
                </span>
            </div>
        @endauth

        <!-- Alur Verifikasi ala Spark Steps -->
        <div class="bg-[#F8FAF9] p-4 rounded-2xl border border-[#E9EFEF] my-5 text-left space-y-3">
            <div class="text-[10px] font-bold uppercase tracking-wider text-[#6C7E75]">Tahapan Aktivasi Akun</div>
            
            <div class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <x-heroicon-o-check class="w-3.5 h-3.5 stroke-2" />
                </div>
                <div class="text-xs">
                    <div class="font-bold text-[#0A0E2E]">Pendaftaran Berhasil</div>
                    <div class="text-[11px] text-[#6C7E75]">Formulir pendaftaran akun civitas telah diterima.</div>
                </div>
            </div>

            <div class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-full bg-[#ffa916]/20 text-[#0A0E2E] flex items-center justify-center shrink-0 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-[#ffa916] animate-pulse"></span>
                </div>
                <div class="text-xs">
                    <div class="font-bold text-[#0A0E2E]">Peninjauan Administrator</div>
                    <div class="text-[11px] text-[#6C7E75]">Admin memverifikasi identitas dan menetapkan peran akses.</div>
                </div>
            </div>

            <div class="flex items-start gap-2.5 opacity-60">
                <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center shrink-0 mt-0.5 text-[10px] font-bold">
                    3
                </div>
                <div class="text-xs">
                    <div class="font-bold text-[#0A0E2E]">Akses Penuh Diaktifkan</div>
                    <div class="text-[11px] text-[#6C7E75]">Anda dapat langsung masuk ke dashboard sarpras.</div>
                </div>
            </div>
        </div>

        <p class="text-[11px] text-[#6C7E75] mb-5 leading-normal">
            Jika proses verifikasi memakan waktu lebih lama dari biasanya, silakan hubungi tim Admin Sarpras ESL secara langsung.
        </p>

        <!-- Tombol Aksi -->
        <div class="space-y-2">
            <button type="button" onclick="window.location.reload()"
                class="w-full py-2.5 px-4 bg-[#ffa916] text-[#0A0E2E] hover:bg-[#ffb738] rounded-xl text-xs font-bold transition-all shadow-[0_4px_12px_rgba(255,169,22,0.25)] flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                <span>Periksa Status Ulang</span>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full py-2 px-4 rounded-xl text-xs font-semibold text-[#6C7E75] hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>