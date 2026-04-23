<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('preferred_contact_method')->nullable(); // phone, email, whatsapp, telegram
            $table->string('title')->nullable();

            $table->longText('description');

            $table->unsignedInteger('quantity')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();

            $table->decimal('budget', 12, 2)->nullable();
            $table->date('deadline')->nullable();

            $table->string('status')->default('new');
            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};
