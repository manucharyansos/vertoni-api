<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,

            'name' => $this->getTranslated('name', $locale),
            'slug' => $this->getTranslated('slug', $locale),
            'short_description' => $this->getTranslated('short_description', $locale),
            'description' => $this->getTranslated('description', $locale),

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

            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,

            'highlights' => $this->highlights ?? [],
            'specifications' => $this->specifications ?? [],

            'category' => $this->whenLoaded('category', function () use ($locale) {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->getTranslated('name', $locale),
                    'slug' => $this->category->getTranslated('slug', $locale),
                    'attribute_schema' => $this->category->effective_attribute_schema,
                    'breadcrumbs' => collect($this->category->breadcrumbs)->map(function ($item) use ($locale) {
                        return [
                            'id' => $item->id,
                            'name' => $item->getTranslated('name', $locale),
                            'slug' => $item->getTranslated('slug', $locale),
                        ];
                    })->values(),
                ];
            }),

            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image' => $image->image_url,
                        'sort_order' => (int) $image->sort_order,
                    ];
                })->values();
            }),

            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants
                    ->sortBy(['sort_order', 'id'])
                    ->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'size' => $variant->size,
                            'color' => $variant->color,
                            'display_name' => $variant->display_name,
                            'attributes' => $variant->attributes ?? [],
                            'sku' => $variant->sku,
                            'image' => $variant->image_url,
                            'price' => $variant->price,
                            'stock' => $variant->stock,
                            'is_active' => (bool) $variant->is_active,
                            'sort_order' => (int) $variant->sort_order,
                        ];
                    })
                    ->values();
            }),
        ];
    }
}
