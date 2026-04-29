<?php

namespace App\Support;

use App\Models\WorkspaceUsageSnapshot;

class OverageBilling
{
    public static function estimate(WorkspaceUsageSnapshot $snapshot): array
    {
        $plan = $snapshot->plan;
        $priceCents = (int) ($plan?->overage_price_cents ?? 0);
        $overage = (int) $snapshot->overage_count;
        $amountCents = $overage * $priceCents;

        return [
            'snapshot' => $snapshot,
            'workspace' => $snapshot->workspace,
            'plan' => $plan,
            'period' => $snapshot->period,
            'overage_count' => $overage,
            'price_cents' => $priceCents,
            'amount_cents' => $amountCents,
            'billable' => $overage > 0 && $priceCents > 0,
        ];
    }

    public static function report(?string $period = null): array
    {
        $period ??= now()->format('Y-m');
        $items = WorkspaceUsageSnapshot::query()
            ->with(['workspace', 'plan'])
            ->where('period', $period)
            ->orderByDesc('overage_count')
            ->get()
            ->map(fn ($snapshot) => self::estimate($snapshot));

        return [
            'period' => $period,
            'items' => $items,
            'billable_items' => $items->where('billable', true)->count(),
            'total_overage' => $items->sum('overage_count'),
            'total_amount_cents' => $items->sum('amount_cents'),
        ];
    }
}