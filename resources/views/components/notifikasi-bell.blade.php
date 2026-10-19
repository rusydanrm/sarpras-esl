@if (in_array(auth()->user()->role, ['admin', 'teknisi']))
    @php
        $daftarNotif = \App\Models\Notifikasi::where('user_id', auth()->id())
            ->where('sudah_dibaca', false)
            ->with(['barang.ruangan', 'kendaraan'])
            ->orderByDesc('tanggal_jatuh_tempo')
            ->limit(8)
            ->get();
        $jumlahBelumDibaca = $daftarNotif->count();
    @endphp

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open"
            class="relative w-10 h-10 rounded-xl border border-[#E9EFEF] bg-white hover:bg-[#F4F6F5] flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all cursor-pointer shadow-[0_2px_6px_rgba(11,19,15,0.02)]"
            title="Notifikasi Pemeliharaan">
            <x-heroicon-o-bell class="w-5 h-5" />
            @if ($jumlahBelumDibaca > 0)
                <span class="absolute -top-1 -right-1 bg-[#ffa916] text-[#0A0E2E] text-[10px] font-extrabold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center shadow-sm">
                    {{ $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca }}
                </span>
            @endif
        </button>

        <div x-show="open" @click.outside="open = false" x-cloak
            class="absolute right-0 mt-2.5 w-80 sm:w-96 bg-white border border-[#E9EFEF] rounded-2xl shadow-2xl z-50 overflow-hidden">
            <div class="px-5 py-4 bg-[#F4F6F5]/80 border-b border-[#E9EFEF] flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Jadwal Servis & Pajak</span>
                    @if ($jumlahBelumDibaca > 0)
                        <span class="bg-[#ffa916]/20 text-[#92400E] text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ $jumlahBelumDibaca }} baru
                        </span>
                    @endif
                </div>
                @if ($jumlahBelumDibaca > 0)
                    <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                        @csrf
                        <button type="submit" class="text-xs text-[#120F8B] hover:text-[#ffa916] hover:underline font-semibold cursor-pointer transition-colors">
                            Tandai dibaca
                        </button>
                    </form>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 text-xs">
                @forelse ($daftarNotif as $n)
                    @php
                        $isLate = now()->gte(\Carbon\Carbon::parse($n->tanggal_jatuh_tempo));
                    @endphp
                    <a href="{{ $n->link_url ?: '#' }}"
                        class="block px-4 py-3 hover:bg-[#F7F8F9] transition-colors group">
                        <div class="flex items-start gap-2.5">
                            <div class="mt-0.5 p-1.5 rounded-lg shrink-0 {{ $isLate ? 'bg-[#EF4444]/10 text-[#EF4444]' : 'bg-[#F59E0B]/10 text-[#F59E0B]' }}">
                                @if ($n->tipe === 'servis_kendaraan')
                                    <x-heroicon-o-truck class="w-4 h-4" />
                                @elseif ($n->tipe === 'pajak_stnk')
                                    <x-heroicon-o-document-text class="w-4 h-4" />
                                @else
                                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[#181B21] font-medium leading-relaxed group-hover:text-[#120F8B]">
                                    {{ $n->pesan }}
                                </p>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-[#9EA9B9]">
                                    <span>Jatuh tempo: <strong class="{{ $isLate ? 'text-[#EF4444]' : 'text-[#495465]' }}">{{ \Carbon\Carbon::parse($n->tanggal_jatuh_tempo)->format('d/m/Y') }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-[#9EA9B9]">
                        <x-heroicon-o-check-circle class="w-8 h-8 mx-auto mb-1.5 opacity-40 text-[#10B981]" />
                        <p class="font-medium text-[#495465]">Tidak ada notifikasi baru</p>
                        <p class="text-[11px] mt-0.5">Semua jadwal servis dan pajak saat ini terpantau aman.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif