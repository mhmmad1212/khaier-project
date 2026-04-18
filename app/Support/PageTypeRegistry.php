<?php

namespace App\Support;

class PageTypeRegistry
{
    public static function all(): array
    {
        return [
            'home' => 'الصفحة الرئيسية',
            'policies' => 'السياسات',
            'feedback' => 'التغذية الراجعة',
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'disclosure' => 'الإفصاح',
            'association_plans' => 'خطط الجمعية',
            'news_index' => 'قائمة الأخبار',
            'news_show' => 'تفاصيل الخبر',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'volunteer_opportunities_index' => 'قائمة فرص التطوع',
            'volunteer_opportunities_show' => 'تفاصيل فرصة التطوع',
            'licenses' => 'صفحة تراخيص الجمعية',
            'inner_footer' => 'فوتر الصفحات الداخلية',
            'inner_header' => 'هيدر الصفحات الداخلية',
            'services' => 'الخدمات',
            'beneficiary_services' => 'خدمات المستفيدين',
            'bank_accounts' => 'الحسابات البنكية',
            'executive_director' => 'المدير التنفيذي',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
            'meeting_minutes_board' => 'محاضر اجتماعات مجلس الإدارة',
            'meeting_minutes_general' => 'محاضر اجتماعات الجمعية العمومية',
            'meeting_minutes_committee' => 'محاضر اجتماعات اللجان',
            'committees' => 'اللجان',
            'page' => 'الصفحات الداخلية',
        ];
    }

    public static function templateTypes(): array
    {
        return static::all();
    }

    public static function siteSettingTemplateTypes(): array
    {
        return [
            'home' => 'الصفحة الرئيسية',
            'policies' => 'السياسات',
            'feedback' => 'التغذية الراجعة',
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'news_index' => 'قائمة الأخبار',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'volunteer_opportunities_index' => 'قائمة فرص التطوع',
            'volunteer_opportunities_show' => 'تفاصيل فرصة التطوع',
            'licenses' => 'صفحة تراخيص الجمعية',
            'services' => 'الخدمات',
            'bank_accounts' => 'الحسابات البنكية',
            'beneficiary_services' => 'خدمات المستفيدين',
            'association_plans' => 'خطط الجمعية',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
            'meeting_minutes_board' => 'محاضر اجتماعات مجلس الإدارة',
            'meeting_minutes_general' => 'محاضر اجتماعات الجمعية العمومية',
            'meeting_minutes_committee' => 'محاضر اجتماعات اللجان',
        ];
    }

    public static function systemPageTypes(): array
    {
        return [
            'policies' => 'السياسات',
            'feedback' => 'التغذية الراجعة',
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'disclosure' => 'الإفصاح',
            'association_plans' => 'خطط الجمعية',
            'news_index' => 'قائمة الأخبار',
            'news_show' => 'تفاصيل الخبر',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'volunteer_opportunities_index' => 'فرص التطوع',
            'licenses' => 'تراخيص الجمعية',
            'services' => 'الخدمات',
            'bank_accounts' => 'الحسابات البنكية',
            'beneficiary_services' => 'خدمات المستفيدين',
            'executive_director' => 'المدير التنفيذي',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
            'meeting_minutes_board' => 'محاضر اجتماعات مجلس الإدارة',
            'meeting_minutes_general' => 'محاضر اجتماعات الجمعية العمومية',
            'meeting_minutes_committee' => 'محاضر اجتماعات اللجان',
            'page' => 'الصفحات الداخلية',
            'committees' => 'اللجان',
        ];
    }

    public static function label(string $key): string
    {
        return static::all()[$key]
            ?? static::systemPageTypes()[$key]
            ?? $key;
    }
}
