<?php

namespace App\Filament\Admin\Resources\CustomOrderResource\Pages;

use App\Filament\Admin\Resources\CustomOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomOrder extends CreateRecord
{
    protected static string $resource = CustomOrderResource::class;
}
