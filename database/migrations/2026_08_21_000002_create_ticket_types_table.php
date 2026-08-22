<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mỗi tour có thể có nhiều loại vé (VD: Vé Nhập Hội, Vé Khám Phá, Vé Lên Đường),
 * mỗi loại có giá và mô tả/điểm nổi bật riêng.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id('ticket_type_id');
            $table->unsignedBigInteger('tour_id');
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->text('description')->nullable()->comment('điểm nổi bật của loại vé');
            $table->boolean('includes_bus')->default(true);
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->nullable();

            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
