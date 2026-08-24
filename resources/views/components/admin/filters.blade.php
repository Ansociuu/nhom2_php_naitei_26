@props(['action', 'placeholder' => 'Tìm kiếm...', 'searchName' => 'search', 'hasFilter' => false])

<form method="GET" action="{{ $action }}" class="flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[240px]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
             class="w-4.5 h-4.5 w-[18px] h-[18px] text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2">
            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>
        </svg>
        <input type="search" name="{{ $searchName }}" value="{{ request($searchName) }}"
               placeholder="{{ $placeholder }}"
               class="w-full h-11 pl-10 pr-4 rounded-xl border-gray-200 bg-gray-50/60 text-sm
                      focus:bg-white focus:border-[#2D5A3D] focus:ring-[#2D5A3D]">
    </div>

    {{ $slot }}

    <button type="submit" class="h-11 px-6 rounded-xl bg-[#2D6A2D] text-white text-sm font-semibold hover:bg-[#245524] transition">
        Lọc
    </button>

    @if ($hasFilter)
        <a href="{{ $action }}" class="h-11 px-4 inline-flex items-center rounded-xl text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition">
            Xoá lọc
        </a>
    @endif
</form>
