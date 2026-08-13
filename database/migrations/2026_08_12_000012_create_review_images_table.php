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
        Schema::create('review_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('review_id');
            $table->string('cloudinary_public_id')->comment('dùng để xóa/transform ảnh qua Cloudinary API');
            $table->string('secure_url')->comment('https CDN url để hiển thị');
            $table->string('format')->comment('jpg/png/webp/...');
            $table->integer('width');
            $table->integer('height');
            $table->integer('bytes');
            $table->integer('display_order')->default(0)->comment('thứ tự hiển thị trong gallery');
            $table->datetime('created_at')->useCurrent();

            $table->foreign('review_id')
                  ->references('review_id')
                  ->on('reviews')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
    }
};
