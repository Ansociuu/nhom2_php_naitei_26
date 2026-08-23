<x-site-layout>
    <section class="relative aspect-[16/9] sm:aspect-[2/1] overflow-hidden">
        <img src="{{ asset('images/marketing/hero-bus.png') }}" alt="Your Weekend Adventure - Sun* Booking Tour"
             class="w-full h-full object-cover">
    </section>

    {{-- Thanh tìm kiếm tour --}}
    <section class="relative z-10 -mt-10 sm:-mt-14">
        <div class="container-page">
            <form method="GET" action="{{ route('tours.index') }}"
                  class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
                        </svg>
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Bạn muốn đi đâu cuối tuần này?"
                               class="form-control pl-12 py-3.5 text-base">
                    </div>

                    <select name="region" class="form-control sm:w-auto sm:min-w-[180px] py-3.5">
                        <option value="">Mọi khu vực</option>
                        <option value="mien_nam" @selected(request('region') === 'mien_nam')>Miền Nam</option>
                        <option value="mien_bac" @selected(request('region') === 'mien_bac')>Miền Bắc</option>
                    </select>

                    <button type="submit" class="btn-accent sm:px-8 shrink-0">Tìm tour</button>
                </div>
            </form>
        </div>
    </section>

    <section class="min-h-[calc(100vh-100px)] max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                <span class="text-[#F4D03F]">#YourWeekend</span><span class="text-gray-900">Trail</span>
            </h2>
            <p class="mt-6 text-gray-600 text-lg leading-relaxed max-w-2xl">
                Cách trung tâm Hà Nội và TP.HCM không quá 5 tiếng di chuyển, những chuyến trail bus của chúng tôi
                sẽ đưa và đón bạn về trong vòng 24 giờ, phù hợp với bất cứ nhu cầu di chuyển và khám phá cho cuối
                tuần của bạn.
            </p>

            <div class="mt-10 grid grid-cols-[auto_1fr_1fr] gap-4 items-start">
                <span class="inline-flex items-center justify-center bg-[#F4D03F] text-gray-900 text-sm font-bold uppercase px-6 py-4 rounded-md">
                    Miền Nam
                </span>
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    @forelse ($southTours as $tour)
                        <a href="{{ route('tours.show', $tour) }}"
                           class="text-center text-base font-semibold text-white bg-[#2D5A3D] rounded-md py-4 px-4 hover:bg-[#254a32]">
                            {{ $tour->title }}
                        </a>
                    @empty
                        <p class="col-span-2 text-gray-400">Chưa có tuyến trail Miền Nam nào.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 grid grid-cols-[auto_1fr_1fr] gap-4 items-start">
                <span class="inline-flex items-center justify-center bg-[#F4D03F] text-gray-900 text-sm font-bold uppercase px-6 py-4 rounded-md">
                    Miền Bắc
                </span>
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    @forelse ($northTours as $tour)
                        <a href="{{ route('tours.show', $tour) }}"
                           class="text-center text-base font-semibold text-white bg-[#2D5A3D] rounded-md py-4 px-4 hover:bg-[#254a32]">
                            {{ $tour->title }}
                        </a>
                    @empty
                        <p class="col-span-2 text-gray-400">Chưa có tuyến trail Miền Bắc nào.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="hidden lg:flex justify-center h-[75vh] max-h-[820px]">
            <x-tours.vietnam-map />
        </div>
    </section>

    @php
        $allTours = $southTours->concat($northTours)->values();
        $pages = $allTours->chunk(3)->values();
    @endphp

    <section class="bg-[#9ACE7D] min-h-[calc(100vh-100px)] flex items-center bg-no-repeat bg-cover bg-center"
             style="background-image: url('{{ asset('images/marketing/topo.svg') }}');"
             x-data="{ page: 0, pages: {{ max($pages->count(), 1) }} }">
        <div class="w-full max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 py-16">
            <div class="relative flex items-center justify-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 uppercase text-center">
                    Hệ Thống Trail Bus Cho Bạn
                </h2>

                @if ($pages->count() > 1)
                    <div class="absolute right-0 flex items-center gap-3">
                        <button type="button" @click="page = (page - 1 + pages) % pages"
                                class="w-12 h-12 rounded-full border-2 border-gray-900/30 text-gray-900 flex items-center justify-center hover:bg-white/40 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" @click="page = (page + 1) % pages"
                                class="w-12 h-12 rounded-full border-2 border-gray-900 text-gray-900 flex items-center justify-center hover:bg-white/40 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            @forelse ($pages as $pageIndex => $chunk)
                <div x-show="page === {{ $pageIndex }}" @if ($pageIndex > 0) x-cloak @endif
                     class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($chunk as $tour)
                        <x-tours.card :tour="$tour" />
                    @endforeach
                </div>
            @empty
                <p class="mt-12 text-gray-800">Chưa có tour nào để hiển thị.</p>
            @endforelse

            @if ($pages->count() > 1)
                <div class="mt-10 flex items-center justify-center gap-2">
                    @foreach ($pages as $pageIndex => $chunk)
                        <button type="button" @click="page = {{ $pageIndex }}"
                                :class="page === {{ $pageIndex }} ? 'bg-gray-900 w-6' : 'bg-gray-900/30 w-2.5'"
                                class="h-2.5 rounded-full transition-all"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="min-h-[calc(100vh-100px)] flex flex-col justify-center max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 py-16">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-center">
            TẠI SAO NÊN CHỌN <span class="text-[#F4A800]">SUN* BOOKING TOUR</span>
        </h2>

        <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 text-center">
            <div>
                <div class="mx-auto w-14 h-14 flex items-center justify-center text-[#2D5A3D]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-11 h-11"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold">Không lo lịch trình</h3>
                <p class="mt-3 text-base text-gray-600">
                    Chỉ cần đặt vé, lên xe và tận hưởng chuyến đi, bạn không cần tự lái hay lo tìm đường, chuẩn bị quá nhiều.
                </p>
            </div>
            <div>
                <div class="mx-auto w-14 h-14 flex items-center justify-center text-[#2D5A3D]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-11 h-11"><circle cx="12" cy="12" r="9"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold">Mỗi tuần, một câu chuyện</h3>
                <p class="mt-3 text-base text-gray-600">
                    Mỗi tuyến trail là một trải nghiệm riêng, với những cảnh sắc khác biệt và những người bạn mới.
                </p>
            </div>
            <div>
                <div class="mx-auto w-14 h-14 flex items-center justify-center text-[#2D5A3D]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-11 h-11"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h4"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold">Thẻ Chinh Phục</h3>
                <p class="mt-3 text-base text-gray-600">
                    Thẻ chinh phục thiết kế riêng cho từng cung đường, kèm in tên riêng của bạn để ghi dấu kỷ niệm.
                </p>
            </div>
            <div>
                <div class="mx-auto w-14 h-14 flex items-center justify-center text-[#2D5A3D]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-11 h-11"><path d="M12 21s-8-4.5-8-11a5 5 0 0 1 8-4 5 5 0 0 1 8 4c0 6.5-8 11-8 11z"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold">Thói quen sống xanh</h3>
                <p class="mt-3 text-base text-gray-600">
                    Cuối tuần không còn loanh quanh thành phố, mà còn là cơ hội để khám phá, vận động và làm mới bản thân.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-gray-100 min-h-[calc(100vh-100px)] flex items-center">
        <div class="w-full max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12 py-16 grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div>
                <h2 class="text-3xl font-extrabold uppercase leading-tight">Những Câu Hỏi<br>Thường Gặp</h2>
                <p class="mt-6 text-base text-gray-500">Tư vấn chi tiết liên hệ hotline</p>
                <p class="text-lg font-bold text-[#2D5A3D]">0933 22 78 78 (Nhánh 3)</p>
            </div>

            <div class="lg:col-span-2 space-y-3" x-data="{ open: 0 }">
                @php
                    $faqs = [
                        ['q' => 'Có người hướng dẫn hoặc leader dẫn đoàn không?', 'a' => 'Tùy vào tuyến trail bạn đăng ký, chúng tôi sẽ cung cấp người hướng dẫn để đảm bảo an toàn và chia sẻ kinh nghiệm cho bạn về thiên nhiên và đặc điểm của từng nơi khác nhau.'],
                        ['q' => 'Có thể đổi hoặc hủy vé sau khi đặt chỗ không?', 'a' => 'Bạn có thể đổi lịch trước 7 ngày so với ngày đi. Việc hủy vé sẽ được hoàn tiền theo chính sách hoàn hủy của từng tuyến.'],
                        ['q' => 'Trail bus này phù hợp cho người mới không? Có khó không?', 'a' => 'Chúng tôi có nhiều cung đường với mức độ khó khác nhau, từ dễ đến thử thách. Mỗi tour đều có ghi rõ độ khó để bạn chủ động lựa chọn phù hợp.'],
                        ['q' => 'Xe bus đón khách ở đâu? Có nhiều điểm đón không?', 'a' => 'Xe sẽ xuất phát từ điểm tập kết tại TP.HCM. Chúng tôi có thể có nhiều điểm đón dọc đường tùy tuyến. Chi tiết sẽ được thông báo sau khi đặt vé.'],
                    ];
                @endphp

                @foreach ($faqs as $index => $faq)
                    <div class="bg-white rounded-xl border">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-base">
                            {{ $faq['q'] }}
                            <span x-text="open === {{ $index }} ? '−' : '+'" class="text-gray-400 text-xl shrink-0"></span>
                        </button>
                        <div x-show="open === {{ $index }}" class="px-6 pb-5 text-base text-gray-600">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-site-layout>
