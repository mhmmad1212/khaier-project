<?php

namespace App\Filament\Central\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.central.auth.login';

    public function getHeading(): string
    {
        return 'تسجيل الدخول إلى لوحة المشرف';
    }
}
