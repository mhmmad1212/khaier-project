<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use App\Services\AssociationProvisioningService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateAssociation extends CreateRecord
{
    protected static string $resource = AssociationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['domain_type'] ?? null) === 'subdomain') {
            $label = trim((string) ($data['subdomain_label'] ?? ''));
            $label = strtolower(preg_replace('/[^a-z0-9\\-]/', '', str_replace(' ', '-', $label)));
            $data['subdomain_label'] = $label;
            $data['domain'] = $label . '.' . env('TENANT_BASE_DOMAIN', 'ramm.sa');
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            $credentials = app(AssociationProvisioningService::class)->provision($this->record);

            Notification::make()
                ->title('تم إنشاء الجمعية بنجاح')
                ->body(
                    "تم تجهيز الجمعية.\n" .
                    "بريد المدير: {$credentials['email']}\n" .
                    "كلمة المرور المؤقتة: {$credentials['password']}"
                )
                ->success()
                ->persistent()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title('فشل إنشاء الجمعية')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            throw $e;
        }
    }
}
