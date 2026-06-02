<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Jobs\SendProductNewsletterEmails;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected bool $notifySubscribers = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->notifySubscribers = (bool) ($data['notify_subscribers'] ?? false);

        unset($data['notify_subscribers']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! $this->notifySubscribers) {
            return;
        }

        if (! $this->record->is_active) {
            Notification::make()
                ->title('Newsletter չի ուղարկվել')
                ->body('Ապրանքը ակտիվ չէ։ Ակտիվացրու ապրանքը և ձեռքով ուղարկիր newsletter-ը խմբագրման էջից։')
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
    }
}
