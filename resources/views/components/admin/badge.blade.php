@props(['variant' => 'gray', 'dot' => false])

@php
    $styles = [
        'green' => 'bg-emerald-50 text-[#2D5A3D] ring-emerald-600/20',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'red'   => 'bg-red-50 text-red-700 ring-red-600/20',
        'blue'  => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'gray'  => 'bg-gray-50 text-gray-600 ring-gray-500/20',
    ];
    $dots = [
        'green' => 'bg-[#2D5A3D]', 'amber' => 'bg-amber-500',
        'red' => 'bg-red-500', 'blue' => 'bg-blue-500', 'gray' => 'bg-gray-400',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset whitespace-nowrap '.($styles[$variant] ?? $styles['gray'])]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dots[$variant] ?? $dots['gray'] }}"></span>
    @endif
    {{ $slot }}
</span>
