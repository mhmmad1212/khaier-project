<?php

namespace App\Jobs;

use App\Models\Association;
use App\Support\AssociationDomainMonitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckAssociationDomainJob implements ShouldQueue
{
    use Queueable;

    public int $associationId;

    public function __construct(int $associationId)
    {
        $this->associationId = $associationId;
    }

    public function handle(): void
    {
        $association = Association::find($this->associationId);

        if (! $association) {
            return;
        }

        AssociationDomainMonitor::check($association);
    }
}
