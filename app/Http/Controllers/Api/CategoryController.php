<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $tree = $request->boolean('tree', false);

        $baseQuery = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')
            ->orderBy('menu_order')
            ->orderBy('id');

        if ($tree) {
            $categories = $baseQuery
                ->whereNull('parent_id')
                ->with([
                    'children' => function ($query) {
                        $query->withCount(['products' => fn ($productQuery) => $productQuery->where('is_active', true)])
                            ->with(['children' => function ($childQuery) {
                                $childQuery->withCount(['products' => fn ($productQuery) => $productQuery->where('is_active', true)])
                                    ->orderBy('sort_order')
                                    ->orderBy('menu_order')
                                    ->orderBy('id');
                            }])
                            ->orderBy('sort_order')
                            ->orderBy('menu_order')
                            ->orderBy('id');
                    },
                ])
                ->get();
        } else {
            $categories = $baseQuery->get();
        }

        return CategoryResource::collection($categories);
    }
}
