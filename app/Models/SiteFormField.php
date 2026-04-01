<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteFormField extends TenantModel
{
    protected $fillable = [
        'site_form_id',
        'label',
        'name',
        'type',
        'placeholder',
        'options',
        'is_required',
        'width',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(SiteForm::class, 'site_form_id');
    }
}
