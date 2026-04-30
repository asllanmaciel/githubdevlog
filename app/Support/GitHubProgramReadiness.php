<?php

namespace App\Support;

use App\Models\BillingPlan;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class GitHubProgramReadiness
{
    public static function report(): array
    {
        $checks = collect([
            self::check('Proposta clara para devs', true, 'Webhook inbox privado, auditavel e focado em GitHub.', 'Produto'),
            self::check('Integracao GitHub documentada', Route::has('webhooks.github') && Route::has('webhooks.github-app'), 'Endpoint manual e caminho para GitHub App existem.', 'Integracao'),
            self::check('Seguranca por assinatura', Route::has('webhooks.github'), 'Fluxo suporta segredo e validacao de payloads GitHub.', 'Seguranca'),
            self::check('Isolamento por workspace', Schema::hasTable('workspaces') && Schema::hasTable('workspace_members'), 'Usuarios nao compartilham eventos fora do workspace.', 'Privacidade'),
            self::check('Onboarding utilizavel', Route::has('register') && Route::has('login'), 'Cadastro, login e painel existem para uso real.', 'UX'),
            self::check('Planos e modelo SaaS', Schema::hasTable('billing_plans') && BillingPlan::count() > 0, 'Modelo comercial por uso pode ser demonstrado.', 'SaaS'),
            self::check('Pagina publica de precos', Route::has('pricing'), 'Modelo comercial fica visivel para devs antes da criacao do workspace.', 'SaaS'),
            self::check('Docs para usuarios', view()->exists('docs.users'), 'Guia de uso orientado ao dev existe.', 'Docs'),
            self::check('Base de conhecimento', Schema::hasTable('knowledge_base_articles') && KnowledgeBaseArticle::where('published', true)->count() >= 3, 'Artigos publicados ajudam suporte e onboarding.', 'Docs'),
            self::check('Status e confianca', Route::has('status'), 'Pagina publica de status existe para operacao.', 'Confianca'),
            self::check('Suporte operacional', Schema::hasTable('support_tickets'), 'Sistema de chamados permite receber feedback e problemas.', 'Suporte'),
            self::check('Politicas publicas', Route::has('privacy') && Route::has('terms') && Route::has('security'), 'Privacidade, termos e seguranca ficam publicados antes da submissao.', 'Confianca'),
        ]);

        $evidence = [
            ['title' => 'URL publica do produto', 'detail' => 'Dominio final com HTTPS, landing page e CTA para cadastro.'],
            ['title' => 'Demo funcional', 'detail' => 'Repositorio GitHub enviando ping/push para um workspace real.'],
            ['title' => 'Descricao curta', 'detail' => 'Uma frase explicando a dor: debug e auditoria de webhooks GitHub sem misturar dados.'],
            ['title' => 'Permissoes solicitadas', 'detail' => 'Listar escopos minimos do GitHub App e por que cada um existe.'],
            ['title' => 'Politicas publicas', 'detail' => 'Privacidade, termos, seguranca e contato de suporte.'],
            ['title' => 'Fluxo comercial', 'detail' => 'Planos, limites, trial e billing Mercado Pago.'],
        ];

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);

        return [
            'percent' => (int) round(($done / $total) * 100),
            'checks' => $checks,
            'missing' => $checks->where('done', false)->values(),
            'evidence' => $evidence,
            'summary' => [
                'done' => $done,
                'total' => $total,
                'missing' => $checks->where('done', false)->count(),
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area): array
    {
        return compact('title', 'done', 'detail', 'area');
    }
}
