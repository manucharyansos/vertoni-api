<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomOrderFile extends Model
{
    protected $fillable = [
        'custom_order_id',
        'file_path',
        'file_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class);
    }
}
