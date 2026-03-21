<?php

namespace App\Models;

use App\Services\TemplateFileGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PageTemplate extends Model
{
    protected $fillable = [
        'page_type',
        'template_key',
        'name',
        'view_path',
        'template_content',
        'template_css',
        'template_js',
        'scope_type',
        'preview_image',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (PageTemplate $template) {
            if (blank($template->template_key)) {
                $template->template_key = 'pending_' . Str::uuid();
            }

            if (blank($template->view_path)) {
                $template->view_path = 'pending';
            }
        });

        static::saved(function (PageTemplate $template) {
            if (
                blank($template->template_key) ||
                str_starts_with((string) $template->template_key, 'pending_')
            ) {
                $template->template_key = 'template_' . $template->id;
                $template->saveQuietly();
            }

            if (! empty($template->template_content)) {
                TemplateFileGenerator::generate($template->fresh());
            }
        });
    }

    public function associations(): BelongsToMany
    {
        return $this->belongsToMany(
            Association::class,
            'page_template_associations',
            'page_template_id',
            'association_id'
        );
    }
}
