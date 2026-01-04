<?php

namespace App\Core\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;


trait AuditTrailTraits
{

    function auditTrail(
        string $action,
        string $actionId,
        string $date,
        string $relatedTo,
        string $comment = '',
        ?array $extra = []
    ) {
        $user = Auth::user();
        AuditTrail::create([
            'action' => $action,
            'action_id' => $actionId,
            'date' => $date,
            'user' => $user->id ?? 1,
            'related_to' => $relatedTo,
            'comment' => $comment,
            'extra' => $extra,
        ]);
    }
}

