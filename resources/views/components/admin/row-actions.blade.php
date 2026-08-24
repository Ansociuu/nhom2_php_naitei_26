{{-- Nhóm liên kết hành động ở cuối mỗi dòng bảng --}}
<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-1']) }}>
    {{ $slot }}
</div>
