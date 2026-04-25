<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartValidationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\CustomOrderController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\NewsletterSubscriptionController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

$registerPublicApiRoutes = function (): void {
    Route::get('/home', HomepageController::class);
    Route::get('/homepage', HomepageController::class);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    Route::post('/cart/validate', CartValidationController::class);
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::post('/contact', [ContactMessageController::class, 'store']);
    Route::post('/newsletter', [NewsletterSubscriptionController::class, 'store']);
    Route::post('/custom-orders', [CustomOrderController::class, 'store']);
};

$registerPublicApiRoutes();

Route::prefix('v1')->group(function () use ($registerPublicApiRoutes) {
    $registerPublicApiRoutes();
});
