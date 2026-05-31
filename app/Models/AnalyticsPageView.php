<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsPageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'analytics_visitor_id',
        'visitor_id',
        'session_id',
        'url',
        'path',
        'path_hash',
        'title',
        'locale',
        'referrer',
        'referrer_domain',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'device_type',
        'browser',
        'os',
        'screen_width',
        'screen_height',
        'viewport_width',
        'viewport_height',
        'page_loaded_ms',
        'time_on_page_seconds',
        'ip_hash',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'screen_width' => 'integer',
        'screen_height' => 'integer',
        'viewport_width' => 'integer',
        'viewport_height' => 'integer',
        'page_loaded_ms' => 'integer',
        'time_on_page_seconds' => 'integer',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitor::class, 'analytics_visitor_id');
    }
}
