<?php

use App\Http\Controllers\FrontendFormController;

use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\Admin\MediaPickerPageController;

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
Route::get('/page/{slug}/project/{id}', [FrontendPageController::class, 'pageProgramProjectShow']);
Route::get('/projects/{id}', [FrontendPageController::class, 'programProjectShow']);
Route::get('/volunteer-opportunities/{slug}', [FrontendPageController::class, 'volunteerOpportunityShow'])->name('volunteer-opportunities.show');
Route::get('/page/{slug}/volunteer/{volunteerSlug}', [FrontendPageController::class, 'pageVolunteerOpportunityShow'])->name('page.volunteer-opportunities.show');
Route::get('/board-members', [WebsiteController::class, 'boardMembers'])->name('board-members');
Route::get('/general-assembly', [WebsiteController::class, 'generalAssembly'])->name('general-assembly.index');



Route::middleware(['web', 'auth'])->prefix('admin/media-library')->group(function () {
    Route::get('/picker-json', [MediaLibraryController::class, 'pickerJson']);
    Route::get('/usage/{itemId}', [MediaLibraryController::class, 'usage'])->whereNumber('itemId');
    Route::post('/upload', [MediaLibraryController::class, 'upload']);
});


Route::get('/admin/media-picker', [\App\Http\Controllers\Admin\MediaPickerPageController::class, 'index']);


Route::get('/admin/media-picker', [MediaPickerPageController::class, 'index']);
Route::post('/admin/media-picker/upload', [MediaPickerPageController::class, 'store']);


Route::get('/forms/{slug}', [FrontendFormController::class, 'show']);
Route::post('/forms/{slug}', [FrontendFormController::class, 'submit']);
Route::get('/forms/{slug}/track', [FrontendFormController::class, 'track']);
Route::post('/forms/{slug}/track', [FrontendFormController::class, 'lookup']);
Route::post('/forms/{slug}/track/{submission}/reply', [FrontendFormController::class, 'customerReply']);
