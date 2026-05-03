<?php

namespace Tests\Feature;

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
