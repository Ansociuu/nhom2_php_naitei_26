<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung UNIQUE (chống dữ liệu trùng / chống double-submit), index cho các cột
 * dùng để lọc/sắp xếp, và CHECK cho các cột số có miền giá trị hợp lệ.
 * Các cột FK đã được InnoDB tự đánh index nên không lặp lại.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });

        Schema::table('social_accounts', function (Blueprint $table) {
            // chặn 2 tài khoản khác nhau cùng liên kết tới một tài khoản mạng xã hội
            $table->unique(['provider', 'provider_user_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            // chặn ghi nhận trùng khi cổng thanh toán gọi callback/IPN nhiều lần
            $table->unique(['gateway', 'gateway_txn_id']);
        });

        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->unique(['tour_id', 'departure_date']);
            $table->index('departure_date');
        });

        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->unique(['tour_id', 'day_number']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            // mỗi user chỉ đánh giá một tour một lần
            $table->unique(['user_id', 'tour_id']);
            $table->index(['tour_id', 'status']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->index(['category_id', 'status']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        Schema::table('tour_images', function (Blueprint $table) {
            $table->index(['tour_id', 'display_order']);
        });

        // NULL luôn thoả CHECK nên score vẫn nullable như thiết kế
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT chk_reviews_score CHECK (score BETWEEN 1 AND 5)');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT chk_bookings_num_adults CHECK (num_adults >= 1)');
        DB::statement('ALTER TABLE tour_itineraries ADD CONSTRAINT chk_itineraries_day_number CHECK (day_number >= 1)');
    }

    /**
     * Khi index/unique composite bắt đầu bằng cột FK, InnoDB dùng luôn nó để phục vụ
     * khóa ngoại và bỏ index tự sinh. Vì vậy phải dựng lại index đơn cột cho FK
     * trước khi drop composite, nếu không MySQL báo lỗi 1553.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tour_itineraries DROP CONSTRAINT chk_itineraries_day_number');
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT chk_bookings_num_adults');
        DB::statement('ALTER TABLE reviews DROP CONSTRAINT chk_reviews_score');

        Schema::table('tour_images', function (Blueprint $table) {
            $table->index('tour_id');
            $table->dropIndex(['tour_id', 'display_order']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->index('category_id');
            $table->dropIndex(['category_id', 'status']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('tour_id');
            $table->dropUnique(['user_id', 'tour_id']);
            $table->dropIndex(['tour_id', 'status']);
        });

        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->index('tour_id');
            $table->dropUnique(['tour_id', 'day_number']);
        });

        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->index('tour_id');
            $table->dropUnique(['tour_id', 'departure_date']);
            $table->dropIndex(['departure_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['gateway', 'gateway_txn_id']);
        });

        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropUnique(['provider', 'provider_user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });
    }
};
