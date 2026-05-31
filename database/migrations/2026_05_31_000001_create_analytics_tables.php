<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 80)->unique();
            $table->string('first_ip_hash', 128)->nullable();
            $table->string('last_ip_hash', 128)->nullable();
            $table->text('first_referrer')->nullable();
            $table->text('last_referrer')->nullable();
            $table->text('first_landing_path')->nullable();
            $table->text('last_path')->nullable();
            $table->string('locale', 10)->nullable();
            $table->string('language', 50)->nullable();
            $table->string('timezone', 100)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('visits_count')->default(0);
            $table->unsignedInteger('page_views_count')->default(0);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('last_seen_at');
            $table->index('device_type');
            $table->index('locale');
        });

        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analytics_visitor_id')->nullable()->constrained('analytics_visitors')->nullOnDelete();
            $table->string('visitor_id', 80)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->text('url')->nullable();
            $table->text('path');
            $table->char('path_hash', 64)->index();
            $table->string('title', 512)->nullable();
            $table->string('locale', 10)->nullable()->index();
            $table->text('referrer')->nullable();
            $table->string('referrer_domain', 255)->nullable()->index();
            $table->string('utm_source', 255)->nullable()->index();
            $table->string('utm_medium', 255)->nullable();
            $table->string('utm_campaign', 255)->nullable()->index();
            $table->string('utm_term', 255)->nullable();
            $table->string('utm_content', 255)->nullable();
            $table->string('device_type', 50)->nullable()->index();
            $table->string('browser', 100)->nullable()->index();
            $table->string('os', 100)->nullable()->index();
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->unsignedSmallInteger('screen_height')->nullable();
            $table->unsignedSmallInteger('viewport_width')->nullable();
            $table->unsignedSmallInteger('viewport_height')->nullable();
            $table->unsignedInteger('page_loaded_ms')->nullable();
            $table->unsignedInteger('time_on_page_seconds')->nullable();
            $table->string('ip_hash', 128)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['path_hash', 'viewed_at']);
            $table->index(['session_id', 'viewed_at']);
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analytics_visitor_id')->nullable()->constrained('analytics_visitors')->nullOnDelete();
            $table->string('visitor_id', 80)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('event_name', 100)->index();
            $table->string('event_label', 512)->nullable();
            $table->text('url')->nullable();
            $table->text('path')->nullable();
            $table->char('path_hash', 64)->nullable()->index();
            $table->json('payload')->nullable();
            $table->string('ip_hash', 128)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['event_name', 'occurred_at']);
            $table->index(['path_hash', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('analytics_page_views');
        Schema::dropIfExists('analytics_visitors');
    }
};
