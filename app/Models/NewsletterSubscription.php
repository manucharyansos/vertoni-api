<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'email',
        'locale',
        'source',
        'status',
        'subscribed_at',
        'welcome_sent_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'welcome_sent_at' => 'datetime',
    ];


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
