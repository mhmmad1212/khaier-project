<?php

namespace App\Http\Middleware;

use App\Models\Association;
use Closure;
use Illuminate\Http\Request;
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

        $association = Association::where('domain', $host)
            ->where('is_active', true)
            ->where('subscription_status', 'active')
            ->first();

        if (! $association) {
            abort(404, 'Association not found for this domain.');
        }

        App::instance('currentAssociation', $association);

        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        DB::purge('tenant');
        DB::reconnect('tenant');

        return $next($request);
    }

    protected function isCentralHost(string $host): bool
    {
        return in_array($host, [
            '127.0.0.1',
            'localhost',
            '157.173.96.33',
        ], true);
    }
}
