<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Sistem Sarpras ESL') }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F8F9] text-[#181B21] min-h-screen flex flex-col justify-between items-center px-4 py-8 antialiased selection:bg-[#120F8B] selection:text-white relative">
    <!-- Subtle Background Ambient -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-[#120F8B]/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-[#120F8B]/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Header Branding -->
    <header class="relative z-10 text-center mt-2 sm:mt-4">
        <a href="{{ route('portal.index') }}" class="inline-flex items-center justify-center group">
            <img src="{{ asset('images/logo_esl.png') }}" alt="Departemen Ekonomi Sumberdaya & Lingkungan"
                class="h-12 sm:h-14 w-auto max-w-[280px] sm:max-w-[320px] object-contain group-hover:scale-105 transition-transform drop-shadow-sm">
        </a>
    </header>

    <!-- Auth Card Container -->
    <main class="relative z-10 w-full sm:max-w-md my-6">
        <div class="bg-white border border-[#DEE2E7] rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 sm:p-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 text-center text-xs text-[#9EA9B9] space-y-1.5 mb-2">
        <div>&copy; {{ date('Y') }} Departemen ESL &middot; Fakultas Ekonomi dan Manajemen, IPB University</div>
        <div>
            <a href="{{ route('portal.index') }}"
                class="text-[#495465] hover:text-[#120F8B] font-medium underline transition-colors">
                ← Kunjungi Portal Publik Sarpras
            </a>
        </div>
    </footer>
</body>

</html>
