@props(['difficulty' => 0, 'size' => 'w-3.5 h-3.5'])

<span class="inline-flex items-center gap-1">
    @for ($i = 1; $i <= 5; $i++)
        <span class="{{ $size }} rounded-sm {{ $i <= $difficulty ? 'bg-amber-500' : 'bg-gray-200' }}"></span>
    @endfor
</span>
