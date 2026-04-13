<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Tenant\TenantModel;

class GeneralAssemblyMember extends TenantModel
{

    protected $connection = 'tenant';

    protected $guarded = [];


    public function photoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'photo_media_id');
    }

}
