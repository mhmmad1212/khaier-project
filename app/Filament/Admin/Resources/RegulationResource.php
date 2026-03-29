<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegulationResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\Regulation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RegulationResource extends Resource
{
    protected static ?string $navigationGroup = 'الشفافية والحوكمة';
    protected static ?int $navigationSort = 2;
    protected static ?string $model = Regulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'اللوائح';
    protected static ?string $modelLabel = 'لائحة';
    protected static ?string $pluralModelLabel = 'اللوائح';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('العنوان')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

            Forms\Components\TextInput::make('slug')
                ->label('الرابط المختصر')
                ->maxLength(255),

            Forms\Components\TextInput::make('short_code')
                ->label('الرابط القصير')
                ->helperText('اختياري')
                ->maxLength(255),

            Actions::make([
                Action::make('generate_short_code')
                    ->label('إنشاء رابط قصير')
                    ->color('primary')
                    ->action(function (Set $set) {
                        do {
                            $code = Str::upper(Str::random(6));
                        } while (Regulation::query()->where('short_code', $code)->exists());

                        $set('short_code', $code);
                    }),

                Action::make('clear_short_code')
                    ->label('حذف الرابط القصير')
                    ->color('gray')
                    ->action(fn (Set $set) => $set('short_code', null)),
            ])->columnSpanFull(),

            Forms\Components\Placeholder::make('short_url_preview')
                ->label('الرابط المختصر')
                ->content(fn (callable $get) => $get('short_code') ? url('/s/' . $get('short_code')) : 'لا يوجد رابط مختصر')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('year')
                ->label('السنة')
                ->numeric(),

            Forms\Components\Textarea::make('description')
                ->label('الوصف')
                ->rows(5)
                ->columnSpanFull(),

            MediaPicker::make('file_media_id')
                ->label('الملف')
                ->columnSpanFull(),

            Forms\Components\DateTimePicker::make('published_at')
                ->label('تاريخ النشر'),

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
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('short_code')
                    ->label('الكود القصير')
                    ->copyable()
                    ->copyMessage('تم نسخ الكود القصير')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('short_code')
                    ->label('الرابط المختصر')
                    ->formatStateUsing(fn ($state) => filled($state) ? url('/s/' . $state) : '-')
                    ->copyable()
                    ->copyMessage('تم نسخ الرابط المختصر')
                    ->limit(30),

                Tables\Columns\TextColumn::make('year')
                    ->label('السنة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fileMedia.title')
                    ->label('الملف')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('النشر')
                    ->dateTime('Y-m-d'),

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
            'index' => Pages\ListRegulations::route('/'),
            'create' => Pages\CreateRegulation::route('/create'),
            'edit' => Pages\EditRegulation::route('/{record}/edit'),
        ];
    }
}
