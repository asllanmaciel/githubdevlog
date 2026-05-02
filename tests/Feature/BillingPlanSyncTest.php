<?php

namespace Tests\Feature;

use App\Models\BillingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPlanSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_plans_creates_low_value_mercado_pago_test_plan(): void
    {
        $this->artisan('devlog:sync-plans')->assertExitCode(0);

        $plan = BillingPlan::where('slug', 'teste-mp')->first();

        $this->assertNotNull($plan);
        $this->assertSame('Teste Mercado Pago', $plan->name);
        $this->assertSame(100, $plan->price_cents);
        $this->assertTrue($plan->active);
    }
}
