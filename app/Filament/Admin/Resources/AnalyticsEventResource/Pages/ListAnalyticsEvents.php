<?php

namespace App\Filament\Admin\Resources\AnalyticsEventResource\Pages;

use App\Filament\Admin\Resources\AnalyticsEventResource;
use Filament\Resources\Pages\ListRecords;

class ListAnalyticsEvents extends ListRecords
{
    protected static string $resource = AnalyticsEventResource::class;

    public function getTitle(): string
    {
        return 'Կայքի գործողություններ';
    }
}
