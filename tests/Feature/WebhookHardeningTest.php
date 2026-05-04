<?php

namespace Tests\Feature;

use App\Models\GithubInstallation;
use App\Models\Repository;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebhookHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_manual_github_signature_is_recorded_with_minimal_payload(): void
    {
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Webhook Hardening',
            'slug' => 'webhook-hardening',
            'webhook_secret' => 'secret',
        ]);

        $this->postJson('/webhooks/github/'.$workspace->uuid, ['repository' => ['full_name' => 'org/repo']], [
            'X-Hub-Signature-256' => 'sha256=invalid',
            'X-GitHub-Delivery' => 'delivery-invalid-1',
            'X-GitHub-Event' => 'push',
        ])->assertUnauthorized();

        $event = WebhookEvent::first();

        $this->assertNotNull($event);
        $this->assertFalse($event->signature_valid);
        $this->assertSame('rejected', $event->status);
        $this->assertSame('invalid_signature', $event->failure_reason);
        $this->assertSame('delivery-invalid-1', $event->delivery_id);
        $this->assertTrue($event->payload['rejected']);
        $this->assertArrayHasKey('payload_sha256', $event->payload);
        $this->assertArrayNotHasKey('repository', $event->payload);
    }

    public function test_valid_manual_github_delivery_is_idempotent(): void
    {
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Webhook Idempotent',
            'slug' => 'webhook-idempotent',
            'webhook_secret' => 'secret',
        ]);
        $rawBody = json_encode(['repository' => ['full_name' => 'org/repo']]);
        $signature = 'sha256='.hash_hmac('sha256', $rawBody, 'secret');

        $headers = [
            'X-Hub-Signature-256' => $signature,
            'X-GitHub-Delivery' => 'delivery-valid-1',
            'X-GitHub-Event' => 'push',
            'Content-Type' => 'application/json',
        ];

        $this->call('POST', '/webhooks/github/'.$workspace->uuid, [], [], [], $this->transformHeadersToServerVars($headers), $rawBody)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->call('POST', '/webhooks/github/'.$workspace->uuid, [], [], [], $this->transformHeadersToServerVars($headers), $rawBody)
            ->assertOk()
            ->assertJson(['ok' => true, 'duplicate' => true]);

        $this->assertSame(1, WebhookEvent::where('signature_valid', true)->count());
        $this->assertSame('accepted', WebhookEvent::first()->status);
    }

    public function test_valid_github_app_delivery_links_repository(): void
    {
        config(['services.github_app.webhook_secret' => 'app-secret']);

        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'GitHub App Workspace',
            'slug' => 'github-app-workspace',
            'webhook_secret' => 'manual-secret',
        ]);

        GithubInstallation::create([
            'workspace_id' => $workspace->id,
            'installation_id' => '128516060',
            'account_login' => 'AM-TIIX',
            'account_type' => 'Organization',
            'permissions' => ['contents' => 'read'],
            'events' => ['push'],
            'installed_at' => now(),
        ]);

        $rawBody = json_encode([
            'installation' => [
                'id' => 128516060,
                'account' => ['login' => 'AM-TIIX', 'type' => 'Organization'],
            ],
            'repository' => [
                'id' => 987654321,
                'full_name' => 'AM-TIIX/TIIX-Global',
                'private' => true,
                'default_branch' => 'main',
            ],
            'sender' => ['login' => 'asllanmaciel'],
        ]);

        $headers = [
            'X-Hub-Signature-256' => 'sha256='.hash_hmac('sha256', $rawBody, 'app-secret'),
            'X-GitHub-Delivery' => 'github-app-delivery-1',
            'X-GitHub-Event' => 'push',
            'Content-Type' => 'application/json',
        ];

        $this->call('POST', '/webhooks/github-app', [], [], [], $this->transformHeadersToServerVars($headers), $rawBody)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $event = WebhookEvent::firstOrFail();
        $repository = Repository::firstOrFail();

        $this->assertSame($repository->id, $event->repository_id);
        $this->assertSame('AM-TIIX/TIIX-Global', $repository->full_name);
        $this->assertSame('987654321', $repository->github_id);
        $this->assertTrue($repository->private);
        $this->assertSame('main', $repository->default_branch);
    }

    public function test_github_app_delivery_appears_in_workspace_events_dashboard(): void
    {
        config(['services.github_app.webhook_secret' => 'app-secret']);

        $user = User::create([
            'name' => 'Workspace User',
            'email' => 'workspace-user@example.com',
            'password' => 'password',
        ]);

        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Dashboard Workspace',
            'slug' => 'dashboard-workspace',
            'webhook_secret' => 'manual-secret',
        ]);

        $workspace->users()->attach($user->id, ['role' => 'owner']);

        GithubInstallation::create([
            'workspace_id' => $workspace->id,
            'installation_id' => '128516060',
            'account_login' => 'AM-TIIX',
            'account_type' => 'Organization',
            'permissions' => ['contents' => 'read'],
            'events' => [],
            'installed_at' => now(),
        ]);

        $rawBody = json_encode([
            'installation' => [
                'id' => 128516060,
                'account' => ['login' => 'AM-TIIX', 'type' => 'Organization'],
            ],
            'repository' => [
                'id' => 987654321,
                'full_name' => 'AM-TIIX/TIIX-Global',
                'private' => true,
                'default_branch' => 'main',
            ],
            'workflow_run' => [
                'event' => 'push',
                'status' => 'completed',
                'conclusion' => 'success',
                'head_branch' => 'main',
                'head_sha' => '85af199',
                'name' => 'changelog-automation',
            ],
            'sender' => ['login' => 'asllanmaciel'],
        ]);

        $headers = [
            'X-Hub-Signature-256' => 'sha256='.hash_hmac('sha256', $rawBody, 'app-secret'),
            'X-GitHub-Delivery' => 'github-app-delivery-dashboard',
            'X-GitHub-Event' => 'workflow_run',
            'Content-Type' => 'application/json',
        ];

        $this->call('POST', '/webhooks/github-app', [], [], [], $this->transformHeadersToServerVars($headers), $rawBody)
            ->assertOk();

        $this->actingAs($user)
            ->get('/dashboard/events')
            ->assertOk()
            ->assertSee('1 evento(s)', false)
            ->assertSee('workflow_run', false)
            ->assertSee('github-app-delivery-dashboard', false)
            ->assertSee('AM-TIIX/TIIX-Global', false);
    }

    public function test_super_admin_can_open_webhook_hardening_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-webhook-hardening@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/webhook-hardening')
            ->assertOk()
            ->assertSee('Webhooks com auditoria e idempotencia', false);
    }
}
