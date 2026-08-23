<x-site-layout title="Tài khoản của tôi">
    {{-- Dải chào mừng --}}
    <section class="bg-[#9ACE7D]">
        <div class="container-page py-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <span class="w-16 h-16 rounded-full bg-[#2D5A3D] text-white text-2xl font-bold flex items-center justify-center shrink-0">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </span>
                <div>
                    <h1 class="page-title">Xin chào, {{ auth()->user()->username }} 👋</h1>
                    <p class="text-base text-gray-800">Sẵn sàng cho cung đường tiếp theo chưa?</p>
                </div>
            </div>

            <a href="{{ route('tours.index') }}" class="btn-accent">Đặt chuyến mới</a>
        </div>
    </section>

    <div class="container-page py-12 space-y-12">
        {{-- Thống kê --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="card-surface p-7 flex items-center gap-5">
                <span class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#2D5A3D] flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="muted-text">Lượt đặt chỗ</div>
                </div>
            </div>

            <div class="card-surface p-7 flex items-center gap-5">
                <span class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7"><path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['completed'] }}</div>
                    <div class="muted-text">Chuyến đã hoàn thành</div>
                </div>
            </div>

            <div class="card-surface p-7 flex items-center gap-5">
                <span class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-7 h-7"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['reviews'] }}</div>
                    <div class="muted-text">Đánh giá đã viết</div>
                </div>
            </div>
        </div>

        {{-- Chuyến sắp tới + lối tắt --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
            <div>
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-bold text-gray-900">Chuyến sắp tới</h2>
                    <a href="{{ route('bookings.index') }}" class="text-base font-semibold text-[#2D5A3D] hover:underline">
                        Xem tất cả →
                    </a>
                </div>

                @if ($upcoming->isEmpty())
                    <div class="mt-5 card-surface p-10 text-center">
                        <p class="card-title">Chưa có chuyến nào sắp tới</p>
                        <p class="mt-2 muted-text">Chọn một cung đường và giữ chỗ cho cuối tuần này.</p>
                        <a href="{{ route('tours.index') }}" class="btn-primary mt-6">Khám phá tour</a>
                    </div>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach ($upcoming as $booking)
                            @php $tour = $booking->schedule->tour; @endphp
                            <a href="{{ route('bookings.show', $booking) }}"
                               class="card-surface p-5 flex items-center gap-5 hover:shadow-md transition">
                                <div class="w-28 h-20 rounded-xl overflow-hidden bg-gray-200 shrink-0">
                                    @if ($tour->coverImageUrl())
                                        <img src="{{ $tour->coverImageUrl() }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="card-title uppercase truncate">TRAIL BUS – {{ $tour->title }}</div>
                                    <div class="mt-1 muted-text">
                                        Khởi hành {{ $booking->schedule->departure_date->format('d/m/Y') }}
                                        &middot; Vé "{{ $booking->ticketType?->name }}"
                                    </div>
                                </div>

                                @php
                                    $daysLeft = (int) now()->startOfDay()->diffInDays($booking->schedule->departure_date, false);
                                @endphp
                                <div class="text-right shrink-0">
                                    @if ($daysLeft > 0)
                                        <div class="text-2xl font-bold text-[#2D5A3D]">{{ $daysLeft }}</div>
                                        <div class="text-sm text-gray-400">ngày nữa</div>
                                    @else
                                        <div class="text-xl font-bold text-[#2D5A3D]">Hôm nay</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="card-surface p-7">
                <h2 class="card-title">Lối tắt</h2>
                <div class="mt-4 space-y-1">
                    <a href="{{ route('bookings.index') }}" class="flex items-center gap-3 py-3 text-base text-gray-700 hover:text-[#2D5A3D]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5 shrink-0"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4" stroke-linecap="round"/></svg>
                        Lịch sử đặt tour
                    </a>
                    <a href="{{ route('reviews.index') }}" class="flex items-center gap-3 py-3 text-base text-gray-700 hover:text-[#2D5A3D] border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5 shrink-0"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/></svg>
                        Đánh giá chuyến đi
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 py-3 text-base text-gray-700 hover:text-[#2D5A3D] border-t">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5 shrink-0"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/></svg>
                        Cài đặt tài khoản
                    </a>
                </div>
            </div>
        </div>

        {{-- Gợi ý --}}
        @if ($suggested->isNotEmpty())
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Có thể bạn sẽ thích</h2>
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($suggested as $tour)
                        <x-tours.card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-site-layout>
