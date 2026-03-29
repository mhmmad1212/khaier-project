<?php

namespace App\Http\Middleware;

use App\Models\Association;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ResolveTenantDatabase
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        if ($this->isCentralHost($host) || $request->is('khaier') || $request->is('khaier/*')) {
            return $next($request);
        }

        $association = Association::where('domain', $host)->first();

        if (! $association) {
            abort(404, 'Association not found for this domain.');
        }

        if (! $association->is_active) {
            return response()->view('errors.association-unavailable', [
                'title' => 'الموقع متوقف',
                'message' => 'الموقع متوقف، الرجاء التواصل مع نظام خيل.',
            ], 503);
        }

        if (($association->site_status ?? 'active') !== 'active') {
            return response()->view('errors.association-unavailable', [
                'title' => 'الموقع متوقف',
                'message' => 'الموقع متوقف، الرجاء التواصل مع نظام خيل.',
            ], 503);
        }

        if (($association->subscription_status ?? 'active') !== 'active') {
            return response()->view('errors.association-unavailable', [
                'title' => 'الاشتراك منتهي',
                'message' => 'الموقع منتهي أو موقوف، الرجاء التواصل مع نظام خيل.',
            ], 503);
        }

        if (! empty($association->subscription_end_date) && Carbon::today()->gt(Carbon::parse($association->subscription_end_date))) {
            return response()->view('errors.association-unavailable', [
                'title' => 'الاشتراك منتهي',
                'message' => 'الموقع منتهي أو موقوف، الرجاء التواصل مع نظام خيل.',
            ], 503);
        }

        App::instance('currentAssociation', $association);

        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        \Illuminate\Support\Facades\Config::set('database.connections.tenant.timezone', '+03:00');
        DB::purge('tenant');
        DB::reconnect('tenant');

        return $next($request);
    }

    protected function isCentralHost(string $host): bool
    {
        $centralHosts = array_filter([
            '127.0.0.1',
            'localhost',
            '157.173.96.33',
            env('CENTRAL_DOMAIN'),
            parse_url((string) config('app.url'), PHP_URL_HOST),
        ]);

        return in_array($host, $centralHosts, true);
    }
}
