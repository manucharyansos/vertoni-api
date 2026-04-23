<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true);

        if ($request->filled('category_id')) {
            $query->whereIn('category_id', $this->collectDescendantCategoryIds($request->integer('category_id')));
        }

        if ($request->filled('category_slug')) {
            $slug = $request->string('category_slug')->toString();
            $category = Category::query()
                ->where('is_active', true)
                ->where(function ($builder) use ($slug) {
                    $builder->where('slug_hy', $slug)
                        ->orWhere('slug_ru', $slug)
                        ->orWhere('slug_en', $slug);
                })
                ->first();

            if ($category) {
                $query->whereIn('category_id', $this->collectDescendantCategoryIds($category->id));
            }
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('search')) {
            $needle = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($needle) {
                $builder->where('name_hy', 'like', "%{$needle}%")
                    ->orWhere('name_ru', 'like', "%{$needle}%")
                    ->orWhere('name_en', 'like', "%{$needle}%")
                    ->orWhere('short_description_hy', 'like', "%{$needle}%")
                    ->orWhere('short_description_ru', 'like', "%{$needle}%")
                    ->orWhere('short_description_en', 'like', "%{$needle}%")
                    ->orWhere('sku', 'like', "%{$needle}%");
            });
        }

        if ($request->filled('color')) {
            $color = trim((string) $request->input('color'));
            $query->whereHas('activeVariants', function ($variantQuery) use ($color) {
                $variantQuery->where('color', $color);
            });
        }

        if ($request->filled('size')) {
            $size = trim((string) $request->input('size'));
            $query->whereHas('activeVariants', function ($variantQuery) use ($size) {
                $variantQuery->where('size', $size);
            });
        }

        $sort = $request->string('sort')->toString();

        $query = match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name_hy'),
            default => $query->orderByDesc('is_featured')->orderByDesc('id'),
        };

        return ProductResource::collection($query->get());
    }

    public function show(Request $request, string $slug)
    {
        $product = Product::query()
            ->with([
                'category.parent',
                'images',
                'variants' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id'),
            ])
            ->where('is_active', true)
            ->where(function ($query) use ($slug) {
                $query->where('slug_hy', $slug)
                    ->orWhere('slug_ru', $slug)
                    ->orWhere('slug_en', $slug);
            })
            ->firstOrFail();

        return new ProductDetailResource($product);
    }

    private function collectDescendantCategoryIds(int $categoryId): array
    {
        $all = Category::query()->select(['id', 'parent_id'])->get();
        $ids = [$categoryId];
        $queue = [$categoryId];

        while ($queue) {
            $current = array_shift($queue);
            $children = $all->where('parent_id', $current);

            foreach ($children as $child) {
                if (! in_array($child->id, $ids, true)) {
                    $ids[] = $child->id;
                    $queue[] = $child->id;
                }
            }
        }

        return $ids;
    }
}
