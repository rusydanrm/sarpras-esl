<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' — ' : '' }}Portal Sarpras ESL IPB University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F4F6F5] text-[#0A0E2E] antialiased min-h-screen flex flex-col font-sans selection:bg-[#ffa916] selection:text-[#0A0E2E]">
    <!-- Topbar Navigation ala Spark Admin -->
    <header class="bg-white/95 backdrop-blur-md border-b border-[#E9EFEF] sticky top-0 z-30 shadow-[0_2px_12px_rgba(10,14,46,0.03)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.index') }}" class="flex items-center gap-2 group transition-transform active:scale-98">
                    <img src="{{ asset('images/logo_esl.png') }}" alt="Departemen Ekonomi Sumberdaya & Lingkungan"
                        class="h-9 sm:h-10 w-auto object-contain">
                </a>
                <div class="hidden sm:block h-6 w-px bg-[#E9EFEF]"></div>
                <div class="hidden sm:flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#0A0E2E] text-[#ffa916]">
                        PORTAL PUBLIK
                    </span>
                    <span class="text-xs text-[#6C7E75] font-medium hidden md:inline">Transparansi Sarana & Prasarana</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('portal.index') }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-semibold text-[#0A0E2E] hover:bg-[#F4F6F5] transition-colors flex items-center gap-1.5">
                    <x-heroicon-o-building-office-2 class="w-4 h-4 text-[#6C7E75]" />
                    <span>Katalog Ruangan</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-[#0A0E2E] text-[#ffa916] hover:bg-[#120F8B] rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-squares-2x2 class="w-4 h-4" />
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 bg-[#0A0E2E] text-white hover:text-[#ffa916] hover:bg-[#120F8B] rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 active:scale-95 cursor-pointer">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                        <span>Masuk Staf</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer ala Spark Admin -->
    <footer class="bg-white border-t border-[#E9EFEF] mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#6C7E75]">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-[#0A0E2E] text-[#ffa916] text-[10px]">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 16 16">
                        <path d="M8 0a1 1 0 0 1 1 1v5.268l4.562-2.634a1 1 0 1 1 1 1.732L10 8l4.562 2.634a1 1 0 1 1-1 1.732L9 9.732V15a1 1 0 1 1-2 0V9.732l-4.562 2.634a1 1 0 1 1-1-1.732L6 8 1.438 5.366a1 1 0 0 1 1-1.732L7 6.268V1a1 1 0 0 1 1-1z"/>
                    </svg>
                </span>
                <span>&copy; {{ date('Y') }} Departemen Ekonomi Sumberdaya dan Lingkungan — IPB University</span>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <span>Sistem Informasi Manajemen Sarana & Prasarana</span>
                <span class="text-[#E9EFEF]">•</span>
                <a href="{{ route('login') }}" class="text-[#0A0E2E] font-semibold hover:text-[#ffa916] transition-colors">Area Login Staf & Dosen</a>
            </div>
        </div>
    </footer>
</body>

</html>