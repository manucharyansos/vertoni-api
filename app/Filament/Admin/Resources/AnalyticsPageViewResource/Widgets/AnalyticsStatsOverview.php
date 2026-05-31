<?php

namespace App\Filament\Admin\Resources\AnalyticsPageViewResource\Widgets;

use App\Models\AnalyticsPageView;
use App\Models\AnalyticsVisitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class AnalyticsStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        if (! DatabaseSchema::hasTable('analytics_page_views') || ! DatabaseSchema::hasTable('analytics_visitors')) {
            return [
                Stat::make('Analytics', 'DB պատրաստ չէ')
                    ->description('Աշխատացրու php artisan migrate --force'),
            ];
        }

        $today = Carbon::today();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fifteenMinutesAgo = Carbon::now()->subMinutes(15);

        $todayViews = AnalyticsPageView::query()->where('viewed_at', '>=', $today)->count();
        $todayVisitors = AnalyticsPageView::query()->where('viewed_at', '>=', $today)->distinct('visitor_id')->count('visitor_id');
        $weekViews = AnalyticsPageView::query()->where('viewed_at', '>=', $sevenDaysAgo)->count();
        $weekVisitors = AnalyticsPageView::query()->where('viewed_at', '>=', $sevenDaysAgo)->distinct('visitor_id')->count('visitor_id');
        $onlineNow = AnalyticsVisitor::query()->where('last_seen_at', '>=', $fifteenMinutesAgo)->count();

        $topPage = AnalyticsPageView::query()
            ->selectRaw('path, COUNT(*) as views_count')
            ->where('viewed_at', '>=', $sevenDaysAgo)
            ->groupBy('path')
            ->orderByDesc('views_count')
            ->first();

        return [
            Stat::make('Այսօրվա դիտումներ', number_format($todayViews))
                ->description('Այսօր բացված էջերի քանակ'),

            Stat::make('Այսօրվա այցելուներ', number_format($todayVisitors))
                ->description('Unique visitor_id ըստ browser-ի'),

            Stat::make('Վերջին 7 օր', number_format($weekViews))
                ->description(number_format($weekVisitors) . ' այցելու'),

            Stat::make('Հիմա կայքում', number_format($onlineNow))
                ->description('Վերջին 15 րոպեում ակտիվ'),

            Stat::make('Ամենաշատ բացված էջ', $topPage?->path ?? 'Տվյալ չկա')
                ->description($topPage ? number_format((int) $topPage->views_count) . ' դիտում / 7 օր' : 'Դեռ այցեր չկան'),
        ];
    }
}
