<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;

class Statistic extends TenantModel
{
    protected $fillable = [
        'title',
        'value',
        'icon',
        'sort_order',
    ];
}
