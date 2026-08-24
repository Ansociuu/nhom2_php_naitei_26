@props(['title' => 'Chưa có dữ liệu', 'message' => null, 'icon' => 'inbox'])

@php
    $paths = [
        'inbox' => '<path d="M3 12h5l2 3h4l2-3h5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 6h14l2 6v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6l2-6Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/>',
        'star' => '<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>',
        'tour' => '<path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7" stroke-linecap="round"/>',
    ];
@endphp

<div class="px-6 py-16 text-center">
    <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-50 text-gray-300 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-7 h-7">
            {!! $paths[$icon] ?? $paths['inbox'] !!}
        </svg>
    </div>
    <p class="mt-4 font-semibold text-gray-700">{{ $title }}</p>
    @if ($message)
        <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
