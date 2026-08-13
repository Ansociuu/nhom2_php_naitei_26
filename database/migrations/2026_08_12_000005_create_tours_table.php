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
        Schema::create('tours', function (Blueprint $table) {
            $table->id('tour_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('highlights')->nullable();
            $table->string('departure_location')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('duration_days');
            $table->text('included_services')->nullable();
            $table->text('excluded_services')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->nullable();

            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
