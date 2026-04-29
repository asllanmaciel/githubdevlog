<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchTest extends Model
{
    protected $fillable = [
        'title',
        'area',
        'priority',
        'status',
        'instructions',
        'expected_result',
        'evidence',
        'executed_by',
        'executed_at',
        'position',
    ];

    protected function casts(): array
    {
        return ['executed_at' => 'datetime'];
    }
}
