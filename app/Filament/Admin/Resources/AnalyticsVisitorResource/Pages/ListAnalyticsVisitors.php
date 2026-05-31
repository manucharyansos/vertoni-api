<?php

namespace App\Filament\Admin\Resources\AnalyticsVisitorResource\Pages;

use App\Filament\Admin\Resources\AnalyticsVisitorResource;
use Filament\Resources\Pages\ListRecords;

class ListAnalyticsVisitors extends ListRecords
{
    protected static string $resource = AnalyticsVisitorResource::class;

    public function getTitle(): string
    {
        return 'Կայքի հաճախողներ';
    }
}
