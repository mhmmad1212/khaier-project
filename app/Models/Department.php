<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends TenantModel
{
    protected $fillable = [
        'association_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }
}
