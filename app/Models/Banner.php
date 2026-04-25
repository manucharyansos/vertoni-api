<?php

namespace App\Models;

use App\Support\MediaUrl;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title_hy',
        'title_ru',
        'title_en',
        'subtitle_hy',
        'subtitle_ru',
        'subtitle_en',
        'button_text_hy',
        'button_text_ru',
        'button_text_en',
        'button_link',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        $column = "{$field}_{$locale}";
        if (filled($this->{$column})) {
            return $this->{$column};
        }

        if ($locale !== 'hy' && filled($this->{$field . '_hy'})) {
            return $this->{$field . '_hy'};
        }

        if ($locale !== 'en' && filled($this->{$field . '_en'})) {
            return $this->{$field . '_en'};
        }

        if ($locale !== 'ru' && filled($this->{$field . '_ru'})) {
            return $this->{$field . '_ru'};
        }

        return null;
    }

    public function getMediaUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->image);
    }

    public function getMediaTypeAttribute(): string
    {
        if (! $this->image) {
            return 'none';
        }

        $extension = strtolower(pathinfo($this->image, PATHINFO_EXTENSION));

        return in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi'], true)
            ? 'video'
            : 'image';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->media_type === 'image' ? $this->media_url : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->media_type === 'video' ? $this->media_url : null;
    }
}
