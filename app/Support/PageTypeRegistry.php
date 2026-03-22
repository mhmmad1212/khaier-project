<?php

namespace App\Support;

class PageTypeRegistry
{
    public static function all(): array
    {
        return [
            'home' => 'الصفحة الرئيسية',
            'policies' => 'السياسات',
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'news_index' => 'قائمة الأخبار',
            'news_show' => 'تفاصيل الخبر',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'services' => 'الخدمات',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
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
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'news_index' => 'قائمة الأخبار',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'services' => 'الخدمات',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
        ];
    }

    public static function systemPageTypes(): array
    {
        return [
            'policies' => 'السياسات',
            'regulations' => 'اللوائح',
            'financial_reports' => 'القوائم المالية',
            'news_index' => 'قائمة الأخبار',
            'news_show' => 'تفاصيل الخبر',
            'program_projects_index' => 'قائمة المشاريع',
            'program_projects_show' => 'تفاصيل المشروع',
            'services' => 'الخدمات',
            'employees' => 'الموظفون',
            'board_members' => 'مجلس الإدارة',
            'general_assembly_members' => 'الجمعية العمومية',
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
