<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('sku')->nullable()->unique();

            $table->string('name_hy');
            $table->string('name_ru');
            $table->string('name_en');

            $table->string('slug_hy')->unique();
            $table->string('slug_ru')->unique();
            $table->string('slug_en')->unique();

            $table->text('short_description_hy')->nullable();
            $table->text('short_description_ru')->nullable();
            $table->text('short_description_en')->nullable();

            $table->longText('description_hy')->nullable();
            $table->longText('description_ru')->nullable();
            $table->longText('description_en')->nullable();

            $table->decimal('price', 12, 2);
            $table->decimal('old_price', 12, 2)->nullable();

            $table->unsignedInteger('stock')->default(0);

            $table->string('main_image')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->string('meta_title_hy')->nullable();
            $table->string('meta_title_ru')->nullable();
            $table->string('meta_title_en')->nullable();

            $table->text('meta_description_hy')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_en')->nullable();

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
