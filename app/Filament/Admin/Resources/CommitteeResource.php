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
    protected static ?string $navigationGroup = 'المجالس واللجان';
    protected static ?int $navigationSort = 3;
    protected static ?string $model = Committee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'اللجان';
    protected static ?string $modelLabel = 'لجنة';
    protected static ?string $pluralModelLabel = 'اللجان';

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

            MediaPicker::make('attachment_media_id')
                ->label('المرفق')
                ->columnSpanFull(),

            Forms\Components\Hidden::make('attachment'),

            Forms\Components\Placeholder::make('current_attachment')
                ->label('المرفق الحالي')
                ->content(function ($record) {
                    if (! $record) {
                        return 'لا يوجد مرفق حالي';
                    }

                    if ($record->attachmentMedia?->title) {
                        return $record->attachmentMedia->title;
                    }

                    return $record->attachment ?: 'لا يوجد مرفق حالي';
                })
                ->visible(fn ($record) => filled($record?->attachment) || filled($record?->attachment_media_id))
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
                Tables\Columns\TextColumn::make('attachment_link')
                    ->label('المرفق')
                    ->getStateUsing(fn ($record) => ($record->attachment_media_id || $record->attachment) ? 'عرض المرفق' : 'لا يوجد مرفق')
                    ->badge()
                    ->color(fn ($record) => ($record->attachment_media_id || $record->attachment) ? 'info' : 'gray')
                    ->url(function ($record) {
                        if ($record->attachmentMedia?->url) {
                            return $record->attachmentMedia->url;
                        }

                        if (filled($record->attachment)) {
                            if (\Illuminate\Support\Str::startsWith($record->attachment, ['http://', 'https://'])) {
                                return $record->attachment;
                            }

                            return \Illuminate\Support\Facades\Storage::disk('public')->url($record->attachment);
                        }

                        return null;
                    })
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم اللجنة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('chairman')
                    ->label('رئيس اللجنة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('members_count')
                    ->label('عدد الأعضاء')
                    ->sortable(),

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
            'index' => Pages\ListCommittees::route('/'),
            'create' => Pages\CreateCommittee::route('/create'),
            'edit' => Pages\EditCommittee::route('/{record}/edit'),
        ];
    }
}
