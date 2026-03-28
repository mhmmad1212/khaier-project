<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends TenantModel
{
    public const SECTION_NEWS = 1;
    public const SECTION_POLICIES = 2;
    public const SECTION_PROGRAM_PROJECTS = 3;
    public const SECTION_REGULATIONS = 4;
    public const SECTION_FINANCIAL_REPORTS = 5;
    public const SECTION_PAGES = 6;
    public const SECTION_SERVICES = 7;
    public const SECTION_EMPLOYEES = 8;
    public const SECTION_BOARD_MEMBERS = 9;
    public const SECTION_GENERAL_ASSEMBLY = 10;
    public const SECTION_COMMITTEES = 11;

    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'media_item_id',
        'section_code',
        'collection',
        'title',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'media_item_id');
    }
}
