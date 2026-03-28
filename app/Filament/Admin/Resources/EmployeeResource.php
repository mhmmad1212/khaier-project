<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'الموظفون';
    protected static ?string $modelLabel = 'موظف';
    protected static ?string $pluralModelLabel = 'الموظفون';
    protected static ?string $navigationGroup = 'الموقع';

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\TextInput::make('name')
                ->label('اسم الموظف')
                ->required(),

            Forms\Components\TextInput::make('position')
                ->label('المسمى الوظيفي')
                ->required(),

            MediaPicker::make('photo_media_id')
                ->label('صورة الموظف')
                ->default(fn () => request('selected_media_id'))
                ->columnSpanFull(),

            Forms\Components\Hidden::make('photo')
                ->default(fn () => request('selected_media_file')),
        ];

        if (Schema::connection('tenant')->hasColumn('employees', 'department')) {
            $schema[] = Forms\Components\TextInput::make('department')
                ->label('القسم');
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'bio')) {
            $schema[] = Forms\Components\Textarea::make('bio')
                ->label('نبذة')
                ->rows(4)
                ->columnSpanFull();
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'email')) {
            $schema[] = Forms\Components\TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email();
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'phone')) {
            $schema[] = Forms\Components\TextInput::make('phone')
                ->label('الهاتف');
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'sort_order')) {
            $schema[] = Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0);
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'is_active')) {
            $schema[] = Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true);
        }

        return $form->schema($schema)->columns(2);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\ViewColumn::make('photo_media_id')
                ->label('الصورة')
                ->view('filament.tables.columns.employee-media-image'),

            Tables\Columns\TextColumn::make('name')
                ->label('الاسم')
                ->searchable(),

            Tables\Columns\TextColumn::make('position')
                ->label('المسمى الوظيفي')
                ->searchable(),
        ];

        if (Schema::connection('tenant')->hasColumn('employees', 'department')) {
            $columns[] = Tables\Columns\TextColumn::make('department')
                ->label('القسم');
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'sort_order')) {
            $columns[] = Tables\Columns\TextColumn::make('sort_order')
                ->label('الترتيب')
                ->sortable();
        }

        if (Schema::connection('tenant')->hasColumn('employees', 'is_active')) {
            $columns[] = Tables\Columns\IconColumn::make('is_active')
                ->label('نشط')
                ->boolean();
        }

        return $table
            ->columns($columns)
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
