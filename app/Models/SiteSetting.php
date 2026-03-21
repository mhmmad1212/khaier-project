<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'site_name',
        'site_description',
        'logo',
        'logo_media_id',
        'favicon_media_id',
        'phone',
        'email',
        'address',
        'license_number',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'beneficiary_portal_url',
        'beneficiary_login_url',
        'beneficiary_register_url',
        'home_template_key',
        'policies_template_key',
        'regulations_template_key',
        'financial_reports_template_key',
        'news_index_template_key',
        'news_show_template_key',
        'page_template_key',
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
