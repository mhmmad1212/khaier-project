<?php

namespace App\Models\Concerns;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttachments
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable')
            ->orderBy('sort_order');
    }

    public function activeAttachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function attachmentsByCollection(string $collection): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable')
            ->where('collection', $collection)
            ->orderBy('sort_order');
    }

    public function attachmentsBySection(int $sectionCode): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable')
            ->where('section_code', $sectionCode)
            ->orderBy('sort_order');
    }
}
