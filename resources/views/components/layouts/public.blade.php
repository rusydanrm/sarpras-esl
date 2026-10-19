<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portal Sarpras ESL' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F8F9] text-[#181B21] antialiased min-h-screen flex flex-col">
    <header class="bg-white border-b border-[#DEE2E7] sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 md:px-8 h-16 flex items-center">
            <a href="{{ route('portal.index') }}" class="flex items-center">
                <img src="{{ asset('images/logo_esl.png') }}" alt="Departemen Ekonomi Sumberdaya & Lingkungan"
                    class="h-10 sm:h-11 w-auto max-w-[240px] sm:max-w-[280px] object-contain">
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 md:px-8 py-6">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-[#DEE2E7] py-4 text-center text-xs text-[#9EA9B9]">
        &copy; {{ date('Y') }} Sistem Inventaris Sarana dan Prasarana ESL, IPB University
    </footer>
</body>

</html>