<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FinancialReportResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\FinancialReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FinancialReportResource extends Resource
{
    protected static ?string $navigationGroup = 'الحوكمة والوثائق';
    protected static ?int $navigationSort = 5;
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $model = FinancialReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'القوائم المالية';
    protected static ?string $modelLabel = 'قائمة مالية';
    protected static ?string $pluralModelLabel = 'القوائم المالية';
    

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
                        } while (FinancialReport::query()->where('short_code', $code)->exists());

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

            Forms\Components\Select::make('quarter')
                ->label('الفترة')
                ->options([
                    'Q1' => 'الربع الأول',
                    'Q2' => 'الربع الثاني',
                    'Q3' => 'الربع الثالث',
                    'Q4' => 'الربع الرابع',
                    'annual' => 'سنوي',
                ]),

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
            ->defaultSort('year', 'desc')
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

                Tables\Columns\TextColumn::make('quarter')
                    ->label('الفترة')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Q1' => 'الربع الأول',
                        'Q2' => 'الربع الثاني',
                        'Q3' => 'الربع الثالث',
                        'Q4' => 'الربع الرابع',
                        'annual' => 'سنوي',
                        default => $state ?: '-',
                    }),

                Tables\Columns\TextColumn::make('fileMedia.title')
                    ->label('الملف')
                    ->placeholder('-'),

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
            'index' => Pages\ListFinancialReports::route('/'),
            'create' => Pages\CreateFinancialReport::route('/create'),
            'edit' => Pages\EditFinancialReport::route('/{record}/edit'),
        ];
    }
}
