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
            self::check('Dominio oficial com HTTPS', str_starts_with((string) config('app.url'), 'https://') && ! str_contains((string) config('app.url'), 'trycloudflare.com') && ! str_contains((string) config('app.url'), 'localhost'), 'Obrigatorio para GitHub App publico, Mercado Pago producao e confianca comercial.', 'Infra', 'Definir dominio, SSL e APP_URL final.'),
            self::check('GitHub App pronto para revisao', filled(config('services.github_app.app_id')) && filled(config('services.github_app.private_key')) && Route::has('webhooks.github-app'), 'App oficial precisa de manifesto claro, callback, webhook e permissoes minimas.', 'GitHub', 'Criar app definitivo, revisar escopos e apontar webhook publico.'),
            self::check('Mercado Pago em producao', config('services.mercado_pago.environment') === 'production', 'Checkout e webhooks precisam sair do sandbox antes de vender planos reais.', 'Billing', 'Virar credenciais, testar assinatura real de baixo valor e rollback.'),
            self::check('Planos comerciais publicados', Schema::hasTable('billing_plans') && BillingPlan::where('active', true)->count() >= 2, 'O produto precisa ter uma grade simples de planos para converter devs.', 'Billing', 'Revisar limites por uso, precos, trial e upgrade/downgrade.'),
            self::check('Pagina publica de precos', Route::has('pricing') && Schema::hasTable('billing_plans') && BillingPlan::where('active', true)->count() > 0, 'SaaS precisa explicar valor, limites e caminho de conversao antes do cadastro.', 'Billing', 'Revisar copy, precos finais e CTA de checkout/cadastro.'),
            self::check('Email transacional real', ! str_contains((string) config('mail.from.address'), 'example.com') && filled(config('mail.from.address')), 'Cadastro, recuperacao, convites e alertas dependem de remetente confiavel.', 'Comunicacao', 'Configurar provedor, dominio autenticado, SPF, DKIM e DMARC.'),
            self::check('Fila fora de sync', (string) config('queue.default') !== 'sync', 'Webhooks, notificacoes e billing nao devem depender de processamento inline em producao.', 'Operacao', 'Ativar database/redis queue e definir worker supervisionado.'),
            self::check('Documentacao publica suficiente', view()->exists('docs.users') && Schema::hasTable('knowledge_base_articles') && KnowledgeBaseArticle::where('published', true)->count() >= 3, 'Usuarios precisam conseguir configurar GitHub, entender seguranca e resolver duvidas sem suporte manual.', 'Docs', 'Completar guia inicial, billing, seguranca, GitHub App e troubleshooting.'),
            self::check('Ativos publicos de launch', Route::has('sitemap') && Route::has('robots'), 'Sitemap e robots ajudam indexacao, revisao publica e higiene de producao.', 'Launch', 'Revisar URLs finais apos dominio oficial.'),
            self::check('Suporte operacional', Schema::hasTable('support_tickets') && class_exists(SupportSla::class), 'Lancamento sem suporte vira gargalo assim que usuarios reais testarem.', 'Suporte', 'Definir SLA publico, triagem, respostas salvas e rotina diaria.'),
            self::check('Roadmap publico/admin atualizado', Schema::hasTable('roadmap_items') && RoadmapItem::whereIn('status', ['todo', 'doing', 'done'])->count() >= 10, 'Ajuda a explicar maturidade do produto e acompanhar progresso de launch.', 'Gestao', 'Manter prioridades, percentuais e bloqueadores atualizados.'),
            self::check('Changelog publico', Route::has('changelog') && Schema::hasTable('roadmap_items') && RoadmapItem::where('status', 'done')->count() > 0, 'Dev quer ver evolucao do produto, releases e sinais de manutencao ativa.', 'Launch', 'Manter entradas relevantes e remover detalhes internos quando necessario.'),
            self::check('Status publico e trilha de auditoria', Route::has('status') && Schema::hasTable('audit_logs') && class_exists(AuditTrail::class), 'Dev confia mais quando ve saude do sistema e quando a plataforma registra acoes sensiveis.', 'Confianca', 'Publicar status, incidentes, auditoria e politicas operacionais.'),
        ]);

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);
        $blockers = $checks->where('done', false)->values();

        return [
            'percent' => (int) round(($done / $total) * 100),
            'ready' => $blockers->isEmpty(),
            'checks' => $checks,
            'blockers' => $blockers,
            'summary' => [
                'done' => $done,
                'total' => $total,
                'blockers' => $blockers->count(),
                'commercial' => $checks->whereIn('area', ['Billing', 'Comunicacao'])->where('done', true)->count(),
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area, string $nextAction): array
    {
        return compact('title', 'done', 'detail', 'area', 'nextAction');
    }
}
