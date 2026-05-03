<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageInvoice extends Model
{
    protected $fillable = [
        'workspace_id',
        'billing_plan_id',
        'workspace_usage_snapshot_id',
        'period',
        'status',
        'events_count',
        'monthly_limit',
        'overage_count',
        'overage_price_cents',
        'amount_cents',
        'currency',
        'provider',
        'provider_reference',
        'issued_at',
        'paid_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function plan()
    {
        return $this->belongsTo(BillingPlan::class, 'billing_plan_id');
    }

    public function snapshot()
    {
        return $this->belongsTo(WorkspaceUsageSnapshot::class, 'workspace_usage_snapshot_id');
    }
}
