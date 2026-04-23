<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\HomeSectionResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SettingResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function __invoke(Request $request)
    {
        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->where('show_on_home', true)
            ->orderBy('home_sort_order')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(3)
            ->get();

        if ($categories->count() < 3) {
            $fallbackCategories = Category::query()
                ->where('is_active', true)
                ->whereNotIn('id', $categories->pluck('id'))
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(3 - $categories->count())
                ->get();

            $categories = $categories->concat($fallbackCategories)->values();
        }

        $products = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('show_on_home', true)
                    ->orWhere('is_featured', true);
            })
            ->orderByDesc('show_on_home')
            ->orderBy('home_sort_order')
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $sections = HomeSection::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $settings = Setting::query()
            ->where('is_public', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'banners' => BannerResource::collection($banners)->resolve($request),
            'categories' => CategoryResource::collection($categories)->resolve($request),
            'products' => ProductResource::collection($products)->resolve($request),
            'sections' => HomeSectionResource::collection($sections)->resolve($request),
            'settings' => SettingResource::collection($settings)->resolve($request),
        ]);
    }
}
