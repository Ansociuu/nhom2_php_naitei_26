<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Đặt tour') }} — {{ $tour->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 sm:p-8">

                {{-- Tour Overview --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $tour->title }}</h3>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>📍 {{ __('Khởi hành') }}: {{ $tour->departure_location ?? 'N/A' }}</div>
                        <div>📅 {{ __('Thời gian') }}: {{ $tour->duration_days }} {{ __('ngày') }}</div>
                        <div>💰 {{ __('Giá gốc') }}: {{ number_format($tour->price, 0, ',', '.') }} ₫ / {{ __('người lớn') }}</div>
                    </div>
                </div>

                @if ($schedules->isEmpty())
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        {{ __('Hiện tại chưa có lịch khởi hành nào khả dụng cho tour này.') }}
                    </div>
                @else
                    @php
                        $oldPassengers = old('passengers', [
                            ['name' => Auth::user()->username ?? '', 'age' => 25]
                        ]);
                    @endphp

                    <form method="POST" action="{{ route('bookings.store') }}"
                          x-data="{
                              scheduleId: '{{ old('schedule_id', $schedules->first()->schedule_id) }}',
                              passengers: {{ Js::from($oldPassengers) }},
                              schedules: {{ Js::from($schedules->map(fn ($s) => [
                                  'id'    => $s->schedule_id,
                                  'price' => floatval($s->price_override ?? $tour->price),
                                  'slots' => $s->available_slots,
                                  'date'  => $s->departure_date->format('d/m/Y'),
                              ])) }},
                              get selectedSchedule() {
                                  return this.schedules.find(s => s.id == this.scheduleId) || this.schedules[0];
                              },
                              get unitPrice() { return this.selectedSchedule?.price || 0; },
                              addPassenger() {
                                  if (this.passengers.length < (this.selectedSchedule?.slots || 99)) {
                                      this.passengers.push({ name: '', age: 20 });
                                  }
                              },
                              removePassenger(index) {
                                  if (this.passengers.length > 1) {
                                      this.passengers.splice(index, 1);
                                  }
                              },
                              getPassengerPrice(age) {
                                  const a = parseInt(age);
                                  if (isNaN(a) || a >= 12) {
                                      return this.unitPrice;
                                  }
                                  return this.unitPrice * 0.5;
                              },
                              get numAdults() {
                                  return this.passengers.filter(p => isNaN(parseInt(p.age)) || parseInt(p.age) >= 12).length;
                              },
                              get numChildren() {
                                  return this.passengers.filter(p => !isNaN(parseInt(p.age)) && parseInt(p.age) < 12).length;
                              },
                              get totalAmount() {
                                  return this.passengers.reduce((sum, p) => sum + this.getPassengerPrice(p.age), 0);
                              },
                              formatVND(n) { return new Intl.NumberFormat('vi-VN').format(n) + ' ₫'; }
                          }">
                        @csrf

                        {{-- General Errors for passengers array --}}
                        @error('passengers')
                            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg text-sm text-red-700 dark:text-red-300">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Schedule Selector --}}
                        <div class="mb-8">
                            <x-input-label for="schedule_id" :value="__('Lịch khởi hành')" />
                            <select id="schedule_id" name="schedule_id" x-model="scheduleId"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                @foreach ($schedules as $schedule)
                                    <option value="{{ $schedule->schedule_id }}" @selected(old('schedule_id') == $schedule->schedule_id)>
                                        {{ $schedule->departure_date->format('d/m/Y') }}
                                        — {{ number_format($schedule->price_override ?? $tour->price, 0, ',', '.') }} ₫
                                        — {{ __('Còn') }} {{ $schedule->available_slots }} {{ __('chỗ') }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('schedule_id')" />
                        </div>

                        {{-- Passenger Information Section --}}
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('Thông tin hành khách') }} (<span x-text="passengers.length"></span>)
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('Từ 12 tuổi trở lên tính giá Người lớn (100%), dưới 12 tuổi tính Trẻ em (50%).') }}
                                    </p>
                                </div>
                                <button type="button" @click="addPassenger()"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 rounded-md text-xs font-semibold transition">
                                    + {{ __('Thêm người đi cùng') }}
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(passenger, index) in passengers" :key="index">
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50/50 dark:bg-gray-750 relative">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                                                      x-text="index === 0 ? '{{ __('Hành khách 1 (Người đặt tour)') }}' : '{{ __('Hành khách ') }}' + (index + 1)"></span>

                                                {{-- Adult / Child classification badge --}}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                      :class="parseInt(passenger.age) >= 12 || isNaN(parseInt(passenger.age))
                                                          ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                                                          : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'"
                                                      x-text="parseInt(passenger.age) >= 12 || isNaN(parseInt(passenger.age)) ? 'Người lớn (100%)' : 'Trẻ em (50%)'">
                                                </span>
                                            </div>

                                            <button type="button" x-show="index > 0" @click="removePassenger(index)"
                                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                ✕ {{ __('Xóa') }}
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            {{-- Name input --}}
                                            <div class="sm:col-span-2">
                                                <label :for="'passenger_name_' + index" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    {{ __('Họ và tên') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text"
                                                       :id="'passenger_name_' + index"
                                                       :name="'passengers[' + index + '][name]'"
                                                       x-model="passenger.name"
                                                       required
                                                       placeholder="{{ __('Ví dụ: Nguyễn Văn A') }}"
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                                            </div>

                                            {{-- Age input --}}
                                            <div>
                                                <label :for="'passenger_age_' + index" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    {{ __('Tuổi') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number"
                                                       :id="'passenger_age_' + index"
                                                       :name="'passengers[' + index + '][age]'"
                                                       x-model.number="passenger.age"
                                                       min="0" max="120" required
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                                            </div>
                                        </div>

                                        {{-- Individual calculated ticket price --}}
                                        <div class="mt-2 text-right text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('Giá vé') }}: <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="formatVND(getPassengerPrice(passenger.age))"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Dynamic Price Summary --}}
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{ __('Chi tiết giá & Chỗ trống') }}</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>{{ __('Người lớn (≥ 12 tuổi)') }}: <span x-text="numAdults"></span> × <span x-text="formatVND(unitPrice)"></span></span>
                                    <span x-text="formatVND(numAdults * unitPrice)"></span>
                                </div>
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>{{ __('Trẻ em (< 12 tuổi)') }}: <span x-text="numChildren"></span> × <span x-text="formatVND(unitPrice * 0.5)"></span></span>
                                    <span x-text="formatVND(numChildren * unitPrice * 0.5)"></span>
                                </div>
                                <div class="flex justify-between font-semibold text-base text-gray-900 dark:text-gray-100 border-t border-gray-300 dark:border-gray-600 pt-2">
                                    <span>{{ __('Tổng cộng') }} (<span x-text="passengers.length"></span> {{ __('khách') }})</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold" x-text="formatVND(totalAmount)"></span>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Lịch này còn') }} <span class="font-medium text-gray-700 dark:text-gray-300" x-text="selectedSchedule?.slots"></span> {{ __('chỗ trống') }}.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                {{ __('← Quay lại') }}
                            </a>
                            <x-primary-button>
                                {{ __('Xác nhận đặt tour') }}
                            </x-primary-button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
