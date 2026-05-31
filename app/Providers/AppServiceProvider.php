<?php

namespace App\Providers;

use App\Filament\Admin\Resources\AnalyticsEventResource\Pages\ListAnalyticsEvents;
use App\Filament\Admin\Resources\AnalyticsPageViewResource\Pages\ListAnalyticsPageViews;
use App\Filament\Admin\Resources\AnalyticsPageViewResource\Widgets\AnalyticsStatsOverview;
use App\Filament\Admin\Resources\AnalyticsVisitorResource\Pages\ListAnalyticsVisitors;
use App\Filament\Admin\Resources\BannerResource\Pages\CreateBanner;
use App\Filament\Admin\Resources\BannerResource\Pages\EditBanner;
use App\Filament\Admin\Resources\BannerResource\Pages\ListBanners;
use App\Filament\Admin\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Admin\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Admin\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Admin\Resources\ContactMessageResource\Pages\CreateContactMessage;
use App\Filament\Admin\Resources\ContactMessageResource\Pages\EditContactMessage;
use App\Filament\Admin\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Admin\Resources\CustomOrderResource\Pages\CreateCustomOrder;
use App\Filament\Admin\Resources\CustomOrderResource\Pages\EditCustomOrder;
use App\Filament\Admin\Resources\CustomOrderResource\Pages\ListCustomOrders;
use App\Filament\Admin\Resources\HomeSectionResource\Pages\CreateHomeSection;
use App\Filament\Admin\Resources\HomeSectionResource\Pages\EditHomeSection;
use App\Filament\Admin\Resources\HomeSectionResource\Pages\ListHomeSections;
use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages\CreateNewsletterSubscription;
use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages\EditNewsletterSubscription;
use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages\ListNewsletterSubscriptions;
use App\Filament\Admin\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Admin\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Admin\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Admin\Resources\SettingResource\Pages\CreateSetting;
use App\Filament\Admin\Resources\SettingResource\Pages\EditSetting;
use App\Filament\Admin\Resources\SettingResource\Pages\ListSettings;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerFilamentLivewireAliases();
    }

    /**
     * Livewire sometimes keeps an old component snapshot after the Filament
     * resources are moved/renamed during deployment. Explicit aliases make the
     * admin edit/create/list pages stable after git pull, cache clear, and hard refresh.
     */
    private function registerFilamentLivewireAliases(): void
    {
        $aliases = [
            'app.filament.admin.resources.analytics-event-resource.pages.list-analytics-events' => ListAnalyticsEvents::class,
            'app.filament.admin.resources.analytics-page-view-resource.pages.list-analytics-page-views' => ListAnalyticsPageViews::class,
            'app.filament.admin.resources.analytics-page-view-resource.widgets.analytics-stats-overview' => AnalyticsStatsOverview::class,
            'app.filament.admin.resources.analytics-visitor-resource.pages.list-analytics-visitors' => ListAnalyticsVisitors::class,

            'app.filament.admin.resources.banner-resource.pages.list-banners' => ListBanners::class,
            'app.filament.admin.resources.banner-resource.pages.create-banner' => CreateBanner::class,
            'app.filament.admin.resources.banner-resource.pages.edit-banner' => EditBanner::class,

            'app.filament.admin.resources.category-resource.pages.list-categories' => ListCategories::class,
            'app.filament.admin.resources.category-resource.pages.create-category' => CreateCategory::class,
            'app.filament.admin.resources.category-resource.pages.edit-category' => EditCategory::class,

            'app.filament.admin.resources.contact-message-resource.pages.list-contact-messages' => ListContactMessages::class,
            'app.filament.admin.resources.contact-message-resource.pages.create-contact-message' => CreateContactMessage::class,
            'app.filament.admin.resources.contact-message-resource.pages.edit-contact-message' => EditContactMessage::class,

            'app.filament.admin.resources.custom-order-resource.pages.list-custom-orders' => ListCustomOrders::class,
            'app.filament.admin.resources.custom-order-resource.pages.create-custom-order' => CreateCustomOrder::class,
            'app.filament.admin.resources.custom-order-resource.pages.edit-custom-order' => EditCustomOrder::class,

            'app.filament.admin.resources.home-section-resource.pages.list-home-sections' => ListHomeSections::class,
            'app.filament.admin.resources.home-section-resource.pages.create-home-section' => CreateHomeSection::class,
            'app.filament.admin.resources.home-section-resource.pages.edit-home-section' => EditHomeSection::class,

            'app.filament.admin.resources.newsletter-subscription-resource.pages.list-newsletter-subscriptions' => ListNewsletterSubscriptions::class,
            'app.filament.admin.resources.newsletter-subscription-resource.pages.create-newsletter-subscription' => CreateNewsletterSubscription::class,
            'app.filament.admin.resources.newsletter-subscription-resource.pages.edit-newsletter-subscription' => EditNewsletterSubscription::class,

            'app.filament.admin.resources.product-resource.pages.list-products' => ListProducts::class,
            'app.filament.admin.resources.product-resource.pages.create-product' => CreateProduct::class,
            'app.filament.admin.resources.product-resource.pages.edit-product' => EditProduct::class,

            'app.filament.admin.resources.setting-resource.pages.list-settings' => ListSettings::class,
            'app.filament.admin.resources.setting-resource.pages.create-setting' => CreateSetting::class,
            'app.filament.admin.resources.setting-resource.pages.edit-setting' => EditSetting::class,
        ];

        foreach ($aliases as $alias => $component) {
            Livewire::component($alias, $component);
        }
    }
}
