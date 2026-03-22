<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CommitteeResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\Committee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommitteeResource extends Resource
{
    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'اللجان';
    protected static ?string $modelLabel = 'لجنة';
    protected static ?string $pluralModelLabel = 'اللجان';
    protected static ?string $navigationGroup = 'الهيكل الإداري';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم اللجنة')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->label('الوصف')
                ->rows(5)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('chairman')
                ->label('رئيس اللجنة')
                ->maxLength(255),

            Forms\Components\TextInput::make('members_count')
                ->label('عدد الأعضاء')
                ->numeric(),

            Forms\Components\Hidden::make('attachment_media_id')
                ->dehydrated(false),

            MediaPicker::make('attachment_media_id')
                ->label('المرفق')
                ->columnSpanFull(),

            Forms\Components\Placeholder::make('current_attachment')
                ->label('المرفق الحالي')
                ->content(fn ($record) => $record && $record->attachment ? $record->attachment : 'لا يوجد مرفق حالي')
                ->visible(fn ($record) => filled($record?->attachment))
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
                    ->label('اسم اللجنة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('chairman')
                    ->label('رئيس اللجنة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('members_count')
                    ->label('عدد الأعضاء')
                    ->sortable(),

                Tables\Columns\TextColumn::make('attachment')
                    ->label('المرفق')
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
            'index' => Pages\ListCommittees::route('/'),
            'create' => Pages\CreateCommittee::route('/create'),
            'edit' => Pages\EditCommittee::route('/{record}/edit'),
        ];
    }
}
