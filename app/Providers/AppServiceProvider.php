<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app()->setLocale('ar');
        setlocale(LC_TIME, 'ar_SA.UTF-8', 'ar_SA', 'ar');
        View::composer('*', function ($view) {
            $request = request();

            if ($request->is('khaier') || $request->is('khaier/*')) {
                return;
            }

            try {
                $settings = SiteSetting::query()->latest('id')->first();
                $view->with('settings', $settings);
            } catch (\Throwable $e) {
            }
        });
    }
}
