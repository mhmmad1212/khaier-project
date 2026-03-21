<?php

namespace App\Filament\Admin\Resources\BoardMemberResource\Pages;

use App\Filament\Admin\Resources\BoardMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBoardMember extends CreateRecord
{
    protected static string $resource = BoardMemberResource::class;
}
