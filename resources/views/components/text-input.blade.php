@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] rounded-md shadow-sm']) }}>
