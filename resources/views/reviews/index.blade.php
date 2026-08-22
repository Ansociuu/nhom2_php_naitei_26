@php
    $filter = request('filter');
    $hasFilter = request()->anyFilled(['q', 'filter']);
@endphp

<x-site-layout title="Đánh giá chuyến đi">
    <div class="container-page py-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="page-title">Đánh giá chuyến đi</h1>
                <p class="page-subtitle">{{ $bookings->count() }} chuyến đã kết thúc</p>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <form method="GET" action="{{ route('reviews.index') }}" class="mt-6 card-surface p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[240px]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                         class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                    </svg>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên tour..."
                           class="form-control pl-10 text-sm">
                </div>

                <div class="inline-flex rounded-full bg-gray-100 p-1">
                    @foreach ([['', 'Tất cả'], ['pending', 'Chưa đánh giá'], ['reviewed', 'Đã đánh giá']] as [$value, $label])
                        <a href="{{ route('reviews.index', array_filter(['q' => request('q'), 'filter' => $value ?: null])) }}"
                           class="px-4 py-2 rounded-full text-sm font-semibold transition
                                  {{ $filter === ($value ?: null) || ($value === '' && !$filter)
                                     ? 'bg-white text-[#2D5A3D] shadow-sm'
                                     : 'text-gray-500 hover:text-gray-800' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <button type="submit" class="btn-primary btn-sm">Tìm</button>

                @if ($hasFilter)
                    <a href="{{ route('reviews.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Xoá lọc</a>
                @endif
            </div>
        </form>

        @if ($bookings->isEmpty())
            <div class="mt-6 card-surface p-14 text-center">
                <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-7 h-7"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/></svg>
                </div>
                <p class="mt-4 card-title">{{ $hasFilter ? 'Không có chuyến nào khớp bộ lọc' : 'Chưa có chuyến đi nào để đánh giá' }}</p>
                <p class="mt-2 muted-text">
                    {{ $hasFilter ? 'Thử bỏ bớt bộ lọc để xem thêm.' : 'Bạn có thể đánh giá sau khi chuyến đi kết thúc.' }}
                </p>
                <a href="{{ $hasFilter ? route('reviews.index') : route('tours.index') }}" class="btn-primary mt-6">
                    {{ $hasFilter ? 'Xoá bộ lọc' : 'Khám phá tour' }}
                </a>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-5">
                @foreach ($bookings as $booking)
                    @php
                        $tour = $booking->schedule->tour;
                        $review = $reviews->get($tour->tour_id);
                    @endphp

                    <div class="card-surface p-5 flex gap-5">
                        <div class="w-32 h-24 rounded-xl overflow-hidden bg-gray-200 shrink-0">
                            @if ($tour->coverImageUrl())
                                <img src="{{ $tour->coverImageUrl() }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="font-bold text-gray-900 truncate">{{ $tour->title }}</h2>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->schedule->departure_date->format('d/m/Y') }}
                                        &middot; Vé "{{ $booking->ticketType?->name }}"
                                    </p>
                                </div>

                                @if ($review)
                                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ match ($review->status) {
                                            'approved' => 'bg-emerald-50 text-[#2D5A3D]',
                                            'rejected' => 'bg-red-50 text-red-600',
                                            default => 'bg-amber-50 text-amber-700',
                                        } }}">
                                        {{ match ($review->status) {
                                            'approved' => 'Đã duyệt',
                                            'rejected' => 'Bị từ chối',
                                            default => 'Chờ duyệt',
                                        } }}
                                    </span>
                                @else
                                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        Chưa đánh giá
                                    </span>
                                @endif
                            </div>

                            @if ($review)
                                <div class="mt-2 flex items-center gap-2">
                                    <x-star-rating :score="$review->score" />
                                    <span class="text-sm text-gray-400">{{ $review->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="mt-1.5 text-sm text-gray-600 line-clamp-2">{{ $review->content }}</p>

                                @if ($review->images->isNotEmpty())
                                    <div class="mt-2 flex gap-2">
                                        @foreach ($review->images->take(4) as $image)
                                            <img src="{{ $image->url() }}" alt="" class="w-11 h-11 rounded-lg object-cover border">
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            <div class="mt-auto pt-3">
                                <a href="{{ route('reviews.create', $booking) }}"
                                   class="{{ $review ? 'btn-ghost' : 'btn-accent' }} btn-sm !text-sm">
                                    {{ $review ? 'Sửa đánh giá' : 'Viết đánh giá' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-site-layout>
