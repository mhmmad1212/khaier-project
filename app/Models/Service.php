<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class Service extends TenantModel
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'url',
        'sort_order',
        'is_active',
    ];
}
