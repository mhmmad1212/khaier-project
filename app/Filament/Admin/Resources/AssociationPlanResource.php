<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssociationPlanResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\AssociationPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssociationPlanResource extends Resource
{
    protected static ?string $navigationGroup = 'الحوكمة والوثائق';
    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $model = AssociationPlan::class;


    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'خطط الجمعية';
    protected static ?string $modelLabel = 'خطة جمعية';
    protected static ?string $pluralModelLabel = 'خطط الجمعية';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('اسم الخطة')
                ->required()
                ->maxLength(255),

            MediaPicker::make('file_media_id')
                ->label('مرفق الخطة')
                ->default(fn () => request('selected_media_id'))
                ->columnSpanFull(),

            Forms\Components\Hidden::make('file')
                ->default(fn () => request('selected_media_file')),

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
                    ->label('اسم الخطة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file')
                    ->label('المرفق')
                    ->limit(40),

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
            'index' => Pages\ListAssociationPlans::route('/'),
            'create' => Pages\CreateAssociationPlan::route('/create'),
            'edit' => Pages\EditAssociationPlan::route('/{record}/edit'),
        ];
    }
}
