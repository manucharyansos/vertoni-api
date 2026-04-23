<?php

namespace App\Traits;

trait HasTranslations
{
    public function getCurrentLocale(): string
    {
        $locale = request()->get('locale') ?? app()->getLocale();

        return in_array($locale, ['hy', 'ru', 'en']) ? $locale : 'hy';
    }

    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? $this->getCurrentLocale();
        $column = "{$field}_{$locale}";

        return $this->{$column} ?? null;
    }
}
