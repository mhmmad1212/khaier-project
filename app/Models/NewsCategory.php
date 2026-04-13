<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsCategory extends TenantModel
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_category_news');
    }
}
