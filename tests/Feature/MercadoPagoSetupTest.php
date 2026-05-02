<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MercadoPagoSetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MercadoPagoSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_mercado_pago_setup_exposes_urls_and_env_snippet(): void
    {
        config([
            'app.url' => 'https://ghdevlog.com',
            'services.mercado_pago.environment' => 'production',
            'services.mercado_pago.access_token' => 'APP_USR_1234567890',
            'services.mercado_pago.public_key' => 'APP_USR_PUBLIC_1234567890',
            'services.mercado_pago.webhook_secret' => 'secret-test',
            'services.mercado_pago.webhook_tolerance_seconds' => 900,
        ]);

        $report = MercadoPagoSetup::report();

        $this->assertStringContainsString('MERCADO_PAGO_ENVIRONMENT=production', $report['env_snippet']);
        $this->assertSame('https://ghdevlog.com/webhooks/mercado-pago', $report['urls'][0]['value']);
        $this->assertArrayHasKey('percent', $report);
    }

    public function test_mercado_pago_check_command_outputs_json_payload(): void
    {
        $this->artisan('devlog:mercado-pago-check --json')
            ->assertExitCode(1)
            ->expectsOutputToContain('env_snippet');
    }

    public function test_super_admin_can_open_mercado_pago_readiness_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-mercado-pago@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/mercado-pago-readiness')
            ->assertOk()
            ->assertSee('Mercado Pago pronto antes de vender', false);
    }
}
