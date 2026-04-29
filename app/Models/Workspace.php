<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name', 'slug', 'webhook_secret', 'github_app_installation_id'];

    public function members()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_members')->withPivot('role')->withTimestamps();
    }

    public function webhookEvents()
    {
        return $this->hasMany(WebhookEvent::class);
    }

    public function subscription()
    {
        return $this->hasOne(WorkspaceSubscription::class)->latestOfMany();
    }

    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }

    public function githubInstallations()
    {
        return $this->hasMany(GithubInstallation::class);
    }
}
