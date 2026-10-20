<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sistem Sarpras ESL' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/apexcharts/apexcharts.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>

<body
    class="bg-[#F4F6F5] text-[#0B130F] font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-[#120F8B] selection:text-white">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" x-cloak x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-[#0A0E2E]/60 backdrop-blur-sm z-40 md:hidden">
        </div>

        <!-- Sidebar Component (Spark Admin Dark Style with IPB Navy & Amber Accent) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="w-72 bg-[#0A0E2E] text-white border-r border-white/10 flex flex-col fixed h-full z-50 transition-transform duration-300 ease-out md:translate-x-0 shadow-2xl">

            <!-- Brand Identity -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-white/10 shrink-0">
                <a href="{{ route('dashboard') }}"
                    @click="sidebarOpen = false; window.location.href = '{{ route('dashboard') }}'"
                    class="flex items-center cursor-pointer group transition-transform duration-150 active:scale-95">
                    <img src="{{ asset('images/logo_esl.png') }}" alt="Logo Departemen ESL"
                        class="h-9 sm:h-10 w-auto max-w-[200px] object-contain pointer-events-none group-hover:opacity-90 transition-opacity">
                </a>
                <button @click="sidebarOpen = false"
                    class="md:hidden w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors">✕</button>
            </div>

            <!-- Navigation Menu (Categorized ala Spark Admin) -->
            <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
                @php
                    $user = auth()->user();

                    // Group 1: Menu Utama
                    $groupMenu = [
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home']
                    ];
                    $groupMenu[] = ['route' => 'barang.index', 'label' => 'Data Barang', 'icon' => 'archive-box'];
                    if (in_array($user->role, ['admin', 'dosen_tendik'])) {
                        $groupMenu[] = ['route' => 'ruangan.index', 'label' => 'Inventaris Ruangan', 'icon' => 'building-office-2'];
                    }

                    // Group 2: Layanan & Operasional
                    $groupLayanan = [
                        ['route' => 'pengajuan.index', 'label' => 'Pengajuan Sarpras', 'icon' => 'clipboard-document-list'],
                        ['route' => 'transportasi.index', 'label' => 'Transportasi', 'icon' => 'truck'],
                        ['route' => 'peminjaman.index', 'label' => 'Peminjaman', 'icon' => 'calendar-days'],
                        ['route' => 'rekomendasi.index', 'label' => 'Maintenance & Repair', 'icon' => 'wrench-screwdriver'],
                    ];

                    // Group 3: Manajemen & Data
                    $groupManajemen = [];
                    if (in_array($user->role, ['admin', 'dosen_tendik'])) {
                        $groupManajemen[] = ['route' => 'impor.index', 'label' => 'Impor Excel', 'icon' => 'arrow-up-tray'];
                    }
                    $groupManajemen[] = ['route' => 'laporan.index', 'label' => 'Laporan & Rekap', 'icon' => 'document-chart-bar'];
                    if ($user->role === 'admin') {
                        $groupManajemen[] = ['route' => 'master-data.index', 'label' => 'Master Data', 'icon' => 'circle-stack'];
                        $groupManajemen[] = ['route' => 'pengguna.index', 'label' => 'Kelola Pengguna', 'icon' => 'users'];
                    }

                    $sections = [
                        ['title' => 'Menu', 'items' => $groupMenu],
                        ['title' => 'Layanan', 'items' => $groupLayanan],
                        ['title' => 'Manajemen', 'items' => $groupManajemen],
                    ];
                @endphp

                @foreach ($sections as $section)
                    @if (count($section['items']) > 0)
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400/80 px-3 mb-2">
                                {{ $section['title'] }}
                            </div>
                            <ul class="space-y-1">
                                @foreach ($section['items'] as $item)
                                    @php
                                        $baseRoute = explode('.', $item['route'])[0];
                                        $active = request()->routeIs($item['route']) || request()->routeIs($baseRoute . '.*');
                                        $targetUrl = route($item['route']);
                                    @endphp
                                    <li>
                                        <a href="{{ $targetUrl }}" @click="sidebarOpen = false; window.location.href = '{{ $targetUrl }}'"
                                            class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150 active:scale-[0.98] group cursor-pointer relative
                                                {{ $active 
                                                    ? 'bg-white/[0.08] text-white font-semibold shadow-sm before:absolute before:-left-4 before:top-2 before:bottom-2 before:w-1.5 before:bg-[#ffa916] before:rounded-r-full' 
                                                    : 'text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium' }}">
                                            <span class="flex items-center gap-3 pointer-events-none">
                                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']"
                                                    class="w-5 h-5 shrink-0 transition-transform duration-150 group-hover:scale-105 pointer-events-none {{ $active ? 'text-[#ffa916]' : 'text-slate-400 group-hover:text-white' }}" />
                                                {{ $item['label'] }}
                                            </span>
                                            @if ($item['route'] === 'pengguna.index' && ($jumlahPendingSidebar ?? 0) > 0)
                                                <span
                                                    class="bg-[#ffa916] text-[#0A0E2E] text-[11px] font-extrabold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center shrink-0 shadow-sm leading-none pointer-events-none">
                                                    {{ $jumlahPendingSidebar }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- Bottom Profile Card (Spark Admin Style) -->
            <div class="p-4 border-t border-white/10 shrink-0 bg-black/20">
                <div class="bg-white/[0.05] border border-white/10 rounded-2xl p-3 flex items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-[#ffa916]/15 text-[#ffa916] border border-[#ffa916]/30 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ $user->nama }}</div>
                            <div class="text-[10px] text-slate-400 font-medium capitalize truncate">
                                {{ str_replace('_', '/', $user->role) }}
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                            title="Keluar"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-400 hover:text-white hover:bg-rose-500/20 transition-all active:scale-95 cursor-pointer">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper Area -->
        <div class="flex-1 md:ml-72 min-w-0 flex flex-col">
            <!-- Top Navbar Component (Spark Admin Style with Centered Search & Quick Actions) -->
            <header class="h-20 flex items-center justify-between px-6 md:px-8 bg-white/90 backdrop-blur-md border-b border-[#E9EFEF] sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Mobile Sidebar Toggle -->
                    <button @click="sidebarOpen = true"
                        class="md:hidden w-10 h-10 rounded-xl border border-[#E9EFEF] flex items-center justify-center text-slate-600 hover:bg-[#F4F6F5] text-lg cursor-pointer">
                        ☰
                    </button>


                </div>

                <!-- Centered Search Pill Input (Spark Admin Style) -->
                <div class="hidden md:flex flex-1 max-w-md mx-6">
                    <div class="w-full relative flex items-center bg-[#F4F6F5] border border-[#E9EFEF] rounded-full px-4 py-2 focus-within:border-[#ffa916] focus-within:ring-2 focus-within:ring-[#ffa916]/20 transition-all shadow-[0_2px_6px_rgba(11,19,15,0.02)]">
                        <input type="text" placeholder="Search anything in Sarpras ESL..."
                            class="bg-transparent border-none text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0 p-0 w-full"
                            onkeydown="if(event.key === 'Enter'){ window.location.href = '{{ route('barang.index') }}?q=' + encodeURIComponent(this.value); }">
                        <button type="button" class="text-slate-400 hover:text-[#120F8B] ml-2 shrink-0">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Fullscreen Toggle -->
                    <button onclick="if(!document.fullscreenElement){document.documentElement.requestFullscreen();}else{document.exitFullscreen();}"
                        class="w-10 h-10 rounded-xl border border-[#E9EFEF] bg-white hover:bg-[#F4F6F5] flex items-center justify-center text-slate-600 hover:text-slate-900 transition-all shadow-[0_2px_6px_rgba(11,19,15,0.02)] cursor-pointer"
                        title="Layar Penuh">
                        <x-heroicon-o-arrows-pointing-out class="w-4 h-4" />
                    </button>

                    <!-- Notification Bell -->
                    <x-notifikasi-bell />

                    <!-- User Profile Pill (Spark Admin Style) -->
                    <div class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-[#E9EFEF]">
                        <div class="w-9 h-9 rounded-full bg-[#120F8B] text-white flex items-center justify-center text-xs font-bold shadow-sm">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900 leading-tight">{{ $user->nama }}</div>
                            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                                {{ str_replace('_', '/', $user->role) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>