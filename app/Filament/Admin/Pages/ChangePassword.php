<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'تغيير كلمة المرور';
    protected static ?string $title = 'تغيير كلمة المرور';
    protected static ?string $navigationGroup = 'إعدادات النظام';
    protected static ?int $navigationSort = 95;

    protected static string $view = 'filament.admin.pages.change-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('تحديث كلمة المرور')
                    ->description('يمكنك تغيير كلمة المرور الخاصة بحسابك من هنا.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->required(),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->same('password_confirmation'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $user = Auth::user();

        if (! $user) {
            Notification::make()
                ->title('تعذر العثور على المستخدم الحالي')
                ->danger()
                ->send();

            return;
        }

        if (! Hash::check($state['current_password'], $user->password)) {
            Notification::make()
                ->title('كلمة المرور الحالية غير صحيحة')
                ->danger()
                ->send();

            return;
        }

        $user->update([
            'password' => Hash::make($state['password']),
        ]);

        $this->form->fill([
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        Notification::make()
            ->title('تم تحديث كلمة المرور بنجاح')
            ->success()
            ->send();
    }
}
