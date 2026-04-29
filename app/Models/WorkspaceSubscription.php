<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceSubscription extends Model
{
    protected $fillable = [
        'workspace_id',
        'billing_plan_id',
        'provider',
        'provider_reference',
        'status',
        'trial_ends_at',
        'current_period_ends_at',
        'canceled_at',
        'cancel_reason',
        'lifecycle_metadata',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function plan()
    {
        return $this->belongsTo(BillingPlan::class, 'billing_plan_id');
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'lifecycle_metadata' => 'array',
        ];
    }
}