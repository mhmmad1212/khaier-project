<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MediaLibraryResource\Pages;
use App\Models\MediaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaLibraryResource extends Resource
{
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static bool $shouldRegisterNavigation = true;



    public static function canViewAny(): bool
    {
        return true;
    }


    protected static ?string $model = MediaItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'مكتبة الوسائط';
    protected static ?int $navigationSort = 7;
    protected static ?string $modelLabel = 'ملف';
    protected static ?string $pluralModelLabel = 'مكتبة الوسائط';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الملف')
                ->schema([
                    Forms\Components\FileUpload::make('file')
                        ->label('الملف')
                        ->disk(config('filesystems.media_disk', 'public'))
                        ->directory('media-library/' . now()->format('Y/m'))
                        ->preserveFilenames()
                        ->visibility('public')
                        ->downloadable()
                        ->openable()
                        ->required()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if (! $state) {
                                return;
                            }

                            $path = is_array($state) ? ($state[0] ?? null) : $state;
                            if (! $path) {
                                return;
                            }

                            $filename = basename($path);
                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                            $mime = match ($extension) {
                                'jpg', 'jpeg' => 'image/jpeg',
                                'png' => 'image/png',
                                'webp' => 'image/webp',
                                'gif' => 'image/gif',
                                'svg' => 'image/svg+xml',
                                'pdf' => 'application/pdf',
                                'doc' => 'application/msword',
                                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'xls' => 'application/vnd.ms-excel',
                                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                default => null,
                            };

                            $set('title', pathinfo($filename, PATHINFO_FILENAME));
                            $set('extension', $extension);
                            $set('mime_type', $mime);
                            $set('is_image', in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']));
                            $set('directory', dirname($path) === '.' ? null : dirname($path));
                            $set('disk', config('filesystems.media_disk', 'public'));
                        }),

                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('alt_text')
                        ->label('النص البديل')
                        ->maxLength(255),

                    Forms\Components\Hidden::make('disk')
                        ->default(config('filesystems.media_disk', 'public')),

                    Forms\Components\Hidden::make('directory'),
                    Forms\Components\Hidden::make('mime_type'),
                    Forms\Components\Hidden::make('extension'),

                    Forms\Components\Hidden::make('is_image')
                        ->default(false),

                    Forms\Components\Hidden::make('is_active')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $path = $data['file'] ?? null;

        if ($path) {
            $fullPath = storage_path('app/public/' . $path);
            $data['size'] = file_exists($fullPath) ? filesize($fullPath) : null;

            if (empty($data['hash']) && file_exists($fullPath)) {
                $data['hash'] = hash_file('sha256', $fullPath);
            }

            if (empty($data['title'])) {
                $data['title'] = pathinfo(basename($path), PATHINFO_FILENAME);
            }
        }

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $path = $data['file'] ?? null;

        if ($path) {
            $fullPath = storage_path('app/public/' . $path);
            $data['size'] = file_exists($fullPath) ? filesize($fullPath) : null;

            if (empty($data['hash']) && file_exists($fullPath)) {
                $data['hash'] = hash_file('sha256', $fullPath);
            }

            if (empty($data['title'])) {
                $data['title'] = pathinfo(basename($path), PATHINFO_FILENAME);
            }
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\ViewColumn::make('preview')
                    ->label('معاينة')
                    ->view('filament.tables.columns.media-preview'),

                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('extension')
                    ->label('الامتداد')
                    ->badge(),

                Tables\Columns\TextColumn::make('size')
                    ->label('الحجم')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return '-';
                        if ($state < 1024) return $state . ' B';
                        if ($state < 1024 * 1024) return round($state / 1024, 1) . ' KB';
                        return round($state / (1024 * 1024), 1) . ' MB';
                    }),

                Tables\Columns\TextColumn::make('file')
                    ->label('🔥 رابط الملف 🔥')
                    ->formatStateUsing(fn ($state) => asset('storage/' . ltrim((string) $state, '/')))
                    ->copyable()
                    ->copyMessage('تم نسخ رابط الملف')
                    ->wrap()
                    ->limit(80),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الرفع')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaLibrary::route('/'),
            'create' => Pages\CreateMediaLibrary::route('/create'),
            'edit' => Pages\EditMediaLibrary::route('/{record}/edit'),
        ];
    }
}
