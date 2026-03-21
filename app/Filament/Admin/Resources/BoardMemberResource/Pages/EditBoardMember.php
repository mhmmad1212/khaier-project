<?php

namespace App\Filament\Admin\Resources\BoardMemberResource\Pages;

use App\Filament\Admin\Resources\BoardMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoardMember extends EditRecord
{
    protected static string $resource = BoardMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
