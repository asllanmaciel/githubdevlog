<?php

namespace Tests\Feature;

use App\Models\BillingEvent;
use App\Models\BillingPlan;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use App\Support\ProductMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_metrics_report_tracks_activation_and_revenue(): void
    {
        $user = User::create(['name' => 'Cliente', 'email' => 'cliente-metrics@example.com', 'password' => 'password']);
        $workspace = Workspace::create(['uuid' => (string) Str::uuid(), 'name' => 'Workspace Metrics', 'slug' => 'workspace-metrics', 'webhook_secret' => 'secret']);
        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);
        $plan = BillingPlan::create(['name' => 'Starter', 'slug' => 'starter', 'price_cents' => 2900, 'currency' => 'BRL', 'event_retention_days' => 30, 'monthly_event_limit' => 10000, 'monthly_ai_analysis_limit' => 25, 'ai_analysis_overage_price_cents' => 15, 'overage_price_cents' => 3, 'active' => true]);
        WorkspaceSubscription::create(['workspace_id' => $workspace->id, 'billing_plan_id' => $plan->id, 'provider' => 'mercado_pago', 'provider_reference' => 'pay-1', 'status' => 'active', 'current_period_ends_at' => now()->addMonth()]);
        WebhookEvent::create(['workspace_id' => $workspace->id, 'source' => 'github', 'event_name' => 'push', 'signature_valid' => true, 'validation_method' => 'test', 'headers' => [], 'payload' => [], 'received_at' => now()]);
        BillingEvent::create(['provider' => 'mercado_pago', 'provider_event_id' => 'pay-1', 'event_type' => 'payment', 'resource_id' => 'pay-1', 'status' => 'processed_active', 'signature_valid' => true, 'payload' => [], 'workspace_id' => $workspace->id, 'billing_plan_id' => $plan->id, 'processed_at' => now()]);

        $report = ProductMetrics::report();

        $this->assertSame('100%', $report['metrics'][2]['value']);
        $this->assertSame('R$ 29,00', $report['metrics'][3]['value']);
        $this->assertSame('1/1', $report['metrics'][5]['value']);
    }

    public function test_super_admin_can_open_product_metrics_page(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin-metrics@example.com', 'password' => 'password', 'is_super_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/product-metrics')
            ->assertOk()
            ->assertSee('Metricas acionaveis do SaaS', false);
    }
}
