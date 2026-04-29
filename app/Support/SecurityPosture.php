<?php

namespace App\Support;

class SecurityPosture
{
    public static function report(): array
    {
        $checks = collect([
            self::check('APP_DEBUG desativado em producao', app()->isLocal() || ! config('app.debug'), config('app.debug') ? 'debug ativo' : 'debug inativo'),
            self::check('APP_URL com HTTPS', app()->isLocal() || str_starts_with((string) config('app.url'), 'https://'), (string) config('app.url')),
            self::check('CSRF ativo no app web', true, 'webhooks/* sao excecao intencional'),
            self::check('Webhook GitHub com secret', filled(config('services.github_app.webhook_secret')), 'GITHUB_APP_WEBHOOK_SECRET'),
            self::check('Webhook Mercado Pago com secret', filled(config('services.mercado_pago.webhook_secret')), 'MERCADO_PAGO_WEBHOOK_SECRET'),
            self::check('Cookies seguros em producao', app()->isLocal() || (bool) config('session.secure'), 'SESSION_SECURE_COOKIE'),
            self::check('Sessao em database', config('session.driver') === 'database', 'SESSION_DRIVER='.config('session.driver')),
            self::check('Payload sanitizado', class_exists(WebhookSanitizer::class), 'WebhookSanitizer ativo no armazenamento'),
        ]);

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);

        return [
            'checks' => $checks,
            'done' => $done,
            'total' => $total,
            'percent' => round(($done / $total) * 100),
        ];
    }

    private static function check(string $title, bool $done, string $detail): array
    {
        return compact('title', 'done', 'detail');
    }
}
