<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mỗi loại vé có giá gốc/giá sale, đối tượng phù hợp, danh sách tính năng (icon chip),
 * và chi phí bao gồm/không bao gồm riêng (khác nhau giữa các loại vé của cùng 1 tour).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->decimal('original_price', 15, 2)->nullable()->after('price')
                ->comment('giá gốc trước giảm, null = không giảm giá');
            $table->string('target_audience')->nullable()->after('original_price')
                ->comment('Dành cho: ...');
            $table->json('features')->nullable()->after('target_audience')
                ->comment('danh sách tính năng dạng chip, VD: ["Vé xe","Hiking","Leader"]');
            $table->text('included_services')->nullable()->after('description');
            $table->text('excluded_services')->nullable()->after('included_services');
            $table->boolean('is_recommended')->default(false)->after('includes_bus');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn([
                'original_price',
                'target_audience',
                'features',
                'included_services',
                'excluded_services',
                'is_recommended',
            ]);
        });
    }
};
