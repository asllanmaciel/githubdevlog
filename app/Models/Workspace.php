<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name', 'slug', 'webhook_secret', 'webhook_secret_rotated_at', 'github_app_installation_id'];

    protected function casts(): array
    {
        return ['webhook_secret_rotated_at' => 'datetime'];
    }

    public function members()
    {
        return $this->hasMany(WorkspaceMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_members')->withPivot('role')->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(WorkspaceInvite::class);
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

    public function secretRotations()
    {
        return $this->hasMany(SecretRotation::class);
    }
}
