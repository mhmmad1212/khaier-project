<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PagesResource\Pages;
use App\Forms\Components\CkEditorMediaPicker;
use App\Forms\Components\MediaPicker;
use App\Models\Page;
use App\Support\PageTypeRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PagesResource extends Resource
{
    protected static ?string $navigationGroup = 'إدارة الموقع';
    protected static ?int $navigationSort = 2;
    
    
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الصفحات';
    protected static ?string $modelLabel = 'صفحة';
    protected static ?string $pluralModelLabel = 'الصفحات';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('page_type')
                ->label('نوع الصفحة')
                ->options([
                    'content' => 'صفحة عادية',
                    'system' => 'صفحة نظامية',
                ])
                ->default('content')
                ->live()
                ->required(),

            Forms\Components\Select::make('system_key')
                ->label('نوع الصفحة النظامية')
                ->options(PageTypeRegistry::systemPageTypes())
                ->visible(fn (Get $get) => $get('page_type') === 'system')
                ->required(fn (Get $get) => $get('page_type') === 'system')
                ->searchable(),

            Forms\Components\TextInput::make('title')
                ->label('عنوان الصفحة')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (blank($get('slug'))) {
                        $set('slug', Str::slug($state));
                    }
                }),

            Forms\Components\TextInput::make('slug')
                ->label('الرابط المختصر')
                ->required()
                ->unique(ignoreRecord: true),

            MediaPicker::make('featured_media_id')
                ->label('الصورة البارزة')
                ->columnSpanFull(),

            CkEditorMediaPicker::make('content')
                ->label('محتوى الصفحة')
                ->helperText('للنصوص والمحتوى العادي. إذا أردت لصق HTML خام فاستخدم الحقل التالي.')
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('page_type') !== 'system')
                ->required(fn (Get $get) => $get('page_type') !== 'system' && blank($get('raw_html'))),

            Forms\Components\Textarea::make('raw_html')
                ->label('HTML خام')
                ->rows(24)
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('page_type') !== 'system')
                ->helperText('ألصق هنا HTML خام مباشرة. إذا تم تعبئة هذا الحقل فسيتم عرضه بدل محتوى الصفحة العادي.'),

            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options([
                    'draft' => 'مسودة',
                    'published' => 'منشور',
                ])
                ->default('published')
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\ViewColumn::make('featured_media_id')
                    ->label('الصورة')
                    ->view('filament.tables.columns.page-media-image'),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('page_type')
                    ->label('نوع الصفحة')
                    ->formatStateUsing(fn ($state) => $state === 'system' ? 'نظامية' : 'عادية'),

                Tables\Columns\TextColumn::make('system_key')
                    ->label('المفتاح النظامي')
                    ->formatStateUsing(fn ($state) => $state ? PageTypeRegistry::label($state) : '-'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('preview')
                    ->label('معاينة')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => url('/page/' . $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePages::route('/create'),
            'edit' => Pages\EditPages::route('/{record}/edit'),
        ];
    }
}
