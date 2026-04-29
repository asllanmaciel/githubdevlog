<?php

namespace App\Support;

use App\Models\BillingEvent;
use App\Models\BillingPlan;
use App\Models\GithubInstallation;
use App\Models\RoadmapItem;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use Illuminate\Support\Facades\Route;

class LaunchReadiness
{
    public static function report(): array
    {
        $appUrl = (string) config('app.url');
        $github = config('services.github_app');
        $mercadoPago = config('services.mercado_pago');

        $checks = collect([
            'Infraestrutura' => [
                self::check('APP_URL em HTTPS', str_starts_with($appUrl, 'https://'), $appUrl ?: 'APP_URL nao definido'),
                self::check('Healthcheck publico', Route::has('health'), url('/health')),
                self::check('Banco com usuarios', User::count() > 0, User::count().' usuario(s)'),
                self::check('Workspaces criados', Workspace::count() > 0, Workspace::count().' workspace(s)'),
            ],
            'SaaS e Billing' => [
                self::check('Planos ativos', BillingPlan::where('active', true)->count() > 0, BillingPlan::where('active', true)->count().' plano(s)'),
                self::check('Assinaturas rastreadas', WorkspaceSubscription::count() > 0, WorkspaceSubscription::count().' assinatura(s)'),
                self::check('Mercado Pago configurado', filled($mercadoPago['access_token'] ?? null), 'Access token por ambiente'),
                self::check('Webhook Mercado Pago com secret', filled($mercadoPago['webhook_secret'] ?? null), 'MERCADO_PAGO_WEBHOOK_SECRET'),
                self::check('Auditoria de cobranca', class_exists(BillingEvent::class), BillingEvent::count().' evento(s) registrados'),
            ],
            'GitHub App e Webhooks' => [
                self::check('Endpoint GitHub manual', Route::has('webhooks.github'), url('/webhooks/github/{workspace}')),
                self::check('Endpoint GitHub App', Route::has('webhooks.github-app'), url('/webhooks/github-app')),
                self::check('App ID configurado', filled($github['app_id'] ?? null), 'GITHUB_APP_ID'),
                self::check('OAuth GitHub configurado', filled($github['client_id'] ?? null) && filled($github['client_secret'] ?? null), 'Client ID + Secret'),
                self::check('Webhook secret GitHub App', filled($github['webhook_secret'] ?? null), 'GITHUB_APP_WEBHOOK_SECRET'),
                self::check('Instalacoes vinculadas', GithubInstallation::count() > 0, GithubInstallation::count().' instalacao(oes)'),
                self::check('Eventos validados', WebhookEvent::where('signature_valid', true)->count() > 0, WebhookEvent::where('signature_valid', true)->count().' evento(s)'),
            ],
            'Produto e Operacao' => [
                self::check('Landing publica', Route::has('home'), url('/')),
                self::check('Docs de usuarios', Route::has('docs.users'), url('/docs/usuarios')),
                self::check('Docs admin', Route::has('docs.admin'), url('/admin/docs')),
                self::check('Termos publicados', Route::has('terms'), url('/terms')),
                self::check('Privacidade publicada', Route::has('privacy'), url('/privacy')),
                self::check('Suporte no produto', Route::has('support'), SupportTicket::count().' chamado(s)'),
                self::check('Roadmap vivo', RoadmapItem::count() > 0, RoadmapItem::where('status', 'done')->count().' de '.RoadmapItem::count().' itens concluidos'),
            ],
        ]);

        $flat = $checks->flatten(1);
        $done = $flat->where('done', true)->count();
        $total = max($flat->count(), 1);

        return [
            'groups' => $checks,
            'done' => $done,
            'total' => $total,
            'percent' => round(($done / $total) * 100),
            'blockers' => $flat->where('done', false)->where('required', true)->values(),
        ];
    }

    private static function check(string $title, bool $done, string $detail, bool $required = true): array
    {
        return compact('title', 'done', 'detail', 'required');
    }
}
