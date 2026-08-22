<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_type_id')->nullable()->after('schedule_id');
            $table->text('note')->nullable()->after('total_amount')->comment('yêu cầu đặc biệt, dị ứng thực phẩm, ...');

            $table->foreign('ticket_type_id')
                  ->references('ticket_type_id')
                  ->on('ticket_types')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn(['ticket_type_id', 'note']);
        });
    }
};
