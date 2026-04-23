<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type' => $this->type,

            'name' => $this->getTranslated('name', $locale),
            'slug' => $this->getTranslated('slug', $locale),
            'description' => $this->getTranslated('description', $locale),

            'image' => $this->image_url,
            'menu_title' => $this->menu_title ?: $this->getTranslated('name', $locale),
            'menu_description' => $this->menu_description ?: $this->getTranslated('description', $locale),
            'menu_image' => $this->menu_image_url ?: $this->image_url,

            'show_on_home' => (bool) ($this->show_on_home ?? false),
            'home_sort_order' => (int) ($this->home_sort_order ?? 0),
            'home_title' => $this->getTranslated('home_title', $locale) ?: $this->getTranslated('name', $locale),
            'home_description' => $this->getTranslated('home_description', $locale) ?: $this->getTranslated('description', $locale),
            'home_image' => $this->home_image_url ?: $this->image_url,

            'product_count' => (int) ($this->products_count ?? 0),
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) $this->sort_order,
            'menu_order' => (int) ($this->menu_order ?? 0),
            'depth' => $this->depth,

            'attribute_schema' => $this->effective_attribute_schema,

            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
