<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\FrontendPageController;
use App\Http\Controllers\SeoController;

/*
|--------------------------------------------------------------------------
| SEO Routes
|--------------------------------------------------------------------------
*/
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

/*
|--------------------------------------------------------------------------
| Central Domain (Marketing Only)
|--------------------------------------------------------------------------
*/
Route::domain(env('CENTRAL_DOMAIN', 'khaier.org'))->group(function () {
    Route::get('/', function () {
        return view('marketing.home');
    });
});

/*
|--------------------------------------------------------------------------
| Public Association Routes
|--------------------------------------------------------------------------
| These routes are intentionally NOT restricted by Route::domain(...)
| so they can work for:
| - subdomains مثل test3.khaier.org
| - custom domains لاحقًا
|--------------------------------------------------------------------------
*/
Route::get('/', [WebsiteController::class, 'home'])->name('website.home');
Route::get('/news', [WebsiteController::class, 'newsIndex'])->name('news.index');
Route::get('/news/{slug}', [FrontendPageController::class, 'newsShow'])->name('news.show');
Route::get('/page/{slug}', [FrontendPageController::class, 'pageShow'])->name('page.show');
Route::get('/page/{slug}/news/{newsSlug}', [FrontendPageController::class, 'pageNewsShow']);
Route::get('/page/{slug}/project/{id}', [FrontendPageController::class, 'pageProjectShow']);
Route::get('/projects/{id}', [FrontendPageController::class, 'programProjectShow']);
Route::get('/board-members', [WebsiteController::class, 'boardMembers'])->name('board-members');
Route::get('/general-assembly', [WebsiteController::class, 'generalAssembly'])->name('general-assembly.index');
