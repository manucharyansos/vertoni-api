<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->nullable()->unique()->after('id');
            $table->string('group')->default('site')->after('key');
            $table->string('label')->nullable()->after('group');
            $table->longText('value')->nullable()->after('label');
            $table->string('type')->default('text')->after('value');
            $table->boolean('is_public')->default(true)->after('type');
            $table->unsignedInteger('sort_order')->default(0)->after('is_public');

            $table->index(['group', 'is_public', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group', 'is_public', 'sort_order']);
            $table->dropColumn(['key', 'group', 'label', 'value', 'type', 'is_public', 'sort_order']);
        });
    }
};
