<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->unique();
            $table->string('type')->default('editorial');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('eyebrow_hy')->nullable();
            $table->string('eyebrow_ru')->nullable();
            $table->string('eyebrow_en')->nullable();

            $table->string('title_hy')->nullable();
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();

            $table->text('description_hy')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();

            $table->string('button_text_hy')->nullable();
            $table->string('button_text_ru')->nullable();
            $table->string('button_text_en')->nullable();
            $table->string('button_link')->nullable();

            $table->string('image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('video')->nullable();
            $table->string('layout')->default('full_bleed');
            $table->string('text_position')->default('bottom_center');
            $table->string('theme')->default('dark');

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
