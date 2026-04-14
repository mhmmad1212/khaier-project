<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $navigationGroup = 'إعدادات الموقع';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;


    
    
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-right';
    protected static ?string $navigationLabel = 'القوائم';
    protected static ?string $modelLabel = 'قائمة';
    protected static ?string $pluralModelLabel = 'القوائم';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم القائمة')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('location')
                ->label('مكان القائمة')
                ->options([
                    'main' => 'الرئيسية',
                    'footer' => 'التذييل',
                    'quick_links' => 'روابط سريعة',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم القائمة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('المكان'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('الإنشاء')
                    ->dateTime('Y-m-d H:i'),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
