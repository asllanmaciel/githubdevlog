<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    protected $fillable = ['workspace_id', 'github_id', 'full_name', 'private', 'default_branch'];

    protected function casts(): array
    {
        return ['private' => 'boolean'];
    }
}
