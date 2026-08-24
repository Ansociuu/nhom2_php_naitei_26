@php
    $navGroups = [
        [
            'label' => 'Tổng quan',
            'items' => [
                ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'admin.revenue.index', 'active' => 'admin.revenue.*', 'label' => 'Doanh thu', 'icon' => 'revenue'],
            ],
        ],
        [
            'label' => 'Kinh doanh',
            'items' => [
                ['route' => 'admin.bookings.index', 'active' => 'admin.bookings.*', 'label' => 'Đơn đặt chỗ', 'icon' => 'booking'],
                ['route' => 'admin.tours.index', 'active' => 'admin.tours.*', 'label' => 'Tour', 'icon' => 'tour'],
                ['route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'label' => 'Danh mục', 'icon' => 'category'],
            ],
        ],
        [
            'label' => 'Cộng đồng',
            'items' => [
                ['route' => 'admin.reviews.index', 'active' => 'admin.reviews.*', 'label' => 'Đánh giá', 'icon' => 'review'],
                ['route' => 'admin.users.index', 'active' => 'admin.users.*', 'label' => 'Người dùng', 'icon' => 'user'],
            ],
        ],
    ];

    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'revenue' => '<path d="M3 3v18h18" stroke-linecap="round"/><path d="m7 14 4-4 3 3 5-6" stroke-linecap="round" stroke-linejoin="round"/>',
        'booking' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/>',
        'tour' => '<path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'category' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'review' => '<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/>',
    ];
@endphp

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Quản trị' }} — Sun* Booking Tour</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=livvic:400,500,600,700,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-900" style="font-family: 'Livvic', sans-serif;">
        <div class="min-h-screen flex" x-data="{ sidebar: false }">
            {{-- Sidebar --}}
            <aside class="fixed lg:static inset-y-0 left-0 z-40 w-72 shrink-0 bg-[#1B2A20] text-gray-300 flex flex-col
                          transition-transform lg:translate-x-0"
                   :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
                <div class="h-[72px] flex items-center gap-3 px-6 border-b border-white/10">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-lg bg-[#F4D03F] text-[#1B2A20] font-extrabold flex items-center justify-center">S</span>
                        <span class="text-white font-bold leading-tight">
                            Sun* Admin
                            <span class="block text-xs font-normal text-gray-400">Booking Tour</span>
                        </span>
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto py-5">
                    @foreach ($navGroups as $group)
                        <div class="px-6 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            {{ $group['label'] }}
                        </div>

                        @foreach ($group['items'] as $item)
                            @continue(! Route::has($item['route']))
                            @php $isActive = request()->routeIs($item['active']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="relative flex items-center gap-3 px-6 py-2.5 text-[15px] transition
                                      {{ $isActive ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                                @if ($isActive)
                                    <span class="absolute left-0 inset-y-1 w-1 rounded-r bg-[#F4D03F]"></span>
                                @endif
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.6" class="w-5 h-5 shrink-0">
                                    {!! $icons[$item['icon']] !!}
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    @endforeach
                </nav>

                <div class="p-4 border-t border-white/10">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-white/5 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l-5-5 5-5M11 12h10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Về trang người dùng
                    </a>
                </div>
            </aside>

            {{-- Lớp phủ khi mở sidebar trên mobile --}}
            <div x-show="sidebar" x-cloak @click="sidebar = false"
                 class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

            {{-- Nội dung --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="sticky top-0 z-20 h-[72px] bg-white border-b flex items-center justify-between gap-4 px-6">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button" @click="sidebar = true" class="lg:hidden text-gray-500 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-xl font-bold truncate">{{ $title ?? 'Quản trị' }}</h1>
                            @isset($subtitle)
                                <p class="text-sm text-gray-500 truncate">{{ $subtitle }}</p>
                            @endisset
                        </div>
                    </div>

                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                                class="inline-flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full border border-gray-200 hover:bg-gray-50">
                            <span class="w-8 h-8 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </span>
                            <span class="hidden sm:block text-sm font-semibold max-w-[140px] truncate">{{ auth()->user()->username }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 text-gray-400">
                                <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border py-1 z-40">
                            <div class="px-4 py-3 border-b">
                                <div class="font-semibold truncate">{{ auth()->user()->username }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Hồ sơ cá nhân
                            </a>
                            <div class="border-t"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                @if (session('status'))
                    <div class="mx-6 mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-[#2D5A3D] px-5 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mx-6 mt-5 rounded-xl bg-emerald-50 border border-emerald-200 text-[#2D5A3D] px-5 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-6 mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-6 mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-3">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
