<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends TenantModel
{
    protected $fillable = [
        'site_name',
        'association_name',
        'site_description',
        'about_text',
        'vision',
        'mission',
        'intro_video_url',

        'logo',
        'logo_media_id',
        'favicon_media_id',

        'phone',
        'email',
        'address',
        'license_number',

        'facebook',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'snapchat_url',
        'whatsapp_url',

        'beneficiary_portal_url',
        'beneficiary_login_url',
        'beneficiary_register_url',
        'store_url',

        'home_template_key',
        'policies_template_key',
        'regulations_template_key',
        'financial_reports_template_key',
        'news_index_template_key',
        'news_show_template_key',
        'committees_template_key',
        'board_members_template_key',
        'general_assembly_members_template_key',
        'employees_template_key',
        'program_projects_index_template_key',
        'program_projects_show_template_key',
        'services_template_key',
        'page_template_key',
        'inner_pages_header_template_key',
        'inner_pages_footer_template_key',
        'licenses_template_key',

        'primary_color',
        'secondary_color',
        'button_color',
    ];

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'logo_media_id');
    }

    public function faviconMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'favicon_media_id');
    }
}
