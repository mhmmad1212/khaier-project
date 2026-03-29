<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model {
    // توجيه الموديل للاتصال بقاعدة بيانات الجمعية بشكل إجباري
    protected $connection = 'tenant';
    
    protected $guarded = [];
    
    protected $casts = [
        'meeting_date' => 'date',
        'is_active' => 'boolean'
    ];
}
