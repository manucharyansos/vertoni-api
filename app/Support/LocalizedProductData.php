<?php

namespace App\Support;

class LocalizedProductData
{
    public static function highlights(?array $items, ?string $locale = null): array
    {
        $locale = self::locale($locale);

        return collect($items ?? [])
            ->map(fn ($item) => self::localizedValue($item, $locale))
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();
    }

    public static function specifications(?array $items, ?string $locale = null): array
    {
        $locale = self::locale($locale);

        return collect($items ?? [])
            ->map(function ($item) use ($locale) {
                if (! is_array($item)) {
                    $value = self::translateScalar((string) $item, $locale);

                    return filled($value) ? [
                        'key' => null,
                        'label' => self::translateLabel(null, 'Specification', $locale),
                        'value' => $value,
                    ] : null;
                }

                $key = $item['key'] ?? null;
                $rawLabel = $item["label_{$locale}"]
                    ?? $item['label']
                    ?? $item['title']
                    ?? $key
                    ?? 'Specification';
                $rawValue = $item["value_{$locale}"]
                    ?? $item['value']
                    ?? $item['text']
                    ?? null;

                $label = self::translateLabel($key, (string) $rawLabel, $locale);
                $value = self::translateScalar((string) $rawValue, $locale);

                if (! filled($label) && ! filled($value)) {
                    return null;
                }

                return [
                    'key' => $key,
                    'label' => $label,
                    'value' => $value,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function attributes(?array $items, ?string $locale = null): array
    {
        return self::specifications($items, $locale);
    }

    private static function localizedValue(mixed $item, string $locale): ?string
    {
        if (is_array($item)) {
            $value = $item["value_{$locale}"]
                ?? $item["text_{$locale}"]
                ?? $item["label_{$locale}"]
                ?? $item['value']
                ?? $item['text']
                ?? $item['label']
                ?? $item['title']
                ?? null;

            return filled($value) ? self::translateScalar((string) $value, $locale) : null;
        }

        return filled($item) ? self::translateScalar((string) $item, $locale) : null;
    }

    private static function translateLabel(?string $key, string $fallback, string $locale): string
    {
        $labels = [
            'type' => ['hy' => 'Տեսակ', 'ru' => 'Тип', 'en' => 'Type'],
            'material' => ['hy' => 'Նյութ', 'ru' => 'Материал', 'en' => 'Material'],
            'finish' => ['hy' => 'Ավարտ', 'ru' => 'Отделка', 'en' => 'Finish'],
            'usage' => ['hy' => 'Օգտագործում', 'ru' => 'Использование', 'en' => 'Use'],
            'brand' => ['hy' => 'Բրենդ', 'ru' => 'Бренд', 'en' => 'Brand'],
            'detail' => ['hy' => 'Դետալ', 'ru' => 'Деталь', 'en' => 'Detail'],
            'style' => ['hy' => 'Ոճ', 'ru' => 'Стиль', 'en' => 'Style'],
            'fit' => ['hy' => 'Հարմարեցում', 'ru' => 'Посадка', 'en' => 'Fit'],
            'custom' => ['hy' => 'Անհատականացում', 'ru' => 'Индивидуализация', 'en' => 'Customisation'],
            'size' => ['hy' => 'Չափ', 'ru' => 'Размер', 'en' => 'Size'],
            'color' => ['hy' => 'Գույն', 'ru' => 'Цвет', 'en' => 'Colour'],
        ];

        if ($key && isset($labels[$key][$locale])) {
            return $labels[$key][$locale];
        }

        return self::translateScalar($fallback, $locale);
    }

    private static function translateScalar(string $value, string $locale): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $dictionary = [
            'Տեսակ' => ['hy' => 'Տեսակ', 'ru' => 'Тип', 'en' => 'Type'],
            'Նյութ' => ['hy' => 'Նյութ', 'ru' => 'Материал', 'en' => 'Material'],
            'Ավարտ' => ['hy' => 'Ավարտ', 'ru' => 'Отделка', 'en' => 'Finish'],
            'Օգտագործում' => ['hy' => 'Օգտագործում', 'ru' => 'Использование', 'en' => 'Use'],
            'Բրենդ' => ['hy' => 'Բրենդ', 'ru' => 'Бренд', 'en' => 'Brand'],
            'Դետալ' => ['hy' => 'Դետալ', 'ru' => 'Деталь', 'en' => 'Detail'],
            'Ոճ' => ['hy' => 'Ոճ', 'ru' => 'Стиль', 'en' => 'Style'],
            'Հարմարեցում' => ['hy' => 'Հարմարեցում', 'ru' => 'Посадка', 'en' => 'Fit'],
            'Անհատականացում' => ['hy' => 'Անհատականացում', 'ru' => 'Индивидуализация', 'en' => 'Customisation'],

            'Կոշիկ' => ['hy' => 'Կոշիկ', 'ru' => 'Обувь', 'en' => 'Shoes'],
            'Պայուսակ' => ['hy' => 'Պայուսակ', 'ru' => 'Сумка', 'en' => 'Bag'],
            'Դրամապանակ' => ['hy' => 'Դրամապանակ', 'ru' => 'Кошелёк', 'en' => 'Wallet'],
            'Գոտի' => ['hy' => 'Գոտի', 'ru' => 'Ремень', 'en' => 'Belt'],
            'Հեռախոսի պատյան' => ['hy' => 'Հեռախոսի պատյան', 'ru' => 'Чехол для телефона', 'en' => 'Phone case'],
            'Գլխարկ' => ['hy' => 'Գլխարկ', 'ru' => 'Кепка', 'en' => 'Cap'],
            'Աքսեսուար' => ['hy' => 'Աքսեսուար', 'ru' => 'Аксессуар', 'en' => 'Accessory'],
            'Բնական կաշի' => ['hy' => 'Բնական կաշի', 'ru' => 'Натуральная кожа', 'en' => 'Natural leather'],
            'Ձեռքի աշխատանք' => ['hy' => 'Ձեռքի աշխատանք', 'ru' => 'Ручная работа', 'en' => 'Handcrafted'],
            'Ամենօրյա' => ['hy' => 'Ամենօրյա', 'ru' => 'На каждый день', 'en' => 'Everyday'],
            'Կաշվե շեշտադրում' => ['hy' => 'Կաշվե շեշտադրում', 'ru' => 'Кожаный акцент', 'en' => 'Leather accent'],
            'Առօրյա' => ['hy' => 'Առօրյա', 'ru' => 'Повседневный', 'en' => 'Casual'],
            'Ըստ մոդելի' => ['hy' => 'Ըստ մոդելի', 'ru' => 'По модели', 'en' => 'Model-specific'],
            'Հնարավոր է' => ['hy' => 'Հնարավոր է', 'ru' => 'Возможно', 'en' => 'Available'],
            'VERTONI' => ['hy' => 'VERTONI', 'ru' => 'VERTONI', 'en' => 'VERTONI'],

            'Հարմարավետ կրել' => ['hy' => 'Հարմարավետ կրել', 'ru' => 'Удобная посадка', 'en' => 'Comfortable fit'],
            'Ձեռքի մաքուր ավարտ' => ['hy' => 'Ձեռքի մաքուր ավարտ', 'ru' => 'Аккуратная ручная отделка', 'en' => 'Clean hand finish'],
            'Դասական ու առօրյա տեսք' => ['hy' => 'Դասական ու առօրյա տեսք', 'ru' => 'Классический и повседневный вид', 'en' => 'Classic everyday look'],
            'Պրեմիում տեսք' => ['hy' => 'Պրեմիում տեսք', 'ru' => 'Премиальный вид', 'en' => 'Premium look'],
            'Ամուր կառուցվածք' => ['hy' => 'Ամուր կառուցվածք', 'ru' => 'Прочная конструкция', 'en' => 'Durable structure'],
            'Անհատական գույն ու չափ' => ['hy' => 'Անհատական գույն ու չափ', 'ru' => 'Индивидуальный цвет и размер', 'en' => 'Custom colour and size'],
            'Ամենօրյա օգտագործում' => ['hy' => 'Ամենօրյա օգտագործում', 'ru' => 'Ежедневное использование', 'en' => 'Daily use'],
            'Նվերային տարբերակ' => ['hy' => 'Նվերային տարբերակ', 'ru' => 'Подарочный вариант', 'en' => 'Gift option'],
            'Կոկիկ պահեստավորում' => ['hy' => 'Կոկիկ պահեստավորում', 'ru' => 'Аккуратное хранение', 'en' => 'Neat storage'],
            'Ամուր կաշի' => ['hy' => 'Ամուր կաշի', 'ru' => 'Прочная кожа', 'en' => 'Durable leather'],
            'Մետաղական ամրակ' => ['hy' => 'Մետաղական ամրակ', 'ru' => 'Металлическая фурнитура', 'en' => 'Metal hardware'],
            'Դասական գոտի' => ['hy' => 'Դասական գոտի', 'ru' => 'Классический ремень', 'en' => 'Classic belt'],
            'Պաշտպանիչ կառուցվածք' => ['hy' => 'Պաշտպանիչ կառուցվածք', 'ru' => 'Защитная конструкция', 'en' => 'Protective structure'],
            'Ըստ հեռախոսի մոդելի' => ['hy' => 'Ըստ հեռախոսի մոդելի', 'ru' => 'Под модель телефона', 'en' => 'Made for the phone model'],
            'Կաշվե մաքուր տեսք' => ['hy' => 'Կաշվե մաքուր տեսք', 'ru' => 'Чистый кожаный вид', 'en' => 'Clean leather look'],
            'Կաշվե դետալ' => ['hy' => 'Կաշվե դետալ', 'ru' => 'Кожаная деталь', 'en' => 'Leather detail'],
            'Թեթև առօրյա տեսք' => ['hy' => 'Թեթև առօրյա տեսք', 'ru' => 'Лёгкий повседневный вид', 'en' => 'Light casual look'],
            'Բրենդային շեշտադրում' => ['hy' => 'Բրենդային շեշտադրում', 'ru' => 'Фирменный акцент', 'en' => 'Branded accent'],
            'Հարմար նվեր' => ['hy' => 'Հարմար նվեր', 'ru' => 'Уместный подарок', 'en' => 'Good gift choice'],
            'Փոքր և գործնական' => ['hy' => 'Փոքր և գործնական', 'ru' => 'Небольшой и практичный', 'en' => 'Small and practical'],
            'Անհատական դետալների հնարավորություն' => ['hy' => 'Անհատական դետալների հնարավորություն', 'ru' => 'Возможность индивидуальных деталей', 'en' => 'Custom detail options'],
        ];

        return $dictionary[$value][$locale] ?? $value;
    }

    private static function locale(?string $locale): string
    {
        $locale = (string) ($locale ?: app()->getLocale());

        return in_array($locale, ['hy', 'ru', 'en'], true) ? $locale : 'hy';
    }
}
