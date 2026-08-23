<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Thanh toán đơn đặt tour') }} #BK-{{ $booking->booking_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-2xl overflow-hidden"
                 x-data="{
                     txn: '{{ $payment->gateway_txn_id }}',
                     secondsLeft: {{ max(0, now()->diffInSeconds($payment->expire_at, false)) }},
                     status: 'pending',
                     pollingInterval: null,
                     timerInterval: null,
                     init() {
                         // Start Countdown Timer
                         this.timerInterval = setInterval(() => {
                             if (this.secondsLeft > 0) {
                                 this.secondsLeft--;
                             } else {
                                 this.status = 'expired';
                                 clearInterval(this.timerInterval);
                                 clearInterval(this.pollingInterval);
                             }
                         }, 1000);

                         // Start Polling Status
                         this.pollingInterval = setInterval(() => {
                             this.checkStatus();
                         }, 2000);
                     },
                     async checkStatus() {
                         if (this.status === 'success' || this.status === 'expired') return;
                         try {
                             const res = await fetch('/payments/' + this.txn + '/status');
                             if (!res.ok) return;
                             const data = await res.json();
                             if (data.status === 'success') {
                                 this.status = 'success';
                                 clearInterval(this.pollingInterval);
                                 clearInterval(this.timerInterval);
                                 setTimeout(() => {
                                     window.location.href = data.redirect_url;
                                 }, 1000);
                             } else if (data.status === 'failed' || data.status === 'expired') {
                                 this.status = 'expired';
                                 clearInterval(this.pollingInterval);
                                 clearInterval(this.timerInterval);
                             }
                         } catch (e) {
                             console.error('Polling error:', e);
                         }
                     },
                     get formattedTime() {
                         const m = Math.floor(this.secondsLeft / 60);
                         const s = this.secondsLeft % 60;
                         return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                     }
                 }">

                {{-- Success Overlay --}}
                <div x-show="status === 'success'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="p-10 text-center space-y-4" style="display: none;">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto">
                        ✓
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('Thanh toán thành công!') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Giao dịch đã hoàn tất. Đang chuyển hướng đến chi tiết đơn đặt...') }}
                    </p>
                </div>

                {{-- Expired Overlay --}}
                <div x-show="status === 'expired'"
                     x-transition:enter="transition ease-out duration-300"
                     class="p-10 text-center space-y-4" style="display: none;">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto">
                        ✕
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('Mã thanh toán đã hết hạn') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Thời gian chờ thanh toán đã kết thúc. Vui lòng tạo lại mã mới để tiếp tục.') }}
                    </p>
                    <div>
                        <a href="{{ route('bookings.pay', $booking) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            🔄 {{ __('Tạo mã thanh toán mới') }}
                        </a>
                    </div>
                </div>

                {{-- Main Checkout Body --}}
                <div x-show="status === 'pending'" class="p-6 sm:p-10">
                    <div class="text-center mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 mb-3">
                            {{ __('Quét mã QR để thanh toán') }}
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                            {{ number_format($booking->total_amount, 0, ',', '.') }} ₫
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">
                            {{ __('Mã giao dịch') }}: {{ $payment->gateway_txn_id }}
                        </p>
                    </div>

                    {{-- QR Code Card --}}
                    <div class="bg-gray-50 dark:bg-gray-750 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 max-w-xs mx-auto text-center shadow-inner">
                        <div class="bg-white p-3 rounded-xl inline-block shadow-sm">
                            <img src="{{ $qrCodeUrl }}"
                                 alt="Payment QR Code"
                                 class="w-56 h-56 mx-auto object-contain rounded-lg" />
                        </div>

                        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ __('Đang chờ thanh toán...') }}</span>
                        </div>

                        <div class="mt-2 text-xs text-gray-400">
                            {{ __('Hết hạn sau:') }} <span class="font-mono font-bold text-red-500" x-text="formattedTime"></span>
                        </div>
                    </div>

                    {{-- Tour Summary --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-sm">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tour') }}</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->tour->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Ngày khởi hành') }}</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->schedule->departure_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Số hành khách') }}</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->num_adults + $booking->num_children }} {{ __('người') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Đơn đặt') }}</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">#BK-{{ $booking->booking_id }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                        <a href="{{ route('bookings.show', $booking) }}" class="hover:underline">
                            {{ __('← Quay lại đơn đặt tour') }}
                        </a>
                        <span>{{ __('Mở ứng dụng máy ảnh trên điện thoại để quét mã') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
