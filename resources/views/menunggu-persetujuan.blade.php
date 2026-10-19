<x-guest-layout>
    <div class="text-center py-10">
        <h1 class="text-xl font-bold text-[#181B21]">Akun Anda Menunggu Persetujuan</h1>
        <p class="text-sm text-[#495465] mt-2">Admin akan meninjau dan mengaktifkan akun Anda. Silakan hubungi Admin
            Sarpras jika sudah menunggu lama.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="text-[#120F8B] text-sm underline">Keluar</button>
        </form>
    </div>
</x-guest-layout>