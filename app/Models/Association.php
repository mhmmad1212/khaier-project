<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Association extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'official_email',
        'official_phone',
        'domain_type',
        'subdomain_label',
        'previous_domain',
        'is_active',
        'site_status',
        'is_subscribed',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'database_host',
        'database_port',
        'database_name',
        'database_username',
        'database_password',
        'creation_mode',
        'cloned_from_association_id',
        'last_domain_changed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'last_domain_changed_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function clonedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cloned_from_association_id');
    }

    public function clones(): HasMany
    {
        return $this->hasMany(self::class, 'cloned_from_association_id');
    }

    public function getPreviewUrl(): ?string
    {
        if (! $this->is_active) {
            return null;
        }

        if (! empty($this->domain_type) && $this->domain_type === 'custom_domain' && ! empty($this->domain)) {
            return str_starts_with($this->domain, 'http://') || str_starts_with($this->domain, 'https://')
                ? $this->domain
                : 'https://' . $this->domain;
        }

        if (! empty($this->domain_type) && $this->domain_type === 'subdomain' && ! empty($this->subdomain_label)) {
            $baseDomain = env('TENANT_BASE_DOMAIN', 'khaier.org');
            return 'https://' . $this->subdomain_label . '.' . $baseDomain;
        }

        if (! empty($this->domain)) {
            return str_starts_with($this->domain, 'http://') || str_starts_with($this->domain, 'https://')
                ? $this->domain
                : 'https://' . $this->domain;
        }

        if (! empty($this->slug)) {
            $baseDomain = env('TENANT_BASE_DOMAIN', 'khaier.org');
            return 'https://' . $this->slug . '.' . $baseDomain;
        }

        return null;
    }




    public function domainCheck(): HasOne
    {
        return $this->hasOne(AssociationDomainCheck::class);
    }


    public function activities(): HasMany
    {
        return $this->hasMany(AssociationActivity::class);
    }

}
