<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $fillable = [
        'fingerprint',
        'level',
        'exception_class',
        'message',
        'file',
        'line',
        'method',
        'url',
        'route',
        'user_id',
        'ip_hash',
        'occurrences',
        'context',
        'first_seen_at',
        'last_seen_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('resolved_at');
    }
}
