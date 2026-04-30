<?php

namespace App\Support;

use App\Models\BillingPlan;
use App\Models\KnowledgeBaseArticle;
use App\Models\RoadmapItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class GoLiveReadiness
{
    public static function report(): array
    {
        $checks = collect([
            self::check('Domínio oficial com HTTPS', str_starts_with((string) config('app.url'), 'https://') && ! str_contains((string) config('app.url'), 'trycloudflare.com') && ! str_contains((string) config('app.url'), 'localhost'), 'Obrigatório para GitHub App público, Mercado Pago produção e confiança comercial.', 'Infra', 'Definir domínio, SSL e APP_URL final.', 'externo'),
            self::check('GitHub App oficial configurado', filled(config('services.github_app.app_id')) && filled(config('services.github_app.private_key')) && Route::has('webhooks.github-app'), 'App oficial precisa de callback, webhook público, permissões mínimas e credenciais reais.', 'GitHub', 'Criar app definitivo, revisar escopos e apontar webhook público.', 'externo'),
            self::check('Mercado Pago em produção', config('services.mercado_pago.environment') === 'production', 'Checkout e webhooks precisam sair do sandbox antes de vender planos reais.', 'Billing', 'Virar credenciais, testar assinatura real de baixo valor e rollback.', 'externo'),
            self::check('Planos comerciais configurados', Schema::hasTable('billing_plans') && BillingPlan::where('active', true)->count() >= 2, 'O produto tem regras internas de plano, consumo e limites para operar como SaaS.', 'Billing', 'Revisar limites finais e comunicação comercial.', 'local'),
            self::check('Uso e billing no painel', Schema::hasTable('billing_plans') && BillingPlan::where('active', true)->count() > 0, 'Conversão por uso aparece dentro do workspace, conectada ao consumo real.', 'Billing', 'Acompanhar primeiros usuários beta.', 'local'),
            self::check('E-mail transacional real', ! str_contains((string) config('mail.from.address'), 'example.com') && filled(config('mail.from.address')), 'Cadastro, recuperação, convites e alertas dependem de remetente confiável.', 'Comunicação', 'Configurar provedor, domínio autenticado, SPF, DKIM e DMARC.', 'externo'),
            self::check('Contato público de suporte', Route::has('contact') && filled(config('devlog.support_email')), 'Usuários, revisores e devs têm canal público antes de criar conta.', 'Suporte', 'Trocar email placeholder pelo domínio oficial quando houver domínio final.', 'local'),
            self::check('Fila fora de sync', (string) config('queue.default') !== 'sync', 'Webhooks, notificações e billing não devem depender de processamento inline em produção.', 'Operação', 'Ativar database/redis queue e worker supervisionado no servidor final.', 'externo'),
            self::check('Documentação pública suficiente', view()->exists('docs.users') && view()->exists('docs.api') && Schema::hasTable('knowledge_base_articles') && KnowledgeBaseArticle::where('published', true)->count() >= 3, 'Usuários conseguem configurar GitHub, entender segurança e resolver dúvidas sem suporte manual.', 'Docs', 'Revisar prints finais após domínio oficial.', 'local'),
            self::check('Ativos públicos de launch', Route::has('sitemap') && Route::has('robots'), 'Sitemap e robots ajudam indexação, revisão pública e higiene de produção.', 'Launch', 'Revisar URLs finais após domínio oficial.', 'local'),
            self::check('Suporte operacional', Schema::hasTable('support_tickets') && class_exists(SupportSla::class), 'Lançamento sem suporte vira gargalo assim que usuários reais testarem.', 'Suporte', 'Definir rotina diária de triagem.', 'local'),
            self::check('Roadmap público/admin atualizado', Schema::hasTable('roadmap_items') && RoadmapItem::where('status', 'done')->count() >= 10, 'Ajuda a explicar maturidade do produto e acompanhar progresso de launch.', 'Gestão', 'Manter prioridades, percentuais e bloqueadores atualizados.', 'local'),
            self::check('Changelog público', Route::has('changelog') && Schema::hasTable('roadmap_items') && RoadmapItem::where('status', 'done')->count() > 0, 'Dev vê evolução do produto, releases e sinais de manutenção ativa.', 'Launch', 'Manter entradas relevantes e remover detalhes internos quando necessário.', 'local'),
            self::check('Status público e trilha de auditoria', Route::has('status') && Schema::hasTable('audit_logs') && class_exists(AuditTrail::class), 'Dev confia mais quando vê saúde do sistema e quando a plataforma registra ações sensíveis.', 'Confiança', 'Publicar status e incidentes reais após live.', 'local'),
        ]);

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);
        $blockers = $checks->where('done', false)->values();
        $localChecks = $checks->where('kind', 'local');
        $externalChecks = $checks->where('kind', 'externo');
        $localDone = $localChecks->where('done', true)->count();
        $externalDone = $externalChecks->where('done', true)->count();

        return [
            'percent' => (int) round(($done / $total) * 100),
            'local_percent' => (int) round(($localDone / max($localChecks->count(), 1)) * 100),
            'external_percent' => (int) round(($externalDone / max($externalChecks->count(), 1)) * 100),
            'ready' => $blockers->isEmpty(),
            'checks' => $checks,
            'blockers' => $blockers,
            'external_blockers' => $blockers->where('kind', 'externo')->values(),
            'local_blockers' => $blockers->where('kind', 'local')->values(),
            'summary' => [
                'done' => $done,
                'total' => $total,
                'blockers' => $blockers->count(),
                'local_done' => $localDone,
                'local_total' => $localChecks->count(),
                'external_done' => $externalDone,
                'external_total' => $externalChecks->count(),
                'commercial' => $checks->whereIn('area', ['Billing', 'Comunicação'])->where('done', true)->count(),
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area, string $nextAction, string $kind): array
    {
        return compact('title', 'done', 'detail', 'area', 'nextAction', 'kind');
    }
}