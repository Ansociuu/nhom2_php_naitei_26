@props(['tour'])

@php
    $ticket = $tour->cheapestTicketType();
    $tags = collect($ticket?->features ?? [])->take(3);
@endphp

<a href="{{ route('tours.show', $tour) }}" class="flex flex-col bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl hover:-translate-y-1 transition duration-200">
    <div class="aspect-[4/3] bg-gray-200">
        @if ($tour->coverImageUrl())
            <img src="{{ $tour->coverImageUrl() }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Chưa có ảnh</div>
        @endif
    </div>

    <div class="flex-1 flex flex-col p-6">
        <h3 class="text-xl font-extrabold uppercase text-gray-900 leading-snug">TRAIL BUS – {{ $tour->title }}</h3>

        @if ($tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-sm font-semibold text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-3.5 h-3.5 text-[#2D5A3D]"><path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-5 space-y-2 text-[15px] text-gray-700">
            @if ($tour->duration_label)
                <div class="flex items-center gap-2">
                    <x-tours.icon name="clock" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span class="font-semibold">Thời Gian:</span> {{ $tour->duration_label }}
                </div>
            @endif
            @if ($tour->distance_km)
                <div class="flex items-center gap-2">
                    <x-tours.icon name="distance" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span class="font-semibold">Độ Dài:</span> {{ rtrim(rtrim(number_format((float) $tour->distance_km, 1), '0'), '.') }}km
                </div>
            @endif
            @if ($tour->difficulty)
                <div class="flex items-center gap-2">
                    <x-tours.icon name="difficulty" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span class="font-semibold">Độ khó:</span> <x-tours.difficulty-dots :difficulty="$tour->difficulty" />
                </div>
            @endif
            @if ($tour->peak_elevation)
                <div class="flex items-center gap-2">
                    <x-tours.icon name="peak" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span class="font-semibold">Độ cao đỉnh:</span> {{ $tour->peak_elevation }}m
                </div>
            @endif
        </div>

        <div class="mt-auto pt-5">
            @if ($ticket)
                @if ($ticket->original_price && $ticket->original_price > $ticket->price)
                    <div class="text-right text-sm text-gray-400 line-through">{{ number_format((float) $ticket->original_price) }} VND</div>
                @endif
                <div class="mt-2 pt-4 border-t flex items-baseline justify-between gap-2">
                    <span class="font-bold text-gray-900">Giá vé:</span>
                    <span class="text-xl font-extrabold text-gray-900">{{ number_format((float) $ticket->price) }} VND</span>
                </div>
            @else
                <div class="pt-4 border-t flex items-baseline justify-between gap-2">
                    <span class="font-bold text-gray-900">Giá vé:</span>
                    <span class="text-xl font-extrabold text-gray-900">{{ number_format((float) $tour->price) }} VND</span>
                </div>
            @endif
        </div>
    </div>
</a>
