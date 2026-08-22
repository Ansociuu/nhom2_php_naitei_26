<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chi tiết đặt tour') }} #BK-{{ $booking->booking_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('status') === 'booking-created')
                <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-200">
                    ✅ {{ __('Đặt tour thành công! Vui lòng thanh toán để xác nhận.') }}
                </div>
            @elseif (session('status') === 'payment-success')
                <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-200">
                    ✅ {{ __('Thanh toán thành công! Đơn đặt tour đã được xác nhận.') }}
                </div>
            @elseif (session('status') === 'booking-cancelled')
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg text-sm text-yellow-800 dark:text-yellow-200">
                    ⚠️ {{ __('Đơn đặt tour đã được hủy.') }}
                </div>
            @endif

            {{-- Status Banner --}}
            <div class="p-4 rounded-lg text-center
                @switch($booking->status)
                    @case('pending')    bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 @break
                    @case('confirmed')  bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 @break
                    @case('cancelled')  bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 @break
                    @case('completed')  bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 @break
                @endswitch">
                <span class="text-lg font-semibold">
                    @switch($booking->status)
                        @case('pending')    🕒 {{ __('Chờ thanh toán') }} @break
                        @case('confirmed')  ✅ {{ __('Đã xác nhận') }} @break
                        @case('cancelled')  ❌ {{ __('Đã hủy') }} @break
                        @case('completed')  🎉 {{ __('Hoàn thành') }} @break
                    @endswitch
                </span>
            </div>

            {{-- Booking Details --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Thông tin đặt tour') }}</h3>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Mã đơn') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">#BK-{{ $booking->booking_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Ngày đặt') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->booked_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Tour') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->tour->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Ngày khởi hành') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->departure_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Nơi khởi hành') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->tour->departure_location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Thời gian') }}</dt>
                        <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->tour->duration_days }} {{ __('ngày') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Passenger Details (booking_details) --}}
            @if ($booking->details->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Danh sách hành khách') }} ({{ $booking->details->count() }})
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-750">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">#</th>
                                    <th scope="col" class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">{{ __('Họ và tên') }}</th>
                                    <th scope="col" class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">{{ __('Tuổi') }}</th>
                                    <th scope="col" class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">{{ __('Phân loại') }}</th>
                                    <th scope="col" class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase text-xs">{{ __('Giá vé') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($booking->details as $index => $detail)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $detail->name }}</td>
                                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $detail->age }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($detail->age >= 12)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                    {{ __('Người lớn') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                    {{ __('Trẻ em') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">
                                            {{ number_format($detail->price, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Price Breakdown --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Chi tiết giá') }}</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>{{ __('Người lớn') }} ({{ $booking->num_adults }} × {{ number_format($booking->unit_price, 0, ',', '.') }} ₫)</span>
                        <span>{{ number_format($booking->num_adults * $booking->unit_price, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="flex justify-between text-gray-700 dark:text-gray-300">
                        <span>{{ __('Trẻ em') }} ({{ $booking->num_children }} × {{ number_format($booking->unit_price * 0.5, 0, ',', '.') }} ₫)</span>
                        <span>{{ number_format($booking->num_children * $booking->unit_price * 0.5, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg text-gray-900 dark:text-gray-100 border-t border-gray-200 dark:border-gray-700 pt-3">
                        <span>{{ __('Tổng cộng') }}</span>
                        <span class="text-indigo-600 dark:text-indigo-400">{{ number_format($booking->total_amount, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            @if ($booking->payment)
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Thanh toán') }}</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Phương thức') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($booking->payment->gateway) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">{{ __('Trạng thái') }}</dt>
                            <dd class="mt-1">
                                @if ($booking->payment->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ {{ __('Thành công') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">{{ ucfirst($booking->payment->status) }}</span>
                                @endif
                            </dd>
                        </div>
                        @if ($booking->payment->paid_at)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('Thời gian thanh toán') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->payment->paid_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                        @if ($booking->payment->expire_at)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">{{ __('Hạn thanh toán') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">{{ $booking->payment->expire_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- QR Pay Button --}}
                @if ($booking->status === 'pending')
                    <a href="{{ route('bookings.pay', $booking) }}"
                       class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                        💳 {{ __('Thanh toán ngay (Quét mã QR)') }}
                    </a>
                @endif

                {{-- Cancel Button --}}
                @if (in_array($booking->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="flex-1"
                          x-data
                          @submit.prevent="if (confirm('{{ __('Bạn có chắc muốn hủy đơn đặt tour này?') }}')) $el.submit()">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                            ✕ {{ __('Hủy đặt tour') }}
                        </button>
                    </form>
                @endif

                {{-- Back to list --}}
                <a href="{{ route('bookings.index') }}"
                   class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
                    ← {{ __('Danh sách đặt tour') }}
                </a>
            </div>

            {{-- Cancellation Info --}}
            @if ($booking->status === 'cancelled' && $booking->cancelled_at)
                <div class="text-sm text-gray-500 dark:text-gray-400 text-center">
                    {{ __('Đã hủy lúc') }}: {{ $booking->cancelled_at->format('d/m/Y H:i') }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
