<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('tour_id');
            $table->string('cloudinary_public_id')->comment('dùng để xóa/transform ảnh qua Cloudinary API');
            $table->string('secure_url')->comment('https CDN url để hiển thị');
            $table->string('format')->comment('jpg/png/webp/...');
            $table->integer('width');
            $table->integer('height');
            $table->integer('bytes');
            $table->boolean('is_cover')->default(false)->comment('ảnh đại diện/thumbnail của tour');
            $table->integer('display_order')->default(0)->comment('thứ tự hiển thị trong gallery');
            $table->datetime('created_at')->useCurrent();

            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_images');
    }
};
