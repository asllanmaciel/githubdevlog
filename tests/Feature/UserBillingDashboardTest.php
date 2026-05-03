<?php

namespace Tests\Feature;

use App\Models\BillingEvent;
use App\Models\BillingPlan;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserBillingDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_billing_dashboard_highlights_subscription_and_payments(): void
    {
        $user = User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'password' => 'password',
        ]);

        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Workspace Cliente',
            'slug' => 'workspace-cliente',
            'webhook_secret' => 'dlog_test',
        ]);

        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);

        $plan = BillingPlan::create([
            'name' => 'Teste Mercado Pago',
            'slug' => 'teste-mp',
            'price_cents' => 100,
            'currency' => 'BRL',
            'event_retention_days' => 7,
            'monthly_event_limit' => 100,
            'monthly_ai_analysis_limit' => 1,
            'ai_analysis_overage_price_cents' => 0,
            'overage_price_cents' => 0,
            'active' => true,
        ]);

        WorkspaceSubscription::create([
            'workspace_id' => $workspace->id,
            'billing_plan_id' => $plan->id,
            'provider' => 'mercado_pago',
            'provider_reference' => '157461560350',
            'status' => 'active',
            'current_period_ends_at' => now()->addMonth(),
        ]);

        BillingEvent::create([
            'provider' => 'mercado_pago',
            'provider_event_id' => 'payment:157461560350',
            'event_type' => 'payment',
            'resource_id' => '157461560350',
            'status' => 'processed_active',
            'signature_valid' => true,
            'payload' => ['type' => 'payment'],
            'workspace_id' => $workspace->id,
            'billing_plan_id' => $plan->id,
            'processed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/dashboard/billing')
            ->assertOk()
            ->assertSee('Plano assinado', false)
            ->assertSee('Teste Mercado Pago', false)
            ->assertSee('Registros de pagamento', false)
            ->assertSee('processed_active', false)
            ->assertSee('157461560350', false);
    }
}
