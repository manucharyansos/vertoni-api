<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        return [
            'id' => $this->id,
            'title' => $this->getTranslated('title', $locale),
            'subtitle' => $this->getTranslated('subtitle', $locale),
            'button_text' => $this->getTranslated('button_text', $locale),
            'button_link' => $this->button_link,
            'link' => $this->button_link,

            'media' => $this->media_url,
            'media_type' => $this->media_type,
            'image' => $this->image_url,
            'video' => $this->video_url,

            'sort_order' => (int) $this->sort_order,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
