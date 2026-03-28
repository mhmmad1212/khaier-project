<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\News;
use App\Models\MediaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'الأخبار';
    protected static ?string $modelLabel = 'خبر';
    protected static ?string $pluralModelLabel = 'الأخبار';
    protected static ?string $navigationGroup = 'الموقع';

    public static function form(Form $form): Form
    {
        return $form->schema([

Forms\Components\Section::make('تحسين محركات البحث (SEO)')
    ->schema([
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('generate_seo')
                            ->label('توليد SEO تلقائي')
                            ->icon('heroicon-o-sparkles')
                            ->color('primary')
                            ->action(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set) {
                                $title = trim((string) ($get('title') ?? ''));
                                $content = trim(strip_tags((string) ($get('excerpt') ?? $get('summary') ?? $get('short_description') ?? $get('description') ?? $get('content') ?? '')));

                                if ($title !== '' && trim((string) $get('meta_title')) === '') {
                                    $set('meta_title', \Illuminate\Support\Str::limit($title, 60, ''));
                                }

                                if ($content === '' && $title !== '') {
                                    $content = $title;
                                }

                                if ($content !== '' && trim((string) $get('meta_description')) === '') {
                                    $set('meta_description', \Illuminate\Support\Str::limit($content, 160, ''));
                                }
                            }),
                    ]),

        Forms\Components\TextInput::make('meta_title')
            ->label('عنوان SEO')
            ->maxLength(60),

        Forms\Components\Textarea::make('meta_description')
            ->label('وصف SEO')
            ->rows(3)
            ->maxLength(160),
    ])
    ->collapsed(),

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
                        } while (News::query()->where('short_code', $code)->exists());

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

            Forms\Components\Textarea::make('excerpt')
                ->label('المقتطف')
                ->rows(3)
                ->columnSpanFull(),

            Forms\Components\RichEditor::make('content')
                ->label('المحتوى')
                ->columnSpanFull(),


            MediaPicker::make('image_media_id')
                ->label('الصورة البارزة')
                ->default(fn () => request('selected_media_id'))
                ->columnSpanFull(),

            Forms\Components\Hidden::make('image')
                ->default(fn () => request('selected_media_file')),

            Forms\Components\DateTimePicker::make('published_at')
                ->label('تاريخ النشر'),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->disk('public'),

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

                Tables\Columns\TextColumn::make('published_at')
                    ->label('النشر')
                    ->dateTime('Y-m-d'),

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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
