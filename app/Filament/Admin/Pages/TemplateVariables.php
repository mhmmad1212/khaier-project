<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateVariables extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket-square';
    protected static ?string $navigationLabel = 'متغيرات القوالب';
    protected static ?string $title = 'متغيرات القوالب';
    protected static ?string $navigationGroup = 'إعدادات النظام';
    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.admin.pages.template-variables';

    public array $variables = [];

    public function mount(): void
    {
        $this->variables = [
            ['group' => 'متغيرات الموقع', 'label' => 'اسم الموقع', 'code' => '{{ site.site_name }}', 'description' => 'يعرض اسم الموقع من الإعدادات العامة'],
            ['group' => 'متغيرات الموقع', 'label' => 'اسم الجمعية', 'code' => '{{ site.association_name }}', 'description' => 'يعرض اسم الجمعية الحالي'],
            ['group' => 'متغيرات الموقع', 'label' => 'وصف الموقع', 'code' => '{{ site.site_description }}', 'description' => 'يعرض وصف الموقع'],
            ['group' => 'متغيرات الموقع', 'label' => 'نبذة الجمعية', 'code' => '{{ site.about_text }}', 'description' => 'يعرض نبذة الجمعية'],
            ['group' => 'متغيرات الموقع', 'label' => 'الرؤية', 'code' => '{{ site.vision }}', 'description' => 'يعرض الرؤية'],
            ['group' => 'متغيرات الموقع', 'label' => 'الرسالة', 'code' => '{{ site.mission }}', 'description' => 'يعرض الرسالة'],
            ['group' => 'متغيرات الموقع', 'label' => 'رقم الجوال', 'code' => '{{ site.phone }}', 'description' => 'رقم التواصل'],
            ['group' => 'متغيرات الموقع', 'label' => 'البريد الإلكتروني', 'code' => '{{ site.email }}', 'description' => 'بريد الجمعية'],
            ['group' => 'متغيرات الموقع', 'label' => 'العنوان', 'code' => '{{ site.address }}', 'description' => 'عنوان الجمعية'],
            ['group' => 'متغيرات الموقع', 'label' => 'رقم الترخيص', 'code' => '{{ site.license_number }}', 'description' => 'رقم الترخيص'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط بوابة المستفيد', 'code' => '{{ site.beneficiary_portal_url }}', 'description' => 'رابط بوابة المستفيدين'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط دخول المستفيدين', 'code' => '{{ site.beneficiary_login_url }}', 'description' => 'رابط دخول المستفيدين'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط تسجيل مستفيد جديد', 'code' => '{{ site.beneficiary_register_url }}', 'description' => 'رابط تسجيل مستفيد جديد'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط المتجر', 'code' => '{{ site.store_url }}', 'description' => 'رابط المتجر'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط الفيديو التعريفي', 'code' => '{{ site.intro_video_url }}', 'description' => 'رابط الفيديو التعريفي'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط فيسبوك', 'code' => '{{ site.facebook }}', 'description' => 'رابط فيسبوك'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط تويتر', 'code' => '{{ site.twitter_url }}', 'description' => 'رابط تويتر'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط إنستغرام', 'code' => '{{ site.instagram_url }}', 'description' => 'رابط إنستغرام'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط يوتيوب', 'code' => '{{ site.youtube_url }}', 'description' => 'رابط يوتيوب'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط تيك توك', 'code' => '{{ site.tiktok_url }}', 'description' => 'رابط تيك توك'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط سناب شات', 'code' => '{{ site.snapchat_url }}', 'description' => 'رابط سناب شات'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط واتساب', 'code' => '{{ site.whatsapp_url }}', 'description' => 'رابط واتساب'],
            ['group' => 'متغيرات الموقع', 'label' => 'اللون الأساسي', 'code' => '{{ site.primary_color }}', 'description' => 'اللون الأساسي للموقع'],
            ['group' => 'متغيرات الموقع', 'label' => 'اللون الثانوي', 'code' => '{{ site.secondary_color }}', 'description' => 'اللون الثانوي للموقع'],
            ['group' => 'متغيرات الموقع', 'label' => 'لون الأزرار', 'code' => '{{ site.button_color }}', 'description' => 'لون الأزرار'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط الشعار', 'code' => '{{ site.logo_url }}', 'description' => 'الرابط المباشر لصورة الشعار'],
            ['group' => 'متغيرات الموقع', 'label' => 'رابط الأيقونة', 'code' => '{{ site.favicon_url }}', 'description' => 'الرابط المباشر لأيقونة الموقع'],

            ['group' => 'متغيرات الصفحة', 'label' => 'عنوان الصفحة', 'code' => '{{ page.title }}', 'description' => 'عنوان الصفحة الحالية'],
            ['group' => 'متغيرات الصفحة', 'label' => 'رابط الصفحة', 'code' => '{{ page.url }}', 'description' => 'الرابط الكامل للصفحة الحالية'],
            ['group' => 'متغيرات الصفحة', 'label' => 'المسار المختصر', 'code' => '{{ page.slug }}', 'description' => 'slug الصفحة الحالية'],
            ['group' => 'متغيرات الصفحة', 'label' => 'محتوى الصفحة', 'code' => '{{ page.content }}', 'description' => 'محتوى الصفحة الحالية'],
            ['group' => 'متغيرات الصفحة', 'label' => 'رابط الصورة البارزة', 'code' => '{{ page.featured_image_url }}', 'description' => 'الرابط المباشر للصورة البارزة للصفحة إن وجدت'],

            ['group' => 'متغيرات السياسة', 'label' => 'اسم السياسة', 'code' => '{{ policy.title }}', 'description' => 'عنوان السياسة الحالية'],
            ['group' => 'متغيرات السياسة', 'label' => 'وصف السياسة', 'code' => '{{ policy.description }}', 'description' => 'وصف السياسة الحالية'],
            ['group' => 'متغيرات السياسة', 'label' => 'رابط السياسة', 'code' => '{{ policy.url }}', 'description' => 'رابط صفحة أو عنصر السياسة الحالية'],
            ['group' => 'متغيرات السياسة', 'label' => 'رابط ملف السياسة', 'code' => '{{ policy.file_url }}', 'description' => 'رابط ملف السياسة إن وجد'],
            ['group' => 'متغيرات السياسة', 'label' => 'تاريخ نشر السياسة', 'code' => '{{ policy.published_at }}', 'description' => 'تاريخ نشر السياسة الحالية'],

            ['group' => 'متغيرات اللائحة', 'label' => 'اسم اللائحة', 'code' => '{{ regulation.title }}', 'description' => 'عنوان اللائحة الحالية'],
            ['group' => 'متغيرات اللائحة', 'label' => 'وصف اللائحة', 'code' => '{{ regulation.description }}', 'description' => 'وصف اللائحة الحالية'],
            ['group' => 'متغيرات اللائحة', 'label' => 'رابط اللائحة', 'code' => '{{ regulation.url }}', 'description' => 'رابط صفحة أو عنصر اللائحة الحالية'],
            ['group' => 'متغيرات اللائحة', 'label' => 'رابط ملف اللائحة', 'code' => '{{ regulation.file_url }}', 'description' => 'رابط ملف اللائحة إن وجد'],
            ['group' => 'متغيرات اللائحة', 'label' => 'تاريخ نشر اللائحة', 'code' => '{{ regulation.published_at }}', 'description' => 'تاريخ نشر اللائحة الحالية'],

            ['group' => 'متغيرات الإفصاح', 'label' => 'اسم الإفصاح', 'code' => '{{ disclosure.title }}', 'description' => 'عنوان عنصر الإفصاح الحالي'],
            ['group' => 'متغيرات الإفصاح', 'label' => 'وصف الإفصاح', 'code' => '{{ disclosure.description }}', 'description' => 'وصف عنصر الإفصاح الحالي'],
            ['group' => 'متغيرات الإفصاح', 'label' => 'رابط الإفصاح', 'code' => '{{ disclosure.url }}', 'description' => 'رابط صفحة أو عنصر الإفصاح الحالي'],
            ['group' => 'متغيرات الإفصاح', 'label' => 'رابط ملف الإفصاح', 'code' => '{{ disclosure.file_url }}', 'description' => 'رابط الملف إن وجد'],

            ['group' => 'متغيرات القوائم المالية', 'label' => 'عنوان القائمة المالية', 'code' => '{{ financial_report.title }}', 'description' => 'عنوان القائمة المالية الحالية'],
            ['group' => 'متغيرات القوائم المالية', 'label' => 'سنة التقرير', 'code' => '{{ financial_report.year }}', 'description' => 'سنة القائمة المالية'],
            ['group' => 'متغيرات القوائم المالية', 'label' => 'رابط التقرير', 'code' => '{{ financial_report.url }}', 'description' => 'رابط القائمة المالية الحالية'],
            ['group' => 'متغيرات القوائم المالية', 'label' => 'رابط ملف التقرير', 'code' => '{{ financial_report.file_url }}', 'description' => 'رابط ملف التقرير المالي'],

            ['group' => 'متغيرات الترخيص', 'label' => 'اسم الترخيص', 'code' => '{{ license.title }}', 'description' => 'عنوان الترخيص الحالي'],
            ['group' => 'متغيرات الترخيص', 'label' => 'رابط الترخيص', 'code' => '{{ license.url }}', 'description' => 'رابط الترخيص الحالي'],
            ['group' => 'متغيرات الترخيص', 'label' => 'رابط ملف الترخيص', 'code' => '{{ license.file_url }}', 'description' => 'رابط الملف'],
            ['group' => 'متغيرات الترخيص', 'label' => 'تاريخ الإصدار', 'code' => '{{ license.issue_date }}', 'description' => 'تاريخ إصدار الترخيص'],
            ['group' => 'متغيرات الترخيص', 'label' => 'اسم الجهة المصدرة', 'code' => '{{ license.issuer }}', 'description' => 'الجهة المصدرة للترخيص'],

            ['group' => 'متغيرات خطة الجمعية', 'label' => 'اسم الخطة', 'code' => '{{ association_plan.title }}', 'description' => 'عنوان خطة الجمعية الحالية'],
            ['group' => 'متغيرات خطة الجمعية', 'label' => 'رابط ملف الخطة', 'code' => '{{ association_plan.file_url }}', 'description' => 'الرابط المباشر لملف خطة الجمعية إن وجد'],

            ['group' => 'متغيرات الخدمة', 'label' => 'اسم الخدمة', 'code' => '{{ service.title }}', 'description' => 'عنوان الخدمة الحالية'],
            ['group' => 'متغيرات الخدمة', 'label' => 'وصف الخدمة', 'code' => '{{ service.description }}', 'description' => 'وصف الخدمة الحالية'],
            ['group' => 'متغيرات الخدمة', 'label' => 'رابط الخدمة', 'code' => '{{ service.url }}', 'description' => 'رابط صفحة الخدمة الحالية'],
            ['group' => 'متغيرات الخدمة', 'label' => 'الأيقونة', 'code' => '{{ service.icon }}', 'description' => 'أيقونة الخدمة الحالية'],

            ['group' => 'متغيرات الحساب البنكي', 'label' => 'اسم الحساب', 'code' => '{{ bank_account.name }}', 'description' => 'اسم الحساب البنكي الحالي'],
            ['group' => 'متغيرات الحساب البنكي', 'label' => 'اسم البنك', 'code' => '{{ bank_account.bank_name }}', 'description' => 'اسم البنك الحالي'],
            ['group' => 'متغيرات الحساب البنكي', 'label' => 'رقم الحساب', 'code' => '{{ bank_account.account_number }}', 'description' => 'رقم الحساب الحالي'],
            ['group' => 'متغيرات الحساب البنكي', 'label' => 'الشعار', 'code' => '{{ bank_account.bank_logo_url }}', 'description' => 'رابط شعار البنك الحالي'],

            ['group' => 'متغيرات خدمة المستفيد', 'label' => 'اسم الخدمة', 'code' => '{{ beneficiary_service.name }}', 'description' => 'اسم خدمة المستفيد الحالية'],
            ['group' => 'متغيرات خدمة المستفيد', 'label' => 'الأيقونة', 'code' => '{{ beneficiary_service.icon }}', 'description' => 'أيقونة خدمة المستفيد الحالية'],
            ['group' => 'متغيرات خدمة المستفيد', 'label' => 'شروط الخدمة', 'code' => '{{ beneficiary_service.conditions }}', 'description' => 'شروط خدمة المستفيد الحالية'],
            ['group' => 'متغيرات خدمة المستفيد', 'label' => 'رابط الشرح', 'code' => '{{ beneficiary_service.guide_url }}', 'description' => 'رابط شرح التقديم الحالي'],
            ['group' => 'متغيرات خدمة المستفيد', 'label' => 'رابط التقديم', 'code' => '{{ beneficiary_service.application_url }}', 'description' => 'رابط تقديم الطلب الحالي'],

            ['group' => 'متغيرات الخبر', 'label' => 'عنوان الخبر', 'code' => '{{ news.title }}', 'description' => 'عنوان الخبر الحالي'],
            ['group' => 'متغيرات الخبر', 'label' => 'ملخص الخبر', 'code' => '{{ news.excerpt }}', 'description' => 'ملخص الخبر الحالي'],
            ['group' => 'متغيرات الخبر', 'label' => 'محتوى الخبر', 'code' => '{{ news.content }}', 'description' => 'محتوى الخبر الحالي'],
            ['group' => 'متغيرات الخبر', 'label' => 'رابط الخبر', 'code' => '{{ news.url }}', 'description' => 'رابط صفحة الخبر الحالية'],
            ['group' => 'متغيرات الخبر', 'label' => 'صورة الخبر', 'code' => '{{ news.image_url }}', 'description' => 'رابط صورة الخبر الحالية'],
            ['group' => 'متغيرات الخبر', 'label' => 'تاريخ نشر الخبر', 'code' => '{{ news.published_at }}', 'description' => 'تاريخ نشر الخبر الحالي'],

            ['group' => 'متغيرات المشروع', 'label' => 'اسم المشروع', 'code' => '{{ project.title }}', 'description' => 'عنوان المشروع الحالي'],
            ['group' => 'متغيرات المشروع', 'label' => 'وصف المشروع', 'code' => '{{ project.description }}', 'description' => 'وصف المشروع الحالي'],
            ['group' => 'متغيرات المشروع', 'label' => 'رابط المشروع', 'code' => '{{ project.url }}', 'description' => 'رابط صفحة المشروع الحالية'],
            ['group' => 'متغيرات المشروع', 'label' => 'صورة المشروع', 'code' => '{{ project.image_url }}', 'description' => 'الرابط المباشر لصورة المشروع الحالية'],
            ['group' => 'متغيرات المشروع', 'label' => 'رابط ملف تقرير المشروع', 'code' => '{{ project.report_file_url }}', 'description' => 'الرابط المباشر لملف تقرير المشروع إن وجد'],

            ['group' => 'متغيرات فرصة التطوع', 'label' => 'اسم الفرصة', 'code' => '{{ volunteer_opportunity.title }}', 'description' => 'اسم فرصة التطوع الحالية'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'الوصف', 'code' => '{{ volunteer_opportunity.description }}', 'description' => 'وصف فرصة التطوع الحالية'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'نوع الفرصة', 'code' => '{{ volunteer_opportunity.opportunity_type }}', 'description' => 'نوع الفرصة الحالية'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'بداية التطوع', 'code' => '{{ volunteer_opportunity.start_date }}', 'description' => 'تاريخ بداية التطوع'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'نهاية الفرصة', 'code' => '{{ volunteer_opportunity.end_date }}', 'description' => 'تاريخ نهاية الفرصة'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'عدد الساعات', 'code' => '{{ volunteer_opportunity.hours_count }}', 'description' => 'عدد ساعات الفرصة'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'رابط منصة تطوع', 'code' => '{{ volunteer_opportunity.platform_url }}', 'description' => 'رابط منصة تطوع للفرصة الحالية'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'الصورة', 'code' => '{{ volunteer_opportunity.image_url }}', 'description' => 'رابط صورة فرصة التطوع الحالية'],
            ['group' => 'متغيرات فرصة التطوع', 'label' => 'الرابط', 'code' => '{{ volunteer_opportunity.url }}', 'description' => 'رابط صفحة تفاصيل الفرصة الحالية'],

            ['group' => 'متغيرات عضو مجلس الإدارة', 'label' => 'اسم العضو', 'code' => '{{ board_member.name }}', 'description' => 'اسم عضو مجلس الإدارة الحالي'],
            ['group' => 'متغيرات عضو مجلس الإدارة', 'label' => 'المنصب', 'code' => '{{ board_member.position }}', 'description' => 'منصب عضو مجلس الإدارة'],
            ['group' => 'متغيرات عضو مجلس الإدارة', 'label' => 'الصورة', 'code' => '{{ board_member.image_url }}', 'description' => 'رابط صورة عضو مجلس الإدارة'],
            ['group' => 'متغيرات عضو مجلس الإدارة', 'label' => 'النبذة', 'code' => '{{ board_member.bio }}', 'description' => 'نبذة عن عضو مجلس الإدارة الحالية'],

            ['group' => 'متغيرات اللجنة', 'label' => 'اسم اللجنة', 'code' => '{{ committee.name }}', 'description' => 'اسم اللجنة الحالية'],
            ['group' => 'متغيرات اللجنة', 'label' => 'وصف اللجنة', 'code' => '{{ committee.description }}', 'description' => 'وصف اللجنة الحالية'],
            ['group' => 'متغيرات اللجنة', 'label' => 'رابط اللجنة', 'code' => '{{ committee.url }}', 'description' => 'رابط صفحة اللجنة الحالية'],
            ['group' => 'متغيرات اللجنة', 'label' => 'رابط مرفق اللجنة', 'code' => '{{ committee.attachment_url }}', 'description' => 'الرابط المباشر لمرفق اللجنة إن وجد'],

            ['group' => 'متغيرات الموظف', 'label' => 'اسم الموظف', 'code' => '{{ employee.name }}', 'description' => 'اسم الموظف الحالي'],
            ['group' => 'متغيرات الموظف', 'label' => 'المسمى الوظيفي', 'code' => '{{ employee.position }}', 'description' => 'المسمى الوظيفي الحالي للموظف'],
            ['group' => 'متغيرات الموظف', 'label' => 'البريد الإلكتروني', 'code' => '{{ employee.email }}', 'description' => 'بريد الموظف'],
            ['group' => 'متغيرات الموظف', 'label' => 'رقم الجوال', 'code' => '{{ employee.phone }}', 'description' => 'رقم الموظف'],
            ['group' => 'متغيرات الموظف', 'label' => 'الصورة', 'code' => '{{ employee.image_url }}', 'description' => 'رابط صورة الموظف'],

            ['group' => 'متغيرات المدير التنفيذي', 'label' => 'اسم المدير التنفيذي', 'code' => '{{ executive_director.name }}', 'description' => 'اسم المدير التنفيذي الحالي'],
            ['group' => 'متغيرات المدير التنفيذي', 'label' => 'رقم التواصل', 'code' => '{{ executive_director.phone }}', 'description' => 'رقم التواصل للمدير التنفيذي'],
            ['group' => 'متغيرات المدير التنفيذي', 'label' => 'البريد الإلكتروني', 'code' => '{{ executive_director.email }}', 'description' => 'البريد الإلكتروني للمدير التنفيذي'],
            ['group' => 'متغيرات المدير التنفيذي', 'label' => 'النبذة', 'code' => '{{ executive_director.bio }}', 'description' => 'نبذة المدير التنفيذي الحالية'],
            ['group' => 'متغيرات المدير التنفيذي', 'label' => 'الصورة', 'code' => '{{ executive_director.image_url }}', 'description' => 'رابط صورة المدير التنفيذي الحالية'],

            ['group' => 'متغيرات الجمعية العمومية', 'label' => 'اسم العضو', 'code' => '{{ assembly_member.name }}', 'description' => 'اسم عضو الجمعية العمومية'],
            ['group' => 'متغيرات الجمعية العمومية', 'label' => 'الصفة', 'code' => '{{ assembly_member.position }}', 'description' => 'صفة أو منصب العضو'],
            ['group' => 'متغيرات الجمعية العمومية', 'label' => 'الصورة', 'code' => '{{ assembly_member.image_url }}', 'description' => 'رابط صورة العضو'],

            ['group' => 'متغيرات النموذج', 'label' => 'اسم النموذج', 'code' => '{{ form.name }}', 'description' => 'اسم النموذج الحالي'],
            ['group' => 'متغيرات النموذج', 'label' => 'الرابط المختصر', 'code' => '{{ form.slug }}', 'description' => 'slug النموذج الحالي'],
            ['group' => 'متغيرات النموذج', 'label' => 'رابط النموذج', 'code' => '{{ form.url }}', 'description' => 'الرابط الكامل لعرض النموذج'],
            ['group' => 'متغيرات النموذج', 'label' => 'رابط الإرسال', 'code' => '{{ form.submit_url }}', 'description' => 'الرابط المستخدم لإرسال النموذج'],
            ['group' => 'متغيرات النموذج', 'label' => 'رابط التتبع', 'code' => '{{ form.track_url }}', 'description' => 'الرابط المستخدم لتتبع الطلب'],
            ['group' => 'متغيرات النموذج', 'label' => 'وصف النموذج', 'code' => '{{ form.description }}', 'description' => 'وصف النموذج الحالي'],
            ['group' => 'متغيرات النموذج', 'label' => 'نص زر الإرسال', 'code' => '{{ form.submit_button_text }}', 'description' => 'نص زر الإرسال'],

            ['group' => 'متغيرات حقل النموذج', 'label' => 'اسم الحقل', 'code' => '{{ field.label }}', 'description' => 'عنوان الحقل الحالي داخل النموذج'],
            ['group' => 'متغيرات حقل النموذج', 'label' => 'الاسم البرمجي', 'code' => '{{ field.name }}', 'description' => 'الاسم البرمجي للحقل'],
            ['group' => 'متغيرات حقل النموذج', 'label' => 'نوع الحقل', 'code' => '{{ field.type }}', 'description' => 'نوع الحقل الحالي'],
            ['group' => 'متغيرات حقل النموذج', 'label' => 'النص المساعد', 'code' => '{{ field.placeholder }}', 'description' => 'placeholder الحقل الحالي'],

            ['group' => 'متغيرات الجمعية', 'label' => 'معرف الجمعية', 'code' => '{{ association.id }}', 'description' => 'معرف الجمعية الحالية'],
            ['group' => 'متغيرات الجمعية', 'label' => 'اسم النطاق', 'code' => '{{ association.domain }}', 'description' => 'اسم النطاق الحالي للجمعية'],
            ['group' => 'متغيرات الجمعية', 'label' => 'اسم الجمعية', 'code' => '{{ association.name }}', 'description' => 'اسم الجمعية من السجل المركزي'],
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('تصدير Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportCsv()),
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'template-variables-' . now()->format('Y-m-d-H-i') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['القسم', 'الاسم', 'المتغير', 'الاستخدام']);

            foreach ($this->variables as $item) {
                fputcsv($handle, [
                    $item['group'] ?? '',
                    $item['label'] ?? '',
                    $item['code'] ?? '',
                    $item['description'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
