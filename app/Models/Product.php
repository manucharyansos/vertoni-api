<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name_hy',
        'name_ru',
        'name_en',
        'slug_hy',
        'slug_ru',
        'slug_en',
        'short_description_hy',
        'short_description_ru',
        'short_description_en',
        'description_hy',
        'description_ru',
        'description_en',
        'specifications',
        'highlights',
        'meta_title_hy',
        'meta_title_ru',
        'meta_title_en',
        'meta_description_hy',
        'meta_description_ru',
        'meta_description_en',
        'price',
        'old_price',
        'stock',
        'has_variants',
        'main_image',
        'is_active',
        'is_featured',
        'show_on_home',
        'home_sort_order',
    ];


    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            $product->normalizeRequiredTranslations();
        });
    }

    protected function normalizeRequiredTranslations(): void
    {
        $fallbackName = trim((string) ($this->name_hy ?: $this->name_ru ?: $this->name_en));
        $fallbackSlug = trim((string) ($this->slug_hy ?: $this->slug_ru ?: $this->slug_en));

        if ($fallbackSlug === '' && $fallbackName !== '') {
            $fallbackSlug = Str::slug($fallbackName);
        }

        if ($fallbackSlug === '') {
            $fallbackSlug = 'product-' . Str::lower(Str::random(8));
        }

        foreach (['hy', 'ru', 'en'] as $locale) {
            $nameColumn = "name_{$locale}";
            $slugColumn = "slug_{$locale}";

            if (blank($this->{$nameColumn})) {
                $this->{$nameColumn} = $fallbackName ?: 'Product';
            }

            if (blank($this->{$slugColumn})) {
                $this->{$slugColumn} = $fallbackSlug;
            }
        }
    }
    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_home' => 'boolean',
        'home_sort_order' => 'integer',
        'has_variants' => 'boolean',
        'specifications' => 'array',
        'highlights' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

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

    public function getMainImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->main_image);
    }

    public function getDisplayPriceAttribute(): string|float|int|null
    {
        if ($this->has_variants && $this->activeVariants()->exists()) {
            return $this->activeVariants()->min('price');
        }

        return $this->price;
    }

    public function getDisplayOldPriceAttribute(): string|float|int|null
    {
        return $this->old_price;
    }

    public function getAvailableColorsAttribute(): array
    {
        return $this->activeVariants()
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->pluck('color')
            ->unique()
            ->values()
            ->toArray();
    }

    public function getAvailableSizesAttribute(): array
    {
        return $this->activeVariants()
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->pluck('size')
            ->unique()
            ->values()
            ->toArray();
    }

    public function getDefaultImageUrlAttribute(): ?string
    {
        if ($this->main_image_url) {
            return $this->main_image_url;
        }

        $firstVariantImage = $this->activeVariants()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->value('image');

        if ($firstVariantImage) {
            return MediaUrl::fromPublicDisk($firstVariantImage);
        }

        $firstGalleryImage = $this->images()->whereNotNull('image')->value('image');

        return MediaUrl::fromPublicDisk($firstGalleryImage);
    }
}
