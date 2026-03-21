<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsCategory extends Model
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
