<?php

namespace App\Support;

use App\Models\UsageInvoice;
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

    public static function invoiceSnapshot(WorkspaceUsageSnapshot $snapshot): ?UsageInvoice
    {
        $estimate = self::estimate($snapshot);

        if (! $estimate['billable']) {
            return null;
        }

        return UsageInvoice::updateOrCreate(
            ['workspace_id' => $snapshot->workspace_id, 'period' => $snapshot->period],
            [
                'billing_plan_id' => $snapshot->billing_plan_id,
                'workspace_usage_snapshot_id' => $snapshot->id,
                'status' => 'draft',
                'events_count' => $snapshot->events_count,
                'monthly_limit' => $snapshot->monthly_limit,
                'overage_count' => $estimate['overage_count'],
                'overage_price_cents' => $estimate['price_cents'],
                'amount_cents' => $estimate['amount_cents'],
                'currency' => $snapshot->plan?->currency ?? 'BRL',
                'provider' => 'mercado_pago',
                'metadata' => [
                    'source' => 'overage_snapshot',
                    'snapshot_id' => $snapshot->id,
                    'captured_at' => now()->toIso8601String(),
                ],
            ]
        );
    }

    public static function generateInvoices(?string $period = null): array
    {
        $period ??= now()->format('Y-m');
        $created = 0;
        $amount = 0;

        WorkspaceUsageSnapshot::query()
            ->with(['workspace', 'plan'])
            ->where('period', $period)
            ->where('overage_count', '>', 0)
            ->chunkById(100, function ($snapshots) use (&$created, &$amount) {
                foreach ($snapshots as $snapshot) {
                    $invoice = self::invoiceSnapshot($snapshot);
                    if ($invoice) {
                        $created++;
                        $amount += $invoice->amount_cents;
                    }
                }
            });

        return [
            'period' => $period,
            'invoices' => $created,
            'amount_cents' => $amount,
        ];
    }
}
