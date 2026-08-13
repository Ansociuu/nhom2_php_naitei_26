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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('gateway')->comment('vnpay/onepay/napas/...');
            $table->string('gateway_txn_id')->nullable()->comment('mã giao dịch phía cổng thanh toán, dùng để đối soát');
            $table->datetime('created_at')->useCurrent();
            $table->datetime('paid_at')->nullable();

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
        Schema::dropIfExists('payments');
    }
};
