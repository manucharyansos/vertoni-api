<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($validated) {
            $subtotal = 0;

            $order = Order::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'note' => $validated['note'] ?? null,
                'status' => 'new',
                'subtotal' => 0,
                'total' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->where('is_active', true)
                    ->findOrFail($item['product_id']);

                $variant = null;
                $price = $product->price;
                $stock = $product->stock;
                $image = $product->main_image_url;
                $size = null;
                $color = null;

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::query()
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->findOrFail($item['variant_id']);

                    $price = $variant->price ?? $product->price;
                    $stock = $variant->stock;
                    $image = $variant->image_url ?: $product->main_image_url;

                    $size = $variant->size;
                    $color = $variant->color;
                }

                if ($item['quantity'] > $stock) {
                    throw ValidationException::withMessages([
                        'items' => ["Requested quantity exceeds stock for product #{$product->id}"],
                    ]);
                }

                $lineTotal = (float) $price * (int) $item['quantity'];
                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name_hy ?? $product->name_ru ?? $product->name_en ?? 'Product',
                    'product_slug' => $product->slug_hy ?? $product->slug_ru ?? $product->slug_en,
                    'size' => $size,
                    'color' => $color,
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                    'image' => $image,
                ]);

                if ($variant) {
                    $variant->decrement('stock', (int) $item['quantity']);
                } else {
                    $product->decrement('stock', (int) $item['quantity']);
                }
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            return $order->load('items');
        });

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }
}
