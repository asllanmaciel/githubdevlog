<?php

namespace App\Support;

use App\Models\WebhookEvent;
use App\Models\Workspace;

class WebhookEventAiAnalysisService
{
    public function __construct(
        private readonly WebhookEventAiAnalyzer $localAnalyzer,
        private readonly OpenAiWebhookEventAnalyzer $openAiAnalyzer,
    ) {}

    public function analyze(WebhookEvent $event, Workspace $workspace, string $mode = 'local'): array
    {
        if ($mode !== 'llm') {
            $analysis = $this->localAnalyzer->analyze($event);
            $analysis['type'] = 'local';
            $analysis['estimated_cost_cents'] = 0;
            $analysis['input_tokens'] = null;
            $analysis['output_tokens'] = null;
            $analysis['error'] = null;

            return $analysis;
        }

        if (! AiAnalysisBilling::canUseAdvanced($workspace)) {
            throw new \RuntimeException('Seu plano não possui análises AI avançadas disponíveis neste mês. Faça upgrade para usar LLM.');
        }

        $analysis = $this->openAiAnalyzer->analyze($event);
        $analysis['error'] = null;

        return $analysis;
    }
}