<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Jobs\SendProductNewsletterEmails;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendNewsletter')
                ->label('Ուղարկել newsletter')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ուղարկել այս ապրանքը բաժանորդներին')
                ->modalDescription('Նամակները կուղարկվեն response-ից հետո, որպեսզի admin-ի էջը չդանդաղի։')
                ->action(function (): void {
                    if (! $this->record->is_active) {
                        Notification::make()
                            ->title('Չի ուղարկվել')
                            ->body('Ապրանքը ակտիվ չէ։ Նախ ակտիվացրու ապրանքը։')
                            ->warning()
                            ->send();

                        return;
                    }

                    SendProductNewsletterEmails::dispatch($this->record->id)->afterResponse();

                    Notification::make()
                        ->title('Newsletter-ը դրվեց ուղարկման հերթի մեջ')
                        ->body('Բաժանորդներին նամակները կուղարկվեն admin-ի էջը չդանդաղեցնելով։')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
