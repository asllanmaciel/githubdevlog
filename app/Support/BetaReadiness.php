<?php

namespace App\Support;

use App\Models\BillingPlan;
use App\Models\KnowledgeBaseArticle;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\Workspace;
use App\Services\MercadoPagoBillingService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class BetaReadiness
{
    public static function report(): array
    {
        $checks = collect([
            self::check('Cadastro e login', Route::has('register.store') && Route::has('login.store'), 'Fluxo basico de conta disponivel', 'Produto'),
            self::check('Workspace privado', Schema::hasTable('workspaces') && Schema::hasTable('workspace_members'), 'Workspace e membros existem', 'Produto'),
            self::check('Permissoes por papel', class_exists(WorkspaceAccess::class) && count(WorkspaceAccess::permissions()) >= 8, 'Owner/admin/developer/viewer definidos', 'Seguranca'),
            self::check('Webhook GitHub manual', Route::has('webhooks.github'), 'Endpoint por workspace disponivel', 'GitHub'),
            self::check('Webhook GitHub App', Route::has('webhooks.github-app'), 'Endpoint de GitHub App preparado', 'GitHub'),
            self::check('Billing Mercado Pago', Route::has('webhooks.mercado-pago') && class_exists(MercadoPagoBillingService::class), 'Webhook e servico Mercado Pago preparados', 'Billing'),
            self::check('Planos cadastraveis', Schema::hasTable('billing_plans') && BillingPlan::count() > 0, 'Planos existem no banco', 'Billing'),
            self::check('Suporte com SLA', Schema::hasTable('support_tickets') && class_exists(SupportSla::class), 'Chamados e SLA disponiveis', 'Suporte'),
            self::check('Docs de usuario', view()->exists('docs.users'), 'Guia publico de uso existe', 'Documentacao'),
            self::check('Status publico', Route::has('status'), 'Pagina publica de status existe', 'Operacao'),
            self::check('Auditoria', Schema::hasTable('audit_logs') && class_exists(AuditTrail::class), 'Acoes criticas auditaveis', 'Seguranca'),
            self::check('Roadmap admin', Schema::hasTable('roadmap_items') && RoadmapItem::count() > 0, 'Roadmap visivel e populado', 'Gestao'),
        ]);

        $external = collect([
            self::external('Domínio definitivo', str_starts_with((string) config('app.url'), 'https://') && ! str_contains((string) config('app.url'), 'trycloudflare.com'), 'Configurar dominio oficial e SSL'),
            self::external('GitHub App oficial', filled(config('services.github_app.app_id')) && filled(config('services.github_app.client_secret')), 'Criar/configurar App no GitHub'),
            self::external('Mercado Pago producao', config('services.mercado_pago.environment') === 'production', 'Virar credenciais para producao'),
            self::external('Email transacional real', ! str_contains((string) config('mail.from.address'), 'example.com'), 'Configurar remetente e provedor real'),
        ]);

        return [
            'percent' => (int) round(($checks->where('done', true)->count() / max($checks->count(), 1)) * 100),
            'ready' => $checks->where('done', false)->isEmpty(),
            'checks' => $checks,
            'external' => $external,
            'metrics' => [
                'users' => Schema::hasTable('users') ? User::count() : 0,
                'workspaces' => Schema::hasTable('workspaces') ? Workspace::count() : 0,
                'plans' => Schema::hasTable('billing_plans') ? BillingPlan::count() : 0,
                'articles' => Schema::hasTable('knowledge_base_articles') ? KnowledgeBaseArticle::where('published', true)->count() : 0,
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area): array
    {
        return compact('title', 'done', 'detail', 'area');
    }

    private static function external(string $title, bool $done, string $detail): array
    {
        return compact('title', 'done', 'detail');
    }
}
