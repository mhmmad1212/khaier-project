<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VideoLibraryResource\Pages;
use App\Models\VideoLibrary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoLibraryResource extends Resource
{
    protected static ?string $model = VideoLibrary::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'مكتبة الفيديو';
    protected static ?string $modelLabel = 'مقطع فيديو';
    protected static ?string $pluralModelLabel = 'مكتبة الفيديو';
    protected static ?string $navigationGroup = 'إدارة المحتوى';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان المقطع')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('youtube_url')
                    ->label('رابط المقطع من يوتيوب')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

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
                    ->label('عنوان المقطع')
                    ->searchable(),

                Tables\Columns\TextColumn::make('youtube_url')
                    ->label('رابط يوتيوب')
                    ->limit(50),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListVideoLibraries::route('/'),
            'create' => Pages\CreateVideoLibrary::route('/create'),
            'edit' => Pages\EditVideoLibrary::route('/{record}/edit'),
        ];
    }
}
