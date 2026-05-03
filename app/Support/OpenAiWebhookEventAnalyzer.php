<?php

namespace App\Support;

use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAiWebhookEventAnalyzer
{
    public function configured(): bool
    {
        return filled(config('services.openai.api_key'));
    }

    public function analyze(WebhookEvent $event): array
    {
        if (! $this->configured()) {
            throw new \RuntimeException('OPENAI_API_KEY não configurada para análise AI avançada.');
        }

        $payload = $event->payload ?? [];
        $context = [
            'event_name' => $event->event_name,
            'action' => $event->action,
            'source' => $event->source,
            'signature_valid' => $event->signature_valid,
            'repository' => data_get($payload, 'repository.full_name'),
            'sender' => data_get($payload, 'sender.login', data_get($payload, 'pusher.name')),
            'ref' => data_get($payload, 'ref'),
            'head_commit' => data_get($payload, 'head_commit'),
            'commits' => collect(data_get($payload, 'commits', []))->take(8)->values()->all(),
            'pull_request' => data_get($payload, 'pull_request') ? [
                'title' => data_get($payload, 'pull_request.title'),
                'state' => data_get($payload, 'pull_request.state'),
                'changed_files' => data_get($payload, 'pull_request.changed_files'),
                'additions' => data_get($payload, 'pull_request.additions'),
                'deletions' => data_get($payload, 'pull_request.deletions'),
            ] : null,
        ];

        $response = Http::withToken(config('services.openai.api_key'))
            ->timeout((int) config('services.openai.timeout', 20))
            ->acceptJson()
            ->post(rtrim((string) config('services.openai.base_url'), '/').'/responses', [
                'model' => config('services.openai.model'),
                'input' => $this->prompt($context),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI retornou erro '.$response->status().': '.Str::limit($response->body(), 220));
        }

        $body = $response->json();
        $text = data_get($body, 'output_text') ?: $this->extractText($body);
        $analysis = $this->decodeAnalysis($text);
        $usage = data_get($body, 'usage', []);

        return [
            'summary' => $analysis['summary'],
            'risk_level' => $analysis['risk_level'],
            'action_items' => $analysis['action_items'],
            'signals' => $analysis['signals'],
            'provider' => 'openai:'.config('services.openai.model'),
            'type' => 'llm',
            'estimated_cost_cents' => (int) config('services.openai.estimated_cost_cents', 15),
            'input_tokens' => data_get($usage, 'input_tokens'),
            'output_tokens' => data_get($usage, 'output_tokens'),
            'generated_at' => now(),
        ];
    }

    private function prompt(array $context): string
    {
        return "Você é o analista AI do GitHub DevLog AI. Analise o webhook abaixo para um dev. Responda apenas JSON válido com as chaves: summary (string em pt-BR), risk_level (low|medium|high), signals (array de strings), action_items (array de strings). Seja objetivo, útil e conservador em risco.\n\nWebhook:\n".json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function extractText(array $body): string
    {
        return collect(data_get($body, 'output', []))
            ->flatMap(fn ($item) => data_get($item, 'content', []))
            ->map(fn ($content) => data_get($content, 'text'))
            ->filter()
            ->implode("\n");
    }

    private function decodeAnalysis(string $text): array
    {
        $clean = trim($text);
        $clean = preg_replace('/^```json\s*|```$/m', '', $clean) ?: $clean;
        $data = json_decode($clean, true);

        if (! is_array($data)) {
            throw new \RuntimeException('Resposta AI não veio em JSON válido.');
        }

        return [
            'summary' => (string) ($data['summary'] ?? 'Análise avançada concluída.'),
            'risk_level' => in_array(($data['risk_level'] ?? 'medium'), ['low', 'medium', 'high'], true) ? $data['risk_level'] : 'medium',
            'signals' => array_values(array_filter((array) ($data['signals'] ?? []))),
            'action_items' => array_values(array_filter((array) ($data['action_items'] ?? []))),
        ];
    }
}
