<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceInvite extends Model
{
    protected $fillable = [
        'workspace_id',
        'invited_by',
        'email',
        'role',
        'token',
        'status',
        'accepted_at',
        'expires_at',
        'sent_at',
        'delivery_error',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isAcceptable(): bool
    {
        return $this->status === 'pending'
            && (! $this->expires_at || $this->expires_at->isFuture());
    }
}
