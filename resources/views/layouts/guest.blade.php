<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sun* Booking Tour') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=livvic:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="font-family: 'Livvic', sans-serif;">
        <div class="min-h-screen relative flex flex-col items-center justify-center px-4 py-10">
            <img src="{{ asset('images/marketing/hero-bus.png') }}" alt=""
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/45 to-black/70"></div>

            <div class="relative w-full sm:max-w-[440px]">
                <div class="bg-white/95 backdrop-blur shadow-2xl overflow-hidden rounded-2xl">
                    <div class="pt-8 flex justify-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/marketing/logo.png') }}" alt="Sun* BookingTour" class="h-11 w-auto">
                        </a>
                    </div>

                    {{ $slot }}
                </div>

                <a href="{{ route('home') }}" class="mt-6 flex items-center justify-center gap-2 text-sm text-white/80 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Về trang chủ
                </a>
            </div>
        </div>
    </body>
</html>
