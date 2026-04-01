<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteFormSubmissionMessage extends TenantModel
{
    protected $fillable = [
        'site_form_submission_id',
        'message',
        'type',
        'is_visible_to_customer',
        'created_by_type',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_visible_to_customer' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(SiteFormSubmission::class, 'site_form_submission_id');
    }
}
