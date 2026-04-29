<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretRotation extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'secret_type',
        'rotated_by',
        'metadata',
        'rotated_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'rotated_at' => 'datetime',
        ];
    }
}
