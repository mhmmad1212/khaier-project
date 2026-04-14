<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VolunteerOpportunityResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\VolunteerOpportunity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VolunteerOpportunityResource extends Resource
{
    protected static ?string $navigationGroup = 'الحوكمة والوثائق';
    protected static ?int $navigationSort = 9;
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $model = VolunteerOpportunity::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'التطوع';
    protected static ?string $modelLabel = 'فرصة تطوع';
    protected static ?string $pluralModelLabel = 'فرص التطوع';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات فرصة التطوع')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('اسم الفرصة')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (blank($get('slug'))) {
                            }
                        }),

                    Forms\Components\Hidden::make('slug'),

                    MediaPicker::make('image_media_id')
                        ->label('صورة الفرصة')
                        ->default(fn () => request('selected_media_id'))
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('image')
                        ->default(fn () => request('selected_media_file')),

                    Forms\Components\Select::make('opportunity_type')
                        ->label('نوع الفرصة')
                        ->options(VolunteerOpportunity::typeOptions())
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('تاريخ بداية التطوع')
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('تاريخ نهاية الفرصة')
                        ->required(),

                    Forms\Components\TextInput::make('hours_count')
                        ->label('عدد ساعات الفرصة')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    Forms\Components\TextInput::make('platform_url')
                        ->label('رابط الفرصة في منصة تطوع')
                        ->url()
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('description')
                        ->label('وصف الفرصة')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'bulletList',
                            'orderedList',
                            'link',
                            'blockquote',
                            'redo',
                            'undo',
                        ])
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('نشط')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('اسم الفرصة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('opportunity_type')
                    ->label('نوع الفرصة')
                    ->formatStateUsing(fn ($state) => VolunteerOpportunity::typeOptions()[$state] ?? 'أخرى'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('بداية التطوع')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('نهاية الفرصة')
                    ->date('Y-m-d'),

                Tables\Columns\TextColumn::make('hours_count')
                    ->label('عدد الساعات')
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
            'index' => Pages\ListVolunteerOpportunities::route('/'),
            'create' => Pages\CreateVolunteerOpportunity::route('/create'),
            'edit' => Pages\EditVolunteerOpportunity::route('/{record}/edit'),
        ];
    }
}
