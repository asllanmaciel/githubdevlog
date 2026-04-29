<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Env;

final class OpenAIService
{
    public function generateDevLog(array $repository, array $commits, string $fromSha, string $toSha, string $pusherName): array
    {
        if (empty($commits)) {
            return [
                'summary' => 'Nenhum commit recebido nesse push.',
                'provider' => 'local-fallback',
                'model' => 'none',
                'usage' => null,
            ];
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $this->buildUserPrompt($repository, $commits, $fromSha, $toSha, $pusherName)],
        ];

        $apiKey = Env::get('OPENAI_API_KEY', '');
        $model = Env::get('OPENAI_MODEL', 'gpt-4o-mini');

        if (empty($apiKey)) {
            return $this->fallbackSummary($commits, $repository['full_name'] ?? 'repositório');
        }

        $body = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 600,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        if ($ch === false) {
            return $this->fallbackSummary($commits, $repository['full_name'] ?? 'repositório');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $apiKey",
            ],
            CURLOPT_TIMEOUT => (int) (int) Env::get('OPENAI_TIMEOUT_SECONDS', '25'),
        ]);

        $response = curl_exec($ch);
        if (!is_string($response)) {
            curl_close($ch);
            return $this->fallbackSummary($commits, $repository['full_name'] ?? 'repositório');
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $payload = json_decode($response, true);
        curl_close($ch);

        if ($status !== 200 || !is_array($payload)) {
            return $this->fallbackSummary($commits, $repository['full_name'] ?? 'repositório');
        }

        $content = trim((string) ($payload['choices'][0]['message']['content'] ?? ''));
        if ($content === '') {
            return $this->fallbackSummary($commits, $repository['full_name'] ?? 'repositório');
        }

        return [
            'summary' => $content,
            'provider' => 'openai',
            'model' => (string) ($payload['model'] ?? $model),
            'usage' => $payload['usage'] ?? null,
        ];
    }

    private function buildUserPrompt(array $repository, array $commits, string $fromSha, string $toSha, string $pusherName): string
    {
        $commitLines = [];
        foreach ($commits as $commit) {
            $sha = $commit['sha'] ?? '';
            $msg = $commit['message'] ?? '';
            $author = trim(($commit['author_name'] ?? '') . ' <' . ($commit['author_email'] ?? '') . '>');
            $commitLines[] = "- {$sha} | {$msg} | {$author}";
        }

        return "Projeto: {$repository['full_name']}\n"
            . "Autor do push: {$pusherName}\n"
            . "Intervalo: {$fromSha} -> {$toSha}\n"
            . "Commits:\n"
            . implode("\n", $commitLines)
            . "\n\n"
            . "Gere um devlog técnico em português com formato Markdown usando:\n"
            . "## Resumo\n## Impacto\n## Principais mudanças\n## Riscos e próximos passos\n"
            . "Use linguagem objetiva e priorize clareza para revisão humana.\n";
    }

    private function systemPrompt(): string
    {
        return 'Você é um engenheiro de software especialista em documentação técnica de evolução de projeto.';
    }

    private function fallbackSummary(array $commits, string $repo): array
    {
        $count = count($commits);
        $highlights = [];
        foreach ($commits as $commit) {
            $msg = (string) ($commit['message'] ?? '');
            if ($msg !== '') {
                $highlights[] = $msg;
            }
        }

        $highlights = array_slice($highlights, 0, 8);
        $list = count($highlights) > 0 ? implode("\n- ", $highlights) : 'Sem mensagens de commit.';
        $summary = "## Resumo\nResumo manual (fallback): {$count} commit(s) recebido(s) em {$repo}\n\n"
            . "## Principais mudanças\n- {$list}\n\n"
            . "## Próximos passos\n- Revisar com cuidado os arquivos alterados e publicar após validação.\n\n"
            . "Nota: resumo gerado sem OpenAI (sem chave configurada).";

        return [
            'summary' => $summary,
            'provider' => 'local-fallback',
            'model' => 'fallback',
            'usage' => null,
        ];
    }
}
