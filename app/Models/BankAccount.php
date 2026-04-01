<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'bank_logo',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
