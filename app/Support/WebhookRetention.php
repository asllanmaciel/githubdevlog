<?php

namespace App\Support;

use App\Models\BillingPlan;
use App\Models\WebhookEvent;
use App\Models\Workspace;

class WebhookRetention
{
    public static function report(): array
    {
        $workspaces = Workspace::with(['subscription.plan'])->get();

        $rows = $workspaces->map(function (Workspace $workspace) {
            $plan = $workspace->subscription?->plan;
            $days = self::retentionDays($plan);
            $cutoff = now()->subDays($days);
            $expired = $workspace->webhookEvents()->where('received_at', '<', $cutoff)->count();
            $total = $workspace->webhookEvents()->count();

            return [
                'workspace_id' => $workspace->id,
                'workspace' => $workspace->name,
                'plan' => $plan?->name ?: 'Plano nao definido',
                'retention_days' => $days,
                'cutoff' => $cutoff,
                'total_events' => $total,
                'expired_events' => $expired,
            ];
        });

        return [
            'rows' => $rows,
            'total_events' => WebhookEvent::count(),
            'expired_events' => $rows->sum('expired_events'),
            'plans' => BillingPlan::orderBy('price_cents')->get(['name', 'slug', 'event_retention_days']),
        ];
    }

    public static function prune(bool $dryRun = true): array
    {
        $report = self::report();
        $deleted = 0;

        foreach ($report['rows'] as $row) {
            if ($row['expired_events'] <= 0) {
                continue;
            }

            if ($dryRun) {
                $deleted += $row['expired_events'];

                continue;
            }

            $deleted += WebhookEvent::query()
                ->where('workspace_id', $row['workspace_id'])
                ->where('received_at', '<', $row['cutoff'])
                ->delete();
        }

        return [
            'dry_run' => $dryRun,
            'deleted_events' => $deleted,
            'checked_workspaces' => $report['rows']->count(),
        ];
    }

    private static function retentionDays(?BillingPlan $plan): int
    {
        $days = (int) ($plan?->event_retention_days ?: 30);

        return max(1, $days);
    }
}
