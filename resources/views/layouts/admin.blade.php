<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin' }} - {{ config('app.name', 'Sun Booking Tour') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div class="min-h-screen flex">
            <aside class="w-64 shrink-0 bg-gray-900 text-gray-200 flex flex-col">
                <div class="h-16 flex items-center px-6 text-lg font-semibold text-white border-b border-gray-800">
                    Sun* Admin
                </div>

                <nav class="flex-1 py-4 space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="block px-6 py-2.5 text-sm {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="block px-6 py-2.5 text-sm {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Quản lý người dùng
                    </a>
                </nav>
            </aside>

            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                    <h1 class="text-lg font-semibold">{{ $title ?? 'Admin' }}</h1>

                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->username }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-800">Đăng xuất</button>
                        </form>
                    </div>
                </header>

                @if (session('status'))
                    <div class="mx-6 mt-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-6 mt-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2">
                        {{ session('error') }}
                    </div>
                @endif

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
