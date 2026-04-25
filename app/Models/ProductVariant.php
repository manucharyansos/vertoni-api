<?php

namespace App\Models;

use App\Support\MediaUrl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'attributes',
        'sku',
        'image',
        'price',
        'stock',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'attributes' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->image);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->color,
            $this->size,
        ]);

        return implode(' / ', $parts);
    }
}
