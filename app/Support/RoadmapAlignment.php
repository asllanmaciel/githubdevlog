<?php

namespace App\Support;

use Illuminate\Support\Collection;

class RoadmapAlignment
{
    public static function report(Collection $roadmap): array
    {
        $metadata = collect(self::metadata());
        $items = $roadmap->map(function ($item) use ($metadata) {
            $extra = $metadata->get($item->title, self::defaultMetadata());

            return [
                'record' => $item,
                'kind' => $extra['kind'],
                'stage' => $extra['stage'],
                'evidence' => $extra['evidence'],
                'next_action' => $extra['next_action'],
                'link' => $extra['link'],
                'done' => $item->status === 'done',
            ];
        });

        $pending = $items->where('done', false);
        $localPending = $pending->where('kind', 'local');
        $externalPending = $pending->where('kind', 'externo');

        return [
            'items' => $items,
            'pending' => $pending->values(),
            'local_pending' => $localPending->values(),
            'external_pending' => $externalPending->values(),
            'next_focus' => $pending
                ->sortBy(fn ($item) => [
                    $item['kind'] === 'local' ? 0 : 1,
                    $item['record']->priority === 'alta' ? 0 : 1,
                    $item['record']->position,
                ])
                ->take(5)
                ->values(),
            'stages' => $items->groupBy('stage')->map(fn ($stageItems) => [
                'total' => $stageItems->count(),
                'done' => $stageItems->where('done', true)->count(),
                'pending' => $stageItems->where('done', false)->count(),
            ]),
        ];
    }

    public static function metadata(): array
    {
        return [
            'Manifesto de produto e trilha de impacto' => self::meta('local', 'Base concluida', 'Landing, docs publicas e narrativa GitHub-first publicadas.', 'Revisar texto final apos dominio e GitHub App oficiais.', '/'),
            'Arquitetura tecnica de referencia' => self::meta('local', 'Base concluida', 'Laravel, Filament, modelos SaaS, billing, webhooks, audit trail e suportes separados.', 'Manter decisoes tecnicas em docs conforme o produto amadurecer.', '/admin/docs'),
            'Roteiro de dados e privacidade por default' => self::meta('local', 'Base concluida', 'Politicas publicas, exportacao/purge de workspace, retencao e sanitizacao de payload.', 'Revisar juridicamente antes de abrir publico.', '/privacy'),
            'Roadmap visual e governanca de prioridades' => self::meta('local', 'Base concluida', 'Roadmap sincronizavel, agrupado por area, com alinhamento de foco.', 'Usar esta tela como ritual antes de cada deploy publico.', '/admin/roadmap'),
            'Metricas de produto com decisoes acionaveis' => self::meta('local', 'Produto operavel', 'Painel de metricas de produto com funil, receita, webhooks e riscos.', 'Acompanhar dados reais depois dos primeiros usuarios externos.', '/admin/product-metrics'),
            'MVP administrativo com acessibilidade e clareza' => self::meta('local', 'Produto operavel', 'Admin com centro operacional, status, incidentes, docs, billing, roadmap e recursos separados.', 'Polir microcopy e contraste apos feedback de uso real.', '/admin'),
            'Fluxo de autenticacao robusto e protecao antifraude' => self::meta('local', 'Produto operavel', 'Rate limit em login/cadastro e painel de seguranca de autenticacao.', 'Adicionar recuperacao de senha e verificacao de email quando o mailer final estiver ativo.', '/admin/auth-security'),
            'Hardening de webhooks e tolerancia a falhas' => self::meta('local', 'Produto operavel', 'Idempotencia por entrega, rejeicoes auditadas e painel de hardening.', 'Evoluir para fila de reprocessamento quando houver volume real.', '/admin/webhook-hardening'),
            'Catalogo de qualidade de codigo e revisao continua' => self::meta('local', 'Base concluida', 'CI no GitHub Actions, PR template, issue templates, checklist de release e processo de branch/commit documentados.', 'Normalizar baseline Pint e exigir branch protection quando houver time ou contribuidores externos.', '/admin/docs'),
            'Camada de eventos AI de baixa latencia' => self::meta('local', 'Produto operavel', 'Analisador local, provider LLM plugado, billing de AI e acao no dashboard.', 'Automatizar analise de risco alto em planos pagos.', '/admin/ai-product-readiness'),
            'Roteiro de onboarding inteligente' => self::meta('local', 'Produto operavel', 'Componente de onboarding no dashboard orienta URL, secret, eventos e primeiro teste.', 'Personalizar passos por papel e por GitHub App conectado.', '/dashboard'),
            'Painel de incidentes e treino operacional' => self::meta('local', 'Produto operavel', 'System status, incident center, bug monitor, runbooks e deploy webhook.', 'Rodar simulado de incidente antes do launch publico.', '/admin/incident-center'),
            'Estrategia de APIs publicas para parceiros' => self::meta('local', 'Proxima entrega', 'Docs de API existem, mas ainda falta contrato autenticado para parceiros.', 'Definir tokens de app, escopos, cotas e versionamento.', '/docs/api'),
            'Plano de internacionalizacao e localizacao' => self::meta('local', 'Proxima fase', 'Produto esta em pt-BR; datas e textos ainda nao estao preparados para multilanguage.', 'Criar camada de traducao e padronizar timezone por workspace.', null),
            'Programa de sucesso do cliente e retencao' => self::meta('local', 'Proxima fase', 'Suporte, notificacoes e billing existem; falta playbook de retencao.', 'Criar marcos de ativacao, campanhas e motivos de cancelamento analisaveis.', '/admin/support-operations'),
            'Escala de infraestrutura e custo previsivel' => self::meta('externo', 'Bloqueio operacional', 'Docker local existe e checks de producao apontam queue/cache/worker.', 'Ativar worker supervisionado, cache persistente e rotina de custo no servidor.', '/admin/production-environment'),
            'Modelo de assinatura com valor percebido por uso' => self::meta('local', 'Produto operavel', 'Mercado Pago producao, plano de teste, webhooks assinados e historico no painel.', 'Revisar precos finais antes de divulgacao publica.', '/admin/mercado-pago-readiness'),
            'Certificacao GitHub e prova de maturidade' => self::meta('externo', 'Bloqueio externo', 'Readiness e submissao existem, mas dependem de GitHub App oficial e evidencias finais.', 'Configurar app oficial, logo, permissoes, callback e demo real.', '/admin/github-readiness'),
            'Lancamento publico com monitoramento 24/7' => self::meta('externo', 'Bloqueio externo', 'Launch center e status existem, mas faltam monitoria externa e rotina de plantao.', 'Fechar dominio, email, worker, GitHub App e runbook de abertura publica.', '/admin/launch-center'),
        ];
    }

    private static function meta(string $kind, string $stage, string $evidence, string $nextAction, ?string $link): array
    {
        return [
            'kind' => $kind,
            'stage' => $stage,
            'evidence' => $evidence,
            'next_action' => $nextAction,
            'link' => $link,
        ];
    }

    private static function defaultMetadata(): array
    {
        return self::meta('local', 'Nao classificado', 'Sem evidencia mapeada.', 'Classificar evidencias e proxima acao.', null);
    }
}
