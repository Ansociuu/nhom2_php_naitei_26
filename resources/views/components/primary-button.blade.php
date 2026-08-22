<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#2D6A2D] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-[#245524] focus:bg-[#245524] active:bg-[#1c421c] focus:outline-none focus:ring-2 focus:ring-[#2D6A2D] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
