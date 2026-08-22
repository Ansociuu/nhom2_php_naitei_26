@php
    $statusLabels = [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
    ];
    $statusClasses = [
        'pending' => 'bg-amber-50 text-amber-700',
        'confirmed' => 'bg-emerald-50 text-[#2D5A3D]',
        'completed' => 'bg-blue-50 text-blue-700',
        'cancelled' => 'bg-red-50 text-red-600',
    ];
    $hasFilter = request()->anyFilled(['q', 'status', 'time']);
@endphp

<x-site-layout title="Lịch sử đặt tour">
    <div class="container-page py-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="page-title">Lịch sử đặt tour</h1>
                <p class="page-subtitle">{{ $bookings->total() }} đơn đặt chỗ</p>
            </div>
            <a href="{{ route('tours.index') }}" class="btn-primary btn-sm">Đặt chuyến mới</a>
        </div>

        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('bookings.index') }}" class="mt-6 card-surface p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[240px]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                         class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên tour..."
                           class="form-control pl-10 text-sm">
                </div>

                <select name="status" class="form-control w-auto min-w-[170px] text-sm">
                    <option value="">Mọi trạng thái</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}@isset($statusCounts[$value]) ({{ $statusCounts[$value] }})@endisset
                        </option>
                    @endforeach
                </select>

                <select name="time" class="form-control w-auto min-w-[160px] text-sm">
                    <option value="">Mọi thời điểm</option>
                    <option value="upcoming" @selected(request('time') === 'upcoming')>Sắp khởi hành</option>
                    <option value="past" @selected(request('time') === 'past')>Đã kết thúc</option>
                </select>

                <button type="submit" class="btn-primary btn-sm">Lọc</button>

                @if ($hasFilter)
                    <a href="{{ route('bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Xoá lọc</a>
                @endif
            </div>
        </form>

        @if ($bookings->isEmpty())
            <div class="mt-6 card-surface p-14 text-center">
                <p class="card-title">{{ $hasFilter ? 'Không tìm thấy đơn phù hợp' : 'Chưa có lịch sử đặt tour nào' }}</p>
                <p class="mt-2 muted-text">
                    {{ $hasFilter ? 'Thử bỏ bớt bộ lọc để xem thêm.' : 'Chọn một cung đường và bắt đầu hành trình cuối tuần của bạn.' }}
                </p>
                <a href="{{ $hasFilter ? route('bookings.index') : route('tours.index') }}" class="btn-primary mt-6">
                    {{ $hasFilter ? 'Xoá bộ lọc' : 'Khám phá tour' }}
                </a>
            </div>
        @else
            {{-- Bảng (màn hình lớn) --}}
            <div class="mt-6 card-surface overflow-hidden hidden lg:block">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-left">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Mã</th>
                            <th class="px-5 py-3 font-semibold">Tour</th>
                            <th class="px-5 py-3 font-semibold">Ngày khởi hành</th>
                            <th class="px-5 py-3 font-semibold">Loại vé</th>
                            <th class="px-5 py-3 font-semibold text-center">Số vé</th>
                            <th class="px-5 py-3 font-semibold text-right">Tổng tiền</th>
                            <th class="px-5 py-3 font-semibold">Trạng thái</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($bookings as $booking)
                            @php $tour = $booking->schedule->tour; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-400">#{{ $booking->booking_id }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-11 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                                            @if ($tour->coverImageUrl())
                                                <img src="{{ $tour->coverImageUrl() }}" alt="" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $tour->title }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">{{ $booking->schedule->departure_date->format('d/m/Y') }}</td>
                                <td class="px-5 py-4">{{ $booking->ticketType?->name ?? '—' }}</td>
                                <td class="px-5 py-4 text-center">{{ $booking->num_adults + $booking->num_children }}</td>
                                <td class="px-5 py-4 text-right font-semibold">{{ number_format((float) $booking->total_amount) }}₫</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-[#2D5A3D] hover:underline whitespace-nowrap">
                                        Chi tiết →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Card (màn hình nhỏ) --}}
            <div class="mt-6 space-y-3 lg:hidden">
                @foreach ($bookings as $booking)
                    @php $tour = $booking->schedule->tour; @endphp
                    <a href="{{ route('bookings.show', $booking) }}" class="card-surface p-4 flex gap-4 hover:shadow-md transition">
                        <div class="w-20 h-16 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                            @if ($tour->coverImageUrl())
                                <img src="{{ $tour->coverImageUrl() }}" alt="" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-semibold text-gray-900 truncate">{{ $tour->title }}</span>
                                <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                {{ $booking->schedule->departure_date->format('d/m/Y') }}
                                &middot; {{ $booking->num_adults + $booking->num_children }} vé
                            </div>
                            <div class="mt-1 font-semibold text-[#2D5A3D]">{{ number_format((float) $booking->total_amount) }}₫</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</x-site-layout>
