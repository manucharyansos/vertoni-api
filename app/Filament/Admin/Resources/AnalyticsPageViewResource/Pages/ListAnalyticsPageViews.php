<?php

namespace App\Filament\Admin\Resources\AnalyticsPageViewResource\Pages;

use App\Filament\Admin\Resources\AnalyticsPageViewResource;
use App\Filament\Admin\Resources\AnalyticsPageViewResource\Widgets\AnalyticsStatsOverview;
use Filament\Resources\Pages\ListRecords;

class ListAnalyticsPageViews extends ListRecords
{
    protected static string $resource = AnalyticsPageViewResource::class;

    public function getTitle(): string
    {
        return 'Կայքի այցելություններ';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsStatsOverview::class,
        ];
    }
}
