<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use App\Models\PageTemplate;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $navigationGroup = 'إدارة الموقع';
    protected static ?int $navigationSort = 1;
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الصفحات';
    protected static ?string $modelLabel = 'صفحة';
    protected static ?string $pluralModelLabel = 'الصفحات';

    public static function form(Form $form): Form
    {
        \Illuminate\Support\Facades\Log::info('DEBUG PageResource::form loaded', [
            'file' => __FILE__,
            'url' => request()?->fullUrl(),
        ]);
        return $form->schema([
            Forms\Components\Section::make('نوع الصفحة')
                ->schema([
                    Forms\Components\Select::make('page_type')
                        ->label('نوع الصفحة')
                        ->options([
                            'content' => 'صفحة عادية',
                            'system' => 'صفحة نظامية',
                        ])
                        ->default('content')
                        ->live()
                        ->required(),

                    Forms\Components\Select::make('system_key')
                        ->label('نوع الصفحة النظامية')
                        ->options([
                            'board_members' => 'مجلس الإدارة',
                            'general_assembly_members' => 'الجمعية العمومية',
                            'employees' => 'الموظفون',
                            'committees' => 'اللجان',
                            'news_index' => 'قائمة الأخبار',
                            'program_projects_index' => 'قائمة المشاريع',
                        ])
                        ->visible(fn (Get $get) => $get('page_type') === 'system' && (\Illuminate\Support\Facades\App::bound('currentAssociation') ? (bool) \Illuminate\Support\Facades\App::make('currentAssociation')->can_edit_system_pages : false)),
                ])->columns(3),

            Forms\Components\Section::make('المحتوى الأساسي')
                ->schema([
                    Forms\Components\Select::make('template_id')
                        ->label('التصميم')
                        ->options(PageTemplate::where('is_active', 1)->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->label('العنوان')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->label('الرابط المختصر')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('الملخص TEST-RAW-HTML')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\Placeholder::make('debug_raw_html_marker')
                        ->label('')
                        ->content('DEBUG: إذا ظهرت هذه الرسالة داخل شاشة الصفحات فهذا يعني أن PageResource الحالي يُقرأ فعليًا من الواجهة.')
                        ->columnSpanFull(),



                    Forms\Components\RichEditor::make('content')
                        ->label('المحتوى العادي')
                        ->helperText('استخدم هذا الحقل للمحتوى النصي العادي. إذا كنت تريد HTML خام كامل فاستخدم الحقل التالي.')
                        ->columnSpanFull()
                        ->visible(fn (Get $get) => $get('page_type') === 'content'),



                ])->columns(2),

            Forms\Components\Section::make('HTML خام للتصميم')
                ->schema([
                    Forms\Components\Textarea::make('raw_html')
                        ->label('HTML خام')
                        ->rows(24)
                        ->columnSpanFull()
                        ->helperText('ألصق هنا HTML خام مباشرة. إذا تم تعبئة هذا الحقل فسيتم عرضه بدل المحتوى العادي. لا تستخدم زر الكود داخل المحرر لهذا الغرض.'),
                ])
                ->columns(1)
                ->collapsible(),

            Forms\Components\Section::make('النشر والعرض')
                ->schema([
                    Forms\Components\TextInput::make('featured_image')
                        ->label('الصورة البارزة')
                        ->maxLength(255)
                        ->helperText('يمكن استبداله لاحقًا برفع ملف.'),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'draft' => 'مسودة',
                            'published' => 'منشور',
                        ])
                        ->default('published')
                        ->required(),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('تاريخ النشر'),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('الترتيب')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('نشط')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->label('عنوان SEO')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('meta_description')
                        ->label('وصف SEO')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable(),

                Tables\Columns\TextColumn::make('page_type')
                    ->label('نوع الصفحة')
                    ->formatStateUsing(fn ($state) => $state === 'system' ? 'نظامية' : 'عادية'),

                Tables\Columns\TextColumn::make('system_key')
                    ->label('المفتاح النظامي')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('الرابط المختصر')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),

                Tables\Columns\IconColumn::make('allow_tenant_edit')
                    ->label('تعديل الجمعية')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
