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

        $product->setRelation('relatedProducts', $this->relatedProductsFor($product));

        return new ProductDetailResource($product);
    }


    private function relatedProductsFor(Product $product, int $limit = 8)
    {
        $baseQuery = fn () => Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->whereKeyNot($product->id);

        $items = collect();

        if ($product->category_id) {
            $items = $items->merge(
                $baseQuery()
                    ->where('category_id', $product->category_id)
                    ->orderByDesc('is_featured')
                    ->orderByDesc('id')
                    ->limit($limit)
                    ->get()
            );
        }

        if ($items->count() < $limit && $product->category) {
            $categoryIds = $this->collectRelatedCategoryIds($product->category);

            if ($categoryIds) {
                $items = $items->merge(
                    $baseQuery()
                        ->whereIn('category_id', $categoryIds)
                        ->orderByDesc('is_featured')
                        ->orderByDesc('id')
                        ->limit($limit * 2)
                        ->get()
                );
            }
        }

        if ($items->count() < $limit) {
            $items = $items->merge(
                $baseQuery()
                    ->orderByDesc('is_featured')
                    ->orderByDesc('id')
                    ->limit($limit * 2)
                    ->get()
            );
        }

        return $items
            ->unique('id')
            ->take($limit)
            ->values();
    }

    private function collectRelatedCategoryIds(Category $category): array
    {
        $ids = [$category->id];

        if ($category->parent_id) {
            $ids[] = $category->parent_id;

            $siblingIds = Category::query()
                ->where('is_active', true)
                ->where('parent_id', $category->parent_id)
                ->pluck('id')
                ->all();

            $ids = array_merge($ids, $siblingIds);
        } else {
            $ids = array_merge($ids, $this->collectDescendantCategoryIds($category->id));
        }

        return array_values(array_unique(array_map('intval', $ids)));
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
