<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticsVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'first_ip_hash',
        'last_ip_hash',
        'first_referrer',
        'last_referrer',
        'first_landing_path',
        'last_path',
        'locale',
        'language',
        'timezone',
        'device_type',
        'browser',
        'os',
        'user_agent',
        'visits_count',
        'page_views_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'visits_count' => 'integer',
        'page_views_count' => 'integer',
    ];

    public function pageViews(): HasMany
    {
        return $this->hasMany(AnalyticsPageView::class, 'analytics_visitor_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class, 'analytics_visitor_id');
    }
}
