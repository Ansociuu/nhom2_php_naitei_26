<x-site-layout title="Thanh toán đơn đặt tour">
    <div class="container-narrow max-w-[900px] py-8">
        <a href="{{ route('bookings.show', $booking) }}" class="text-base text-gray-500 hover:text-[#2D5A3D]">
            ← Quay lại đơn đặt tour
        </a>

        <div class="mt-4 card-surface overflow-hidden"
             x-data="{
                 txn: '{{ $payment->gateway_txn_id }}',
                 secondsLeft: {{ max(0, (int) now()->diffInSeconds($payment->expire_at, false)) }},
                 status: 'pending',
                 pollingInterval: null,
                 timerInterval: null,
                 init() {
                     this.timerInterval = setInterval(() => {
                         if (this.secondsLeft > 0) {
                             this.secondsLeft--;
                         } else {
                             this.status = 'expired';
                             clearInterval(this.timerInterval);
                             clearInterval(this.pollingInterval);
                         }
                     }, 1000);

                     this.pollingInterval = setInterval(() => this.checkStatus(), 2000);
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
                             setTimeout(() => window.location.href = data.redirect_url, 1200);
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

            {{-- Thanh toán thành công --}}
            <div x-show="status === 'success'" x-cloak x-transition class="p-14 text-center">
                <div class="mx-auto w-20 h-20 rounded-full bg-emerald-50 text-[#2D5A3D] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-10 h-10">
                        <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-gray-900">Thanh toán thành công!</h2>
                <p class="mt-2 muted-text">Giao dịch đã hoàn tất. Đang chuyển đến chi tiết đơn đặt...</p>
            </div>

            {{-- Hết hạn --}}
            <div x-show="status === 'expired'" x-cloak x-transition class="p-14 text-center">
                <div class="mx-auto w-20 h-20 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-10 h-10">
                        <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2 class="mt-5 text-2xl font-bold text-gray-900">Mã thanh toán đã hết hạn</h2>
                <p class="mt-2 muted-text">Thời gian chờ thanh toán đã kết thúc. Vui lòng tạo mã mới để tiếp tục.</p>
                <a href="{{ route('bookings.pay', $booking) }}" class="btn-primary mt-6">Tạo mã thanh toán mới</a>
            </div>

            {{-- Đang chờ thanh toán --}}
            <div x-show="status === 'pending'">
                <div class="bg-[#9ACE7D] px-7 py-5 text-center">
                    <div class="text-sm font-semibold text-gray-800">Quét mã QR để thanh toán</div>
                    <div class="mt-1 text-[32px] leading-tight font-bold text-black">
                        {{ number_format((float) $booking->total_amount) }} ₫
                    </div>
                </div>

                <div class="p-7 grid grid-cols-1 md:grid-cols-[300px_1fr] gap-8 items-start">
                    {{-- QR --}}
                    <div class="text-center">
                        <div class="inline-block bg-white p-4 rounded-2xl border-2 border-gray-200">
                            <img src="{{ $qrCodeUrl }}" alt="Mã QR thanh toán"
                                 class="w-56 h-56 object-contain rounded-lg">
                        </div>

                        <div class="mt-4 inline-flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Đang chờ thanh toán...
                        </div>

                        <div class="mt-2 text-sm text-gray-500">
                            Hết hạn sau
                            <span class="font-mono font-bold text-red-600 text-base" x-text="formattedTime"></span>
                        </div>
                    </div>

                    {{-- Thông tin đơn --}}
                    <div>
                        <h2 class="card-title">Đơn đặt #{{ $booking->booking_id }}</h2>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5 text-base">
                            <div>
                                <div class="text-gray-400">Tour</div>
                                <div class="font-semibold">{{ $booking->schedule->tour->title }}</div>
                            </div>
                            <div>
                                <div class="text-gray-400">Ngày khởi hành</div>
                                <div class="font-semibold">{{ $booking->schedule->departure_date->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-400">Số hành khách</div>
                                <div class="font-semibold">{{ $booking->num_adults + $booking->num_children }} người</div>
                            </div>
                            <div>
                                <div class="text-gray-400">Mã giao dịch</div>
                                <div class="font-mono text-sm">{{ $payment->gateway_txn_id }}</div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900">
                            <p class="font-semibold">Cách thanh toán</p>
                            <ol class="mt-2 list-decimal list-inside space-y-1">
                                <li>Mở ứng dụng ngân hàng hoặc máy ảnh trên điện thoại</li>
                                <li>Quét mã QR bên cạnh</li>
                                <li>Xác nhận — trang này sẽ tự chuyển khi thanh toán xong</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
