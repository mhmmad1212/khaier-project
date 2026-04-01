<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class PageVisit extends TenantModel
{
    protected $fillable = [
        'url',
        'type',
        'entity_id',
        'ip',
        'user_agent',
    ];
}
