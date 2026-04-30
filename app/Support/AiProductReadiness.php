<?php

namespace App\Support;

use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Schema;

class AiProductReadiness
{
    public static function report(): array
    {
        $schemaReady = Schema::hasColumn('webhook_events', 'ai_summary')
            && Schema::hasColumn('webhook_events', 'ai_risk_level')
            && Schema::hasColumn('webhook_events', 'ai_action_items');

        $totalEvents = Schema::hasTable('webhook_events') ? WebhookEvent::count() : 0;
        $analyzedEvents = $schemaReady ? WebhookEvent::whereNotNull('ai_generated_at')->count() : 0;
        $coverage = $totalEvents > 0 ? (int) round(($analyzedEvents / $totalEvents) * 100) : 0;

        $checks = collect([
            self::check('Campos persistidos', $schemaReady, 'Resumo, risco, sinais, ações e provider ficam salvos no evento.'),
            self::check('Analisador local', class_exists(WebhookEventAiAnalyzer::class), 'Provider local-devlog-ai-v1 gera valor sem depender de chave externa.'),
            self::check('Ação no dashboard', route('events.ai-analysis.generate', ['event' => 1], false) !== '', 'Usuário pode gerar análise AI a partir do card do webhook.'),
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
                'coverage' => $coverage,
                'risk_distribution' => $schemaReady
                    ? WebhookEvent::whereNotNull('ai_risk_level')->selectRaw('ai_risk_level, count(*) as total')->groupBy('ai_risk_level')->pluck('total', 'ai_risk_level')->all()
                    : [],
            ],
            'next_steps' => [
                'Adicionar provider LLM opcional para análises mais profundas.',
                'Gerar análise automaticamente em eventos de risco alto ou billing.',
                'Criar filtros por risco e por eventos sem análise no dashboard.',
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail): array
    {
        return compact('title', 'done', 'detail');
    }
}