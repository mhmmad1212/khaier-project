<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function bootHasUniqueSlug(): void
    {
        static::creating(function ($model) {
            $model->slug = $model->generateUniqueSlugValue();
        });

        static::updating(function ($model) {
            if (blank($model->slug) || $model->isDirty('slug') || ($model->isDirty('title') && blank($model->getOriginal('slug')))) {
                $model->slug = $model->generateUniqueSlugValue();
            }
        });
    }

    public function generateUniqueSlugValue(): string
    {
        $source = $this->slug ?: ($this->title ?? null) ?: ($this->name ?? null) ?: 'item';

        $base = Str::slug($source);
        if (blank($base)) {
            $base = 'item';
        }

        $slug = $base;
        $i = 2;

        while ($this->slugExists($slug)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        if ($this->exists && $this->getKey()) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->exists();
    }
}
