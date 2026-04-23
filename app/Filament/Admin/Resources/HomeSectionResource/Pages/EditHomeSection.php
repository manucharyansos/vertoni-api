<?php

namespace App\Filament\Admin\Resources\HomeSectionResource\Pages;

use App\Filament\Admin\Resources\HomeSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeSection extends EditRecord
{
    protected static string $resource = HomeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
