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
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id('itinerary_id');
            $table->unsignedBigInteger('tour_id');
            $table->integer('day_number')->comment('ngày thứ mấy: 1, 2, 3...');
            $table->string('title')->comment('VD: Hà Nội – Sapa');
            $table->text('description')->nullable();
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->nullable();

            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
