<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * tour_images bị khôi phục lại schema Cloudinary cũ (mất cột image_url) do một
 * migration/seed khác trên DB chung. Thêm lại image_url mà KHÔNG xoá các cột
 * cloudinary_* để không mất dữ liệu ảnh hiện có của các tour khác.
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

        // Cho phép thêm ảnh bằng image_url mà không cần điền metadata Cloudinary.
        Schema::table('tour_images', function (Blueprint $table) {
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
        Schema::table('tour_images', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
