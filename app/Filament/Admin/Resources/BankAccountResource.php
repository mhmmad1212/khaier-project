<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 8;
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'الحسابات البنكية';
    protected static ?string $modelLabel = 'حساب بنكي';
    protected static ?string $pluralModelLabel = 'الحسابات البنكية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الحساب البنكي')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الحساب')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('bank_name')
                            ->label('اسم البنك')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('account_number')
                            ->label('رقم الحساب')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('bank_logo')
                            ->label('شعار البنك')
                            ->image()
                            ->disk('public')
                            ->directory('bank-accounts')
                            ->visibility('public'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('الترتيب')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('bank_logo')
                    ->label('الشعار'),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الحساب')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('اسم البنك')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('رقم الحساب')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
