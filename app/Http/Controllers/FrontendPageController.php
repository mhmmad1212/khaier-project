<?php

namespace App\Http\Controllers;

use App\Models\AssociationPlan;
use App\Models\BoardMember;
use App\Models\Committee;
use App\Models\Disclosure;
use App\Models\Employee;
use App\Models\FinancialReport;
use App\Models\GeneralAssemblyMember;
use App\Models\License;
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

        if ($page->page_type === 'system' && $page->system_key === 'disclosure') {
            $template = $this->resolveTemplateByKey($siteSettings->disclosure_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'generated-templates.disclosure.template_24';

            $items = Disclosure::query()
                ->orderByDesc('id')
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

        if ($page->page_type === 'system' && $page->system_key === 'licenses') {
            $template = $this->resolveTemplateByKey($siteSettings->licenses_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.licenses.index';

            $items = License::query()
                ->where('is_active', true)
                ->with('fileMedia')
                ->orderBy('sort_order')
                ->orderByDesc('id')
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

        if ($page->page_type === 'system' && $page->system_key === 'association_plans') {
            $template = $this->resolveTemplateByKey($siteSettings->association_plans_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.association-plans.index';

            $items = AssociationPlan::query()
                ->orderByDesc('id')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items'));
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

        if ($page->page_type === 'system' && $page->system_key === 'committees') {
            $template = $this->resolveTemplateByKey($siteSettings->committees_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.system.committees';

            $committees = Committee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $committees;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'committees', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'program_projects') {
            $template = $this->resolveTemplateByKey($siteSettings->program_projects_index_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.program-projects.index';

            $items = ProgramProject::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items'));
        }

        return view('themes.default.page', compact('association', 'siteSettings', 'page'));
    }
}
