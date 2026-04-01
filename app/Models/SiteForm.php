<?php

namespace App\Models;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteForm extends TenantModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'success_message',
        'submit_button_text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(SiteFormField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SiteFormSubmission::class)->latest('id');
    }
}
