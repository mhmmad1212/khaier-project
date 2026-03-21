<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'type',
        'page_id',
        'target',
        'icon',
        'sort_order',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function getResolvedUrlAttribute(): string
    {
        if ($this->type === 'page' && $this->page) {
            return url('/page/' . $this->page->slug);
        }

        if ($this->type === 'news') {
            return '/news';
        }

        return $this->url ?: '#';
    }
}
