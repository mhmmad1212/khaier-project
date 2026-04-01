<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteFormSubmission extends TenantModel
{
    protected $fillable = [
        'site_form_id',
        'reference_number',
        'phone',
        'status',
        'allow_customer_reply',
        'data',
        'ip',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
        'allow_customer_reply' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(SiteForm::class, 'site_form_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SiteFormSubmissionMessage::class)->oldest('id');
    }
}
