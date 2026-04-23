<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartValidationController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'in:hy,ru,en'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $locale = $validated['locale'] ?? app()->getLocale();
        $items = collect($validated['items'] ?? []);

        if ($items->isEmpty()) {
            return response()->json(['items' => []]);
        }

        $productIds = $items->pluck('product_id')->filter()->unique()->values();
        $variantIds = $items->pluck('variant_id')->filter()->unique()->values();

        $products = Product::query()
            ->where('is_active', true)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $variants = ProductVariant::query()
            ->where('is_active', true)
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $cleanItems = [];

        foreach ($items as $item) {
            $product = $products->get((int) $item['product_id']);

            if (! $product) {
                continue;
            }

            $variant = null;
            $price = $product->price;
            $stock = (int) $product->stock;
            $image = $product->main_image_url ?: $product->default_image_url;
            $size = null;
            $color = null;

            if (! empty($item['variant_id'])) {
                $variant = $variants->get((int) $item['variant_id']);

                if (! $variant || (int) $variant->product_id !== (int) $product->id) {
                    continue;
                }

                $price = $variant->price ?? $product->price;
                $stock = (int) $variant->stock;
                $image = $variant->image_url ?: ($product->main_image_url ?: $product->default_image_url);
                $size = $variant->size;
                $color = $variant->color;
            }

            if ($stock <= 0) {
                continue;
            }

            $quantity = min((int) $item['quantity'], $stock);

            $cleanItems[] = [
                'product_id' => (int) $product->id,
                'variant_id' => $variant?->id,
                'name' => $product->getTranslated('name', $locale) ?: $product->getTranslated('name', 'hy') ?: 'Product',
                'slug' => $product->getTranslated('slug', $locale) ?: $product->getTranslated('slug', 'hy') ?: (string) $product->id,
                'image' => $image,
                'price' => (float) $price,
                'quantity' => $quantity,
                'stock' => $stock,
                'size' => $size,
                'color' => $color,
            ];
        }

        return response()->json(['items' => $cleanItems]);
    }
}
