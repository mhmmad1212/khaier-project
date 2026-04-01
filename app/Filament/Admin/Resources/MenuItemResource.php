<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuItemResource\Pages;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Filament\Forms;
use App\Forms\Components\LocalIconGrid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class MenuItemResource extends Resource
{
    protected static ?string $navigationGroup = 'إدارة الموقع';
    protected static ?int $navigationSort = 4;
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'عناصر القوائم';
    protected static ?string $modelLabel = 'عنصر قائمة';
    protected static ?string $pluralModelLabel = 'عناصر القوائم';

    public static function form(Form $form): Form
    {
        $hasTargetColumn = Schema::connection('tenant')->hasColumn('menu_items', 'target');

        $schema = [
            Forms\Components\Select::make('menu_id')
                ->label('القائمة')
                ->options(Menu::query()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->live()
                ->required(),

            Forms\Components\Select::make('parent_id')
                ->label('العنصر الأب')
                ->options(function (callable $get, ?MenuItem $record) {
                    $menuId = $get('menu_id');

                    if (! $menuId) {
                        return [];
                    }

                    return MenuItem::query()
                        ->where('menu_id', $menuId)
                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                        ->orderBy('sort_order')
                        ->pluck('title', 'id');
                })
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('اتركه فارغًا إذا كان عنصرًا رئيسيًا.'),

            Forms\Components\TextInput::make('title')
                ->label('العنوان')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('type')
                ->label('النوع')
                ->options([
                    'link' => 'رابط داخلي',
                    'page' => 'صفحة داخلية',
                    'news' => 'أخبار',
                    'external' => 'رابط خارجي',
                ])
                ->default('link')
                ->live()
                ->required(),

            Forms\Components\TextInput::make('url')
                ->label('الرابط')
                ->visible(fn (callable $get) => in_array($get('type'), ['link', 'external', 'news']))
                ->required(fn (callable $get) => in_array($get('type'), ['link', 'external', 'news']))
                ->maxLength(255)
                ->helperText('مثال: / أو /news أو https://example.com أو #')
                ->rule(function (callable $get) {
                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                        $type = $get('type');
                        $value = trim((string) $value);

                        if ($value === '') {
                            return;
                        }

                        if ($type === 'external') {
                            $isValidExternal =
                                $value === '#'
                                || str_starts_with($value, 'http://')
                                || str_starts_with($value, 'https://');

                            if (! $isValidExternal) {
                                $fail('يجب كتابة الرابط بشكل صحيح');
                            }
                        }

                        if (in_array($type, ['link', 'news'])) {
                            $isValidInternal =
                                $value === '#'
                                || str_starts_with($value, '/');

                            if (! $isValidInternal) {
                                $fail('يجب كتابة الرابط بشكل صحيح');
                            }
                        }
                    };
                }),

            Forms\Components\Select::make('page_id')
                ->label('الصفحة الداخلية')
                ->options(Page::query()->orderBy('title')->pluck('title', 'id'))
                ->searchable()
                ->preload()
                ->visible(fn (callable $get) => $get('type') === 'page')
                ->required(fn (callable $get) => $get('type') === 'page')
                ->helperText('اختر صفحة من صفحات الموقع.'),

            LocalIconGrid::make('icon')
                ->label('الأيقونة')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),
        ];

        if ($hasTargetColumn) {
            $schema[] = Forms\Components\Select::make('target')
                ->label('فتح الرابط')
                ->options([
                    '_self' => 'في نفس الصفحة',
                    '_blank' => 'في صفحة جديدة',
                ])
                ->default('_self')
                ->visible(fn (callable $get) => $get('type') === 'external');
        }

        if (Schema::connection('tenant')->hasColumn('menu_items', 'is_active')) {
            $schema[] = Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true);
        }

        return $form->schema($schema)->columns(2);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return static::sanitizeData($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return static::sanitizeData($data);
    }

    protected static function sanitizeData(array $data): array
    {
        $hasTargetColumn = Schema::connection('tenant')->hasColumn('menu_items', 'target');

        if (! $hasTargetColumn) {
            unset($data['target']);
        }

        if (($data['type'] ?? null) === 'page') {
            $data['url'] = null;
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('menu.name')
                ->label('القائمة')
                ->searchable(),

            Tables\Columns\TextColumn::make('title')
                ->label('العنوان')
                ->searchable(),

            Tables\Columns\TextColumn::make('parent.title')
                ->label('الأب')
                ->placeholder('-'),

            Tables\Columns\TextColumn::make('type')
                ->label('النوع'),

            Tables\Columns\ViewColumn::make('icon')
                ->label('الأيقونة')
                ->view('filament.tables.columns.menu-item-icon'),

            Tables\Columns\TextColumn::make('url')
                ->label('الرابط')
                ->formatStateUsing(function ($state, MenuItem $record) {
                    if ($record->type === 'page' && $record->page) {
                        return '/page/' . $record->page->slug;
                    }

                    return $state ?: '-';
                })
                ->limit(35),

            Tables\Columns\TextColumn::make('sort_order')
                ->label('الترتيب')
                ->sortable(),
        ];

        if (Schema::connection('tenant')->hasColumn('menu_items', 'is_active')) {
            $columns[] = Tables\Columns\IconColumn::make('is_active')
                ->label('نشط')
                ->boolean();
        }

        return $table
            ->defaultSort('menu_id')
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
