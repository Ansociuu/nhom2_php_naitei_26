@props(['disabled' => false])

<div class="relative" x-data="{ show: false }">
    <input @disabled($disabled) :type="show ? 'text' : 'password'"
        {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#2D5A3D] focus:ring-[#2D5A3D] rounded-md shadow-sm pr-10']) }}>

    <button type="button" @click="show = !show"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600"
            tabindex="-1">
        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5">
            <path d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5">
            <path d="M3 3l18 18M10.6 10.6a3 3 0 0 0 4.24 4.24M9.5 5.2A9.4 9.4 0 0 1 12 5c6 0 9.5 7 9.5 7a13.6 13.6 0 0 1-2.9 3.9M6.2 6.6C3.6 8.4 2.5 12 2.5 12s3.5 7 9.5 7a9.3 9.3 0 0 0 3.4-.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
</div>
