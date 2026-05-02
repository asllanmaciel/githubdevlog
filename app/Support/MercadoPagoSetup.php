<?php

namespace App\Support;

use App\Models\BillingEvent;
use App\Models\BillingPlan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MercadoPagoSetup
{
    public static function report(): array
    {
        $config = config('services.mercado_pago');
        $appUrl = rtrim((string) config('app.url'), '/');
        $webhookUrl = $appUrl.'/webhooks/mercado-pago';
        $returnUrl = $appUrl.'/billing/return';
        $pricingUrl = $appUrl.'/pricing';
        $activePlans = Schema::hasTable('billing_plans') ? BillingPlan::where('active', true)->count() : 0;
        $billingEventsReady = Schema::hasTable('billing_events');
        $validEvents = $billingEventsReady ? BillingEvent::where('provider', 'mercado_pago')->where('signature_valid', true)->count() : 0;
        $failedEvents = $billingEventsReady ? BillingEvent::where('provider', 'mercado_pago')->whereNotNull('error_message')->count() : 0;

        $env = [
            ['key' => 'MERCADO_PAGO_ENVIRONMENT', 'done' => ($config['environment'] ?? null) === 'production', 'value' => (string) ($config['environment'] ?? 'sandbox'), 'description' => 'Ambiente precisa estar em production para vender planos reais.'],
            ['key' => 'MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN', 'done' => filled($config['access_token'] ?? null), 'value' => self::mask($config['access_token'] ?? null), 'description' => 'Token privado usado para criar preferencias e consultar pagamentos.'],
            ['key' => 'MERCADO_PAGO_PRODUCTION_PUBLIC_KEY', 'done' => filled($config['public_key'] ?? null), 'value' => self::mask($config['public_key'] ?? null), 'description' => 'Chave publica da conta de producao.'],
            ['key' => 'MERCADO_PAGO_WEBHOOK_SECRET', 'done' => filled($config['webhook_secret'] ?? null), 'value' => self::mask($config['webhook_secret'] ?? null), 'description' => 'Secret compartilhado para validar x-signature dos webhooks.'],
            ['key' => 'MERCADO_PAGO_WEBHOOK_TOLERANCE_SECONDS', 'done' => ((int) ($config['webhook_tolerance_seconds'] ?? 0)) >= 300, 'value' => (string) ($config['webhook_tolerance_seconds'] ?? 900), 'description' => 'Janela de tolerancia contra replay de webhook.'],
        ];

        $steps = [
            ['title' => 'Usar dominio HTTPS oficial', 'done' => str_starts_with($appUrl, 'https://') && ! str_contains($appUrl, 'localhost') && ! str_contains($appUrl, 'trycloudflare.com'), 'detail' => $appUrl],
            ['title' => 'Virar credenciais para producao', 'done' => collect($env)->whereIn('key', ['MERCADO_PAGO_ENVIRONMENT', 'MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN', 'MERCADO_PAGO_PRODUCTION_PUBLIC_KEY'])->every(fn ($item) => $item['done']), 'detail' => 'Conta Mercado Pago de producao com token e public key reais.'],
            ['title' => 'Configurar webhook assinado', 'done' => filled($config['webhook_secret'] ?? null) && Route::has('webhooks.mercado-pago') && Route::has('webhooks.mercado-pago.health'), 'detail' => $webhookUrl],
            ['title' => 'Manter planos comerciais ativos', 'done' => $activePlans >= 2, 'detail' => $activePlans.' plano(s) ativo(s).'],
            ['title' => 'Receber ao menos um webhook valido', 'done' => $validEvents > 0, 'detail' => $validEvents.' evento(s) Mercado Pago com assinatura valida.'],
            ['title' => 'Sem falhas criticas de billing abertas', 'done' => $failedEvents === 0, 'detail' => $failedEvents.' evento(s) com erro registrado.'],
        ];

        $done = collect($steps)->where('done', true)->count();
        $total = max(count($steps), 1);

        return [
            'ready' => collect($steps)->where('done', false)->isEmpty(),
            'percent' => (int) round(($done / $total) * 100),
            'summary' => [
                'done' => $done,
                'total' => $total,
                'active_plans' => $activePlans,
                'valid_events' => $validEvents,
                'failed_events' => $failedEvents,
            ],
            'urls' => [
                ['label' => 'Webhook URL', 'value' => $webhookUrl, 'description' => 'Cole no painel Mercado Pago em suas notificacoes/webhooks.'],
                ['label' => 'Return URL', 'value' => $returnUrl, 'description' => 'Usada pelo checkout para success, pending e failure.'],
                ['label' => 'Pagina de precos', 'value' => $pricingUrl, 'description' => 'Referencia publica dos planos antes do checkout.'],
            ],
            'env' => $env,
            'env_snippet' => self::envSnippet(),
            'steps' => $steps,
            'test_plan' => [
                'Criar ou revisar os planos ativos em /admin/billing-plans.',
                'Iniciar checkout com uma conta pagadora real de baixo valor.',
                'Confirmar recebimento do POST em /admin/billing-events.',
                'Validar que a assinatura do webhook ficou marcada como valida.',
                'Conferir assinatura do workspace e fatura apos retorno do Mercado Pago.',
            ],
        ];
    }

    private static function mask(?string $value): string
    {
        if (! filled($value)) {
            return 'pendente';
        }

        return strlen($value) <= 10 ? str_repeat('*', strlen($value)) : substr($value, 0, 5).'...'.substr($value, -4);
    }

    private static function envSnippet(): string
    {
        return implode("\n", [
            'MERCADO_PAGO_ENVIRONMENT=production',
            'MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN=',
            'MERCADO_PAGO_PRODUCTION_PUBLIC_KEY=',
            'MERCADO_PAGO_WEBHOOK_SECRET='.Str::random(48),
            'MERCADO_PAGO_WEBHOOK_TOLERANCE_SECONDS=900',
        ]);
    }
}
