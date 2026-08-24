@props([
    'title' => null,
    'subtitle' => null,
    'flush' => false,   // true = nội dung sát mép (dùng cho bảng)
])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-2xl shadow-[0_1px_2px_rgba(16,24,40,.05)] overflow-hidden']) }}>
    @if ($title || isset($actions))
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="font-bold text-gray-900 truncate">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-gray-500 truncate">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $flush ? '' : 'p-6' }}">
        {{ $slot }}
    </div>
</div>
