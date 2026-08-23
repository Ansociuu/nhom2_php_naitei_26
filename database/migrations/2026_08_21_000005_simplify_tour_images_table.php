<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin nhập ảnh tour bằng URL trực tiếp, không upload qua Cloudinary,
 * nên bỏ các cột metadata Cloudinary và thay bằng một cột URL duy nhất.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_images', function (Blueprint $table) {
            $table->string('image_url')->after('tour_id');
        });

        Schema::table('tour_images', function (Blueprint $table) {
            $table->dropColumn(['cloudinary_public_id', 'secure_url', 'format', 'width', 'height', 'bytes']);
        });
    }

    public function down(): void
    {
        Schema::table('tour_images', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable();
            $table->string('secure_url')->nullable();
            $table->string('format')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('bytes')->nullable();
        });

        Schema::table('tour_images', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
