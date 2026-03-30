<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\Employee;
use App\Models\FinancialReport;
use App\Models\GeneralAssemblyMember;
use App\Models\News;
use App\Models\Page;
use App\Models\PageTemplate;
use App\Models\Policy;
use App\Models\ProgramProject;
use App\Models\Regulation;
use App\Models\Service;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class FrontendPageController extends Controller
{
    protected function currentAssociation()
    {
        return App::bound('currentAssociation') ? App::make('currentAssociation') : null;
    }

    protected function siteSettings()
    {
        return DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();
    }

    protected function resolveTemplateByKey(?string $templateKey): ?PageTemplate
    {
        if (! $templateKey) {
            return null;
        }

        return PageTemplate::query()
            ->where('template_key', $templateKey)
            ->where('is_active', true)
            ->first();
    }

    public function pageShow(string $slug)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $siteSettings = $this->siteSettings();

        if ($page->page_type === 'system' && $page->system_key === 'policies') {
            $template = $this->resolveTemplateByKey($siteSettings->policies_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.policies.index';

            $items = Policy::query()
                ->where('is_active', true)
                ->with('fileMedia')
                ->orderByDesc('published_at')
                ->orderBy('sort_order')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'regulations') {
            $template = $this->resolveTemplateByKey($siteSettings->regulations_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.regulations.index';

            $items = Regulation::query()
                ->where('is_active', true)
                ->with('fileMedia')
                ->orderByDesc('published_at')
                ->orderBy('sort_order')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'financial_reports') {
            $template = $this->resolveTemplateByKey($siteSettings->financial_reports_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.financial-reports.index';

            $items = FinancialReport::query()
                ->where('is_active', true)
                ->with('fileMedia')
                ->orderByDesc('year')
                ->orderByDesc('published_at')
                ->orderBy('sort_order')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'news_index') {
            $template = $this->resolveTemplateByKey($siteSettings->news_index_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.news.index';

            $items = News::query()
                ->where('is_active', true)
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get();

            $news = $items;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items', 'news'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'services') {
            $template = $this->resolveTemplateByKey($siteSettings->services_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.services.index';

            $services = Service::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'services'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'employees') {
            $template = $this->resolveTemplateByKey($siteSettings->employees_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.system.employees';

            $employees = Employee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $employees;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'employees', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'board_members') {
            $template = $this->resolveTemplateByKey($siteSettings->board_members_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.system.board-members';

            $boardMembers = BoardMember::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $boardMembers;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'boardMembers', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'general_assembly_members') {
            $template = $this->resolveTemplateByKey($siteSettings->general_assembly_members_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.system.general-assembly-members';

            $generalAssemblyMembers = GeneralAssemblyMember::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $generalAssemblyMembers;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'generalAssemblyMembers', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'program_projects_index') {
            $template = $this->resolveTemplateByKey($siteSettings->program_projects_index_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.program-projects.index';

            $projects = ProgramProject::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'projects'));
        }

        $template = null;

        if (! empty($page->template_id)) {
            $template = PageTemplate::query()
                ->where('id', $page->template_id)
                ->where('is_active', true)
                ->first();
        }

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.page.show';

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page'));
    }

    public function pageNewsShow(string $slug, string $newsSlug)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('page_type', 'system')
            ->where('system_key', 'news_show')
            ->firstOrFail();

        $siteSettings = $this->siteSettings();

        $news = News::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('slug', $newsSlug)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->news_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.news.show';

        $item = $news;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'news', 'item'));
    }

    public function newsShow(string $slug)
    {
        $detailPage = Page::query()
            ->where('is_active', true)
            ->where('page_type', 'system')
            ->where('system_key', 'news_show')
            ->orderByDesc('id')
            ->first();

        if ($detailPage) {
            return redirect('/page/' . $detailPage->slug . '/news/' . $slug);
        }

        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $siteSettings = $this->siteSettings();

        $news = News::query()
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->news_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.news.show';

        $item = $news;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'news', 'item'));
    }

    public function pageProgramProjectShow(string $slug, int $id)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('page_type', 'system')
            ->where('system_key', 'program_projects_show')
            ->firstOrFail();

        $siteSettings = $this->siteSettings();

        $project = ProgramProject::query()
            ->with(['galleryImages.mediaItem', 'coverMedia', 'reportMedia'])
            ->where('is_active', true)
            ->findOrFail($id);

        $template = $this->resolveTemplateByKey($siteSettings->program_projects_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.program-projects.show';

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'project'));
    }

    public function programProjectShow(int $id)
    {
        $detailPage = Page::query()
            ->where('is_active', true)
            ->where('page_type', 'system')
            ->where('system_key', 'program_projects_show')
            ->orderByDesc('id')
            ->first();

        if ($detailPage) {
            return redirect('/page/' . $detailPage->slug . '/project/' . $id);
        }

        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $siteSettings = $this->siteSettings();

        $project = ProgramProject::query()
            ->with(['galleryImages.mediaItem', 'coverMedia', 'reportMedia'])
            ->where('is_active', true)
            ->findOrFail($id);

        $template = $this->resolveTemplateByKey($siteSettings->program_projects_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.program-projects.show';

        return view($viewPath, compact('association', 'siteSettings', 'template', 'project'));
    }

}
