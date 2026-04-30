<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'workspace_id', 'repository_id', 'source', 'event_name', 'action', 'delivery_id',
        'signature_valid', 'validation_method', 'headers', 'payload', 'ai_summary', 'ai_risk_level',
        'ai_action_items', 'ai_signals', 'ai_provider', 'ai_generated_at', 'received_at', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'payload' => 'array',
            'signature_valid' => 'boolean',
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
