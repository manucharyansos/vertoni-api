<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('title_hy')->nullable();
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();

            $table->text('subtitle_hy')->nullable();
            $table->text('subtitle_ru')->nullable();
            $table->text('subtitle_en')->nullable();

            $table->string('button_text_hy')->nullable();
            $table->string('button_text_ru')->nullable();
            $table->string('button_text_en')->nullable();

            $table->string('button_link')->nullable();

            $table->string('image')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);


            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
