<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Models\ProgramProject;
use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class SeoController extends Controller
{
    protected function currentAssociation()
    {
        return App::bound('currentAssociation') ? App::make('currentAssociation') : null;
    }

    public function robots(): Response
    {
        $baseUrl = url('/');

        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: ' . $baseUrl . '/sitemap.xml',
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $baseUrl = rtrim(url('/'), '/');

        $urls = collect();

        $urls->push([
            'loc' => $baseUrl . '/',
            'lastmod' => now()->toAtomString(),
            'priority' => '1.0',
        ]);

        if (class_exists(Page::class)) {
            Page::query()
                ->where('is_active', true)
                ->get(['slug', 'updated_at'])
                ->each(function ($page) use ($urls, $baseUrl) {
                    if (! empty($page->slug)) {
                        $urls->push([
                            'loc' => $baseUrl . '/page/' . $page->slug,
                            'lastmod' => optional($page->updated_at)->toAtomString(),
                            'priority' => '0.8',
                        ]);
                    }
                });
        }

        if (class_exists(News::class)) {
            News::query()
                ->where('is_active', true)
                ->where('status', 'published')
                ->get(['slug', 'updated_at'])
                ->each(function ($news) use ($urls, $baseUrl) {
                    if (! empty($news->slug)) {
                        $urls->push([
                            'loc' => $baseUrl . '/news/' . $news->slug,
                            'lastmod' => optional($news->updated_at)->toAtomString(),
                            'priority' => '0.7',
                        ]);
                    }
                });

            $urls->push([
                'loc' => $baseUrl . '/news',
                'lastmod' => now()->toAtomString(),
                'priority' => '0.9',
            ]);
        }

        if (class_exists(ProgramProject::class)) {
            ProgramProject::query()
                ->where('is_active', true)
                ->get(['id', 'updated_at'])
                ->each(function ($project) use ($urls, $baseUrl) {
                    $urls->push([
                        'loc' => $baseUrl . '/projects/' . $project->id,
                        'lastmod' => optional($project->updated_at)->toAtomString(),
                        'priority' => '0.7',
                    ]);
                });
        }

        $xml = view('themes.default.partials.sitemap-xml', [
            'urls' => $urls->unique('loc')->values(),
        ])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
