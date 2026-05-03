<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrail
{
    public static function record(
        string $action,
        ?Model $subject = null,
        ?Workspace $workspace = null,
        array $metadata = [],
        ?User $user = null,
        ?Request $request = null,
        string $actorType = 'user'
    ): AuditLog {
        $user ??= Auth::user();
        $request ??= request();

        return AuditLog::create([
            'user_id' => $user?->id,
            'workspace_id' => $workspace?->id,
            'actor_type' => $user ? $actorType : 'system',
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => self::cleanMetadata($metadata),
            'created_at' => now(),
        ]);
    }

    private static function cleanMetadata(array $metadata): array
    {
        foreach ($metadata as $key => $value) {
            if (str_contains(strtolower((string) $key), 'secret') || str_contains(strtolower((string) $key), 'token')) {
                $metadata[$key] = '[redacted]';
            }
        }

        return $metadata;
    }
}
