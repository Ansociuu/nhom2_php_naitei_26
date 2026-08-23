<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung các thuộc tính đặc thù của tour trekking trong ngày tại Việt Nam:
 * khu vực, tỉnh/thành, độ khó, độ cao đỉnh, quãng đường, thời gian ước tính.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->enum('region', ['mien_bac', 'mien_nam'])->nullable()->after('category_id');
            $table->string('province')->nullable()->after('region')->comment('Tỉnh/Thành, VD: Lâm Đồng');
            $table->unsignedTinyInteger('difficulty')->nullable()->after('province')->comment('Độ khó 1-5');
            $table->unsignedInteger('peak_elevation')->nullable()->after('difficulty')->comment('Độ cao đỉnh (m)');
            $table->decimal('distance_km', 6, 2)->nullable()->after('peak_elevation')->comment('Độ dài quãng đường (km)');
            $table->string('duration_label')->nullable()->after('duration_days')->comment('VD: Khoảng 6 tiếng tùy thể lực');
        });

        DB::statement('ALTER TABLE tours ADD CONSTRAINT chk_tours_difficulty CHECK (difficulty BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tours DROP CONSTRAINT chk_tours_difficulty');

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'region',
                'province',
                'difficulty',
                'peak_elevation',
                'distance_km',
                'duration_label',
            ]);
        });
    }
};
