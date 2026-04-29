<?php

namespace App\Support;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvite;
use App\Models\WorkspaceMember;
use Illuminate\Support\Str;

class WorkspaceAccess
{
    public static function roles(): array
    {
        return [
            'owner' => 'Owner',
            'admin' => 'Admin',
            'developer' => 'Developer',
            'viewer' => 'Viewer',
        ];
    }

    public static function canManage(User $user, Workspace $workspace): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return WorkspaceMember::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }

    public static function currentRole(User $user, Workspace $workspace): ?string
    {
        return WorkspaceMember::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->value('role');
    }

    public static function invite(Workspace $workspace, User $inviter, string $email, string $role): array
    {
        $email = strtolower(trim($email));
        $role = array_key_exists($role, self::roles()) ? $role : 'developer';
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            WorkspaceMember::updateOrCreate(
                ['workspace_id' => $workspace->id, 'user_id' => $existingUser->id],
                ['role' => $role],
            );

            WorkspaceInvite::where('workspace_id', $workspace->id)
                ->where('email', $email)
                ->where('status', 'pending')
                ->update(['status' => 'accepted', 'accepted_at' => now()]);

            return ['status' => 'member_added', 'user' => $existingUser];
        }

        $invite = WorkspaceInvite::updateOrCreate(
            ['workspace_id' => $workspace->id, 'email' => $email, 'status' => 'pending'],
            [
                'invited_by' => $inviter->id,
                'role' => $role,
                'token' => Str::random(48),
                'expires_at' => now()->addDays(14),
            ],
        );

        return ['status' => 'invite_pending', 'invite' => $invite];
    }
}