<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldSkip($request)) {
            return $response;
        }

        if (! App::bound('currentAssociation')) {
            return $response;
        }

        try {
            $ip = $request->ip();
            $url = $request->path();

            $alreadyVisited = PageVisit::query()
                ->where('ip', $ip)
                ->where('url', $url)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->exists();

            if (! $alreadyVisited) {
                PageVisit::create([
                    'url' => $url,
                    'type' => $this->detectType($request),
                    'entity_id' => $this->detectEntityId($request),
                    'ip' => $ip,
                    'user_agent' => (string) $request->userAgent(),
                ]);
            }
        } catch (\Throwable $e) {
            // لا نكسر الموقع لو فشل التتبع
        }

        return $response;
    }

    protected function shouldSkip(Request $request): bool
    {
        return $request->ajax()
            || $request->is('admin')
            || $request->is('admin/*')
            || $request->is('khaier')
            || $request->is('khaier/*')
            || $request->is('livewire/*')
            || $request->is('_ignition/*')
            || $request->is('up')
            || str_starts_with($request->path(), 'storage/');
    }

    protected function detectType(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, '/news/')) {
            return 'news';
        }

        if (str_contains($path, '/project/')) {
            return 'project';
        }

        if ($path === '/' || $path === '') {
            return 'home';
        }

        return 'page';
    }

    protected function detectEntityId(Request $request): ?int
    {
        $id = $request->route('id');

        return is_numeric($id) ? (int) $id : null;
    }
}
