<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung các cột nghiệp vụ còn thiếu:
 * - reviews: chưa có cột nào lưu nội dung bài đánh giá
 * - bookings: chưa lưu số khách và giá chốt tại thời điểm đặt
 * - payments: đổi CASCADE -> RESTRICT để không mất lịch sử giao dịch khi xoá booking
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('content')->nullable()->after('tour_id')
                ->comment('nội dung bài đánh giá');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('num_adults')->default(1)->after('schedule_id')
                ->comment('số khách người lớn');
            $table->unsignedInteger('num_children')->default(0)->after('num_adults')
                ->comment('số khách trẻ em');
            $table->decimal('unit_price', 15, 2)->default(0)->after('num_children')
                ->comment('giá 1 khách chốt lúc đặt, giữ nguyên kể cả khi tour đổi giá');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);

            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);

            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->cascadeOnDelete();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['num_adults', 'num_children', 'unit_price']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};
