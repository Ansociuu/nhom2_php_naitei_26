@props(['label', 'region', 'tours'])

@php
    $tours = $tours ?? collect();
    $menuId = 'menu-'.$region;
@endphp

<div class="relative" x-data="{ open: false, active: 0 }"
     @mouseenter="open = true" @mouseleave="open = false"
     @keydown.escape.window="open = false">

    <button type="button" @click="open = !open"
            :aria-expanded="open.toString()" aria-controls="{{ $menuId }}"
            class="inline-flex items-center gap-1.5 hover:text-[#2D5A3D] {{ request('region') === $region ? 'text-[#2D5A3D]' : '' }}">
        {{ $label }}
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             class="w-4 h-4 transition-transform" :class="open && 'rotate-180'">
            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    @if ($tours->isNotEmpty())
        <div id="{{ $menuId }}" x-show="open" x-cloak x-transition.opacity
             class="fixed left-0 right-0 top-[100px] bg-white border-t shadow-xl z-40">
            <div class="container-page py-10 grid grid-cols-1 lg:grid-cols-[300px_1fr_360px] gap-10">
                {{-- Cột trái: danh sách trail --}}
                <div class="border-r pr-6">
                    <p class="text-sm font-bold uppercase text-[#F4A800]">Trail Bus</p>
                    <ul class="mt-4 space-y-1">
                        @foreach ($tours as $index => $tour)
                            <li>
                                <a href="{{ route('tours.show', $tour) }}"
                                   @mouseenter="active = {{ $index }}"
                                   class="flex items-center gap-2.5 py-2 text-base transition"
                                   :class="active === {{ $index }} ? 'font-bold text-gray-900' : 'text-gray-600 hover:text-gray-900'">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                          :class="active === {{ $index }} ? 'bg-[#F4A800]' : 'bg-transparent'"></span>
                                    Trail Bus – {{ $tour->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('tours.index', ['region' => $region]) }}"
                       class="mt-4 inline-block text-sm font-semibold text-[#2D5A3D] hover:underline">
                        Xem tất cả {{ $label }} →
                    </a>
                </div>

                {{-- Cột giữa: mô tả trail đang chọn --}}
                @foreach ($tours as $index => $tour)
                    <div x-show="active === {{ $index }}" @if ($index > 0) x-cloak @endif>
                        <h3 class="text-2xl font-extrabold text-gray-900">Trail Bus – {{ $tour->title }}</h3>
                        <p class="mt-4 text-base leading-relaxed text-gray-600 line-clamp-6">
                            {{ $tour->description }}
                        </p>

                        <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700">
                            @if ($tour->duration_label)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-tours.icon name="clock" class="w-4 h-4 text-amber-500" />
                                    {{ $tour->duration_label }}
                                </span>
                            @endif
                            @if ($tour->distance_km)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-tours.icon name="distance" class="w-4 h-4 text-amber-500" />
                                    {{ rtrim(rtrim(number_format((float) $tour->distance_km, 1), '0'), '.') }}km
                                </span>
                            @endif
                            @if ($tour->peak_elevation)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-tours.icon name="peak" class="w-4 h-4 text-amber-500" />
                                    {{ $tour->peak_elevation }}m
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('tours.show', $tour) }}" class="btn-primary btn-sm mt-6">
                            Xem chi tiết →
                        </a>
                    </div>
                @endforeach

                {{-- Cột phải: ảnh trail đang chọn --}}
                <div>
                    @foreach ($tours as $index => $tour)
                        <div x-show="active === {{ $index }}" @if ($index > 0) x-cloak @endif>
                            <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-gray-200">
                                @if ($tour->coverImageUrl())
                                    <img src="{{ $tour->coverImageUrl() }}" alt="{{ $tour->title }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
