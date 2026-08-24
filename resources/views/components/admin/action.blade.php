@props(['href' => null, 'variant' => 'default', 'type' => 'button'])

@php
    $styles = [
        'default' => 'text-gray-600 hover:text-[#2D5A3D] hover:bg-emerald-50',
        'primary' => 'text-[#2D5A3D] hover:bg-emerald-50 font-semibold',
        'warning' => 'text-amber-600 hover:text-amber-700 hover:bg-amber-50',
        'danger'  => 'text-red-600 hover:text-red-700 hover:bg-red-50',
    ];
    $class = 'inline-flex items-center whitespace-nowrap px-2.5 py-1.5 rounded-lg text-sm transition '.($styles[$variant] ?? $styles['default']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
