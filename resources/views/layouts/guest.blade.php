<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Sistem Sarpras ESL') }} — IPB University</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F4F6F5] text-[#0A0E2E] min-h-screen flex flex-col justify-between items-center px-4 py-8 antialiased selection:bg-[#ffa916] selection:text-[#0A0E2E] relative font-sans">
    <!-- Subtle Background Ambient (Spark Navy & Amber Glow) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#0A0E2E]/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-32 w-80 h-80 bg-[#ffa916]/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-[#120F8B]/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Header Branding ala Spark -->
    <header class="relative z-10 text-center mt-2 sm:mt-4">
        <a href="{{ route('portal.index') }}" class="inline-flex items-center justify-center group transition-transform active:scale-98">
            <img src="{{ asset('images/logo_esl.png') }}" alt="Departemen Ekonomi Sumberdaya & Lingkungan"
                class="h-12 sm:h-14 w-auto max-w-[280px] sm:max-w-[320px] object-contain drop-shadow-xs group-hover:opacity-90 transition-opacity">
        </a>
    </header>

    <!-- Auth Card Container -->
    <main class="relative z-10 w-full sm:max-w-md my-6">
        <div class="bg-white border border-[#E9EFEF] rounded-3xl shadow-[0_8px_30px_rgba(10,14,46,0.06)] p-6 sm:p-8 relative overflow-hidden">
            <!-- Decorative Spark Corner Accent -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-[#ffa916]/10 via-transparent to-transparent pointer-events-none rounded-tr-3xl"></div>
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 text-center text-xs text-[#6C7E75] space-y-1.5 mb-2 font-medium">
        <div>&copy; {{ date('Y') }} Departemen ESL &middot; Fakultas Ekonomi dan Manajemen, IPB University</div>
        <div>
            <a href="{{ route('portal.index') }}"
                class="text-[#0A0E2E] hover:text-[#ffa916] font-semibold underline transition-colors inline-flex items-center gap-1">
                <span>← Kunjungi Portal Publik Sarpras</span>
            </a>
        </div>
    </footer>
</body>

</html>
