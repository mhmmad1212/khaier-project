<?php
namespace App\Filament\Traits;

use Filament\Actions\Action;

trait HasBackButton
{
    // إضافة زر "العودة للقائمة" كزر رابع
    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        $actions[] = Action::make('back_to_list')
            ->label('العودة للقائمة')
            ->color('gray')
            ->url(fn () => $this->getResource()::getUrl('index'));

        return $actions;
    }

    // التوجيه التلقائي للقائمة بعد نجاح الحفظ أو الإضافة
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}