<?php

namespace App\Filament\Admin\Resources\ProgramProjectResource\RelationManagers;

use App\Forms\Components\MediaPicker;
use App\Models\Attachment;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'مرفقات المشروع';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            MediaPicker::make('media_item_id')
                ->label('الملف')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('collection')
                ->label('نوع المرفق')
                ->options([
                    'gallery' => 'صور المشروع',
                    'report' => 'تقرير',
                    'document' => 'ملف',
                ])
                ->default('gallery')
                ->required(),

            Forms\Components\TextInput::make('title')
                ->label('عنوان (اختياري)')
                ->maxLength(255),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('collection')
                    ->label('النوع'),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان'),

                Tables\Columns\TextColumn::make('media.title')
                    ->label('الملف'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function ($livewire, array $data) {
                        // هنا الحل الحقيقي
                        return $livewire->getOwnerRecord()
                            ->attachments()
                            ->create([
                                'media_item_id' => $data['media_item_id'] ?? null,
                                'collection' => $data['collection'] ?? 'gallery',
                                'title' => $data['title'] ?? null,
                                'sort_order' => $data['sort_order'] ?? 0,
                                'is_active' => $data['is_active'] ?? true,
                                'section_code' => Attachment::SECTION_PROGRAM_PROJECTS,
                            ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function ($record, array $data) {
                        $record->update([
                            'media_item_id' => $data['media_item_id'] ?? $record->media_item_id,
                            'collection' => $data['collection'] ?? $record->collection,
                            'title' => $data['title'] ?? $record->title,
                            'sort_order' => $data['sort_order'] ?? $record->sort_order,
                            'is_active' => $data['is_active'] ?? $record->is_active,
                        ]);

                        return $record;
                    }),

                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
