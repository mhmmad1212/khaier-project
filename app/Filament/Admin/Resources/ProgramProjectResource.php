<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProgramProjectResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\ProgramProject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramProjectResource extends Resource
{
    protected static ?string $model = ProgramProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'البرامج والمشاريع';
    protected static ?string $modelLabel = 'برنامج / مشروع';
    protected static ?string $pluralModelLabel = 'البرامج والمشاريع';
    protected static ?string $navigationGroup = 'الموقع';
    protected static ?int $navigationSort = 35;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('اسم المشروع أو البرنامج')
                ->required()
                ->maxLength(255),

            Forms\Components\RichEditor::make('description')
                ->label('وصف المشروع أو البرنامج')
                ->columnSpanFull(),

            MediaPicker::make('cover_image_media_id')
                ->label('الصورة الرمزية للمشروع أو البرنامج')
                ->columnSpanFull(),

            Forms\Components\DatePicker::make('start_date')
                ->label('تاريخ بداية البرنامج'),

            Forms\Components\DatePicker::make('end_date')
                ->label('تاريخ نهاية البرنامج'),

            Forms\Components\TextInput::make('project_amount')
                ->label('مبلغ المشروع')
                ->numeric()
                ->prefix('ر.س'),

            Forms\Components\TextInput::make('donation_amount')
                ->label('مبلغ التبرع للمشروع')
                ->numeric()
                ->prefix('ر.س'),

            Forms\Components\TextInput::make('donation_url')
                ->label('رابط التبرع للمشروع')
                ->url()
                ->columnSpanFull(),

            Forms\Components\Repeater::make('gallery')
                ->label('صور المشروع')
                ->schema([
                    MediaPicker::make('media_item_id')
                        ->label('الصورة')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0),
                ])
                ->default([])
                ->addActionLabel('إضافة صورة')
                ->reorderable()
                ->collapsed()
                ->columnSpanFull(),

            MediaPicker::make('report_media_id')
                ->label('تقرير المشروع')
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
                Tables\Columns\TextColumn::make('title')
                    ->label('الاسم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('البداية')
                    ->date(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('النهاية')
                    ->date(),

                Tables\Columns\TextColumn::make('project_amount')
                    ->label('مبلغ المشروع')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('donation_amount')
                    ->label('مبلغ التبرع')
                    ->money('SAR')
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
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramProjects::route('/'),
            'create' => Pages\CreateProgramProject::route('/create'),
            'edit' => Pages\EditProgramProject::route('/{record}/edit'),
        ];
    }
}
