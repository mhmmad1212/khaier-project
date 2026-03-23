<?php

namespace App\Filament\Admin\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.admin.auth.login';

    public function getHeading(): string
    {
        return 'مرحبًا بكم في تسجيل الدخول لمواقع الخير الإلكترونية';
    }
}
