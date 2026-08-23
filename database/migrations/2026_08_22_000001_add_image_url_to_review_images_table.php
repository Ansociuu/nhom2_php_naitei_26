<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Người dùng tải ảnh đánh giá trực tiếp lên server (storage) thay vì qua Cloudinary,
 * nên thêm cột image_url và nới lỏng các cột metadata Cloudinary để không bắt buộc.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('review_images', 'image_url')) {
            Schema::table('review_images', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('review_id');
            });
        }

        Schema::table('review_images', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->change();
            $table->string('secure_url')->nullable()->change();
            $table->string('format')->nullable()->change();
            $table->integer('width')->nullable()->change();
            $table->integer('height')->nullable()->change();
            $table->integer('bytes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('review_images', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
