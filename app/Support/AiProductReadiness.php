<?php

namespace App\Support;

use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AiProductReadiness
{
    public static function report(): array
    {
        $schemaReady = Schema::hasColumn('webhook_events', 'ai_summary')
            && Schema::hasColumn('webhook_events', 'ai_risk_level')
            && Schema::hasColumn('webhook_events', 'ai_action_items')
            && Schema::hasColumn('webhook_events', 'ai_analysis_type');

        $totalEvents = Schema::hasTable('webhook_events') ? WebhookEvent::count() : 0;
        $analyzedEvents = $schemaReady ? WebhookEvent::whereNotNull('ai_generated_at')->count() : 0;
        $advancedEvents = $schemaReady ? WebhookEvent::where('ai_analysis_type', 'llm')->count() : 0;
        $coverage = $totalEvents > 0 ? (int) round(($analyzedEvents / $totalEvents) * 100) : 0;

        $checks = collect([
            self::check('Campos persistidos', $schemaReady, 'Resumo, risco, sinais, ações, provider, tipo e custo ficam salvos no evento.'),
            self::check('Analisador local grátis', class_exists(WebhookEventAiAnalyzer::class), 'Provider local-devlog-ai-v1 gera valor sem depender de chave externa.'),
            self::check('Provider LLM plugado', class_exists(OpenAiWebhookEventAnalyzer::class), 'Serviço OpenAI pronto para análise avançada paga.'),
            self::check('OPENAI_API_KEY configurável', filled(config('services.openai.api_key')) || app()->environment('local'), 'Ambiente aceita chave real sem alterar código.'),
            self::check('Ação no dashboard', Route::has('events.ai-analysis.generate'), 'Usuário pode gerar AI grátis ou avançada a partir do card do webhook.'),
            self::check('Billing de AI avançada', class_exists(AiAnalysisBilling::class), 'Planos controlam limite mensal e custo estimado de análises LLM.'),
            self::check('Auditoria da geração', class_exists(AuditTrail::class), 'Toda análise gerada registra trilha para governança.'),
            self::check('Documentação AI', file_exists(base_path('docs/ai-event-analysis.md')), 'Há documentação do recurso e caminho de evolução para LLM.'),
        ]);

        $done = $checks->where('done', true)->count();
        $total = max($checks->count(), 1);

        return [
            'percent' => (int) round(($done / $total) * 100),
            'ready' => $done === $total,
            'done' => $done,
            'total' => $total,
            'checks' => $checks,
            'usage' => [
                'total_events' => $totalEvents,
                'analyzed_events' => $analyzedEvents,
                'advanced_events' => $advancedEvents,
                'coverage' => $coverage,
                'estimated_cost_cents' => $schemaReady ? (int) WebhookEvent::sum('ai_estimated_cost_cents') : 0,
                'risk_distribution' => $schemaReady
                    ? WebhookEvent::whereNotNull('ai_risk_level')->selectRaw('ai_risk_level, count(*) as total')->groupBy('ai_risk_level')->pluck('total', 'ai_risk_level')->all()
                    : [],
            ],
            'next_steps' => [
                'Definir preços finais por pacote de análises AI avançadas.',
                'Gerar análise avançada automaticamente para eventos de risco alto em planos pagos.',
                'Criar filtros por risco e por eventos sem análise no dashboard.',
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail): array
    {
        return compact('title', 'done', 'detail');
    }
}
