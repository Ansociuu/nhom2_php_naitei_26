<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hai nhánh cùng làm phần hành khách của đơn đặt chỗ bằng hai bảng khác nhau:
 * `booking_details` (tên, tuổi, giá — luồng thanh toán dùng bảng này) và
 * `booking_passengers` (thêm SĐT, vị trí ngồi, người đặt).
 *
 * Gộp về một bảng duy nhất `booking_details` để tránh trùng dữ liệu.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('booking_details')) {
            return;
        }

        Schema::table('booking_details', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_details', 'phone')) {
                $table->string('phone')->nullable()->after('age');
            }
            if (! Schema::hasColumn('booking_details', 'seat_no')) {
                $table->string('seat_no')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('booking_details', 'is_booker')) {
                $table->boolean('is_booker')->default(false)->after('seat_no')
                    ->comment('người đi trùng với người đặt');
            }
        });

        // Chuyển dữ liệu cũ (nếu có) rồi bỏ bảng trùng.
        if (Schema::hasTable('booking_passengers')) {
            foreach (DB::table('booking_passengers')->get() as $passenger) {
                DB::table('booking_details')->insert([
                    'booking_id' => $passenger->booking_id,
                    'name' => $passenger->full_name,
                    'age' => $passenger->age ?? 0,
                    'price' => 0,
                    'phone' => $passenger->phone,
                    'seat_no' => $passenger->seat_no,
                    'is_booker' => $passenger->is_booker,
                    'created_at' => $passenger->created_at,
                ]);
            }

            Schema::dropIfExists('booking_passengers');
        }
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn(['phone', 'seat_no', 'is_booker']);
        });
    }
};
