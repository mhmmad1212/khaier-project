<?php

namespace App\Http\Controllers;

use App\Models\PageTemplate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class WebsiteController extends Controller
{
    public function home()
    {
        $association = App::bound('currentAssociation')
            ? App::make('currentAssociation')
            : null;

        if (! $association) {
            abort(404);
        }

        $sliders = DB::connection('tenant')->table('sliders')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $statistics = DB::connection('tenant')->table('statistics')
            ->orderBy('sort_order')
            ->get();

        $news = DB::connection('tenant')->table('news')
            ->where('is_active', true)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $partners = DB::connection('tenant')->table('partners')
            ->orderBy('sort_order')
            ->get();

        $theme = $association->theme_key ?: 'default';

        $siteSettings = DB::connection('tenant')
            ->table('site_settings')
            ->orderByDesc('id')
            ->first();

        $homeTemplate = null;

        if ($siteSettings && !empty($siteSettings->home_template_key)) {
            $homeTemplate = PageTemplate::where('template_key', $siteSettings->home_template_key)
                ->where('is_active', true)
                ->first();
        }

        $viewPath = ($homeTemplate && !empty($homeTemplate->view_path))
            ? $homeTemplate->view_path
            : "themes.{$theme}.home";

        return view($viewPath, [
            'association' => $association,
            'sliders' => $sliders,
            'statistics' => $statistics,
            'news' => $news,
            'partners' => $partners,
            'template' => $homeTemplate,
            'siteSettings' => $siteSettings,
        ]);
    }
}
