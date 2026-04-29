<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceUsageSnapshot extends Model
{
    protected $fillable = [
        'workspace_id',
        'billing_plan_id',
        'period',
        'events_count',
        'monthly_limit',
        'usage_percent',
        'overage_count',
        'period_started_at',
        'period_ended_at',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'period_started_at' => 'datetime',
            'period_ended_at' => 'datetime',
            'captured_at' => 'datetime',
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
}