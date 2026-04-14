<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MediaItemResource\Pages;
use App\Models\MediaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaItemResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;


    protected static ?string $model = MediaItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'مكتبة الوسائط';
    protected static ?string $modelLabel = 'وسيط';
    protected static ?string $pluralModelLabel = 'مكتبة الوسائط';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('عنوان الملف')
                ->maxLength(255),

            Forms\Components\FileUpload::make('file')
                ->label('الملف')
                ->disk('public')
                ->directory(fn () => 'tenants/' . config('database.connections.tenant.database') . '/media')
                ->visibility('public')
                ->acceptedFileTypes([
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/gif',
                    'application/pdf',
                ])
                ->openable()
                ->downloadable()
                ->previewable()
                ->preserveFilenames(false)
                ->getUploadedFileNameForStorageUsing(function ($file) {
                    return now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                })
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('alt_text')
                ->label('النص البديل')
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\ViewColumn::make('preview')
                    ->label('المعاينة')
                    ->view('filament.tables.columns.media-preview'),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->formatStateUsing(fn ($state, $record) => $state ?: basename((string) $record->file))
                    ->searchable(),

                Tables\Columns\TextColumn::make('extension')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : '-'),

                Tables\Columns\TextColumn::make('size')
                    ->label('الحجم')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : '-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('open_file')
                    ->label('عرض')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => asset('storage/' . $record->file))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if ($record->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->file);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            if ($record->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->file)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->file);
                            }
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaItems::route('/'),
            'create' => Pages\CreateMediaItem::route('/create'),
            'edit' => Pages\EditMediaItem::route('/{record}/edit'),
        ];
    }
}
