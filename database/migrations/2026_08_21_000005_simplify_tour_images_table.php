<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ảnh tour có thể nhập bằng URL trực tiếp (không qua Cloudinary), nên thêm cột
 * `image_url`.
 *
 * Lưu ý: các cột metadata Cloudinary được GIỮ NGUYÊN vì CloudinaryService và
 * Admin\TourImageController vẫn ghi vào chúng — chỉ nới lỏng thành nullable để
 * hai cách thêm ảnh cùng dùng được một bảng.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tour_images', 'image_url')) {
            Schema::table('tour_images', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('tour_id');
            });
        }

        foreach (['cloudinary_public_id', 'secure_url', 'format', 'width', 'height', 'bytes'] as $column) {
            if (! Schema::hasColumn('tour_images', $column)) {
                continue;
            }

            Schema::table('tour_images', function (Blueprint $table) use ($column) {
                match ($column) {
                    'width', 'height', 'bytes' => $table->integer($column)->nullable()->change(),
                    default => $table->string($column)->nullable()->change(),
                };
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tour_images', 'image_url')) {
            Schema::table('tour_images', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }
};
