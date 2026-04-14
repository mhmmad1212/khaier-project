<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StatisticResource\Pages;

use App\Models\Statistic;
use Filament\Forms;
use App\Forms\Components\LocalIconGrid;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StatisticResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;


    protected static ?string $model = Statistic::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'الإحصائيات';
    protected static ?string $modelLabel = 'إحصائية';
    protected static ?string $pluralModelLabel = 'الإحصائيات';
    
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('عنوان الإحصائية')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('value')
                ->label('القيمة')
                ->required()
                ->maxLength(255),

            LocalIconGrid::make('icon')
                ->label('الأيقونة')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('القيمة')
                    ->searchable(),

                Tables\Columns\ViewColumn::make('icon')
                    ->label('الأيقونة')
                    ->view('filament.tables.columns.menu-item-icon'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatistics::route('/'),
            'create' => Pages\CreateStatistic::route('/create'),
            'edit' => Pages\EditStatistic::route('/{record}/edit'),
        ];
    }
}
