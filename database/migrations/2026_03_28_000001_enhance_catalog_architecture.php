<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('type')->default('catalog')->after('parent_id');
            $table->string('menu_title')->nullable()->after('image');
            $table->text('menu_description')->nullable()->after('menu_title');
            $table->string('menu_image')->nullable()->after('menu_description');
            $table->json('attribute_schema')->nullable()->after('menu_image');
            $table->unsignedInteger('menu_order')->default(0)->after('sort_order');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->json('specifications')->nullable()->after('description_en');
            $table->json('highlights')->nullable()->after('specifications');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->json('attributes')->nullable()->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['attributes']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'highlights']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'menu_title',
                'menu_description',
                'menu_image',
                'attribute_schema',
                'menu_order',
            ]);
        });
    }
};
