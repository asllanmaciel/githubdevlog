<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_counts_all_workspace_events_even_when_feed_is_limited(): void
    {
        $user = User::create([
            'name' => 'Cliente Eventos',
            'email' => 'cliente-eventos@example.com',
            'password' => 'password',
        ]);
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Workspace Eventos',
            'slug' => 'workspace-eventos',
            'webhook_secret' => 'secret',
        ]);
        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);

        foreach (range(1, 55) as $index) {
            WebhookEvent::create([
                'workspace_id' => $workspace->id,
                'source' => 'github-app',
                'event_name' => 'push',
                'delivery_id' => 'delivery-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'signature_valid' => true,
                'validation_method' => 'test',
                'headers' => [],
                'payload' => ['repository' => ['full_name' => 'org/repo'], 'sender' => ['login' => 'dev']],
                'received_at' => now()->subMinutes(55 - $index),
            ]);
        }

        $this->actingAs($user)
            ->get('/dashboard/events')
            ->assertOk()
            ->assertSee('55 evento(s)', false)
            ->assertSee('55 válido(s)', false)
            ->assertSee('delivery-055', false)
            ->assertDontSee('delivery-001', false);
    }

    public function test_authenticated_dashboard_responses_are_not_cached(): void
    {
        $user = User::create([
            'name' => 'Cliente Cache',
            'email' => 'cliente-cache@example.com',
            'password' => 'password',
        ]);
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Workspace Cache',
            'slug' => 'workspace-cache',
            'webhook_secret' => 'secret',
        ]);
        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);

        $this->actingAs($user)
            ->get('/dashboard/events')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Pragma', 'no-cache');
    }
}
