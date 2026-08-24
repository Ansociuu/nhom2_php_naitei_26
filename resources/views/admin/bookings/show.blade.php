<x-admin-layout title="Chi tiết đơn #BK-{{ $booking->booking_id }}">
    <div>

            <!-- Status Banner -->
            <div class="p-4 rounded-lg @if($booking->status == 'pending') bg-yellow-100 text-yellow-800 @elseif($booking->status == 'confirmed') bg-green-100 text-green-800 @elseif($booking->status == 'completed') bg-blue-100 text-blue-800 @elseif($booking->status == 'cancelled') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif font-semibold text-lg flex items-center justify-between shadow-sm">
                <span>
                    Trạng thái: 
                    @if($booking->status == 'pending')
                        Chờ xử lý
                    @elseif($booking->status == 'confirmed')
                        Đã xác nhận
                    @elseif($booking->status == 'completed')
                        Đã hoàn thành
                    @elseif($booking->status == 'cancelled')
                        Đã hủy
                    @else
                        {{ $booking->status }}
                    @endif
                </span>
                
                @if($booking->status == 'cancelled' && $booking->cancelled_at)
                    <span class="text-sm font-normal">Hủy lúc: {{ \Carbon\Carbon::parse($booking->cancelled_at)->format('d/m/Y H:i') }}</span>
                @elseif($booking->status == 'confirmed' && $booking->confirmed_at)
                    <span class="text-sm font-normal">Xác nhận lúc: {{ \Carbon\Carbon::parse($booking->confirmed_at)->format('d/m/Y H:i') }}</span>
                @endif
            </div>

            <!-- Booking Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Thông tin Đặt tour</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Mã đơn:</p>
                            <p class="font-medium">#BK-{{ $booking->booking_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ngày đặt:</p>
                            <p class="font-medium">{{ $booking->booked_at ? \Carbon\Carbon::parse($booking->booked_at)->format('d/m/Y H:i') : \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Khách hàng:</p>
                            <p class="font-medium">{{ $booking->user->username ?? 'N/A' }} <span class="text-sm text-gray-500">({{ $booking->user->email ?? 'N/A' }})</span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tour:</p>
                            <p class="font-medium">{{ $booking->schedule->tour->title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ngày khởi hành:</p>
                            <p class="font-medium">{{ $booking->schedule && $booking->schedule->departure_date ? \Carbon\Carbon::parse($booking->schedule->departure_date)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nơi khởi hành:</p>
                            <p class="font-medium">{{ $booking->schedule->tour->departure_location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Thời gian:</p>
                            <p class="font-medium">{{ $booking->schedule->tour->duration_days ?? 'N/A' }} ngày</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passengers Details Table -->
            @if($booking->details && $booking->details->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-semibold">Danh sách hành khách ({{ $booking->details->count() }})</h3>
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-[#2D5A3D] hover:bg-emerald-100 rounded-md text-xs font-semibold transition">
                            ✏️ Sửa thông tin hành khách
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ và tên</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tuổi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Giá vé</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($booking->details as $index => $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->age }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($detail->age >= 12 || $detail->price == $booking->unit_price)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Người lớn</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-50 text-[#2D5A3D]">Trẻ em</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ number_format($detail->price) }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Price Breakdown Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Chi tiết thanh toán</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Người lớn ({{ $booking->num_adults }} x {{ number_format($booking->unit_price) }} ₫)</span>
                            <span class="font-medium">{{ number_format($booking->num_adults * $booking->unit_price) }} ₫</span>
                        </div>
                        @if($booking->num_children > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trẻ em ({{ $booking->num_children }} x {{ number_format($booking->unit_price * 0.7) }} ₫)</span>
                            <span class="font-medium">{{ number_format($booking->num_children * ($booking->unit_price * 0.7)) }} ₫</span>
                        </div>
                        @endif
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="font-semibold text-lg">Tổng tiền</span>
                            <span class="font-bold text-lg text-emerald-600">{{ number_format($booking->total_amount) }} ₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Info Card -->
            @if($booking->payment)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4 border-b pb-2">Thông tin giao dịch</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Phương thức:</p>
                            <p class="font-medium uppercase">{{ $booking->payment->gateway }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái:</p>
                            <p class="font-medium">
                                @if($booking->payment->status == 'success')
                                    <span class="text-green-600">Thành công</span>
                                @elseif($booking->payment->status == 'pending')
                                    <span class="text-yellow-600">Đang xử lý</span>
                                @elseif($booking->payment->status == 'failed')
                                    <span class="text-red-600">Thất bại</span>
                                @elseif($booking->payment->status == 'refunded')
                                    <span class="text-[#2D5A3D]">Đã hoàn tiền</span>
                                @else
                                    {{ $booking->payment->status }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Mã giao dịch:</p>
                            <p class="font-medium">{{ $booking->payment->gateway_txn_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Thời gian thanh toán:</p>
                            <p class="font-medium">{{ $booking->payment->paid_at ? \Carbon\Carbon::parse($booking->payment->paid_at)->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                        @if($booking->payment->expire_at && $booking->payment->status == 'pending')
                        <div>
                            <p class="text-sm text-gray-500">Hạn thanh toán:</p>
                            <p class="font-medium text-red-600">{{ \Carbon\Carbon::parse($booking->payment->expire_at)->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Admin Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-wrap gap-4 items-center justify-between">
                    <div>
                        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            &larr; Quay lại danh sách
                        </a>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 items-center">
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="inline-flex items-center px-4 py-2 bg-[#2D6A2D] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#245524] active:bg-[#1c421c] focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2 transition ease-in-out duration-150">
                            ✏️ Sửa thông tin
                        </a>

                        @if($booking->status === 'pending')
                            <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" onsubmit="return confirm('Xác nhận duyệt đơn đặt tour này?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    ✓ Xác nhận đơn
                                </button>
                            </form>
                        @endif

                        @if($booking->payment && $booking->payment->status === 'success')
                            <form action="{{ route('admin.bookings.refund', $booking) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn HOÀN TIỀN cho đơn này? Trạng thái thanh toán sẽ đổi sang Refunded và đơn đặt tour sẽ bị hủy.');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#2D6A2D] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#245524] focus:bg-[#245524] active:bg-[#1c421c] focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2 transition ease-in-out duration-150">
                                    💰 Hoàn tiền (Refund)
                                </button>
                            </form>
                        @endif

                        @if($booking->status === 'confirmed')
                            <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2 transition ease-in-out duration-150">
                                    ✓ Hoàn thành
                                </button>
                            </form>
                        @endif

                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn đặt tour này?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    ✕ Hủy đơn
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
