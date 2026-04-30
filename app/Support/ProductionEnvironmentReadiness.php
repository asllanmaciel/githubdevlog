<?php

namespace App\Support;

class ProductionEnvironmentReadiness
{
    public static function report(): array
    {
        $checks = collect([
            self::check('APP_URL', config('app.url'), fn ($value) => str_starts_with((string) $value, 'https://') && ! str_contains((string) $value, 'localhost') && ! str_contains((string) $value, 'trycloudflare.com'), 'Domínio oficial com HTTPS'),
            self::check('QUEUE_CONNECTION', config('queue.default'), fn ($value) => (string) $value !== 'sync', 'Fila assíncrona para webhooks e notificações'),
            self::check('MAIL_MAILER', config('mail.default'), fn ($value) => filled($value) && (string) $value !== 'log', 'Provedor de e-mail transacional'),
            self::check('MAIL_FROM_ADDRESS', config('mail.from.address'), fn ($value) => filled($value) && ! str_contains((string) $value, 'example.com'), 'Remetente real e autenticado'),
            self::check('DEVLOG_SUPPORT_EMAIL', config('devlog.support_email'), fn ($value) => filled($value) && ! str_contains((string) $value, 'githubdevlog.ai'), 'Contato público real de suporte'),
            self::check('GITHUB_APP_ID', config('services.github_app.app_id'), fn ($value) => filled($value), 'GitHub App oficial'),
            self::check('GITHUB_APP_PRIVATE_KEY_PATH', config('services.github_app.private_key_path'), fn ($value) => filled($value) && file_exists((string) $value), 'Chave privada do GitHub App'),
            self::check('GITHUB_APP_WEBHOOK_SECRET', config('services.github_app.webhook_secret'), fn ($value) => filled($value), 'Secret do webhook GitHub App'),
            self::check('GITHUB_APP_CLIENT_ID', config('services.github_app.client_id'), fn ($value) => filled($value), 'OAuth client id do GitHub App'),
            self::check('GITHUB_APP_CLIENT_SECRET', config('services.github_app.client_secret'), fn ($value) => filled($value), 'OAuth client secret do GitHub App'),
            self::check('GITHUB_APP_SETUP_URL', config('services.github_app.setup_url'), fn ($value) => filled($value) && ! str_contains((string) $value, 'your-github-app-slug'), 'URL oficial de instalação do GitHub App'),
            self::check('MERCADO_PAGO_ENVIRONMENT', config('services.mercado_pago.environment'), fn ($value) => (string) $value === 'production', 'Ambiente Mercado Pago em produção'),
            self::check('MERCADO_PAGO_ACCESS_TOKEN', config('services.mercado_pago.access_token'), fn ($value) => filled($value), 'Access token Mercado Pago'),
            self::check('MERCADO_PAGO_PUBLIC_KEY', config('services.mercado_pago.public_key'), fn ($value) => filled($value), 'Public key Mercado Pago'),
            self::check('MERCADO_PAGO_WEBHOOK_SECRET', config('services.mercado_pago.webhook_secret'), fn ($value) => filled($value), 'Secret de webhook Mercado Pago'),
        ]);

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);

        return [
            'percent' => (int) round(($done / $total) * 100),
            'done' => $done,
            'total' => $total,
            'checks' => $checks,
            'missing' => $checks->where('done', false)->values(),
        ];
    }

    private static function check(string $key, mixed $value, callable $passes, string $purpose): array
    {
        $filled = filled($value);

        return [
            'key' => $key,
            'done' => (bool) $passes($value),
            'purpose' => $purpose,
            'state' => $filled ? 'configurada' : 'ausente',
            'display' => self::safeDisplay($value),
        ];
    }

    private static function safeDisplay(mixed $value): string
    {
        if (! filled($value)) {
            return 'não configurada';
        }

        $value = (string) $value;

        if (str_contains(strtolower($value), 'secret') || strlen($value) > 28) {
            return substr($value, 0, 6).'...'.substr($value, -4);
        }

        return $value;
    }
}
