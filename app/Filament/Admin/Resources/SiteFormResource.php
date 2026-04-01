<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiteFormResource\Pages;
use App\Models\SiteForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SiteFormResource extends Resource
{
    protected static ?string $model = SiteForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'النماذج';
    protected static ?string $modelLabel = 'نموذج';
    protected static ?string $pluralModelLabel = 'النماذج';
    protected static ?string $navigationGroup = 'إدارة المحتوى';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات النموذج')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم النموذج')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug((string) $state));
                            }
                        })
                        ->maxLength(255),

                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط المختصر')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->label('وصف مختصر')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('success_message')
                        ->label('رسالة النجاح بعد الإرسال')
                        ->rows(3)
                        ->default('تم إرسال النموذج بنجاح')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('submit_button_text')
                        ->label('نص زر الإرسال')
                        ->default('إرسال')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('نشط')
                        ->default(true),
                ])
                ->columns(2),

            Forms\Components\Section::make('حقول النموذج')
                ->schema([
                    Forms\Components\Repeater::make('fields')
                        ->relationship()
                        ->label('الحقول')
                        ->orderColumn('sort_order')
                        ->reorderableWithButtons()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'حقل جديد')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->label('اسم الحقل')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('name')
                                ->label('الاسم البرمجي')
                                ->helperText('مثال: full_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('type')
                                ->label('نوع الحقل')
                                ->options([
                                    'text' => 'نص',
                                    'email' => 'بريد إلكتروني',
                                    'phone' => 'جوال',
                                    'textarea' => 'نص طويل',
                                    'select' => 'قائمة اختيار',
                                    'number' => 'رقم',
                                    'date' => 'تاريخ',
                                    'url' => 'رابط',
                                    'file' => 'مرفق',
                                ])
                                ->default('text')
                                ->required()
                                ->live(),

                            Forms\Components\TextInput::make('placeholder')
                                ->label('النص المساعد')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('options')
                                ->label('خيارات الحقل')
                                ->helperText('خيار في كل سطر، للقائمة فقط')
                                ->visible(fn (Forms\Get $get) => $get('type') === 'select')
                                ->rows(4)
                                ->dehydrateStateUsing(function ($state) {
                                    if (blank($state)) {
                                        return null;
                                    }

                                    $items = collect(preg_split('/\r\n|\r|\n/', (string) $state))
                                        ->map(fn ($item) => trim($item))
                                        ->filter()
                                        ->values()
                                        ->all();

                                    return $items;
                                })
                                ->formatStateUsing(function ($state) {
                                    if (is_array($state)) {
                                        return implode(PHP_EOL, $state);
                                    }

                                    return $state;
                                }),

                            Forms\Components\Select::make('width')
                                ->label('عرض الحقل')
                                ->options([
                                    'full' => 'كامل',
                                    'half' => 'نصف',
                                ])
                                ->default('full')
                                ->required(),

                            Forms\Components\Toggle::make('is_required')
                                ->label('إلزامي')
                                ->default(false),

                            Forms\Components\Toggle::make('is_active')
                                ->label('نشط')
                                ->default(true),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم النموذج')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->copyable(),

                Tables\Columns\TextColumn::make('fields_count')
                    ->label('عدد الحقول')
                    ->counts('fields'),

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('عدد الردود')
                    ->counts('submissions'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewForm')
                    ->label('عرض النموذج')
                    ->icon('heroicon-o-eye')
                    ->url(fn (SiteForm $record) => url('/forms/' . $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('viewSubmissions')
                    ->label('الردود')
                    ->icon('heroicon-o-inbox')
                    ->url(fn (SiteForm $record) => static::getUrl('submissions', ['record' => $record])),
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
            'index' => Pages\ListSiteForms::route('/'),
            'create' => Pages\CreateSiteForm::route('/create'),
            'edit' => Pages\EditSiteForm::route('/{record}/edit'),
            'submissions' => Pages\ViewSiteFormSubmissions::route('/{record}/submissions'),
            'submission' => Pages\ViewSiteFormSubmission::route('/{record}/submissions/{submission}'),
        ];
    }
}
