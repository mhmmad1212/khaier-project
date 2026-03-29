<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BoardMemberResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\BoardMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;

class BoardMemberResource extends Resource
{
    protected static ?string $navigationGroup = 'المجالس واللجان';
    protected static ?int $navigationSort = 2;
    protected static ?string $model = BoardMember::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'مجلس الإدارة';
    protected static ?string $modelLabel = 'عضو';
    protected static ?string $pluralModelLabel = 'أعضاء مجلس الإدارة';
    

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\TextInput::make('name')
                ->label('اسم العضو')
                ->required(),

            Forms\Components\TextInput::make('position')
                ->label('المنصب')
                ->required(),

            MediaPicker::make('photo_media_id')
                ->label('صورة العضو')
                ->columnSpanFull(),
        ];

        if (Schema::connection('tenant')->hasColumn('board_members', 'bio')) {
            $schema[] = Forms\Components\Textarea::make('bio')
                ->label('نبذة عن العضو')
                ->rows(4)
                ->columnSpanFull();
        }

        if (Schema::connection('tenant')->hasColumn('board_members', 'email')) {
            $schema[] = Forms\Components\TextInput::make('email')
                ->label('البريد الإلكتروني')
                ->email();
        }

        if (Schema::connection('tenant')->hasColumn('board_members', 'phone')) {
            $schema[] = Forms\Components\TextInput::make('phone')
                ->label('الهاتف');
        }

        if (Schema::connection('tenant')->hasColumn('board_members', 'sort_order')) {
            $schema[] = Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0);
        }

        if (Schema::connection('tenant')->hasColumn('board_members', 'is_active')) {
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
                ->view('filament.tables.columns.board-member-media-image'),

            Tables\Columns\TextColumn::make('name')
                ->label('الاسم')
                ->searchable(),

            Tables\Columns\TextColumn::make('position')
                ->label('المنصب'),
        ];

        if (Schema::connection('tenant')->hasColumn('board_members', 'sort_order')) {
            $columns[] = Tables\Columns\TextColumn::make('sort_order')
                ->label('الترتيب')
                ->sortable();
        }

        if (Schema::connection('tenant')->hasColumn('board_members', 'is_active')) {
            $columns[] = Tables\Columns\IconColumn::make('is_active')
                ->label('نشط')
                ->boolean();
        }

        return $table
            ->columns($columns)
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
            'index' => Pages\ListBoardMembers::route('/'),
            'create' => Pages\CreateBoardMember::route('/create'),
            'edit' => Pages\EditBoardMember::route('/{record}/edit'),
        ];
    }
}
