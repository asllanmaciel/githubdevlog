<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'subject',
        'status',
        'priority',
        'category',
        'message',
        'first_response_due_at',
        'resolution_due_at',
        'responded_at',
        'internal_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'first_response_due_at' => 'datetime',
            'resolution_due_at' => 'datetime',
            'responded_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
