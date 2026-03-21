<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasShortCode
{
    public function generateUniqueShortCode(): string
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (static::query()
            ->where('short_code', $code)
            ->when($this->exists && $this->getKey(), fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists());

        return $code;
    }
}
