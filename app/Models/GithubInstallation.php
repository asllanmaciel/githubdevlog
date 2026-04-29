<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GithubInstallation extends Model
{
    protected $fillable = ['workspace_id', 'installation_id', 'account_login', 'account_type', 'permissions', 'events', 'installed_at'];

    protected function casts(): array
    {
        return ['permissions' => 'array', 'events' => 'array', 'installed_at' => 'datetime'];
    }
}
