<?php

namespace App\Support;

use App\Models\GithubInstallation;
use App\Models\Repository;
use App\Models\SecretRotation;
use App\Models\UsageInvoice;
use App\Models\WebhookEvent;
use App\Models\WebhookEventNote;
use App\Models\WebhookEventTask;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Models\WorkspaceSubscription;
use App\Models\WorkspaceUsageSnapshot;
use Illuminate\Support\Facades\DB;

class WorkspaceDataPurge
{
    public static function report(Workspace $workspace): array
    {
        $eventIds = $workspace->webhookEvents()->pluck('id');

        return [
            'workspace' => [
                'id' => $workspace->id,
                'uuid' => $workspace->uuid,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
            ],
            'counts' => [
                'members' => WorkspaceMember::where('workspace_id', $workspace->id)->count(),
                'repositories' => Repository::where('workspace_id', $workspace->id)->count(),
                'github_installations' => GithubInstallation::where('workspace_id', $workspace->id)->count(),
                'secret_rotations' => SecretRotation::where('workspace_id', $workspace->id)->count(),
                'subscriptions' => WorkspaceSubscription::where('workspace_id', $workspace->id)->count(),
                'webhook_events' => WebhookEvent::where('workspace_id', $workspace->id)->count(),
                'notes' => WebhookEventNote::whereIn('webhook_event_id', $eventIds)->count(),
                'tasks' => WebhookEventTask::whereIn('webhook_event_id', $eventIds)->count(),
                'usage_snapshots' => class_exists(WorkspaceUsageSnapshot::class) ? WorkspaceUsageSnapshot::where('workspace_id', $workspace->id)->count() : 0,
                'usage_invoices' => class_exists(UsageInvoice::class) ? UsageInvoice::where('workspace_id', $workspace->id)->count() : 0,
            ],
        ];
    }

    public static function purge(Workspace $workspace, bool $dryRun = true): array
    {
        $report = self::report($workspace);

        if ($dryRun) {
            return [
                'dry_run' => true,
                'workspace_id' => $workspace->id,
                'deleted' => $report['counts'],
                'workspace_deleted' => false,
            ];
        }

        DB::transaction(function () use ($workspace) {
            $eventIds = $workspace->webhookEvents()->pluck('id');

            WebhookEventNote::whereIn('webhook_event_id', $eventIds)->delete();
            WebhookEventTask::whereIn('webhook_event_id', $eventIds)->delete();
            WebhookEvent::where('workspace_id', $workspace->id)->delete();
            Repository::where('workspace_id', $workspace->id)->delete();
            GithubInstallation::where('workspace_id', $workspace->id)->delete();
            SecretRotation::where('workspace_id', $workspace->id)->delete();
            WorkspaceMember::where('workspace_id', $workspace->id)->delete();
            WorkspaceSubscription::where('workspace_id', $workspace->id)->delete();

            if (class_exists(WorkspaceUsageSnapshot::class)) {
                WorkspaceUsageSnapshot::where('workspace_id', $workspace->id)->delete();
            }

            if (class_exists(UsageInvoice::class)) {
                UsageInvoice::where('workspace_id', $workspace->id)->delete();
            }

            $workspace->delete();
        });

        return [
            'dry_run' => false,
            'workspace_id' => $workspace->id,
            'deleted' => $report['counts'],
            'workspace_deleted' => true,
        ];
    }
}