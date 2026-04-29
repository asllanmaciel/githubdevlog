<?php

namespace App\Support;

use App\Models\BillingPlan;
use App\Models\Notification;
use App\Models\Workspace;
use App\Models\WorkspaceUsageSnapshot;

class WorkspaceUsage
{
    public static function plan(Workspace $workspace): ?BillingPlan
    {
        return $workspace->subscription()->with('plan')->first()?->plan
            ?? BillingPlan::where('slug', 'free')->first();
    }

    public static function usageThisMonth(Workspace $workspace): int
    {
        return $workspace->webhookEvents()
            ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public static function limit(Workspace $workspace): int
    {
        $plan = self::plan($workspace);

        return max((int) ($plan?->monthly_event_limit ?? 1000), 1);
    }

    public static function percent(Workspace $workspace): int
    {
        return min(100, (int) round((self::usageThisMonth($workspace) / self::limit($workspace)) * 100));
    }

    public static function limitReached(Workspace $workspace): bool
    {
        return self::usageThisMonth($workspace) >= self::limit($workspace);
    }

    public static function nearLimit(Workspace $workspace, int $threshold = 80): bool
    {
        return self::percent($workspace) >= $threshold;
    }

    public static function report(Workspace $workspace): array
    {
        $plan = self::plan($workspace);
        $usage = self::usageThisMonth($workspace);
        $limit = self::limit($workspace);
        $percent = min(100, (int) round(($usage / $limit) * 100));

        return [
            'workspace' => $workspace,
            'plan' => $plan,
            'usage' => $usage,
            'limit' => $limit,
            'remaining' => max($limit - $usage, 0),
            'percent' => $percent,
            'near_limit' => $percent >= 80,
            'limit_reached' => $usage >= $limit,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ];
    }

    public static function notifyLimitReached(Workspace $workspace): void
    {
        $report = self::report($workspace);
        $plan = $report['plan'];

        Notification::firstOrCreate(
            [
                'workspace_id' => $workspace->id,
                'title' => 'Limite mensal de webhooks atingido',
                'type' => 'billing',
                'read_at' => null,
            ],
            [
                'body' => 'O workspace atingiu '.$report['usage'].'/'.$report['limit'].' eventos no plano '.($plan?->name ?? 'Free').'. Novos eventos serao recusados ate upgrade ou renovacao mensal.',
            ]
        );
    }

    public static function snapshot(Workspace $workspace, ?string $period = null): WorkspaceUsageSnapshot
    {
        $periodDate = $period
            ? now()->createFromFormat('Y-m', $period)->startOfMonth()
            : now()->startOfMonth();
        $periodKey = $periodDate->format('Y-m');
        $periodStart = $periodDate->copy()->startOfMonth();
        $periodEnd = $periodDate->copy()->endOfMonth();
        $plan = self::plan($workspace);
        $limit = self::limit($workspace);
        $eventsCount = $workspace->webhookEvents()
            ->whereBetween('received_at', [$periodStart, $periodEnd])
            ->count();
        $usagePercent = min(100, (int) round(($eventsCount / max($limit, 1)) * 100));

        return WorkspaceUsageSnapshot::updateOrCreate(
            ['workspace_id' => $workspace->id, 'period' => $periodKey],
            [
                'billing_plan_id' => $plan?->id,
                'events_count' => $eventsCount,
                'monthly_limit' => $limit,
                'usage_percent' => $usagePercent,
                'overage_count' => max($eventsCount - $limit, 0),
                'period_started_at' => $periodStart,
                'period_ended_at' => $periodEnd,
                'captured_at' => now(),
            ]
        );
    }
}
