<?php

namespace App\Services;

use App\Models\BillingPlan;
use App\Models\Workspace;
use Illuminate\Support\Str;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoBillingService
{
    public function isConfigured(): bool
    {
        return filled(config('services.mercado_pago.access_token'))
            && class_exists(MercadoPagoConfig::class)
            && class_exists(PreferenceClient::class);
    }

    public function environment(): string
    {
        return (string) config('services.mercado_pago.environment', 'sandbox');
    }

    public function checkoutStatus(): array
    {
        return [
            'provider' => 'mercado_pago',
            'environment' => $this->environment(),
            'sdk' => class_exists(MercadoPagoConfig::class) ? 'installed' : 'missing',
            'configured' => $this->isConfigured(),
            'next_step' => 'Criar preferencia de pagamento e webhook de confirmacao.',
        ];
    }

    public function createCheckoutPreference(Workspace $workspace, BillingPlan $plan, string $payerEmail): object
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Mercado Pago nao configurado. Preencha as credenciais no .env.');
        }

        MercadoPagoConfig::setAccessToken((string) config('services.mercado_pago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);

        $client = new PreferenceClient();
        $requestOptions = new RequestOptions();
        $requestOptions->setCustomHeaders([
            'X-Idempotency-Key: '.(string) Str::uuid(),
        ]);

        return $client->create([
            'external_reference' => 'workspace:'.$workspace->id.';plan:'.$plan->id,
            'notification_url' => route('webhooks.mercado-pago'),
            'back_urls' => [
                'success' => route('billing.return', ['status' => 'success']),
                'pending' => route('billing.return', ['status' => 'pending']),
                'failure' => route('billing.return', ['status' => 'failure']),
            ],
            'payer' => [
                'email' => $payerEmail,
            ],
            'items' => [[
                'id' => (string) $plan->id,
                'title' => 'GitHub DevLog AI - '.$plan->name,
                'description' => number_format($plan->monthly_event_limit, 0, ',', '.').' eventos por mes e retencao de '.$plan->event_retention_days.' dias.',
                'quantity' => 1,
                'currency_id' => $plan->currency ?: 'BRL',
                'unit_price' => max($plan->price_cents / 100, 0.01),
            ]],
            'metadata' => [
                'workspace_id' => $workspace->id,
                'billing_plan_id' => $plan->id,
                'environment' => $this->environment(),
            ],
        ], $requestOptions);
    }

    public function checkoutUrl(object $preference): ?string
    {
        if ($this->environment() === 'production') {
            return $preference->init_point ?? null;
        }

        return $preference->sandbox_init_point ?? $preference->init_point ?? null;
    }
}