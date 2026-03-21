<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Association;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'المستخدمون';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $pluralModelLabel = 'المستخدمون';
    protected static ?string $navigationGroup = 'إدارة النظام';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->maxLength(255)
                ->helperText('اتركها فارغة عند التعديل إذا لم ترد تغييرها.'),

            Forms\Components\Toggle::make('is_super_admin')
                ->label('مشرف عام')
                ->default(false)
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state) {
                    if ($state) {
                        $set('association_id', null);
                    }
                })
                ->helperText('إذا كان المستخدم مشرفًا عامًا فسيكون دخوله من /khaier'),

            Forms\Components\Select::make('association_id')
                ->label('الجمعية')
                ->options(Association::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive()
                ->hidden(fn (Get $get): bool => (bool) $get('is_super_admin'))
                ->afterStateUpdated(function (Set $set, $state) {
                    if (filled($state)) {
                        $set('is_super_admin', false);
                    }
                })
                ->helperText('اختر الجمعية إذا كان المستخدم مدير جمعية، وسيكون دخوله من /admin.'),

            Forms\Components\Placeholder::make('access_note')
                ->label('طريقة الدخول')
                ->content(function (Get $get): string {
                    if ($get('is_super_admin')) {
                        return 'هذا المستخدم سيدخل من لوحة المشرف: /khaier';
                    }

                    if ($get('association_id')) {
                        return 'هذا المستخدم سيدخل من لوحة الجمعية: /admin';
                    }

                    return 'اختر إما مشرف عام أو اربط المستخدم بجمعية.';
                }),
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
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('association.name')
                    ->label('الجمعية')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label('مشرف عام')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
