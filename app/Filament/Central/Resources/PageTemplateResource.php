<?php

namespace App\Filament\Central\Resources;

use App\Filament\Central\Resources\PageTemplateResource\Pages;
use App\Models\Association;
use App\Models\PageTemplate;
use App\Support\PageTypeRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageTemplateResource extends Resource
{
    protected static ?string $model = PageTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'مكتبة التصاميم';
    protected static ?string $modelLabel = 'تصميم';
    protected static ?string $pluralModelLabel = 'مكتبة التصاميم';
    protected static ?string $navigationGroup = 'إدارة النظام';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات التصميم')
                ->schema([
                    Forms\Components\Select::make('page_type')
                        ->label('نوع الصفحة')
                        ->options(PageTypeRegistry::templateTypes())
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('اسم التصميم')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('scope_type')
                        ->label('نطاق الإتاحة')
                        ->options([
                            'global' => 'عام لكل الجمعيات',
                            'restricted' => 'مخصص لجمعيات محددة',
                        ])
                        ->required()
                        ->default('global')
                        ->live(),

                    Forms\Components\Select::make('associations')
                        ->label('الجمعيات المسموح لها')
                        ->multiple()
                        ->relationship('associations', 'name')
                        ->options(fn () => Association::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->visible(fn (Forms\Get $get) => $get('scope_type') === 'restricted')
                        ->preload()
                        ->searchable(),

                    Forms\Components\TextInput::make('preview_image')
                        ->label('رابط/صورة المعاينة')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('نشط')
                        ->default(true),

                    Forms\Components\Placeholder::make('template_key_info')
                        ->label('المعرف الفني')
                        ->content(fn (?PageTemplate $record) => $record?->template_key ?: 'سيتولد تلقائيًا بعد الحفظ')
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make('view_path_info')
                        ->label('مسار ملف العرض')
                        ->content(fn (?PageTemplate $record) => $record?->view_path ?: 'سيتولد تلقائيًا بعد الحفظ')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('template_content')
                        ->label('محتوى Blade')
                        ->helperText('ضع هنا Blade كامل للصفحة، وسيتم توليد الملف تلقائيًا عند الحفظ.')
                        ->rows(24)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('template_css')
                        ->label('CSS إضافي')
                        ->rows(10)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('template_js')
                        ->label('JavaScript إضافي')
                        ->rows(10)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم التصميم')
                    ->searchable(),

                Tables\Columns\TextColumn::make('page_type')
                    ->label('نوع الصفحة')
                    ->formatStateUsing(fn (string $state): string => PageTypeRegistry::label($state)),

                Tables\Columns\TextColumn::make('scope_type')
                    ->label('النطاق')
                    ->formatStateUsing(fn (string $state): string => $state === 'global' ? 'عام' : 'مخصص'),

                Tables\Columns\TextColumn::make('associations_count')
                    ->label('الجمعية')
                    ->getStateUsing(fn (\App\Models\PageTemplate $record): string => $record->scope_type === 'global'
                        ? 'الكل'
                        : (string) $record->associations()->count()
                    ),

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
            'index' => Pages\ListPageTemplates::route('/'),
            'create' => Pages\CreatePageTemplate::route('/create'),
            'edit' => Pages\EditPageTemplate::route('/{record}/edit'),
        ];
    }
}
