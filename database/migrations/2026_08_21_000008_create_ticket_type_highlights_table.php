<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Điểm nổi bật (ảnh + tiêu đề + mô tả) hiển thị theo từng loại vé,
 * VD: "Người Dẫn Đường", "Chinh Phục Đỉnh 837m".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_type_highlights', function (Blueprint $table) {
            $table->id('highlight_id');
            $table->unsignedBigInteger('ticket_type_id');
            $table->string('image_url');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->datetime('created_at')->useCurrent();

            $table->foreign('ticket_type_id')
                  ->references('ticket_type_id')
                  ->on('ticket_types')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_type_highlights');
    }
};
