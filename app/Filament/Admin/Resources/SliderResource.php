<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SliderResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\MediaItem;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'السلايدر';
    protected static ?string $modelLabel = 'شريحة';
    protected static ?string $pluralModelLabel = 'السلايدر';
    protected static ?string $navigationGroup = 'الموقع';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('button_text')
                    ->label('نص الزر')
                    ->maxLength(255),

                MediaPicker::make('image_media_id')
                ->label('صورة السلايدر')
                ->default(fn () => request('selected_media_id'))
                ->columnSpanFull(),

            Forms\Components\Hidden::make('image')
                ->default(fn () => request('selected_media_file')),

            Forms\Components\TextInput::make('button_url')
                    ->label('رابط الزر')
                    ->url()
                    ->maxLength(255),



                Forms\Components\TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('imageMedia.title')
                    ->label('الصورة')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('button_text')
                    ->label('نص الزر')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('button_url')
                    ->label('رابط الزر')
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
