<?php

namespace Tests\Feature;

use App\Models\BillingEvent;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_basic_application_health(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('mode', 'health')
            ->assertJsonPath('ok', true)
            ->assertJsonStructure([
                'ok',
                'mode',
                'app',
                'environment',
                'checked_at',
                'checks' => [
                    'application',
                    'database',
                    'storage',
                ],
            ]);
    }

    public function test_readiness_endpoint_keeps_operational_checks_separate(): void
    {
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Readiness Workspace',
            'slug' => 'readiness-workspace',
            'webhook_secret' => 'secret',
        ]);

        WebhookEvent::create([
            'workspace_id' => $workspace->id,
            'source' => 'github',
            'event_name' => 'push',
            'signature_valid' => false,
            'validation_method' => 'test',
            'headers' => [],
            'payload' => [],
            'received_at' => now(),
        ]);

        BillingEvent::create([
            'provider' => 'mercado_pago',
            'provider_event_id' => 'pending-readiness',
            'event_type' => 'payment',
            'status' => 'pending_lookup',
            'signature_valid' => true,
            'payload' => [],
        ]);

        $this->getJson('/readiness')
            ->assertStatus(503)
            ->assertJsonPath('mode', 'readiness')
            ->assertJsonPath('ok', false)
            ->assertJsonStructure([
                'checks' => [
                    'billing',
                    'github_webhooks',
                    'environment',
                    'communication',
                ],
            ]);
    }
}
