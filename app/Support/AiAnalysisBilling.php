<?php

namespace App\Support;

use App\Models\Workspace;

class AiAnalysisBilling
{
    public static function advancedUsageThisMonth(Workspace $workspace): int
    {
        return $workspace->webhookEvents()
            ->where('ai_analysis_type', 'llm')
            ->whereBetween('ai_generated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public static function advancedLimit(Workspace $workspace): int
    {
        $plan = WorkspaceUsage::plan($workspace);

        return (int) ($plan?->monthly_ai_analysis_limit ?? 0);
    }

    public static function advancedRemaining(Workspace $workspace): int
    {
        $limit = self::advancedLimit($workspace);

        if ($limit <= 0) {
            return 0;
        }

        return max($limit - self::advancedUsageThisMonth($workspace), 0);
    }

    public static function canUseAdvanced(Workspace $workspace): bool
    {
        return self::advancedLimit($workspace) > 0 && self::advancedRemaining($workspace) > 0;
    }

    public static function report(Workspace $workspace): array
    {
        $plan = WorkspaceUsage::plan($workspace);
        $limit = self::advancedLimit($workspace);
        $usage = self::advancedUsageThisMonth($workspace);

        return [
            'plan' => $plan,
            'usage' => $usage,
            'limit' => $limit,
            'remaining' => max($limit - $usage, 0),
            'percent' => $limit > 0 ? min(100, (int) round(($usage / $limit) * 100)) : 0,
            'enabled' => $limit > 0,
            'can_use' => $limit > 0 && $usage < $limit,
            'overage_price_cents' => (int) ($plan?->ai_analysis_overage_price_cents ?? 0),
        ];
    }
}
