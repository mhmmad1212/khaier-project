<?php
namespace App\Filament\Admin\Resources;
use App\Models\Disclosure;
use App\Filament\Admin\Resources\DisclosureResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DisclosureResource extends Resource
{
    protected static ?string $model = Disclosure::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $modelLabel = 'إفصاح';
    protected static ?string $pluralModelLabel = 'الإفصاح';
    protected static ?string $navigationGroup = 'الشفافية والحوكمة';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الإفصاح')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255),
                    \App\Forms\Components\MediaPicker::make('file_media_id')->label('المرفق (ملف الإفصاح)'),
                    Forms\Components\Hidden::make('file'),
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable(),
                Tables\Columns\TextColumn::make('file_link')
                    ->label('المرفق')
                    ->getStateUsing(fn ($record) => $record->file_media_id || $record->file ? 'عرض المرفق' : 'لا يوجد')
                    ->badge()
                    ->color(fn ($record) => $record->file_media_id || $record->file ? 'success' : 'gray')
                    ->url(function ($record) {
                        if ($record->file_media_id) {
                            try {
                                $file = \Illuminate\Support\Facades\DB::connection('tenant')->table('media_items')->where('id', $record->file_media_id)->value('file');
                                if ($file) return str_starts_with($file, 'http') ? $file : asset('storage/' . $file);
                            } catch (\Exception $e) {}
                        }
                        if ($record->file) return str_starts_with($record->file, 'http') ? $record->file : asset('storage/' . $record->file);
                        return null;
                    })
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإضافة')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => Pages\ListDisclosures::route('/'),
            'create' => Pages\CreateDisclosure::route('/create'),
            'edit' => Pages\EditDisclosure::route('/{record}/edit'),
        ];
    }
}