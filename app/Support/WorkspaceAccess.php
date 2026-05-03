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

    public static function permissions(): array
    {
        return [
            'manage_workspace' => ['owner', 'admin'],
            'manage_members' => ['owner', 'admin'],
            'manage_billing' => ['owner', 'admin'],
            'manage_secrets' => ['owner', 'admin'],
            'manage_github_app' => ['owner', 'admin'],
            'create_test_events' => ['owner', 'admin', 'developer'],
            'annotate_events' => ['owner', 'admin', 'developer'],
            'open_support' => ['owner', 'admin', 'developer', 'viewer'],
            'view_events' => ['owner', 'admin', 'developer', 'viewer'],
        ];
    }

    public static function labels(): array
    {
        return [
            'manage_workspace' => 'Gerenciar workspace',
            'manage_members' => 'Convidar/remover membros',
            'manage_billing' => 'Gerenciar planos e assinatura',
            'manage_secrets' => 'Rotacionar secrets',
            'manage_github_app' => 'Conectar GitHub App',
            'create_test_events' => 'Criar eventos de teste',
            'annotate_events' => 'Criar notas e tarefas',
            'open_support' => 'Abrir suporte',
            'view_events' => 'Ver eventos',
        ];
    }

    public static function can(User $user, Workspace $workspace, string $permission): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $role = self::currentRole($user, $workspace);

        return $role !== null && in_array($role, self::permissions()[$permission] ?? [], true);
    }

    public static function canManage(User $user, Workspace $workspace): bool
    {
        return self::can($user, $workspace, 'manage_workspace');
    }

    public static function currentRole(User $user, Workspace $workspace): ?string
    {
        return WorkspaceMember::where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->value('role');
    }

    public static function roleMatrix(): array
    {
        $permissions = self::permissions();

        return collect(self::roles())->mapWithKeys(function (string $label, string $role) use ($permissions) {
            return [$role => [
                'label' => $label,
                'permissions' => collect($permissions)
                    ->map(fn (array $roles, string $permission) => in_array($role, $roles, true))
                    ->all(),
            ]];
        })->all();
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
