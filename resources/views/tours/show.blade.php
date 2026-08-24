@php
    $recommendedTicket = $tour->ticketTypes->firstWhere('is_recommended', true) ?? $tour->ticketTypes->first();
@endphp

<x-site-layout :title="$tour->title">
    <div class="bg-[#9ACE7D] sticky top-[100px] z-20">
        <div class="container-page py-4 flex items-center justify-between gap-6 text-black">
            <div class="min-w-0">
                <a href="{{ route('tours.index') }}" class="text-2xl font-bold hover:underline">Trail Bus – {{ $tour->title }}</a>
                @if ($tour->distance_km)
                    <div class="text-base">
                        {{ rtrim(rtrim(number_format((float) $tour->distance_km, 1), '0'), '.') }} km/ ngày
                    </div>
                @endif
            </div>

            @if ($recommendedTicket)
                <div class="flex items-center gap-5 shrink-0">
                    <div class="text-right">
                        @if ($recommendedTicket->original_price && $recommendedTicket->original_price > $recommendedTicket->price)
                            <div class="text-base font-bold text-red-600 line-through">{{ number_format((float) $recommendedTicket->original_price) }} VND</div>
                        @endif
                        <div class="text-[25px] font-bold leading-tight">{{ number_format((float) $recommendedTicket->price) }}</div>
                    </div>
                    <a href="#chon-ve" class="btn-accent">Mua Ngay</a>
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="aspect-[4/3] bg-gray-200 rounded-lg overflow-hidden">
            @if ($tour->coverImageUrl())
                <img src="{{ $tour->coverImageUrl() }}" alt="{{ $tour->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">Chưa có ảnh</div>
            @endif
        </div>

        <div>
            <h1 class="section-title">TRAIL BUS - {{ $tour->title }}</h1>

            <div class="mt-6 grid grid-cols-2 gap-5 text-base">
                @if ($tour->elevation_gain)
                    <div class="flex items-center gap-2 text-[#2D5A3D]">
                        <x-tours.icon name="elevation-gain" class="w-5 h-5 shrink-0" />
                        <div>
                            <div class="text-gray-400">Độ cao tích lũy</div>
                            <div class="font-bold text-gray-900">{{ $tour->elevation_gain }}m</div>
                        </div>
                    </div>
                @endif
                @if ($tour->distance_km)
                    <div class="flex items-center gap-2 text-[#2D5A3D]">
                        <x-tours.icon name="distance" class="w-5 h-5 shrink-0" />
                        <div>
                            <div class="text-gray-400">Độ dài quãng đường</div>
                            <div class="font-bold text-gray-900">-{{ rtrim(rtrim(number_format((float) $tour->distance_km, 1), '0'), '.') }}km</div>
                        </div>
                    </div>
                @endif
                @if ($tour->peak_elevation)
                    <div class="flex items-center gap-2 text-[#2D5A3D]">
                        <x-tours.icon name="peak" class="w-5 h-5 shrink-0" />
                        <div>
                            <div class="text-gray-400">Độ cao đỉnh</div>
                            <div class="font-bold text-gray-900">{{ $tour->peak_elevation }}m</div>
                        </div>
                    </div>
                @endif
                @if ($tour->difficulty)
                    <div class="flex items-center gap-2 text-[#2D5A3D]">
                        <x-tours.icon name="difficulty" class="w-5 h-5 shrink-0" />
                        <div>
                            <div class="text-gray-400">Độ khó</div>
                            <x-tours.difficulty-dots :difficulty="$tour->difficulty" />
                        </div>
                    </div>
                @endif
            </div>

            <p class="mt-6 text-base leading-relaxed text-gray-700 whitespace-pre-line">{{ $tour->description }}</p>
        </div>
    </div>

    @if ($tour->ticketTypes->isNotEmpty())
        <div id="chon-ve" class="bg-white bg-topo-gray py-20 scroll-mt-[200px]">
            <div class="container-page">
                <h2 class="section-title text-center">Chọn Vé Trải Nghiệm</h2>

                @php
                    $featureIcons = [
                        'Vé xe' => 'bus',
                        'Hiking' => 'hiking',
                        'Nước uống' => 'water',
                        'Leader' => 'leader',
                        'Trong ngày' => 'day',
                        'Tour 1 ngày' => 'day',
                        'Tracklog' => 'tracklog',
                    ];
                @endphp

                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    @foreach ($tour->ticketTypes as $ticketType)
                        <div class="relative flex flex-col rounded-2xl p-7 pt-9 shadow-sm transition
                                    {{ $ticketType->is_recommended
                                        ? 'bg-[#EAF6DF] border-2 border-[#7FB85F]'
                                        : 'bg-white border border-gray-200' }}">
                            @if ($ticketType->is_recommended)
                                <span class="absolute -top-4 left-7 inline-flex items-center gap-1.5 bg-black text-white text-base font-bold px-5 py-2 rounded-full">
                                    ⭐ Must Choose
                                </span>
                                <span class="absolute top-5 right-5 w-9 h-9 rounded-full border-2 border-[#7FB85F] flex items-center justify-center text-[#4F8F32]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="w-5 h-5">
                                        <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            @endif

                            <h3 class="text-2xl font-bold uppercase text-black">Vé “{{ $ticketType->name }}”</h3>

                            @if (!empty($ticketType->features))
                                <div class="mt-5 grid grid-cols-2 gap-x-5 gap-y-4 text-lg text-black">
                                    @foreach ($ticketType->features as $feature)
                                        <span class="inline-flex items-center gap-3">
                                            <x-tours.icon :name="$featureIcons[$feature] ?? 'distance'" class="w-7 h-7 text-black shrink-0" />
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($ticketType->target_audience)
                                <p class="mt-6 text-lg leading-relaxed text-black">
                                    <span class="font-bold">Dành cho:</span>
                                    {{ $ticketType->target_audience }}
                                </p>
                            @endif

                            <div class="mt-auto pt-6">
                                @if ($ticketType->original_price && $ticketType->original_price > $ticketType->price)
                                    <div class="text-right text-lg font-bold text-gray-400 line-through">
                                        {{ number_format((float) $ticketType->original_price) }} VND
                                    </div>
                                @endif
                                <div class="mt-3 pt-5 border-t border-gray-300 flex items-baseline justify-between gap-3">
                                    <span class="text-xl font-bold text-black">Giá vé</span>
                                    <span class="text-2xl font-bold text-black">{{ number_format((float) $ticketType->price) }} VND</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($recommendedTicket && $recommendedTicket->highlights->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h2 class="section-title">Điểm Nổi Bật Của Vé “{{ $recommendedTicket->name }}”</h2>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ($recommendedTicket->highlights as $highlight)
                    <div>
                        <div class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-200">
                            <img src="{{ $highlight->image_url }}" alt="{{ $highlight->title }}" class="w-full h-full object-cover">
                        </div>
                        <h3 class="mt-4 font-extrabold text-center uppercase text-base">{{ $highlight->title }}</h3>
                        @if ($highlight->description)
                            <p class="mt-2 text-sm leading-relaxed text-gray-600 text-center">{{ $highlight->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2" x-data="{ open: {{ $recommendedTicket?->ticket_type_id ?? 'null' }} }">
            <h2 class="section-title">Cấu Trúc Chi Phí</h2>

            <div class="mt-4 space-y-3">
                @foreach ($tour->ticketTypes as $ticketType)
                    <div class="bg-white rounded-lg border">
                        <button type="button" @click="open = open === {{ $ticketType->ticket_type_id }} ? null : {{ $ticketType->ticket_type_id }}"
                                class="w-full flex items-center justify-between px-5 py-4 text-left text-base font-semibold">
                            Vé "{{ $ticketType->name }}" – {{ number_format((float) $ticketType->price) }} VND
                            <span x-text="open === {{ $ticketType->ticket_type_id }} ? '−' : '+'" class="text-gray-400"></span>
                        </button>

                        <div x-show="open === {{ $ticketType->ticket_type_id }}" class="px-5 pb-5 grid grid-cols-1 sm:grid-cols-2 gap-5 text-base">
                            @if ($ticketType->included_services)
                                <div>
                                    <div class="font-semibold text-emerald-700">Chi phí bao gồm:</div>
                                    <p class="mt-1 text-gray-600 whitespace-pre-line">{{ $ticketType->included_services }}</p>
                                </div>
                            @endif
                            @if ($ticketType->excluded_services)
                                <div>
                                    <div class="font-semibold text-red-500">Chi phí không bao gồm:</div>
                                    <p class="mt-1 text-gray-600 whitespace-pre-line">{{ $ticketType->excluded_services }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($tour->itineraries->isNotEmpty())
                <div class="mt-10">
                    <h2 class="section-title">Lịch trình</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($tour->itineraries as $itinerary)
                            <div class="border-l-2 border-[#2D5A3D] pl-4">
                                <div class="text-sm font-semibold text-[#2D5A3D]">Ngày {{ $itinerary->day_number }}: {{ $itinerary->title }}</div>
                                @if ($itinerary->description)
                                    <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $itinerary->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @php
                $scored = $tour->reviews->whereNotNull('score');
                $avgScore = $scored->isNotEmpty() ? round($scored->avg('score'), 1) : null;
            @endphp

            <div class="mt-10">
                <div class="flex flex-wrap items-center gap-4">
                    <h2 class="section-title">Đánh giá</h2>
                    @if ($avgScore)
                        <div class="flex items-center gap-2">
                            <x-star-rating :score="round($avgScore)" class="w-5 h-5" />
                            <span class="font-bold text-gray-900">{{ $avgScore }}</span>
                            <span class="text-sm text-gray-400">({{ $tour->reviews->count() }} đánh giá)</span>
                        </div>
                    @endif
                </div>

                @if ($tour->reviews->isEmpty())
                    <p class="mt-3 text-sm text-gray-400">Chưa có đánh giá nào cho tour này.</p>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach ($tour->reviews as $review)
                            <div class="border rounded-xl p-5 bg-white">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-[#2D5A3D] text-white text-sm font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($review->user->username, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900 truncate">{{ $review->user->username }}</div>
                                        <div class="flex items-center gap-2">
                                            @if ($review->score)
                                                <x-star-rating :score="$review->score" />
                                            @endif
                                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($review->content)
                                    <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $review->content }}</p>
                                @endif

                                @if ($review->images->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($review->images as $image)
                                            <img src="{{ $image->url() }}" alt="" class="w-24 h-24 rounded-lg object-cover border">
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Lượt thích --}}
                                <div class="mt-4 pt-3 border-t">
                                    @auth
                                        <button type="button"
                                                x-data="{
                                                    liked: {{ $review->liked_by_me ?? false ? 'true' : 'false' }},
                                                    count: {{ $review->likes_count ?? 0 }},
                                                    busy: false,
                                                    async toggle() {
                                                        if (this.busy) return;
                                                        this.busy = true;
                                                        try {
                                                            const res = await fetch('{{ route('reviews.like', $review) }}', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                                    'Accept': 'application/json',
                                                                },
                                                            });
                                                            if (!res.ok) return;
                                                            const data = await res.json();
                                                            this.liked = data.liked;
                                                            this.count = data.count;
                                                        } finally {
                                                            this.busy = false;
                                                        }
                                                    }
                                                }"
                                                @click="toggle()" :disabled="busy"
                                                :class="liked ? 'text-[#2D5A3D] bg-emerald-50 border-[#2D5A3D]' : 'text-gray-500 border-gray-300 hover:bg-gray-50'"
                                                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-sm font-semibold transition disabled:opacity-60">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"
                                                 class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'">
                                                <path d="M7 10v11H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h3Zm0 0 4.5-7a2.5 2.5 0 0 1 2.4 3.2L13 10h5.2a2 2 0 0 1 2 2.5l-1.7 7a2 2 0 0 1-2 1.5H7"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span x-text="liked ? 'Đã thích' : 'Hữu ích'"></span>
                                            <span x-show="count > 0" x-text="'· ' + count"></span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-2 text-sm text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4">
                                                <path d="M7 10v11H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h3Zm0 0 4.5-7a2.5 2.5 0 0 1 2.4 3.2L13 10h5.2a2 2 0 0 1 2 2.5l-1.7 7a2 2 0 0 1-2 1.5H7"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            {{ $review->likes_count ?? 0 }} lượt thích ·
                                            <a href="{{ route('login') }}" class="text-[#2D5A3D] hover:underline">Đăng nhập để thích</a>
                                        </span>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-gray-900 rounded-t-lg px-5 py-4">
                <h2 class="text-white text-2xl font-bold uppercase text-center">Đặt Chỗ</h2>
            </div>

            <div class="border rounded-b-lg p-5">
                @if ($tour->schedules->isEmpty() || $tour->ticketTypes->isEmpty())
                    <p class="text-sm text-gray-500">Tour này hiện chưa có lịch khởi hành hoặc loại vé để đặt chỗ.</p>
                @else
                    <form method="GET" action="{{ route('bookings.create', $tour) }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Ngày khởi hành</label>
                            <select name="schedule_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach ($tour->schedules as $schedule)
                                    <option value="{{ $schedule->schedule_id }}" @disabled($schedule->available_slots < 1)>
                                        {{ \Illuminate\Support\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                        ({{ $schedule->available_slots }} chỗ trống)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Loại vé</label>
                            <select name="ticket_type_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach ($tour->ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->ticket_type_id }}" @selected($recommendedTicket && $ticketType->ticket_type_id === $recommendedTicket->ticket_type_id)>
                                        Vé "{{ $ticketType->name }}" — {{ number_format((float) $ticketType->price) }} VND
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-[#F4D03F] text-gray-900 font-bold rounded-md hover:bg-[#e8c530]">
                            Xác Nhận Đặt Chỗ →
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-site-layout>
