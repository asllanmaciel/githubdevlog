<?php

namespace App\Support;

class ProductionEnvironment
{
    public static function report(): array
    {
        $appUrl = (string) config('app.url');
        $github = config('services.github_app');
        $mercadoPago = config('services.mercado_pago');

        $checks = collect([
            'Aplicacao' => [
                self::check('APP_ENV definido para production', app()->environment('production'), app()->environment(), true),
                self::check('APP_DEBUG desativado', ! config('app.debug'), config('app.debug') ? 'debug ativo' : 'debug inativo', true),
                self::check('APP_URL final em HTTPS', str_starts_with($appUrl, 'https://'), $appUrl ?: 'APP_URL vazio', true),
                self::check('APP_KEY configurada', filled(config('app.key')), 'APP_KEY', true),
            ],
            'Banco e filas' => [
                self::check('Banco MySQL selecionado', config('database.default') === 'mysql', 'DB_CONNECTION='.config('database.default'), true),
                self::check('Sessao em banco', config('session.driver') === 'database', 'SESSION_DRIVER='.config('session.driver'), true),
                self::check('Cache persistente', config('cache.default') === 'database', 'CACHE_STORE='.config('cache.default'), false),
                self::check('Queue persistente', config('queue.default') === 'database', 'QUEUE_CONNECTION='.config('queue.default'), false),
            ],
            'Mercado Pago' => [
                self::check('Ambiente Mercado Pago em production', ($mercadoPago['environment'] ?? null) === 'production', 'MERCADO_PAGO_ENVIRONMENT='.($mercadoPago['environment'] ?? 'vazio'), true),
                self::check('Access token Mercado Pago', filled($mercadoPago['access_token'] ?? null), 'token configurado por ambiente', true),
                self::check('Public key Mercado Pago', filled($mercadoPago['public_key'] ?? null), 'public key configurada por ambiente', true),
                self::check('Webhook secret Mercado Pago', filled($mercadoPago['webhook_secret'] ?? null), 'MERCADO_PAGO_WEBHOOK_SECRET', true),
            ],
            'GitHub App' => [
                self::check('GitHub App ID', filled($github['app_id'] ?? null), 'GITHUB_APP_ID', true),
                self::check('GitHub OAuth Client ID', filled($github['client_id'] ?? null), 'GITHUB_APP_CLIENT_ID', true),
                self::check('GitHub OAuth Client Secret', filled($github['client_secret'] ?? null), 'GITHUB_APP_CLIENT_SECRET', true),
                self::check('GitHub Webhook Secret', filled($github['webhook_secret'] ?? null), 'GITHUB_APP_WEBHOOK_SECRET', true),
                self::check('GitHub Private Key Path', filled($github['private_key_path'] ?? null), 'GITHUB_APP_PRIVATE_KEY_PATH', true),
                self::check('GitHub Callback URL', filled($github['callback_url'] ?? null) && str_contains((string) $github['callback_url'], '/github/callback'), $github['callback_url'] ?: 'pendente', true),
                self::check('GitHub Webhook URL', filled($github['webhook_url'] ?? null) && str_contains((string) $github['webhook_url'], '/webhooks/github-app'), $github['webhook_url'] ?: 'pendente', true),
            ],
        ]);

        $flat = $checks->flatten(1);
        $done = $flat->where('done', true)->count();
        $total = max($flat->count(), 1);
        $requiredPending = $flat->where('required', true)->where('done', false)->values();

        return [
            'groups' => $checks,
            'done' => $done,
            'total' => $total,
            'percent' => round(($done / $total) * 100),
            'required_pending' => $requiredPending,
            'ready' => $requiredPending->isEmpty(),
        ];
    }

    private static function check(string $title, bool $done, string $detail, bool $required): array
    {
        return compact('title', 'done', 'detail', 'required');
    }
}
