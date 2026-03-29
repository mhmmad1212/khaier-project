<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiteSettingResource\Pages;
use App\Forms\Components\MediaPicker;
use App\Models\MediaItem;
use App\Models\SiteSetting;
use App\Models\PageTemplate;
use Illuminate\Support\Facades\DB;
use App\Support\LogoColorExtractor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $navigationGroup = 'إعدادات النظام';
    protected static ?int $navigationSort = 1;
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات الموقع';
    protected static ?string $modelLabel = 'إعدادات الموقع';
    protected static ?string $pluralModelLabel = 'إعدادات الموقع';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('إعدادات الموقع')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('عام')
                        ->schema([
                            Forms\Components\TextInput::make('site_name')->label('اسم الموقع'),
                            Forms\Components\TextInput::make('association_name')->label('اسم الجمعية'),
                            Forms\Components\Textarea::make('site_description')->label('وصف الموقع')->rows(3)->columnSpanFull(),
                            Forms\Components\TextInput::make('phone')->label('رقم الهاتف'),
                            Forms\Components\TextInput::make('email')->label('البريد الإلكتروني')->email(),
                            Forms\Components\TextInput::make('license_number')->label('رقم الترخيص'),
                            Forms\Components\Textarea::make('address')->label('العنوان')->rows(3)->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('المحتوى')
                        ->schema([
                            Forms\Components\RichEditor::make('about_text')->label('نبذة')->columnSpanFull(),
                            Forms\Components\RichEditor::make('vision')->label('الرؤية')->columnSpanFull(),
                            Forms\Components\RichEditor::make('mission')->label('الرسالة')->columnSpanFull(),
                            Forms\Components\TextInput::make('intro_video_url')->label('رابط فيديو تعريفي')->url()->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('روابط المستفيدين')
                        ->schema([
                            Forms\Components\TextInput::make('beneficiary_portal_url')->label('بوابة المستفيدين')->url(),
                            Forms\Components\TextInput::make('beneficiary_login_url')->label('دخول المستفيدين')->url(),
                            Forms\Components\TextInput::make('beneficiary_register_url')->label('تسجيل مستفيد جديد')->url(),
                            Forms\Components\TextInput::make('store_url')->label('رابط المتجر')->url(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('التواصل')
                        ->schema([
                            Forms\Components\TextInput::make('facebook')->label('فيسبوك')->url(),
                            Forms\Components\TextInput::make('twitter_url')->label('تويتر')->url(),
                            Forms\Components\TextInput::make('instagram_url')->label('إنستغرام')->url(),
                            Forms\Components\TextInput::make('youtube_url')->label('يوتيوب')->url(),
                            Forms\Components\TextInput::make('tiktok_url')->label('تيك توك')->url(),
                            Forms\Components\TextInput::make('snapchat_url')->label('سناب')->url(),
                            Forms\Components\TextInput::make('whatsapp_url')->label('واتساب')->url(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('التصاميم')
                        ->schema([
                            Forms\Components\Select::make('home_template_key')
                                ->label('تصميم الصفحة الرئيسية')
                                ->options(fn () => static::templateOptions('home'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('policies_template_key')
                                ->label('تصميم صفحة السياسات')
                                ->options(fn () => static::templateOptions('policies'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('regulations_template_key')
                                ->label('تصميم صفحة اللوائح')
                                ->options(fn () => static::templateOptions('regulations'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('financial_reports_template_key')
                                ->label('تصميم صفحة القوائم المالية')
                                ->options(fn () => static::templateOptions('financial_reports'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('news_index_template_key')
                                ->label('تصميم قائمة الأخبار')
                                ->options(fn () => static::templateOptions('news_index'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('news_show_template_key')
                                ->label('تصميم تفاصيل الخبر')
                                ->options(fn () => static::templateOptions('news_show'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('committees_template_key')
                                ->label('تصميم صفحة اللجان')
                                ->options(fn () => static::templateOptions('committees'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('board_members_template_key')
                                ->label('تصميم صفحة مجلس الإدارة')
                                ->options(fn () => static::templateOptions('board_members'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('general_assembly_members_template_key')
                                ->label('تصميم صفحة الجمعية العمومية')
                                ->options(fn () => static::templateOptions('general_assembly_members'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('employees_template_key')
                                ->label('تصميم صفحة الموظفين')
                                ->options(fn () => static::templateOptions('employees'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('program_projects_index_template_key')
                                ->label('تصميم صفحة قائمة المشاريع')
                                ->options(fn () => static::templateOptions('program_projects_index'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('program_projects_show_template_key')
                                ->label('تصميم صفحة تفاصيل المشروع')
                                ->options(fn () => static::templateOptions('program_projects_show'))
                                ->searchable()
                                ->preload(),

                            Forms\Components\Select::make('services_template_key')
                                ->label('تصميم صفحة الخدمات')
                                ->options(fn () => static::templateOptions('services'))
                                ->searchable()
                                ->preload(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('الهوية والألوان')
                        ->schema([
                            MediaPicker::make('logo_media_id')
                                ->label('الشعار')
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $media = MediaItem::query()->find($state);
                                    if (! $media || empty($media->file)) {
                                        return;
                                    }

                                    $path = storage_path('app/public/' . ltrim($media->file, '/'));
                                    $colors = LogoColorExtractor::suggestFromPath($path);

                                    if (! empty($colors['primary_color'])) {
                                        $set('primary_color', $colors['primary_color']);
                                    }
                                    if (! empty($colors['secondary_color'])) {
                                        $set('secondary_color', $colors['secondary_color']);
                                    }
                                    if (! empty($colors['button_color'])) {
                                        $set('button_color', $colors['button_color']);
                                    }
                                })
                                ->columnSpanFull(),

                            MediaPicker::make('favicon_media_id')
                                ->label('أيقونة الموقع')
                                ->columnSpanFull(),

                            Actions::make([
                                Action::make('suggest_colors_from_logo')
                                    ->label('اقتراح الألوان من الشعار')
                                    ->color('primary')
                                    ->action(function (Get $get, Set $set) {
                                        $logoId = $get('logo_media_id');

                                        if (! $logoId) {
                                            return;
                                        }

                                        $media = MediaItem::query()->find($logoId);
                                        if (! $media || empty($media->file)) {
                                            return;
                                        }

                                        $path = storage_path('app/public/' . ltrim($media->file, '/'));
                                        $colors = LogoColorExtractor::suggestFromPath($path);

                                        if (! empty($colors['primary_color'])) {
                                            $set('primary_color', $colors['primary_color']);
                                        }
                                        if (! empty($colors['secondary_color'])) {
                                            $set('secondary_color', $colors['secondary_color']);
                                        }
                                        if (! empty($colors['button_color'])) {
                                            $set('button_color', $colors['button_color']);
                                        }
                                    }),
                            ])->columnSpanFull(),

                            Forms\Components\ColorPicker::make('primary_color')->label('اللون الرئيسي'),
                            Forms\Components\ColorPicker::make('secondary_color')->label('اللون الثانوي'),
                            Forms\Components\ColorPicker::make('button_color')->label('لون الأزرار'),
                        ])->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }


    protected static function templateOptions(string $pageType): array
    {
        $associationId = DB::connection('mysql')
            ->table('associations')
            ->where('domain', request()->getHost())
            ->value('id');

        return PageTemplate::query()
            ->where('page_type', $pageType)
            ->where('is_active', true)
            ->where(function ($query) use ($associationId) {
                $query->where('scope_type', 'global');

                if ($associationId) {
                    $query->orWhere(function ($subQuery) use ($associationId) {
                        $subQuery->where('scope_type', 'restricted')
                            ->whereExists(function ($existsQuery) use ($associationId) {
                                $existsQuery->select(DB::raw(1))
                                    ->from('page_template_associations')
                                    ->whereColumn('page_template_associations.page_template_id', 'page_templates.id')
                                    ->where('page_template_associations.association_id', $associationId);
                            });
                    });
                }
            })
            ->orderBy('sort_order')
            ->pluck('name', 'template_key')
            ->toArray();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site_name')->label('اسم الموقع')->searchable(),
                Tables\Columns\TextColumn::make('association_name')->label('اسم الجمعية'),
                Tables\Columns\TextColumn::make('phone')->label('الهاتف'),
                Tables\Columns\TextColumn::make('email')->label('البريد'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
