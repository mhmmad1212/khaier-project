<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssociationDomainCheck extends Model
{
    protected $fillable = [
        'association_id',
        'expected_host',
        'resolved_value',
        'dns_status',
        'http_status',
        'ssl_status',
        'is_pointing_correctly',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'is_pointing_correctly' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }
}
