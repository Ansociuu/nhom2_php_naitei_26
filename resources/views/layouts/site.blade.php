<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Sun* Booking Tour') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=livvic:400,500,600,700,900|caveat:700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900" style="font-family: 'Livvic', sans-serif;">
        <header class="bg-white border-b sticky top-0 z-30">
            <div class="max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 h-[100px] flex items-center justify-between gap-8">
                <a href="{{ route('home') }}" class="flex items-center shrink-0">
                    <img src="{{ asset('images/marketing/logo.png') }}" alt="Sun* BookingTour" class="h-14 w-auto">
                </a>

                <nav class="hidden md:flex items-center gap-10 text-lg font-semibold text-gray-900">
                    <x-nav-mega-menu label="Miền Nam" region="mien_nam"
                                     :tours="($navToursByRegion ?? collect())->get('mien_nam', collect())" />
                    <x-nav-mega-menu label="Miền Bắc" region="mien_bac"
                                     :tours="($navToursByRegion ?? collect())->get('mien_bac', collect())" />
                    <a href="{{ route('tours.index') }}" class="hover:text-[#2D5A3D]">
                        Thẻ Chinh Phục
                    </a>
                    <a href="{{ route('tours.index') }}" class="hover:text-[#2D5A3D]">
                        About Us
                    </a>
                </nav>

                <div class="flex items-center gap-5 text-base shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7 text-gray-700">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    @auth
                        <a href="{{ route('bookings.index') }}" class="hidden lg:inline text-gray-700 hover:text-[#2D5A3D] font-semibold">
                            Lịch sử đặt tour
                        </a>

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button" @click="open = !open"
                                    class="inline-flex items-center gap-2.5 pl-2 pr-4 py-2 rounded-full border border-gray-200 hover:border-gray-300 hover:bg-gray-50 max-w-[220px]">
                                <span class="w-8 h-8 shrink-0 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center">
                                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                </span>
                                <span class="font-semibold text-gray-800 truncate">{{ auth()->user()->username }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 text-gray-400 shrink-0">
                                    <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition
                                 class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg border py-1 z-40">
                                <div class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 truncate">{{ auth()->user()->username }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="border-t"></div>
                                <a href="{{ route('reviews.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/></svg>
                                    Đánh giá chuyến đi
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                                    Cài đặt tài khoản
                                </a>
                                <div class="border-t"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-4 h-4"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 17l5-5-5-5M21 12H9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#2D6A2D] text-white text-base font-semibold rounded-full hover:bg-[#245524]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6" stroke-linecap="round"/></svg>
                            Đăng nhập
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <main>
            {{ $slot }}
        </main>

        <footer class="bg-[#0F0F0F] text-gray-400 mt-16">
            <div class="max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 py-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 text-base">
                <div class="lg:col-span-2">
                    <p class="font-bold text-[#F4A800]">SUN* BOOKING TOUR</p>
                    <p class="mt-3">Cuối tuần này, bạn đã có vé chưa? Giữ chỗ ngay để không bỏ lỡ hành trình xanh khám phá cuối tuần này nhé!</p>
                    <p class="mt-4">53 Đường 2, KĐT Vạn Phúc, P. Hiệp Bình Phước, TP. Thủ Đức, TP. HCM</p>
                    <p class="mt-1">hello@sunbookingtour.vn</p>
                    <p class="mt-1">0933 22 78 78 (Nhánh 3)</p>

                    <p class="mt-4 text-white">Kết nối với chúng tôi</p>
                    <div class="mt-2 flex gap-3 text-gray-400">
                        <span>Facebook</span>
                        <span>Instagram</span>
                        <span>Zalo</span>
                    </div>
                </div>

                <div>
                    <p class="font-semibold text-white">Về Sun* Booking Tour</p>
                    <ul class="mt-3 space-y-2">
                        <li>Giới thiệu Sun* Booking Tour</li>
                        <li>Cộng tác viên</li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold text-white">Chính sách đặt chỗ</p>
                    <ul class="mt-3 space-y-2">
                        <li>Chính sách bảo mật</li>
                        <li>Điều kiện sử dụng Website</li>
                        <li>Chính sách hoàn hủy</li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold text-white">Danh sách trail</p>
                    <ul class="mt-3 space-y-2">
                        <li><a href="{{ route('tours.index', ['region' => 'mien_nam']) }}" class="hover:text-white">Miền Nam</a></li>
                        <li><a href="{{ route('tours.index', ['region' => 'mien_bac']) }}" class="hover:text-white">Miền Bắc</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-500">
                COMPATIBLE WITH STRAVA
            </div>
        </footer>
    </body>
</html>
