<?php

namespace App\Services;

use App\Models\BillingPlan;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
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

    public function webhookSecretConfigured(): bool
    {
        return filled(config('services.mercado_pago.webhook_secret'));
    }

    public function validateWebhookSignature(Request $request): array
    {
        $secret = (string) config('services.mercado_pago.webhook_secret', '');

        if ($secret === '') {
            return [
                'configured' => false,
                'valid' => false,
                'reason' => 'MERCADO_PAGO_WEBHOOK_SECRET nao configurado.',
            ];
        }

        $xSignature = (string) $request->header('x-signature', '');
        $xRequestId = (string) $request->header('x-request-id', '');

        if ($xSignature === '') {
            return [
                'configured' => true,
                'valid' => false,
                'reason' => 'Header x-signature ausente.',
            ];
        }

        $signatureParts = [];

        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key && $value) {
                $signatureParts[trim($key)] = trim($value);
            }
        }

        $ts = $signatureParts['ts'] ?? null;
        $receivedHash = $signatureParts['v1'] ?? null;

        if (! $ts || ! $receivedHash) {
            return [
                'configured' => true,
                'valid' => false,
                'reason' => 'Header x-signature sem ts ou v1.',
            ];
        }

        $tolerance = (int) config('services.mercado_pago.webhook_tolerance_seconds', 900);
        $timestamp = strlen($ts) > 10 ? ((int) floor(((int) $ts) / 1000)) : (int) $ts;

        if ($tolerance > 0 && abs(time() - $timestamp) > $tolerance) {
            return [
                'configured' => true,
                'valid' => false,
                'reason' => 'Timestamp da assinatura fora da janela de tolerancia.',
            ];
        }

        $dataId = (string) (
            $request->query('data.id')
            ?? $request->query('id')
            ?? data_get($request->all(), 'data.id')
            ?? ''
        );

        if ((string) ($request->query('type') ?? data_get($request->all(), 'type')) === 'order') {
            $dataId = Str::lower($dataId);
        }

        $manifest = $this->mercadoPagoSignatureManifest($dataId, $xRequestId, $ts);
        $expectedHash = hash_hmac('sha256', $manifest, $secret);

        return [
            'configured' => true,
            'valid' => hash_equals($expectedHash, $receivedHash),
            'reason' => hash_equals($expectedHash, $receivedHash) ? 'Assinatura Mercado Pago valida.' : 'Assinatura Mercado Pago invalida.',
            'manifest' => $manifest,
        ];
    }

    private function mercadoPagoSignatureManifest(string $dataId, string $xRequestId, string $ts): string
    {
        $parts = [];

        if ($dataId !== '') {
            $parts[] = 'id:'.$dataId;
        }

        if ($xRequestId !== '') {
            $parts[] = 'request-id:'.$xRequestId;
        }

        if ($ts !== '') {
            $parts[] = 'ts:'.$ts;
        }

        return implode(';', $parts).';';
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

    public function getPayment(int|string $paymentId): object
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Mercado Pago nao configurado. Preencha as credenciais no .env.');
        }

        MercadoPagoConfig::setAccessToken((string) config('services.mercado_pago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);

        return (new PaymentClient())->get((int) $paymentId);
    }

    public function parseExternalReference(?string $externalReference): array
    {
        $result = ['workspace_id' => null, 'billing_plan_id' => null];

        if (! $externalReference) {
            return $result;
        }

        foreach (explode(';', $externalReference) as $pair) {
            [$key, $value] = array_pad(explode(':', $pair, 2), 2, null);

            if ($key === 'workspace') {
                $result['workspace_id'] = (int) $value;
            }

            if ($key === 'plan') {
                $result['billing_plan_id'] = (int) $value;
            }
        }

        return $result;
    }
}
