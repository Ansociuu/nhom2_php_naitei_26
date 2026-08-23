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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id('booking_detail_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('name')->comment('họ và tên khách');
            $table->unsignedTinyInteger('age')->comment('tuổi');
            $table->decimal('price', 15, 2)->comment('giá vé áp dụng cho khách này');
            $table->datetime('created_at')->useCurrent();

            $table->foreign('booking_id')
                  ->references('booking_id')
                  ->on('bookings')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
