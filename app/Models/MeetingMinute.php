<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinute extends Model {
    // توجيه الموديل للاتصال بقاعدة بيانات الجمعية بشكل إجباري
    protected $connection = 'tenant';
    
    protected $guarded = [];
    
    protected $casts = [
        'meeting_date' => 'date',
        'is_active' => 'boolean'
    ];


    public function fileMedia(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class, 'file_media_id');
    }

}