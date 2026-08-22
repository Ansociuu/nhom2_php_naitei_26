@props(['score' => 0, 'class' => 'w-4 h-4'])

<span class="inline-flex items-center gap-0.5 text-amber-400">
    @for ($i = 1; $i <= 5; $i++)
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             fill="{{ $i <= $score ? 'currentColor' : 'none' }}"
             stroke="currentColor" stroke-width="1.5" class="{{ $class }} {{ $i <= $score ? '' : 'text-gray-300' }}">
            <path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.4l6.1-.9L12 3Z" stroke-linejoin="round"/>
        </svg>
    @endfor
</span>
