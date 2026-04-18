<?php

namespace App\Http\Controllers;

use App\Models\AssociationPlan;
use App\Models\BeneficiaryService;
use App\Models\ExecutiveDirectorProfile;
use App\Models\Feedback;
use App\Models\BankAccount;
use App\Models\BoardMember;
use App\Models\Committee;
use App\Models\Disclosure;
use App\Models\Employee;
use App\Models\FinancialReport;
use App\Models\GeneralAssemblyMember;
use App\Models\License;
use App\Models\MeetingMinute;
use App\Models\News;
use App\Models\Page;
use App\Models\PageTemplate;
use App\Models\Policy;
use App\Models\ProgramProject;
use App\Models\Regulation;
use App\Models\Service;
use App\Models\VolunteerOpportunity;
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

        if ($page->page_type === 'system' && $page->system_key === 'feedback') {
            $template = $this->resolveTemplateByKey($siteSettings->feedback_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.feedback.index';

            $items = Feedback::query()
                ->where('is_active', true)
                ->with('fileMedia')
                ->orderByDesc('created_at')
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

            $licenses = $items;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'items', 'licenses'));
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

        if ($page->page_type === 'system' && $page->system_key === 'executive_director') {
            $template = $this->resolveTemplateByKey($siteSettings->executive_director_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.executive_director.index';

            $executiveDirector = ExecutiveDirectorProfile::query()->with('imageMedia')->first();

            $item = $executiveDirector;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'executiveDirector', 'item'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'beneficiary_services') {
            $template = $this->resolveTemplateByKey($siteSettings->beneficiary_services_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.beneficiary-services.index';

            $beneficiaryServices = BeneficiaryService::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $beneficiaryServices;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'beneficiaryServices', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'bank_accounts') {
            $template = $this->resolveTemplateByKey($siteSettings->bank_accounts_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.bank-accounts.index';

            $bankAccounts = BankAccount::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();

            $items = $bankAccounts;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'bankAccounts', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'volunteer_opportunities_index') {
            $template = $this->resolveTemplateByKey($siteSettings->volunteer_opportunities_index_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.volunteer-opportunities.index';

            $volunteerOpportunities = VolunteerOpportunity::query()
                ->with('imageMedia')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->get();

            $items = $volunteerOpportunities;

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'volunteerOpportunities', 'items'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'meeting_minutes_board') {
            $template = $this->resolveTemplateByKey($siteSettings->meeting_minutes_board_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.meeting-minutes.index';

            $meetingMinutes = MeetingMinute::query()
                ->with('fileMedia')
                ->where('category', 'board')
                ->orderByDesc('meeting_date')
                ->orderByDesc('id')
                ->get();

            $items = $meetingMinutes;
            $categoryLabel = 'محاضر اجتماعات مجلس الإدارة';

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'meetingMinutes', 'items', 'categoryLabel'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'meeting_minutes_general') {
            $template = $this->resolveTemplateByKey($siteSettings->meeting_minutes_general_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.meeting-minutes.index';

            $meetingMinutes = MeetingMinute::query()
                ->with('fileMedia')
                ->where('category', 'general')
                ->orderByDesc('meeting_date')
                ->orderByDesc('id')
                ->get();

            $items = $meetingMinutes;
            $categoryLabel = 'محاضر اجتماعات الجمعية العمومية';

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'meetingMinutes', 'items', 'categoryLabel'));
        }

        if ($page->page_type === 'system' && $page->system_key === 'meeting_minutes_committee') {
            $template = $this->resolveTemplateByKey($siteSettings->meeting_minutes_committee_template_key ?? null);
            $viewPath = ($template && ! empty($template->view_path))
                ? $template->view_path
                : 'themes.default.meeting-minutes.index';

            $meetingMinutes = MeetingMinute::query()
                ->with('fileMedia')
                ->where('category', 'committee')
                ->orderByDesc('meeting_date')
                ->orderByDesc('id')
                ->get();

            $items = $meetingMinutes;
            $categoryLabel = 'محاضر اجتماعات اللجان';

            return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'meetingMinutes', 'items', 'categoryLabel'));
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
                ->with('photoMedia')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get()
                ->map(function ($employee) {
                    $employee->image_url =
                        $employee->photoMedia?->url
                        ?: (
                            ! empty($employee->photo)
                                ? (
                                    \Illuminate\Support\Str::startsWith($employee->photo, ['http://', 'https://'])
                                        ? $employee->photo
                                        : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($employee->photo, '/'))
                                )
                                : null
                        );

                    return $employee;
                });

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

        $renderedRawHtml = null;

        if (! empty($page->raw_html)) {
            $renderedRawHtml = \Illuminate\Support\Facades\Blade::render($page->raw_html, [
                'association' => $association,
                'siteSettings' => $siteSettings,
                'page' => $page,
            ]);
        }

        return view('themes.default.page', compact('association', 'siteSettings', 'page', 'renderedRawHtml'));
    }
    public function newsShow(string $slug)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $siteSettings = $this->siteSettings();

        $news = News::query()
            ->with(['imageMedia', 'categories'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'published')
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->news_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.news.show';

        $item = $news;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'news', 'item'));
    }


    public function programProjectShow(int|string $id)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $siteSettings = $this->siteSettings();

        $project = ProgramProject::query()
            ->with(['coverMedia', 'reportMedia', 'galleryImages', 'attachments'])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->program_projects_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.program-projects.show';

        $item = $project;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'project', 'item'));
    }


    public function pageProgramProjectShow(string $slug, int|string $id)
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

        $project = ProgramProject::query()
            ->with(['coverMedia', 'reportMedia', 'galleryImages', 'attachments'])
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->program_projects_show_template_key ?? null);

        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.program-projects.show';

        $item = $project;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'project', 'item'));
    }




    public function volunteerOpportunityShow(string $slug)
    {
        $association = $this->currentAssociation();

        if (! $association) {
            abort(404);
        }

        $siteSettings = $this->siteSettings();

        $volunteerOpportunity = VolunteerOpportunity::query()
            ->with('imageMedia')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->volunteer_opportunities_show_template_key ?? null);
        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.volunteer-opportunities.show';

        $item = $volunteerOpportunity;
        $page = null;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'volunteerOpportunity', 'item'));
    }

    public function pageVolunteerOpportunityShow(string $slug, string $volunteerSlug)
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

        $volunteerOpportunity = VolunteerOpportunity::query()
            ->with('imageMedia')
            ->where('slug', $volunteerSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $template = $this->resolveTemplateByKey($siteSettings->volunteer_opportunities_show_template_key ?? null);
        $viewPath = ($template && ! empty($template->view_path))
            ? $template->view_path
            : 'themes.default.volunteer-opportunities.show';

        $item = $volunteerOpportunity;

        return view($viewPath, compact('association', 'siteSettings', 'template', 'page', 'volunteerOpportunity', 'item'));
    }

}