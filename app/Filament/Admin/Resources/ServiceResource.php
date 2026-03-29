<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ServiceResource\Pages;
use App\Forms\Components\LocalIconGrid;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $navigationGroup = 'المشاريع والبرامج';
    protected static ?int $navigationSort = 2;
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'الخدمات';
    protected static ?string $modelLabel = 'خدمة';
    protected static ?string $pluralModelLabel = 'الخدمات';
    
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم الخدمة')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('وصف الخدمة')
                ->rows(4)
                ->columnSpanFull(),

            LocalIconGrid::make('icon')
                ->label('الأيقونة')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('url')
                ->label('الرابط')
                ->url()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخدمة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(40)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('icon')
                    ->label('الأيقونة')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->limit(35)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
