<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEventTask extends Model
{
    protected $fillable = ['webhook_event_id', 'assigned_to', 'title', 'status', 'due_at'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime'];
    }
}
