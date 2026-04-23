<?php

namespace App\Filament\Admin\Resources\CustomOrderResource\Pages;

use App\Filament\Admin\Resources\CustomOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomOrders extends ListRecords
{
    protected static string $resource = CustomOrderResource::class;
}
