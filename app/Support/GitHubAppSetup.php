<?php

namespace App\Support;

use App\Models\GithubInstallation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class GitHubAppSetup
{
    public static function report(): array
    {
        $config = config('services.github_app');

        $webhookUrl = $config['webhook_url'] ?: url('/webhooks/github-app');
        $callbackUrl = $config['callback_url'] ?: url('/github/callback');
        $setupUrl = $config['setup_url'] ?: url('/github/install');
        $homepageUrl = url('/github');
        $privateKeyPath = (string) ($config['private_key_path'] ?? '');
        $env = [
            ['key' => 'GITHUB_APP_ID', 'done' => filled($config['app_id'] ?? null), 'value' => self::mask($config['app_id'] ?? null), 'description' => 'ID numerico do GitHub App.'],
            ['key' => 'GITHUB_APP_CLIENT_ID', 'done' => filled($config['client_id'] ?? null), 'value' => self::mask($config['client_id'] ?? null), 'description' => 'Client ID do OAuth do GitHub App.'],
            ['key' => 'GITHUB_APP_CLIENT_SECRET', 'done' => filled($config['client_secret'] ?? null), 'value' => self::mask($config['client_secret'] ?? null), 'description' => 'Client Secret do OAuth.'],
            ['key' => 'GITHUB_APP_WEBHOOK_SECRET', 'done' => filled($config['webhook_secret'] ?? null), 'value' => self::mask($config['webhook_secret'] ?? null), 'description' => 'Secret usado para validar X-Hub-Signature-256.'],
            ['key' => 'GITHUB_APP_PRIVATE_KEY_PATH', 'done' => filled($privateKeyPath) && file_exists($privateKeyPath), 'value' => $privateKeyPath ?: 'pendente', 'description' => 'Caminho local/servidor para o .pem privado do GitHub App.'],
            ['key' => 'GITHUB_APP_WEBHOOK_URL', 'done' => filled($config['webhook_url'] ?? null) && str_starts_with($webhookUrl, 'https://'), 'value' => $webhookUrl, 'description' => 'URL publica final do webhook.'],
            ['key' => 'GITHUB_APP_CALLBACK_URL', 'done' => filled($config['callback_url'] ?? null) && str_starts_with($callbackUrl, 'https://'), 'value' => $callbackUrl, 'description' => 'Callback final do OAuth.'],
            ['key' => 'GITHUB_APP_SETUP_URL', 'done' => filled($config['setup_url'] ?? null) && ! str_contains($setupUrl, 'your-github-app-slug'), 'value' => $setupUrl, 'description' => 'URL final de instalacao.'],
        ];

        $steps = [
            ['title' => 'Criar GitHub App no GitHub', 'done' => filled($config['app_id'] ?? null), 'detail' => 'Use nome, homepage, webhook URL e callback URL do painel.'],
            ['title' => 'Preencher OAuth e App ID no .env', 'done' => filled($config['app_id'] ?? null) && filled($config['client_id'] ?? null) && filled($config['client_secret'] ?? null), 'detail' => 'GITHUB_APP_ID, GITHUB_APP_CLIENT_ID e GITHUB_APP_CLIENT_SECRET.'],
            ['title' => 'Configurar webhook secret', 'done' => filled($config['webhook_secret'] ?? null), 'detail' => 'Mesmo valor no GitHub e no .env.'],
            ['title' => 'Salvar chave privada .pem', 'done' => filled($privateKeyPath) && file_exists($privateKeyPath), 'detail' => 'Gerar Private key no GitHub App e apontar o caminho fora de public_html.'],
            ['title' => 'Instalar App em uma conta ou organizacao', 'done' => GithubInstallation::count() > 0, 'detail' => GithubInstallation::count().' instalacao(oes) vinculada(s).'],
            ['title' => 'Validar rotas oficiais', 'done' => Route::has('github.install') && Route::has('github.callback') && Route::has('webhooks.github-app'), 'detail' => 'Instalacao, callback e webhook GitHub App ativos.'],
        ];

        $done = collect($steps)->where('done', true)->count();
        $total = max(count($steps), 1);

        return [
            'ready' => collect($steps)->where('done', false)->isEmpty(),
            'percent' => (int) round(($done / $total) * 100),
            'urls' => [
                ['label' => 'Webhook URL', 'value' => $webhookUrl, 'description' => 'Cole em Webhook URL ao criar o GitHub App.'],
                ['label' => 'Callback URL', 'value' => $callbackUrl, 'description' => 'Cole em Callback URL para OAuth/instalacao.'],
                ['label' => 'Setup URL', 'value' => $setupUrl, 'description' => 'Use como URL de instalacao/setup do app.'],
                ['label' => 'Homepage URL', 'value' => $homepageUrl, 'description' => 'Pagina publica para revisao do Developer Program.'],
            ],
            'env' => $env,
            'env_snippet' => self::envSnippet($webhookUrl, $callbackUrl, $setupUrl),
            'app_profile' => [
                'name' => $config['name'] ?: 'GitHub DevLog AI',
                'description' => 'Inbox privado para webhooks do GitHub com assinatura validada, timeline auditavel, payload sanitizado e colaboracao por workspace.',
                'homepage_url' => $homepageUrl,
                'callback_url' => $callbackUrl,
                'webhook_url' => $webhookUrl,
            ],
            'permissions' => [
                'Repository contents: Read-only',
                'Metadata: Read-only',
                'Pull requests: Read-only',
                'Issues: Read-only',
                'Commit statuses: Read-only',
                'Actions: Read-only',
            ],
            'events' => ['push', 'pull_request', 'workflow_run', 'issues', 'installation', 'installation_repositories'],
            'steps' => $steps,
        ];
    }

    private static function mask(?string $value): string
    {
        if (! filled($value)) {
            return 'pendente';
        }

        return strlen($value) <= 8 ? str_repeat('*', strlen($value)) : substr($value, 0, 4).'...'.substr($value, -4);
    }

    private static function envSnippet(string $webhookUrl, string $callbackUrl, string $setupUrl): string
    {
        return implode("\n", [
            'GITHUB_APP_ID=',
            'GITHUB_APP_CLIENT_ID=',
            'GITHUB_APP_CLIENT_SECRET=',
            'GITHUB_APP_WEBHOOK_SECRET='.Str::random(48),
            'GITHUB_APP_PRIVATE_KEY_PATH=/home/ghdevlog/app/storage/app/private/github-app.pem',
            'GITHUB_APP_WEBHOOK_URL="'.$webhookUrl.'"',
            'GITHUB_APP_CALLBACK_URL="'.$callbackUrl.'"',
            'GITHUB_APP_SETUP_URL="'.$setupUrl.'"',
        ]);
    }
}
