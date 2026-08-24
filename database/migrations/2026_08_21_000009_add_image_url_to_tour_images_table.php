<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảo đảm bảng tour_images có đủ cột cho cả hai cách thêm ảnh:
 * - `image_url` cho ảnh nhập bằng URL
 * - các cột Cloudinary cho ảnh upload qua CloudinaryService
 *
 * Migration này mang tính vá lại: trên những database đã lỡ bỏ cột Cloudinary
 * (do phiên bản trước của migration 000005), các cột đó được tạo lại ở dạng
 * nullable để Admin\TourImageController hoạt động trở lại.
 */
return new class extends Migration
{
    private const CLOUDINARY_COLUMNS = [
        'cloudinary_public_id' => 'string',
        'secure_url' => 'string',
        'format' => 'string',
        'width' => 'integer',
        'height' => 'integer',
        'bytes' => 'integer',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('tour_images', 'image_url')) {
            Schema::table('tour_images', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('tour_id');
            });
        }

        foreach (self::CLOUDINARY_COLUMNS as $column => $type) {
            Schema::table('tour_images', function (Blueprint $table) use ($column, $type) {
                if (Schema::hasColumn('tour_images', $column)) {
                    $type === 'integer'
                        ? $table->integer($column)->nullable()->change()
                        : $table->string($column)->nullable()->change();

                    return;
                }

                $type === 'integer'
                    ? $table->integer($column)->nullable()
                    : $table->string($column)->nullable();
            });
        }
    }

    public function down(): void
    {
        // Không đảo ngược: việc bỏ cột ở đây sẽ làm hỏng luồng upload Cloudinary.
    }
};
