<?php
namespace App\Filament\Admin\Resources;

use App\Models\MeetingMinute;
use App\Filament\Admin\Resources\MeetingMinuteResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Forms\Components\MediaPicker;

class MeetingMinuteResource extends Resource
{
    protected static ?string $navigationGroup = 'الحوكمة والوثائق';
    protected static ?int $navigationSort = 6;
    protected static bool $shouldRegisterNavigation = true;


    protected static ?string $model = MeetingMinute::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'محضر';
    protected static ?string $pluralModelLabel = 'محاضر الاجتماعات';
    
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المحضر')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان المحضر')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('category')
                        ->label('جهة الاجتماع')
                        ->options([
                            'board' => 'مجلس إدارة',
                            'general' => 'جمعية عمومية',
                            'committee' => 'لجنة',
                        ])
                        ->required(),
                    Forms\Components\Select::make('meeting_type')
                        ->label('نوع الاجتماع')
                        ->options([
                            'regular' => 'عادي / دوري',
                            'emergency' => 'طارئ',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('meeting_date')
                        ->label('تاريخ الاجتماع'),
                    Forms\Components\Textarea::make('description')
                        ->label('وصف مختصر')
                        ->columnSpanFull(),
                    MediaPicker::make('file_media_id')->label('المرفق (ملف المحضر)'),
                    Forms\Components\Hidden::make('file'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('الجهة')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'board' => 'مجلس إدارة',
                            'general' => 'جمعية عمومية',
                            'committee' => 'لجنة',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'board' => 'primary',
                        'general' => 'success',
                        'committee' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meeting_type')
                    ->label('نوع الاجتماع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'regular' => 'دوري',
                        'emergency' => 'طارئ',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'emergency' => 'danger',
                        'regular' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meeting_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_link')
                    ->label('المرفق')
                    ->getStateUsing(fn ($record) => $record->file_media_id || $record->file ? 'عرض المرفق' : 'لا يوجد مرفق')
                    ->badge()
                    ->color(fn ($record) => $record->file_media_id || $record->file ? 'info' : 'gray')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('الجهة')
                    ->options([
                        'board' => 'مجلس إدارة',
                            'general' => 'جمعية عمومية',
                            'committee' => 'لجنة',
                    ]),
                Tables\Filters\SelectFilter::make('meeting_type')
                    ->label('نوع الاجتماع')
                    ->options([
                        'regular' => 'دوري',
                        'emergency' => 'طارئ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => Pages\ListMeetingMinutes::route('/'),
            'create' => Pages\CreateMeetingMinute::route('/create'),
            'edit' => Pages\EditMeetingMinute::route('/{record}/edit'),
        ];
    }
}
