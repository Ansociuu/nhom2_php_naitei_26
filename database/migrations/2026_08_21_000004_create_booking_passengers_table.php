<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chi tiết từng hành khách trong một lượt đặt chỗ: họ tên, tuổi, SĐT, vị trí ngồi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_passengers', function (Blueprint $table) {
            $table->id('passenger_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('full_name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('seat_no')->nullable();
            $table->boolean('is_booker')->default(false)->comment('người đi trùng với người đặt');
            $table->datetime('created_at')->useCurrent();

            $table->foreign('booking_id')
                  ->references('booking_id')
                  ->on('bookings')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_passengers');
    }
};
