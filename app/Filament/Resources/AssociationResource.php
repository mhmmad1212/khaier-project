<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssociationResource\Pages;
use App\Filament\Resources\AssociationResource\RelationManagers\ActivitiesRelationManager;
use App\Models\Association;
use App\Services\AssociationActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssociationResource extends Resource
{
    protected static ?string $model = Association::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'الجمعيات';
    protected static ?string $modelLabel = 'جمعية';
    protected static ?string $pluralModelLabel = 'الجمعيات';
    protected static ?string $navigationGroup = 'إدارة النظام';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الجمعية')
                ->schema([
                    Forms\Components\Toggle::make('can_edit_system_pages')
                        ->label('السماح للجمعية بتعديل الصفحات النظامية')
                        ->helperText('إذا كان مغلقًا فلن تتمكن الجمعية من تعديل صفحات النظام مثل مجلس الإدارة والأخبار')
                        ->default(false),

                    Forms\Components\TextInput::make('name')
                        ->label('اسم الجمعية')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('slug')
                        ->label('المعرف المختصر')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('مثال: abar-hail'),

                    Forms\Components\Select::make('domain_type')
                        ->label('نوع الدومين')
                        ->options([
                            'subdomain' => 'رابط داخلي مؤقت',
                            'custom_domain' => 'دومين خاص',
                        ])
                        ->default('custom_domain')
                        ->live()
                        ->required(),

                    Forms\Components\TextInput::make('subdomain_label')
                        ->label('اسم الرابط الداخلي')
                        ->helperText('مثال: riyadh-demo وسيصبح riyadh-demo.khaier.org')
                        ->maxLength(255)
                        ->visible(fn (Forms\Get $get) => $get('domain_type') === 'subdomain')
                        ->required(fn (Forms\Get $get) => $get('domain_type') === 'subdomain')
                        ->dehydrated(fn (Forms\Get $get) => $get('domain_type') === 'subdomain'),

                    Forms\Components\TextInput::make('domain')
                        ->label('الدومين')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('مثال: abar-hail.org.sa')
                        ->visible(fn (Forms\Get $get) => $get('domain_type') === 'custom_domain')
                        ->required(fn (Forms\Get $get) => $get('domain_type') === 'custom_domain')
                        ->dehydrated(fn (Forms\Get $get) => $get('domain_type') === 'custom_domain'),

                    Forms\Components\TextInput::make('official_phone')
                        ->label('رقم جوال المسؤول على الموقع')
                        ->tel()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('official_email')
                        ->label('الإيميل الرسمي للجمعية')
                        ->email()
                        ->maxLength(255),

                    Forms\Components\Select::make('is_subscribed')
                        ->label('هل الجمعية مشتركة في نظام خيّر؟')
                        ->options([
                            1 => 'نعم',
                            0 => 'لا',
                        ])
                        ->default(1)
                        ->required(),

                    Forms\Components\Select::make('site_status')
                        ->label('حالة الموقع')
                        ->options([
                            'active' => 'نشطة',
                            'closed' => 'مغلقة',
                            'suspended' => 'موقوفة',
                        ])
                        ->default('active')
                        ->required(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('مفعلة في النظام')
                        ->default(true),
                ])
                ->columns(2),

            Forms\Components\Section::make('الاشتراك')
                ->schema([
                    Forms\Components\Select::make('subscription_status')
                        ->label('حالة الاشتراك')
                        ->options([
                            'active' => 'نشط',
                            'expired' => 'منتهي',
                            'suspended' => 'معلق',
                        ])
                        ->default('active')
                        ->required(),

                    Forms\Components\DatePicker::make('subscription_start_date')
                        ->label('تاريخ البداية')
                        ->native(false),

                    Forms\Components\DatePicker::make('subscription_end_date')
                        ->label('تاريخ النهاية')
                        ->native(false),
                ])
                ->columns(3),

            Forms\Components\Section::make('طريقة الإنشاء')
                ->schema([
                    Forms\Components\Select::make('creation_mode')
                        ->label('نوع الإنشاء')
                        ->options([
                            'empty' => 'جمعية فارغة',
                            'clone' => 'استنساخ من جمعية موجودة',
                        ])
                        ->default('empty')
                        ->live()
                        ->required(),

                    Forms\Components\Select::make('cloned_from_association_id')
                        ->label('الجمعية المصدر')
                        ->options(fn () => Association::query()->where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->visible(fn (Forms\Get $get) => $get('creation_mode') === 'clone')
                        ->required(fn (Forms\Get $get) => $get('creation_mode') === 'clone'),
                ])
                ->columns(2),
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
                    ->label('اسم الجمعية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('domain')
                    ->label('الدومين')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('domain_type')
                    ->label('نوع الدومين')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'subdomain' => 'رابط داخلي مؤقت',
                        'custom_domain' => 'دومين خاص',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('site_status')
                    ->label('حالة الموقع')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'active' => 'نشطة',
                        'closed' => 'مغلقة',
                        'suspended' => 'موقوفة',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('subscription_status')
                    ->label('الاشتراك')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'suspended' => 'معلق',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('subscription_end_date')
                    ->label('ينتهي في')
                    ->date('Y-m-d')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('official_phone')
                    ->label('جوال المسؤول')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('official_email')
                    ->label('الإيميل الرسمي')
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعلة')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview')
                        ->label('معاينة الموقع')
                        ->icon('heroicon-o-eye')
                        ->url(fn (Association $record): ?string => $record->getPreviewUrl())
                        ->openUrlInNewTab()
                        ->visible(fn (Association $record): bool => filled($record->getPreviewUrl())),

                    Tables\Actions\Action::make('resetTenantPassword')
                        ->label('إعادة تعيين كلمة المرور')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->modalHeading('إعادة تعيين كلمة مرور مستخدم الجمعية')
                        ->modalDescription('سيتم تغيير كلمة المرور مباشرة داخل قاعدة بيانات الجمعية.')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label('المستخدم')
                                ->options(function (Association $record): array {
                                    $connectionName = 'tenant_reset_' . $record->id;

                                    config([
                                        'database.connections.' . $connectionName => [
                                            'driver' => 'mysql',
                                            'host' => $record->database_host ?: '127.0.0.1',
                                            'port' => $record->database_port ?: 3306,
                                            'database' => $record->database_name,
                                            'username' => $record->database_username,
                                            'password' => $record->database_password,
                                            'charset' => 'utf8mb4',
                                            'collation' => 'utf8mb4_unicode_ci',
                                            'prefix' => '',
                                            'prefix_indexes' => true,
                                            'strict' => true,
                                            'engine' => null,
                                        ],
                                    ]);

                                    \Illuminate\Support\Facades\DB::purge($connectionName);

                                    return \Illuminate\Support\Facades\DB::connection($connectionName)
                                        ->table('users')
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn ($user) => [
                                            $user->id => trim(($user->name ?? '') . ' - ' . ($user->email ?? ''))
                                        ])
                                        ->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('password')
                                ->label('كلمة المرور الجديدة')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(8),

                            Forms\Components\TextInput::make('password_confirmation')
                                ->label('تأكيد كلمة المرور')
                                ->password()
                                ->revealable()
                                ->required()
                                ->same('password'),
                        ])
                        ->action(function (Association $record, array $data): void {
                            $connectionName = 'tenant_reset_' . $record->id;

                            config([
                                'database.connections.' . $connectionName => [
                                    'driver' => 'mysql',
                                    'host' => $record->database_host ?: '127.0.0.1',
                                    'port' => $record->database_port ?: 3306,
                                    'database' => $record->database_name,
                                    'username' => $record->database_username,
                                    'password' => $record->database_password,
                                    'charset' => 'utf8mb4',
                                    'collation' => 'utf8mb4_unicode_ci',
                                    'prefix' => '',
                                    'prefix_indexes' => true,
                                    'strict' => true,
                                    'engine' => null,
                                ],
                            ]);

                            \Illuminate\Support\Facades\DB::purge($connectionName);

                            $user = \Illuminate\Support\Facades\DB::connection($connectionName)
                                ->table('users')
                                ->where('id', $data['user_id'])
                                ->first();

                            if (! $user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('تعذر العثور على المستخدم')
                                    ->body('لم يتم العثور على المستخدم المحدد داخل قاعدة الجمعية.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            \Illuminate\Support\Facades\DB::connection($connectionName)
                                ->table('users')
                                ->where('id', $user->id)
                                ->update([
                                    'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                                    'updated_at' => now(),
                                ]);

                            AssociationActivityLogger::log(
                                $record,
                                4,
                                'password_reset',
                                'تم تغيير كلمة المرور',
                                'تم تغيير كلمة المرور للمستخدم: ' . (($user->name ?? '') !== '' ? ($user->name . ' - ') : '') . ($user->email ?? ('#' . $user->id))
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('تم تحديث كلمة المرور')
                                ->body('تم تغيير كلمة المرور للمستخدم: ' . ($user->email ?? ('#' . $user->id)))
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('activities')
                        ->label('سجل الحركات')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->url(fn (Association $record): string => url('/khaier/association-activities?association=' . $record->id))
                        ->openUrlInNewTab(false),

                    Tables\Actions\Action::make('renewSubscription')
                        ->label('تجديد الاشتراك')
                        ->icon('heroicon-o-calendar-days')
                        ->color('primary')
                        ->modalHeading('تجديد اشتراك الجمعية')
                        ->modalDescription('اختر مدة التجديد أو استخدم تاريخًا مخصصًا لتحديث تاريخ انتهاء الاشتراك.')
                        ->form([
                            Forms\Components\Select::make('renewal_type')
                                ->label('نوع التجديد')
                                ->options([
                                    '1_month' => 'شهر',
                                    '6_months' => '6 أشهر',
                                    '1_year' => 'سنة',
                                    '2_years' => 'سنتان',
                                    '3_years' => '3 سنوات',
                                    'custom_date' => 'تاريخ مخصص',
                                ])
                                ->default('1_year')
                                ->live()
                                ->required(),

                            Forms\Components\DatePicker::make('custom_date')
                                ->label('تاريخ الانتهاء الجديد')
                                ->native(false)
                                ->visible(fn (Forms\Get $get) => $get('renewal_type') === 'custom_date')
                                ->required(fn (Forms\Get $get) => $get('renewal_type') === 'custom_date'),

                            Forms\Components\Placeholder::make('current_end_date')
                                ->label('تاريخ الانتهاء الحالي')
                                ->content(fn (Association $record): string => $record->subscription_end_date
                                    ? \Illuminate\Support\Carbon::parse($record->subscription_end_date)->format('Y-m-d')
                                    : 'غير محدد'),
                        ])
                        ->action(function (Association $record, array $data): void {
                            $baseDate = $record->subscription_end_date
                                ? \Illuminate\Support\Carbon::parse($record->subscription_end_date)
                                : now();

                            if ($baseDate->isPast()) {
                                $baseDate = now();
                            }

                            $newEndDate = match ($data['renewal_type']) {
                                '1_month' => $baseDate->copy()->addMonth(),
                                '6_months' => $baseDate->copy()->addMonths(6),
                                '1_year' => $baseDate->copy()->addYear(),
                                '2_years' => $baseDate->copy()->addYears(2),
                                '3_years' => $baseDate->copy()->addYears(3),
                                'custom_date' => \Illuminate\Support\Carbon::parse($data['custom_date']),
                                default => $baseDate->copy()->addYear(),
                            };

                            $record->update([
                                'subscription_status' => 'active',
                                'subscription_end_date' => $newEndDate->toDateString(),
                            ]);

                            AssociationActivityLogger::log(
                                $record,
                                2,
                                'renewed',
                                'تم تجديد الاشتراك',
                                'تم التجديد بنوع: ' . $data['renewal_type'] . ' | تاريخ الانتهاء الجديد: ' . $newEndDate->format('Y-m-d')
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('تم تجديد الاشتراك')
                                ->body('تاريخ الانتهاء الجديد: ' . $newEndDate->format('Y-m-d'))
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\EditAction::make(),
                ])
                ->label('الإجراءات')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssociations::route('/'),
            'create' => Pages\CreateAssociation::route('/create'),
            'edit' => Pages\EditAssociation::route('/{record}/edit'),
        ];
    }
}
