@php
    $defaultTicketId = (string) request('ticket_type_id') !== ''
        ? (int) request('ticket_type_id')
        : ($tour->ticketTypes->firstWhere('is_recommended', true)?->ticket_type_id ?? $tour->ticketTypes->first()?->ticket_type_id);

    $defaultScheduleId = (string) request('schedule_id') !== ''
        ? (int) request('schedule_id')
        : $tour->schedules->firstWhere(fn ($s) => $s->available_slots > 0)?->schedule_id;

    $ticketPrices = $tour->ticketTypes->mapWithKeys(fn ($t) => [$t->ticket_type_id => (float) $t->price]);
    $scheduleSlots = $tour->schedules->mapWithKeys(fn ($s) => [$s->schedule_id => (int) $s->available_slots]);
@endphp

<x-site-layout title="Đặt chỗ">
    <div class="container-narrow max-w-6xl py-12">
        <a href="{{ route('tours.show', $tour) }}" class="text-base text-gray-500 hover:text-[#2D5A3D]">← Quay lại tour</a>
        <h1 class="mt-3 page-title uppercase">Đặt chỗ – Trail Bus {{ $tour->title }}</h1>

        @if ($errors->any())
            <div class="mt-5 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-4">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($tour->schedules->isEmpty() || $tour->ticketTypes->isEmpty())
            <p class="mt-6 text-lg text-gray-500">Tour này hiện chưa có lịch khởi hành hoặc loại vé để đặt chỗ.</p>
        @else
            <form method="POST" action="{{ route('bookings.store', $tour) }}"
                  x-data="bookingForm({
                      ticketId: {{ $defaultTicketId ?? 'null' }},
                      scheduleId: {{ $defaultScheduleId ?? 'null' }},
                      prices: {{ $ticketPrices->toJson() }},
                      slots: {{ $scheduleSlots->toJson() }}
                  })"
                  class="mt-8 grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
                @csrf

                <div class="space-y-6">
                    {{-- Bước 1: ngày khởi hành --}}
                    <section class="card-surface p-6">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#F4D03F] text-gray-900 font-bold flex items-center justify-center shrink-0">1</span>
                            <h2 class="text-xl font-extrabold uppercase">Ngày khởi hành</h2>
                        </div>

                        <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($tour->schedules as $schedule)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="schedule_id" value="{{ $schedule->schedule_id }}"
                                           class="peer sr-only" required
                                           @disabled($schedule->available_slots < 1)
                                           x-model.number="scheduleId">
                                    <div class="rounded-xl border-2 p-4 text-center transition
                                                peer-checked:border-[#2D5A3D] peer-checked:bg-emerald-50
                                                peer-disabled:opacity-40 peer-disabled:cursor-not-allowed
                                                border-gray-200 hover:border-gray-300">
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ $schedule->departure_date->format('d/m') }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $schedule->departure_date->format('Y') }}</div>
                                        <div class="mt-2 text-xs {{ $schedule->available_slots > 0 ? 'text-[#2D5A3D]' : 'text-red-500' }}">
                                            {{ $schedule->available_slots > 0 ? $schedule->available_slots.' chỗ trống' : 'Hết chỗ' }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    {{-- Bước 2: loại vé --}}
                    <section class="card-surface p-6">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#F4D03F] text-gray-900 font-bold flex items-center justify-center shrink-0">2</span>
                            <h2 class="text-xl font-extrabold uppercase">Chọn loại vé</h2>
                        </div>

                        <div class="mt-5 space-y-3">
                            @foreach ($tour->ticketTypes as $ticketType)
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="ticket_type_id" value="{{ $ticketType->ticket_type_id }}"
                                           class="peer sr-only" required x-model.number="ticketId">
                                    <div class="rounded-xl border-2 p-5 transition border-gray-200 hover:border-gray-300
                                                peer-checked:border-[#2D5A3D] peer-checked:bg-emerald-50">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-lg font-extrabold uppercase">Vé "{{ $ticketType->name }}"</span>
                                                    @if ($ticketType->is_recommended)
                                                        <span class="px-2.5 py-0.5 rounded-full bg-gray-900 text-white text-xs font-semibold">★ Phổ biến</span>
                                                    @endif
                                                </div>

                                                @if (!empty($ticketType->features))
                                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-base text-gray-700">
                                                        @foreach ($ticketType->features as $feature)
                                                            <span>• {{ $feature }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if ($ticketType->target_audience)
                                                    <p class="mt-2 text-base text-gray-600">
                                                        <span class="font-semibold">Dành cho:</span> {{ $ticketType->target_audience }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="text-right shrink-0">
                                                @if ($ticketType->original_price && $ticketType->original_price > $ticketType->price)
                                                    <div class="text-sm text-gray-400 line-through">{{ number_format((float) $ticketType->original_price) }}</div>
                                                @endif
                                                <div class="text-xl font-extrabold text-gray-900">{{ number_format((float) $ticketType->price) }}</div>
                                                <div class="text-xs text-gray-500">VND / vé</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    {{-- Bước 3: số vé + thông tin người đi --}}
                    <section class="card-surface p-6">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-[#F4D03F] text-gray-900 font-bold flex items-center justify-center shrink-0">3</span>
                            <h2 class="text-xl font-extrabold uppercase">Số vé &amp; người đi</h2>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-gray-50 border">
                            <div>
                                <div class="font-semibold text-gray-900">Số lượng vé</div>
                                <div class="text-sm text-gray-500">Mỗi vé tương ứng một người đi</div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="button" @click="setCount(passengers.length - 1)"
                                        :disabled="passengers.length <= 1"
                                        class="w-11 h-11 rounded-full border-2 border-gray-300 text-xl font-bold text-gray-700
                                               hover:border-[#2D5A3D] hover:text-[#2D5A3D] disabled:opacity-40 disabled:cursor-not-allowed">−</button>
                                <span class="w-10 text-center text-2xl font-extrabold" x-text="passengers.length"></span>
                                <button type="button" @click="setCount(passengers.length + 1)"
                                        :disabled="passengers.length >= maxSeats()"
                                        class="w-11 h-11 rounded-full border-2 border-gray-300 text-xl font-bold text-gray-700
                                               hover:border-[#2D5A3D] hover:text-[#2D5A3D] disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                            </div>
                        </div>

                        <p class="mt-2 text-sm text-gray-500" x-show="maxSeats() < 99" x-cloak>
                            Chuyến này còn <span class="font-semibold" x-text="maxSeats()"></span> chỗ trống.
                        </p>

                        <div class="mt-5 space-y-4">
                            <template x-for="(passenger, index) in passengers" :key="index">
                                <div class="rounded-xl border p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center"
                                                  x-text="index + 1"></span>
                                            <span class="font-bold text-gray-900">
                                                <span x-show="index === 0">Người đặt (đại diện)</span>
                                                <span x-show="index > 0">Người đi thứ <span x-text="index + 1"></span></span>
                                            </span>
                                        </div>

                                        <button type="button" x-show="passengers.length > 1" @click="removeAt(index)"
                                                class="text-sm text-red-500 hover:text-red-700 hover:underline">Xoá</button>
                                    </div>

                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <label class="form-label">Họ và tên <span class="text-red-500">*</span></label>
                                            <input type="text" :name="`passengers[${index}][full_name]`" x-model="passenger.full_name"
                                                   placeholder="VD: Nguyễn Văn A" required
                                                   class="mt-1.5 form-control">
                                        </div>

                                        <div>
                                            <label class="form-label">Tuổi</label>
                                            <input type="number" :name="`passengers[${index}][age]`" x-model="passenger.age"
                                                   placeholder="VD: 25" min="0" max="120"
                                                   class="mt-1.5 form-control">
                                            <p class="form-hint">Dưới 12 tuổi được tính là trẻ em</p>
                                        </div>

                                        <div>
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="tel" :name="`passengers[${index}][phone]`" x-model="passenger.phone"
                                                   placeholder="VD: 0912 345 678"
                                                   class="mt-1.5 form-control">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-5">
                            <label for="note" class="form-label">Ghi chú (tuỳ chọn)</label>
                            <textarea id="note" name="note" rows="3" placeholder="Yêu cầu đặc biệt, dị ứng thực phẩm, ..."
                                      class="mt-1.5 form-control">{{ old('note') }}</textarea>
                        </div>
                    </section>
                </div>

                {{-- Tóm tắt đơn --}}
                <aside class="lg:sticky lg:top-[116px]">
                    <div class="card-surface overflow-hidden">
                        <div class="bg-gray-900 px-5 py-4">
                            <h2 class="text-white font-extrabold uppercase text-center">Tóm tắt đơn</h2>
                        </div>

                        <div class="p-5 space-y-3 text-base">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Tour</span>
                                <span class="font-semibold text-right">{{ $tour->title }}</span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Ngày đi</span>
                                <span class="font-semibold text-right" x-text="scheduleLabel()"></span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Loại vé</span>
                                <span class="font-semibold text-right" x-text="ticketLabel()"></span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Số vé</span>
                                <span class="font-semibold" x-text="passengers.length + ' vé'"></span>
                            </div>

                            <div class="pt-3 border-t flex justify-between items-baseline gap-3">
                                <span class="font-bold">Tổng tiền</span>
                                <span class="text-2xl font-extrabold text-[#2D5A3D]" x-text="formatPrice(total())"></span>
                            </div>

                            <button type="submit"
                                    class="w-full mt-2 py-4 bg-[#F4D03F] text-gray-900 font-extrabold rounded-xl hover:bg-[#e8c530] transition">
                                Xác Nhận Đặt Chỗ →
                            </button>
                            <p class="text-xs text-center text-gray-400">Bạn chưa bị trừ tiền ở bước này.</p>
                        </div>
                    </div>
                </aside>
            </form>

            @php
                $scheduleLabels = $tour->schedules->mapWithKeys(fn ($s) => [$s->schedule_id => $s->departure_date->format('d/m/Y')]);
                $ticketLabels = $tour->ticketTypes->mapWithKeys(fn ($t) => [$t->ticket_type_id => $t->name]);
            @endphp

            <script>
                function bookingForm({ ticketId, scheduleId, prices, slots }) {
                    return {
                        ticketId,
                        scheduleId,
                        prices,
                        slots,
                        scheduleLabels: @json($scheduleLabels),
                        ticketLabels: @json($ticketLabels),
                        passengers: [{ full_name: '', age: '', phone: '' }],

                        init() {
                            // Đổi ngày khởi hành có thể giảm số chỗ còn lại, cắt bớt cho khớp.
                            this.$watch('scheduleId', () => this.setCount(this.passengers.length));
                        },
                        maxSeats() {
                            return this.slots[this.scheduleId] ?? 99;
                        },
                        setCount(next) {
                            const target = Math.max(1, Math.min(next, this.maxSeats()));
                            while (this.passengers.length < target) {
                                this.passengers.push({ full_name: '', age: '', phone: '' });
                            }
                            while (this.passengers.length > target) {
                                this.passengers.pop();
                            }
                        },
                        removeAt(index) {
                            if (this.passengers.length > 1) this.passengers.splice(index, 1);
                        },
                        total() {
                            return (this.prices[this.ticketId] ?? 0) * this.passengers.length;
                        },
                        formatPrice(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' VND';
                        },
                        scheduleLabel() {
                            return this.scheduleLabels[this.scheduleId] ?? 'Chưa chọn';
                        },
                        ticketLabel() {
                            const name = this.ticketLabels[this.ticketId];
                            return name ? `Vé "${name}"` : 'Chưa chọn';
                        },
                    };
                }
            </script>
        @endif
    </div>
</x-site-layout>
