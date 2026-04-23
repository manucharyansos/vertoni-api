<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('name_hy');
            $table->string('name_ru');
            $table->string('name_en');

            $table->string('slug_hy')->unique();
            $table->string('slug_ru')->unique();
            $table->string('slug_en')->unique();

            $table->text('description_hy')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();

            $table->string('image')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('meta_title_hy')->nullable();
            $table->string('meta_title_ru')->nullable();
            $table->string('meta_title_en')->nullable();

            $table->text('meta_description_hy')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_en')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
