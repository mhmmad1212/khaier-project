<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class BeneficiaryService extends TenantModel
{
    protected $fillable = [
        'name',
        'icon',
        'conditions',
        'guide_url',
        'application_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
