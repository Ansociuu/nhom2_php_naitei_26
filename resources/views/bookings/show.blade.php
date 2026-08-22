<x-site-layout title="Xác nhận đặt chỗ">
    <div class="container-narrow max-w-[1100px] py-8">
        <a href="{{ route('bookings.index') }}" class="text-base text-gray-500 hover:text-[#2D5A3D]">← Lịch sử đặt tour</a>

        <div class="mt-4 card-surface overflow-hidden">
            <div class="bg-emerald-50 border-b px-7 py-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="page-title text-[#2D5A3D]">Xác nhận đặt chỗ</h1>
                        <p class="muted-text mt-1">Mã đặt chỗ #{{ $booking->booking_id }}</p>
                    </div>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold
                        {{ match ($booking->status) {
                            'confirmed' => 'bg-white text-[#2D5A3D]',
                            'completed' => 'bg-white text-blue-700',
                            'cancelled' => 'bg-white text-red-600',
                            default => 'bg-white text-amber-700',
                        } }}">
                        @switch($booking->status)
                            @case('pending') Chờ xử lý @break
                            @case('confirmed') Đã xác nhận @break
                            @case('cancelled') Đã hủy @break
                            @case('completed') Hoàn tất @break
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="p-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-base">
                    <div>
                        <div class="text-gray-400">Tour</div>
                        <div class="font-semibold">{{ $booking->schedule->tour->title }}</div>
                    </div>
                    <div>
                        <div class="text-gray-400">Ngày khởi hành</div>
                        <div class="font-semibold">{{ $booking->schedule->departure_date->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-400">Loại vé</div>
                        <div class="font-semibold">Vé "{{ $booking->ticketType?->name }}"</div>
                    </div>
                    <div>
                        <div class="text-gray-400">Số vé</div>
                        @php
                            $breakdown = $booking->num_adults.' người lớn';
                            if ($booking->num_children) {
                                $breakdown .= ', '.$booking->num_children.' trẻ em';
                            }
                        @endphp
                        <div class="font-semibold">{{ $booking->num_adults + $booking->num_children }} vé
                            <span class="text-gray-400 font-normal">({{ $breakdown }})</span>
                        </div>
                    </div>
                    <div class="sm:col-span-2 pt-4 border-t flex items-baseline justify-between">
                        <span class="text-gray-400">Tổng tiền</span>
                        <span class="text-2xl font-extrabold text-[#2D5A3D]">{{ number_format((float) $booking->total_amount) }} VND</span>
                    </div>
                    @if ($booking->note)
                        <div class="sm:col-span-2">
                            <div class="text-gray-400">Ghi chú</div>
                            <div>{{ $booking->note }}</div>
                        </div>
                    @endif
                </div>

                <div class="mt-8">
                    <h2 class="card-title">Thông tin người đi</h2>
                    <div class="mt-3 divide-y border rounded-xl overflow-hidden">
                        @foreach ($booking->passengers as $passenger)
                            <div class="p-4 flex flex-wrap items-center gap-4 text-base">
                                <span class="w-7 h-7 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center shrink-0">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="font-semibold">{{ $passenger->full_name }}</span>
                                @if ($passenger->is_booker)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#2D5A3D] text-xs font-semibold">Người đặt</span>
                                @endif
                                @if ($passenger->age)
                                    <span class="text-gray-500">{{ $passenger->age }} tuổi</span>
                                @endif
                                @if ($passenger->phone)
                                    <span class="text-gray-500">{{ $passenger->phone }}</span>
                                @endif
                                @if ($passenger->seat_no)
                                    <span class="text-gray-500">Vị trí ngồi: {{ $passenger->seat_no }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('tours.index') }}" class="btn-ghost">Xem thêm tour</a>
                    <a href="{{ route('bookings.index') }}" class="btn-primary">Lịch sử đặt tour</a>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
