<?php

namespace App\Models;

use App\Support\MediaUrl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeSection extends Model
{
    protected $fillable = [
        'key',
        'type',
        'category_id',
        'eyebrow_hy',
        'eyebrow_ru',
        'eyebrow_en',
        'title_hy',
        'title_ru',
        'title_en',
        'description_hy',
        'description_ru',
        'description_en',
        'button_text_hy',
        'button_text_ru',
        'button_text_en',
        'button_link',
        'image',
        'mobile_image',
        'video',
        'layout',
        'text_position',
        'theme',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $column = "{$field}_{$locale}";

        if (filled($this->{$column})) {
            return $this->{$column};
        }

        foreach (['hy', 'ru', 'en'] as $fallbackLocale) {
            $fallbackColumn = "{$field}_{$fallbackLocale}";
            if (filled($this->{$fallbackColumn})) {
                return $this->{$fallbackColumn};
            }
        }

        return null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->image);
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->mobile_image);
    }

    public function getVideoUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->video);
    }
}
