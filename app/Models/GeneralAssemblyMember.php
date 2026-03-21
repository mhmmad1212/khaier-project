<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralAssemblyMember extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'position',
        'join_date',
        'photo',
        'sort_order',
        'is_active',
    ];
}
