@props(['name'])

<select name="{{ $name }}"
        {{ $attributes->merge(['class' => 'h-11 rounded-xl border-gray-200 bg-gray-50/60 text-sm min-w-[170px] focus:bg-white focus:border-[#2D5A3D] focus:ring-[#2D5A3D]']) }}>
    {{ $slot }}
</select>
