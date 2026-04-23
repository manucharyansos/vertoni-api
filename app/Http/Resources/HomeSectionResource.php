<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        return [
            'id' => $this->id,
            'key' => $this->key,
            'type' => $this->type,
            'eyebrow' => $this->getTranslated('eyebrow', $locale),
            'title' => $this->getTranslated('title', $locale),
            'description' => $this->getTranslated('description', $locale),
            'button_text' => $this->getTranslated('button_text', $locale),
            'button_link' => $this->button_link,
            'image' => $this->image_url,
            'mobile_image' => $this->mobile_image_url,
            'video' => $this->video_url,
            'layout' => $this->layout,
            'text_position' => $this->text_position,
            'theme' => $this->theme,
            'sort_order' => (int) $this->sort_order,
            'is_active' => (bool) $this->is_active,
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
