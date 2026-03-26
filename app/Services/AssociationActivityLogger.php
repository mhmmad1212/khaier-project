<?php

namespace App\Services;

use App\Models\Association;
use App\Models\AssociationActivity;
use Illuminate\Support\Facades\Auth;

class AssociationActivityLogger
{
    public static function log(
        Association $association,
        int $actionCode,
        string $actionType,
        string $title,
        ?string $details = null,
        ?int $performedByUserId = null
    ): AssociationActivity {
        return AssociationActivity::create([
            'association_id' => $association->id,
            'action_code' => $actionCode,
            'action_type' => $actionType,
            'title' => $title,
            'details' => $details,
            'performed_by_user_id' => $performedByUserId ?? Auth::id(),
        ]);
    }
}
