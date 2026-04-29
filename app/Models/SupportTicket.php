<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = ['workspace_id', 'user_id', 'subject', 'status', 'priority', 'message', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }
}