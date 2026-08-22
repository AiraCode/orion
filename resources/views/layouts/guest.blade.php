<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ORION') }} - Tycoon Simulator</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 h-screen w-screen overflow-hidden bg-[#f0f4fc]">
        <div class="h-full flex items-center justify-center relative">
            <div class="relative w-full max-w-sm mx-4">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <div class="inline-flex w-16 h-16 rounded-2xl items-center justify-center bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 shadow-xl shadow-blue-500/30 mb-3 animate-float">
                        <span class="text-white font-extrabold text-2xl font-heading">O</span>
                    </div>
                    <div class="text-3xl font-extrabold tracking-wider bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent font-heading">ORION</div>
                    <div class="text-xs font-bold mt-1 tracking-widest text-slate-400 uppercase">Tycoon Business Simulator</div>
                </div>

                <!-- Login card -->
                <div class="game-card p-6 space-y-4 shadow-xl">
                    {{ $slot }}
                </div>

                <!-- Version badge -->
                <div class="text-center mt-4 text-xs text-slate-400 font-semibold">
                    ORION v4.2 &middot; Business Simulation Game Edition
                </div>
            </div>
        </div>
    </body>
</html>
