<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'workspace_id', 'repository_id', 'source', 'event_name', 'action', 'delivery_id',
        'dedupe_key', 'signature_valid', 'status', 'failure_reason', 'retry_count', 'last_retried_at',
        'reprocessed_at', 'validation_method', 'headers', 'payload', 'ai_summary', 'ai_risk_level',
        'ai_action_items', 'ai_signals', 'ai_provider', 'ai_analysis_type', 'ai_estimated_cost_cents',
        'ai_input_tokens', 'ai_output_tokens', 'ai_error', 'ai_generated_at', 'received_at', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'payload' => 'array',
            'ai_action_items' => 'array',
            'ai_signals' => 'array',
            'signature_valid' => 'boolean',
            'retry_count' => 'integer',
            'last_retried_at' => 'datetime',
            'reprocessed_at' => 'datetime',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
            'ai_generated_at' => 'datetime',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
