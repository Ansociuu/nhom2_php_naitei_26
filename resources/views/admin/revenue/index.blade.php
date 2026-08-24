<x-admin-layout title="Báo cáo Doanh thu">
    <div>
            
            <!-- Bộ lọc thời gian -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.revenue.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="from" class="block text-sm font-medium text-gray-700">Từ ngày</label>
                        <input type="date" name="from" id="from" value="{{ request('from', $from->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700">Đến ngày</label>
                        <input type="date" name="to" id="to" value="{{ request('to', $to->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 focus:border-[#2D5A3D]:border-[#2D5A3D] focus:ring-[#2D5A3D]:ring-[#2D5A3D] rounded-md shadow-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2D5A3D] focus:ring-offset-2:ring-offset-gray-800 transition ease-in-out duration-150">
                            Lọc
                        </button>
                        <a href="{{ route('admin.revenue.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Xóa</a>
                    </div>
                </form>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tổng doanh thu -->
                <div class="bg-emerald-50 overflow-hidden shadow sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-50 text-[#2D5A3D] mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[#2D5A3D]">Tổng doanh thu</p>
                            <p class="text-2xl font-bold text-[#2D5A3D]">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</p>
                        </div>
                    </div>
                </div>

                <!-- Tổng đơn đặt -->
                <div class="bg-blue-50 overflow-hidden shadow sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-700 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-700">Tổng đơn đặt</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($totalBookings) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tỷ lệ xác nhận -->
                <div class="bg-green-50 overflow-hidden shadow sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-700 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700">Tỷ lệ xác nhận</p>
                            <p class="text-2xl font-bold text-green-900">
                                {{ $totalBookings > 0 ? round(($confirmedBookings / $totalBookings) * 100, 1) : 0 }}%
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Giá trị TB/đơn -->
                <div class="bg-purple-50 overflow-hidden shadow sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-50 text-[#2D5A3D] mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[#2D5A3D]">Giá trị TB/đơn</p>
                            <p class="text-2xl font-bold text-[#2D5A3D]">{{ number_format($avgBookingValue, 0, ',', '.') }} VNĐ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Xu hướng theo tháng và Top Tour -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Xu hướng theo tháng -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Xu hướng doanh thu (6 tháng qua)</h3>
                        @if(empty($monthlyTrend))
                            <p class="text-gray-500">Chưa có dữ liệu.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($monthlyTrend as $trend)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium">{{ $trend['month'] }}</span>
                                            <span class="text-gray-600">{{ number_format($trend['revenue'], 0, ',', '.') }} VNĐ</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            @php $width = $maxMonthlyRevenue > 0 ? ($trend['revenue'] / $maxMonthlyRevenue) * 100 : 0; @endphp
                                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $width }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Doanh thu theo Tour -->
                <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Top Tour doanh thu cao</h3>
                        @if($topTours->isEmpty())
                            <p class="text-gray-500">Chưa có dữ liệu doanh thu.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Tour</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số đơn</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doanh thu</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($topTours as $index => $tour)
                                            <tr>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                                    {{ Str::limit($tour->title, 40) }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $tour->booking_count }}
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                                                    {{ number_format($tour->total_revenue, 0, ',', '.') }} đ
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Giao dịch gần đây -->
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Giao dịch gần đây</h3>
                    @if($recentTransactions->isEmpty())
                        <p class="text-gray-500">Chưa có giao dịch nào.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã GD</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tour</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ Str::limit($transaction->gateway_txn_id, 15) ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->booking->user->username ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ Str::limit($transaction->booking->schedule->tour->title ?? 'N/A', 30) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ number_format($transaction->amount, 0, ',', '.') }} đ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ optional($transaction->paid_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
