@php
    $regionLabel = match (request('region')) {
        'mien_bac' => 'Trail Miền Bắc',
        'mien_nam' => 'Trail Miền Nam',
        default => 'Danh sách tour',
    };
@endphp

<x-site-layout :title="$regionLabel">
    <div class="container-page py-8">
        <h1 class="page-title">{{ $regionLabel }}</h1>
        <p class="page-subtitle">{{ $tours->total() }} cung đường đang mở bán</p>

        <form method="GET" action="{{ route('tours.index') }}" class="mt-8 flex flex-wrap items-center gap-3">
            <select name="region" class="form-control w-auto min-w-[190px]">
                <option value="">Tất cả khu vực</option>
                <option value="mien_bac" @selected(request('region') === 'mien_bac')>Miền Bắc</option>
                <option value="mien_nam" @selected(request('region') === 'mien_nam')>Miền Nam</option>
            </select>

            <select name="province" class="form-control w-auto min-w-[190px]">
                <option value="">Tất cả tỉnh/thành</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province }}" @selected(request('province') === $province)>{{ $province }}</option>
                @endforeach
            </select>

            <select name="difficulty" class="form-control w-auto min-w-[190px]">
                <option value="">Tất cả độ khó</option>
                <option value="1" @selected(request('difficulty') === '1')>Rất dễ</option>
                <option value="2" @selected(request('difficulty') === '2')>Dễ</option>
                <option value="3" @selected(request('difficulty') === '3')>Trung bình</option>
                <option value="4" @selected(request('difficulty') === '4')>Khó</option>
                <option value="5" @selected(request('difficulty') === '5')>Rất khó</option>
            </select>

            <button type="submit" class="btn-primary">Lọc</button>

            @if (request()->anyFilled(['region', 'province', 'difficulty']))
                <a href="{{ route('tours.index') }}" class="text-base text-gray-500 hover:text-gray-800">
                    Xoá lọc
                </a>
            @endif
        </form>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($tours as $tour)
                <x-tours.card :tour="$tour" />
            @empty
                <div class="col-span-full card-surface p-12 text-center">
                    <p class="card-title">Không tìm thấy tour phù hợp</p>
                    <p class="muted-text mt-2">Thử bỏ bớt bộ lọc để xem thêm cung đường khác.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $tours->links() }}
        </div>
    </div>
</x-site-layout>
