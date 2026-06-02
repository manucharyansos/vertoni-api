<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('newsletter_subscriptions') && ! Schema::hasColumn('newsletter_subscriptions', 'welcome_sent_at')) {
            Schema::table('newsletter_subscriptions', function (Blueprint $table) {
                $table->timestamp('welcome_sent_at')->nullable()->after('subscribed_at');
            });
        }

        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'newsletter_sent_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->timestamp('newsletter_sent_at')->nullable()->after('home_sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('newsletter_subscriptions') && Schema::hasColumn('newsletter_subscriptions', 'welcome_sent_at')) {
            Schema::table('newsletter_subscriptions', function (Blueprint $table) {
                $table->dropColumn('welcome_sent_at');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'newsletter_sent_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('newsletter_sent_at');
            });
        }
    }
};
