<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssociationResource\Pages;
use App\Models\Association;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssociationResource extends Resource
{
    protected static ?string $model = Association::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'الجمعيات';
    protected static ?string $modelLabel = 'جمعية';
    protected static ?string $pluralModelLabel = 'الجمعيات';
    protected static ?string $navigationGroup = 'إدارة النظام';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Toggle::make('can_edit_system_pages')
                ->label('السماح للجمعية بتعديل الصفحات النظامية')
                ->helperText('إذا كان مغلقًا فلن تتمكن الجمعية من تعديل صفحات النظام مثل مجلس الإدارة والأخبار')
                ->default(false),

            Forms\Components\TextInput::make('name')
                ->label('اسم الجمعية')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('slug')
                ->label('المعرف المختصر')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('مثال: abar-hail'),

            Forms\Components\TextInput::make('domain')
                ->label('الدومين')
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('مثال: abar-hail.org.sa'),

            Forms\Components\Toggle::make('is_active')
                ->label('نشطة')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الجمعية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('المعرف')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('domain')
                    ->label('الدومين')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListAssociations::route('/'),
            'create' => Pages\CreateAssociation::route('/create'),
            'edit' => Pages\EditAssociation::route('/{record}/edit'),
        ];
    }
}
