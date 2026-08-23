@props(['name', 'class' => 'w-4 h-4'])

@php
    $paths = [
        'elevation-gain' => '<path d="M3 20h18M6 20V10l4 3 4-7 4 5v9" stroke-linecap="round" stroke-linejoin="round"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2" stroke-linecap="round" stroke-linejoin="round"/>',
        'distance' => '<path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="9" r="2.5"/>',
        'peak' => '<path d="m3 19 6-11 3 5 3-7 6 13H3Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'difficulty' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="0.7" fill="currentColor"/>',
        'bus' => '<rect x="3" y="5" width="18" height="12" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 13h18M7 17v2M17 17v2" stroke-linecap="round"/>',
        'hiking' => '<circle cx="14" cy="5" r="1.6"/><path d="M13 8l-3 3 2 2-1 6M10 11 6 13l1 5M13 8l3 2 3-1" stroke-linecap="round" stroke-linejoin="round"/>',
        'water' => '<path d="M12 3s6 6.5 6 11a6 6 0 1 1-12 0c0-4.5 6-11 6-11Z" stroke-linecap="round" stroke-linejoin="round"/>',
        'leader' => '<circle cx="12" cy="7" r="3.2"/><path d="M5 21c0-4 3-6.5 7-6.5s7 2.5 7 6.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'day' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1" stroke-linecap="round"/>',
        'tracklog' => '<path d="M4 19V9l6-4 4 3 6-4v14l-6 4-4-3-6 4Z" stroke-linecap="round" stroke-linejoin="round"/>',
    ];
    $path = $paths[$name] ?? $paths['distance'];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" {{ $attributes->merge(['class' => $class]) }}>{!! $path !!}</svg>
