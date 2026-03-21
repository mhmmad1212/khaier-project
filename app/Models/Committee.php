<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'description',
        'chairman',
        'members_count',
        'attachment',
        'sort_order',
        'is_active',
    ];
}
