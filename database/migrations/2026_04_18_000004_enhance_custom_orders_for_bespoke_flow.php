<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_orders', function (Blueprint $table) {
            $table->string('material')->nullable()->after('color');
            $table->string('reference_url')->nullable()->after('deadline');
            $table->string('source')->nullable()->after('reference_url');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('allow_custom_order')->default(true)->after('is_featured');
            $table->index('allow_custom_order');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['allow_custom_order']);
            $table->dropColumn('allow_custom_order');
        });

        Schema::table('custom_orders', function (Blueprint $table) {
            $table->dropColumn(['material', 'reference_url', 'source']);
        });
    }
};
