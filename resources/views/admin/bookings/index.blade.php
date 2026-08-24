<x-admin-layout title="Quản lý Đơn đặt tour">
    <div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filter form -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã đơn hoặc email..." class="w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                            </div>
                            <div class="w-full md:w-64">
                                <select name="status" class="w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Lọc
                                </button>
                                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                    Xóa
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tour</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày khởi hành</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số khách</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #BK-{{ $booking->booking_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>{{ $booking->user->username ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ Str::limit($booking->schedule->tour->title ?? 'N/A', 30) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->schedule && $booking->schedule->departure_date ? \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->num_adults + $booking->num_children }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">
                                            {{ number_format($booking->total_amount) }} ₫
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($booking->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ xử lý</span>
                                            @elseif($booking->status == 'confirmed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã xác nhận</span>
                                            @elseif($booking->status == 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Đã hoàn thành</span>
                                            @elseif($booking->status == 'cancelled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Đã hủy</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $booking->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($booking->payment)
                                                @if($booking->payment->status == 'success')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã TT</span>
                                                @elseif($booking->payment->status == 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Đang TT</span>
                                                @elseif($booking->payment->status == 'refunded')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-50 text-[#2D5A3D]">Hoàn tiền</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Lỗi TT</span>
                                                @endif
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Chưa TT</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            @if($booking->status === 'pending')
                                                <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Xác nhận duyệt đơn này?');">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-900 font-semibold">Duyệt</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-blue-600 hover:text-blue-900">Sửa</a>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-[#2D5A3D] hover:text-[#2D5A3D]:text-[#2D5A3D]">Chi tiết</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            Không có đơn đặt tour nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
