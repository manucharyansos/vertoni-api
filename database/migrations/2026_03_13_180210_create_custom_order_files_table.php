<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_order_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('custom_order_id');

            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('custom_order_id')
                ->references('id')
                ->on('custom_orders')
                ->onDelete('cascade');

            $table->index(['custom_order_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_order_files');
    }
};
