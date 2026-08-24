<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trung tâm Điều khiển Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stat Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tổng Doanh thu -->
                <a href="{{ route('admin.revenue.index') }}" class="block bg-indigo-50 dark:bg-indigo-900/30 overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-800/50 text-indigo-700 dark:text-indigo-300 mr-4">
                            💵
                        </div>
                        <div>
                            <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Tổng doanh thu</p>
                            <p class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
                            <span class="text-xs text-indigo-600 dark:text-indigo-400 underline">Xem chi tiết báo cáo &rarr;</span>
                        </div>
                    </div>
                </a>

                <!-- Tổng Đơn đặt tour -->
                <a href="{{ route('admin.bookings.index') }}" class="block bg-blue-50 dark:bg-blue-900/30 overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-700 dark:text-blue-300 mr-4">
                            🎫
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Tổng đơn đặt tour</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($totalBookings) }}</p>
                            @if($pendingBookingsCount > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-200 text-yellow-900 rounded-full">⚠️ {{ $pendingBookingsCount }} đơn chờ duyệt</span>
                            @else
                                <span class="text-xs text-blue-600 dark:text-blue-400">Không có đơn chờ</span>
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Tour Du Lịch -->
                <a href="{{ route('admin.tours.index') }}" class="block bg-emerald-50 dark:bg-emerald-900/30 overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 dark:bg-emerald-800/50 text-emerald-700 dark:text-emerald-300 mr-4">
                            ⛰️
                        </div>
                        <div>
                            <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Tour đang hoạt động</p>
                            <p class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ number_format($activeToursCount) }}</p>
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 underline">Quản lý tour &rarr;</span>
                        </div>
                    </div>
                </a>

                <!-- Tổng Số Người Dùng -->
                <a href="{{ route('admin.users.index') }}" class="block bg-purple-50 dark:bg-purple-900/30 overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-800/50 text-purple-700 dark:text-purple-300 mr-4">
                            👥
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-700 dark:text-purple-300">Tổng người dùng</p>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($totalUsersCount) }}</p>
                            <span class="text-xs text-purple-600 dark:text-purple-400 underline">Quản lý người dùng &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Two Columns Layout for Latest Pending Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Đơn Đặt Tour Mới Nhất -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                🎫 Đơn đặt tour mới nhất
                            </h3>
                            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Xem tất cả &rarr;
                            </a>
                        </div>

                        @if($latestBookings->isEmpty())
                            <p class="text-sm text-gray-500 py-4 text-center">Chưa có đơn đặt tour nào.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 uppercase">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Mã đơn</th>
                                            <th class="px-3 py-2 text-left">Khách</th>
                                            <th class="px-3 py-2 text-left">Tổng tiền</th>
                                            <th class="px-3 py-2 text-left">Trạng thái</th>
                                            <th class="px-3 py-2 text-right">Xử lý</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($latestBookings as $booking)
                                            <tr>
                                                <td class="px-3 py-3 font-medium">#BK-{{ $booking->booking_id }}</td>
                                                <td class="px-3 py-3">{{ $booking->user->username ?? 'N/A' }}</td>
                                                <td class="px-3 py-3 font-semibold text-emerald-600">{{ number_format($booking->total_amount) }} ₫</td>
                                                <td class="px-3 py-3">
                                                    @if($booking->status === 'pending')
                                                        <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Chờ duyệt</span>
                                                    @elseif($booking->status === 'confirmed')
                                                        <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Đã xác nhận</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">{{ $booking->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3 text-right">
                                                    @if($booking->status === 'pending')
                                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Duyệt đơn này?');">
                                                            @csrf
                                                            <button type="submit" class="text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700 font-semibold">Duyệt</button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-xs text-indigo-600 hover:underline">Chi tiết</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Đánh Giá Mới Chờ Duyệt -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                ⭐ Đánh giá chờ duyệt
                            </h3>
                            <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                                Xem tất cả &rarr;
                            </a>
                        </div>

                        @if($latestPendingReviews->isEmpty())
                            <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-2xl mb-1">🎉</p>
                                <p class="text-sm font-medium">Hiện không có bài đánh giá nào chờ duyệt!</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($latestPendingReviews as $review)
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 text-sm font-medium">
                                                <span>{{ $review->user->username ?? 'N/A' }}</span>
                                                <span class="text-amber-500">★ {{ $review->score }}/5</span>
                                                <span class="text-xs text-gray-400">đánh giá {{ $review->tour->title ?? 'Tour' }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">"{{ $review->content }}"</p>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700">
                                                    ✓ Duyệt
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700">
                                                    ✕ Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
