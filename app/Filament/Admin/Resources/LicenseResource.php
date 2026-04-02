<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LicenseResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $navigationGroup = 'الشفافية والحوكمة';
    protected static ?int $navigationSort = 2;
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'التراخيص';
    protected static ?string $modelLabel = 'ترخيص';
    protected static ?string $pluralModelLabel = 'التراخيص';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('اسم الترخيص')
                ->required()
                ->maxLength(255),

            MediaPicker::make('file_media_id')
                ->label('مرفق الترخيص')
                ->default(fn () => request('selected_media_id'))
                ->columnSpanFull(),

            Forms\Components\Hidden::make('file')
                ->default(fn () => request('selected_media_file')),

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
                Tables\Columns\TextColumn::make('title')
                    ->label('اسم الترخيص')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file')
                    ->label('المرفق')
                    ->limit(40),

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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
