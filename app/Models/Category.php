<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = [
        'parent_id',
        'type',
        'name_hy',
        'name_ru',
        'name_en',
        'slug_hy',
        'slug_ru',
        'slug_en',
        'description_hy',
        'description_ru',
        'description_en',
        'image',
        'menu_title',
        'menu_description',
        'menu_image',
        'attribute_schema',
        'is_active',
        'sort_order',
        'menu_order',
        'meta_title_hy',
        'meta_title_ru',
        'meta_title_en',
        'meta_description_hy',
        'meta_description_ru',
        'meta_description_en',
        'show_on_home',
        'home_sort_order',
        'home_title_hy',
        'home_title_ru',
        'home_title_en',
        'home_description_hy',
        'home_description_ru',
        'home_description_en',
        'home_image',
    ];


    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            $category->normalizeRequiredTranslations();
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
            $fallbackSlug = 'category-' . Str::lower(Str::random(8));
        }

        foreach (['hy', 'ru', 'en'] as $locale) {
            $nameColumn = "name_{$locale}";
            $slugColumn = "slug_{$locale}";

            if (blank($this->{$nameColumn})) {
                $this->{$nameColumn} = $fallbackName ?: 'Category';
            }

            if (blank($this->{$slugColumn})) {
                $this->{$slugColumn} = $fallbackSlug;
            }
        }
    }
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'menu_order' => 'integer',
        'show_on_home' => 'boolean',
        'home_sort_order' => 'integer',
        'attribute_schema' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('menu_order')
            ->orderBy('id');
    }

    public function activeChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('menu_order')
            ->orderBy('id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->image);
    }

    public function getMenuImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->menu_image);
    }

    public function getHomeImageUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->home_image);
    }

    public function getEffectiveAttributeSchemaAttribute(): array
    {
        if (! empty($this->attribute_schema)) {
            return $this->attribute_schema;
        }

        return $this->parent?->effective_attribute_schema ?? [];
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    public function getBreadcrumbsAttribute(): array
    {
        $items = [];
        $current = $this;

        while ($current) {
            array_unshift($items, $current);
            $current = $current->parent;
        }

        return $items;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isLeaf(): bool
    {
        return ! $this->children()->exists();
    }
}
