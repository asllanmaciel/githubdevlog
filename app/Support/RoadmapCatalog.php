<?php

namespace App\Support;

use App\Models\RoadmapItem;

class RoadmapCatalog
{
    public static function sync(): int
    {
        $synced = 0;

        foreach (self::items() as $item) {
            $roadmapItem = RoadmapItem::firstOrNew(['title' => $item['title']]);

            $roadmapItem->fill([
                'area' => $item['area'],
                'priority' => $item['priority'],
                'description' => $item['description'],
                'position' => $item['position'],
            ]);

            if (! $roadmapItem->exists) {
                $roadmapItem->status = $item['status'];
                $roadmapItem->completed_at = $item['completed_at'];
            }

            if ($roadmapItem->exists && $item['status'] === 'done' && $roadmapItem->status !== 'done') {
                $roadmapItem->status = 'done';
                $roadmapItem->completed_at ??= now();
            }

            if ($roadmapItem->isDirty()) {
                $roadmapItem->save();
                $synced++;
            }
        }

        return $synced;
    }

    public static function expectedTotal(): int
    {
        return count(self::items());
    }

    public static function items(): array
    {
        return [
            self::item('Manifesto de produto e trilha de impacto', 'Estrategia e posicionamento', 'done', 'alta', 'Definir proposta de valor, ICP, criterios de sucesso e o que diferencia a experiencia do DevLog no mercado.', 10),
            self::item('Arquitetura tecnica de referencia', 'Fundacao do sistema', 'done', 'alta', 'Padronizar camadas de dominio, servicos, observabilidade e contratos para evoluir sem retrabalho.', 20),
            self::item('Roteiro de dados e privacidade por default', 'Fundacao do sistema', 'done', 'alta', 'Definir retention policy, LGPD-by-design, anonimizacao e trilha de auditoria para dados sensiveis.', 30),
            self::item('Roadmap visual e governanca de prioridades', 'Governanca', 'done', 'media', 'Criar cadencia de revisao, criterios de entrada e saida e dashboard unico de progresso por iniciativa.', 40),
            self::item('Metricas de produto com decisoes acionaveis', 'Observabilidade e operacao', 'done', 'alta', 'Centralizar funil, ativacao, receita, webhooks, billing e riscos operacionais em um painel acionavel.', 50),
            self::item('MVP administrativo com acessibilidade e clareza', 'Produto e UX', 'done', 'alta', 'Operar admin com centros de status, incidentes, docs, billing, roadmap, seguranca, metricas e suporte sem depender de telas soltas.', 60),
            self::item('Fluxo de autenticacao robusto e protecao antifraude', 'Seguranca', 'done', 'alta', 'Aplicar rate limit em login/cadastro, registrar auditoria de sucesso/falha e expor sinais suspeitos no admin.', 70),
            self::item('Hardening de webhooks e tolerancia a falhas', 'Confiabilidade', 'done', 'alta', 'Registrar rejeicoes com payload minimo, aplicar idempotencia por entrega e expor diagnostico operacional para webhooks criticos.', 80),
            self::item('Catalogo de qualidade de codigo e revisao continua', 'Confiabilidade', 'done', 'media', 'Implantar templates de PR/issues, checklist de release e CI com testes, build e cache de rotas/views; Pint fica documentado por escopo ate baseline geral.', 90),
            self::item('Camada de eventos AI de baixa latencia', 'IA e inteligencia', 'done', 'alta', 'Entregar analise local imediata, provider LLM plugado, billing de AI avancada e acao manual no dashboard sem travar webhooks.', 100),
            self::item('Roteiro de onboarding inteligente', 'Produto e UX', 'done', 'alta', 'Guiar workspace novo por endpoint, content type, secret, eventos recomendados e primeiro webhook validado no dashboard.', 110),
            self::item('Painel de incidentes e treino operacional', 'Observabilidade e operacao', 'done', 'media', 'Operar status do sistema, monitor interno de bugs, runbooks e deploy webhook para reduzir tempo de resposta em incidentes reais.', 120),
            self::item('Estrategia de APIs publicas para parceiros', 'Evolucao de plataforma', 'pending', 'media', 'Publicar contratos claros, autenticacao por aplicativo e cotas para expansao B2B.', 130),
            self::item('Plano de internacionalizacao e localizacao', 'Produto e UX', 'pending', 'media', 'Suportar fusos, datas, idioma e suporte multilingue sem perder consistencia de design.', 140),
            self::item('Programa de sucesso do cliente e retencao', 'Crescimento', 'pending', 'media', 'Mapear marcos de retencao, campanhas de reativacao e upgrade com valor percebido.', 150),
            self::item('Escala de infraestrutura e custo previsivel', 'Escalabilidade', 'pending', 'alta', 'Separar workloads criticos, aplicar caching e criar alertas de custo por componente.', 160),
            self::item('Modelo de assinatura com valor percebido por uso', 'Monetizacao', 'done', 'alta', 'Operar checkout Mercado Pago em producao, plano de teste, assinatura visivel no dashboard e historico de pagamento por workspace.', 170),
            self::item('Certificacao GitHub e prova de maturidade', 'Go-live', 'pending', 'alta', 'Completar dependencias de lancamento, evidencias e documentacao final para submissao.', 180),
            self::item('Lancamento publico com monitoramento 24/7', 'Go-live', 'pending', 'alta', 'Ativar monitoria, guardrails de seguranca e comunicacao publica durante o go-live.', 190),
        ];
    }

    private static function item(string $title, string $area, string $status, string $priority, string $description, int $position): array
    {
        return [
            'title' => $title,
            'area' => $area,
            'status' => $status,
            'priority' => $priority,
            'description' => $description,
            'position' => $position,
            'completed_at' => $status === 'done' ? now() : null,
        ];
    }
}
