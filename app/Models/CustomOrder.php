<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomOrder extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'phone',
        'email',
        'preferred_contact_method',
        'title',
        'description',
        'quantity',
        'size',
        'color',
        'budget',
        'deadline',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'budget' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CustomOrderFile::class)->orderBy('sort_order');
    }
}
