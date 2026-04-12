<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BeneficiaryServiceResource\Pages;
use App\Forms\Components\LocalIconGrid;
use App\Models\BeneficiaryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BeneficiaryServiceResource extends Resource
{
    protected static ?string $navigationGroup = 'خدمات المستفيدين';
    protected static ?int $navigationSort = 1;
    protected static ?string $model = BeneficiaryService::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'خدمات المستفيدين';
    protected static ?string $modelLabel = 'خدمة مستفيد';
    protected static ?string $pluralModelLabel = 'خدمات المستفيدين';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('البيانات الأساسية')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم الخدمة')
                        ->required()
                        ->maxLength(255),

                    LocalIconGrid::make('icon')
                        ->label('أيقونة الخدمة')
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('conditions')
                        ->label('شروط الخدمة')
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
                        ->columnSpanFull()
                        ->required(),

                    Forms\Components\TextInput::make('guide_url')
                        ->label('رابط شرح التقديم')
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('application_url')
                        ->label('رابط تقديم الطلب')
                        ->url()
                        ->maxLength(255)
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
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخدمة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('الأيقونة')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('guide_url')
                    ->label('رابط الشرح')
                    ->limit(35)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('application_url')
                    ->label('رابط التقديم')
                    ->limit(35)
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
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaryServices::route('/'),
            'create' => Pages\CreateBeneficiaryService::route('/create'),
            'edit' => Pages\EditBeneficiaryService::route('/{record}/edit'),
        ];
    }
}
