<?php

namespace App\Jobs;

use App\Models\Association;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchAssociationDomainChecksJob implements ShouldQueue
{
    use Queueable;

    public array $ids;

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function handle(): void
    {
        $query = Association::query();

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        $query->chunk(20, function ($associations) {
            foreach ($associations as $association) {
                CheckAssociationDomainJob::dispatch($association->id);
            }
        });
    }
}
