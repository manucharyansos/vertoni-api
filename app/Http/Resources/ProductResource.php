<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        $images = $this->whenLoaded('images', function () {
            return $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image_url,
                    'url' => $image->image_url,
                    'sort_order' => (int) $image->sort_order,
                ];
            })->values();
        });

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,

            'name' => $this->getTranslated('name', $locale),
            'slug' => $this->getTranslated('slug', $locale),
            'short_description' => $this->getTranslated('short_description', $locale),

            'price' => $this->price,
            'old_price' => $this->old_price,
            'display_price' => $this->display_price,
            'display_old_price' => $this->display_old_price,

            'stock' => $this->stock,
            'has_variants' => (bool) $this->has_variants,

            'main_image' => $this->main_image_url,
            'default_image' => $this->default_image_url,

            'available_colors' => $this->available_colors,
            'available_sizes' => $this->available_sizes,
            'color_count' => max(count($this->available_colors ?? []), $this->relationLoaded('images') ? $this->images->count() : 0),
            'images' => $images,

            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'show_on_home' => (bool) ($this->show_on_home ?? false),
            'home_sort_order' => (int) ($this->home_sort_order ?? 0),

            'highlights' => $this->highlights ?? [],
            'specifications' => $this->specifications ?? [],

            'category' => $this->whenLoaded('category', function () use ($locale) {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->getTranslated('name', $locale),
                    'slug' => $this->category->getTranslated('slug', $locale),
                ];
            }),
        ];
    }
}
