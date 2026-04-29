<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingEvent extends Model
{
    protected $fillable = [
        'provider',
        'provider_event_id',
        'request_id',
        'event_type',
        'action',
        'resource_id',
        'external_reference',
        'status',
        'signature_valid',
        'payload',
        'workspace_id',
        'workspace_subscription_id',
        'billing_plan_id',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'payload' => 'array',
            'processed_at' => 'datetime',
        ];
    }
}
