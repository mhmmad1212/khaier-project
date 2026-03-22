<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramProjectImage extends TenantModel
{
    protected $fillable = [
        'program_project_id',
        'media_item_id',
        'sort_order',
    ];

    public function programProject(): BelongsTo
    {
        return $this->belongsTo(ProgramProject::class);
    }

    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'media_item_id');
    }
}
