<?php

namespace App\Filament\Resources\AssociationResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'حركات الجمعية';
    protected static ?string $modelLabel = 'حركة';
    protected static ?string $pluralModelLabel = 'حركات الجمعية';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action_code')
                    ->label('الرمز')
                    ->badge(),

                Tables\Columns\TextColumn::make('action_type')
                    ->label('النوع')
                    ->badge(),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('details')
                    ->label('التفاصيل')
                    ->wrap()
                    ->limit(120),

                Tables\Columns\TextColumn::make('performedBy.name')
                    ->label('بواسطة')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
