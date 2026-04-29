<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapItem extends Model
{
    protected $fillable = ['title', 'area', 'status', 'priority', 'description', 'position', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }
}