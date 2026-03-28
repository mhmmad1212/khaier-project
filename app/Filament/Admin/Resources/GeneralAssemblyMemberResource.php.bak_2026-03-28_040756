<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\GeneralAssemblyMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GeneralAssemblyMemberResource extends Resource
{
    protected static ?string $model = GeneralAssemblyMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'الجمعية العمومية';
    protected static ?string $modelLabel = 'عضو جمعية عمومية';
    protected static ?string $pluralModelLabel = 'الجمعية العمومية';
    protected static ?string $navigationGroup = 'الهيكل الإداري';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('position')
                ->label('المنصب')
                ->maxLength(255),

            Forms\Components\DatePicker::make('join_date')
                ->label('تاريخ الانضمام'),

            Forms\Components\Hidden::make('photo_media_id')
                ->dehydrated(false),

            MediaPicker::make('photo_media_id')
                ->label('الصورة')
                ->columnSpanFull(),

            Forms\Components\Placeholder::make('current_photo')
                ->label('الصورة الحالية')
                ->content(fn ($record) => $record && $record->photo ? $record->photo : 'لا توجد صورة حالية')
                ->visible(fn ($record) => filled($record?->photo))
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
                    ->label('الاسم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('المنصب')
                    ->searchable(),

                Tables\Columns\TextColumn::make('join_date')
                    ->label('تاريخ الانضمام')
                    ->date(),

                Tables\Columns\TextColumn::make('photo')
                    ->label('الصورة')
                    ->limit(30)
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
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralAssemblyMembers::route('/'),
            'create' => Pages\CreateGeneralAssemblyMember::route('/create'),
            'edit' => Pages\EditGeneralAssemblyMember::route('/{record}/edit'),
        ];
    }
}
