<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\RelationManagers;

use App\Forms\Components\MediaPicker;
use App\Models\Attachment;
use App\Support\UnifiedMediaPicker;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'مرفقات الجمعية العمومية';

    public function form(Forms\Form $form): Forms\Form
    {
        $payload = UnifiedMediaPicker::selectedPayload(request());

        return $form->schema([
            MediaPicker::make('media_item_id')
                ->label('الملف')
                ->default(fn () => $payload['selected_media_id'] ?? null)
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('collection')
                ->label('نوع المرفق')
                ->options([
                    'document' => 'ملف',
                    'image' => 'صورة',
                ])
                ->default('document')
                ->required(),

            Forms\Components\TextInput::make('title')
                ->label('عنوان (اختياري)'),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
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
                    ->mutateFormDataUsing(function ($data) {
                        $payload = UnifiedMediaPicker::selectedPayload(request());

                        if (! empty($payload['selected_media_id'])) {
                            $data['media_item_id'] = $payload['selected_media_id'];
                        }

                        $data['section_code'] = Attachment::SECTION_GENERAL_ASSEMBLY;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function ($data) {
                        $payload = UnifiedMediaPicker::selectedPayload(request());

                        if (! empty($payload['selected_media_id'])) {
                            $data['media_item_id'] = $payload['selected_media_id'];
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
