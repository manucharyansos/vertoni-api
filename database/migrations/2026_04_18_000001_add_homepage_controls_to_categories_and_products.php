<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_on_home')->default(false)->after('menu_order');
            $table->unsignedInteger('home_sort_order')->default(0)->after('show_on_home');
            $table->string('home_title_hy')->nullable()->after('home_sort_order');
            $table->string('home_title_ru')->nullable()->after('home_title_hy');
            $table->string('home_title_en')->nullable()->after('home_title_ru');
            $table->text('home_description_hy')->nullable()->after('home_title_en');
            $table->text('home_description_ru')->nullable()->after('home_description_hy');
            $table->text('home_description_en')->nullable()->after('home_description_ru');
            $table->string('home_image')->nullable()->after('home_description_en');

            $table->index(['show_on_home', 'home_sort_order']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_on_home')->default(false)->after('is_featured');
            $table->unsignedInteger('home_sort_order')->default(0)->after('show_on_home');

            $table->index(['show_on_home', 'home_sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['show_on_home', 'home_sort_order']);
            $table->dropColumn(['show_on_home', 'home_sort_order']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['show_on_home', 'home_sort_order']);
            $table->dropColumn([
                'show_on_home',
                'home_sort_order',
                'home_title_hy',
                'home_title_ru',
                'home_title_en',
                'home_description_hy',
                'home_description_ru',
                'home_description_en',
                'home_image',
            ]);
        });
    }
};
