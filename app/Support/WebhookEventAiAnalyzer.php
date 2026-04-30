<?php

namespace App\Support;

use App\Models\WebhookEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WebhookEventAiAnalyzer
{
    public function analyze(WebhookEvent $event): array
    {
        $payload = $event->payload ?? [];
        $repo = data_get($payload, 'repository.full_name', 'repositório não informado');
        $sender = data_get($payload, 'sender.login', data_get($payload, 'pusher.name', 'GitHub'));
        $branch = $this->branch($payload);
        $files = $this->changedFiles($payload);
        $commits = collect(data_get($payload, 'commits', []));
        $eventName = (string) $event->event_name;
        $action = $event->action ?: data_get($payload, 'action');
        $signals = $this->signals($event, $files, $commits);
        $risk = $this->riskLevel($signals);

        return [
            'summary' => $this->summary($eventName, $action, $repo, $sender, $branch, $files, $commits, $risk),
            'risk_level' => $risk,
            'action_items' => $this->actionItems($event, $risk, $signals),
            'signals' => $signals,
            'provider' => 'local-devlog-ai-v1',
            'generated_at' => now(),
        ];
    }

    private function summary(string $eventName, ?string $action, string $repo, string $sender, string $branch, Collection $files, Collection $commits, string $risk): string
    {
        $parts = [];
        $parts[] = ucfirst($eventName).($action ? ' '.$action : '').' recebido em '.$repo;
        $parts[] = 'Origem: '.$sender.($branch !== 'n/a' ? ' na branch '.$branch : '');

        if ($commits->isNotEmpty()) {
            $message = Str::of((string) data_get($commits->first(), 'message', ''))->replace("\n", ' ')->limit(140);
            $parts[] = $commits->count().' commit(s) detectado(s)'.($message->isNotEmpty() ? ', começando por "'.$message.'"' : '');
        }

        if ($files->isNotEmpty()) {
            $parts[] = $files->count().' arquivo(s) relevante(s) alterado(s): '.$files->take(5)->implode(', ');
        }

        $parts[] = 'Risco estimado: '.$this->riskLabel($risk).'.';

        return implode('. ', array_filter($parts));
    }

    private function signals(WebhookEvent $event, Collection $files, Collection $commits): array
    {
        $signals = [];

        if (! $event->signature_valid) {
            $signals[] = 'Assinatura ausente ou inválida';
        }

        if ($files->isEmpty() && $commits->isEmpty()) {
            $signals[] = 'Payload sem lista clara de arquivos ou commits';
        }

        $patterns = [
            'env/configuração sensível' => ['.env', 'config/', 'services.php', 'auth.php'],
            'banco de dados' => ['migration', 'database/', 'schema'],
            'autenticação/autorização' => ['auth', 'login', 'password', 'permission', 'policy', 'middleware'],
            'billing/pagamentos' => ['billing', 'payment', 'mercado', 'checkout', 'subscription', 'invoice'],
            'webhook/integração' => ['webhook', 'github', 'signature', 'payload'],
        ];

        foreach ($patterns as $label => $needles) {
            if ($files->contains(fn ($file) => Str::contains(Str::lower((string) $file), $needles))) {
                $signals[] = 'Alteração em '.$label;
            }
        }

        if ($commits->contains(fn ($commit) => Str::contains(Str::lower((string) data_get($commit, 'message')), ['fix', 'hotfix', 'security', 'bug', 'rollback']))) {
            $signals[] = 'Mensagem de commit sugere correção, segurança ou rollback';
        }

        return array_values(array_unique($signals));
    }

    private function riskLevel(array $signals): string
    {
        $criticalSignals = collect($signals)->filter(fn ($signal) => Str::contains($signal, ['sensível', 'pagamentos', 'Assinatura ausente']));
        $mediumSignals = collect($signals)->filter(fn ($signal) => Str::contains($signal, ['banco de dados', 'autenticação', 'webhook', 'rollback']));

        if ($criticalSignals->isNotEmpty()) {
            return 'high';
        }

        if ($mediumSignals->count() >= 1 || count($signals) >= 3) {
            return 'medium';
        }

        return 'low';
    }

    private function actionItems(WebhookEvent $event, string $risk, array $signals): array
    {
        $items = [];

        if (! $event->signature_valid) {
            $items[] = 'Conferir o Secret configurado no GitHub antes de confiar neste payload.';
        }

        if ($risk === 'high') {
            $items[] = 'Revisar este evento com um responsável antes de acionar automações.';
        }

        if (collect($signals)->contains(fn ($signal) => Str::contains($signal, 'pagamentos'))) {
            $items[] = 'Validar impacto em billing, checkout, assinatura e webhooks financeiros.';
        }

        if (collect($signals)->contains(fn ($signal) => Str::contains($signal, 'banco de dados'))) {
            $items[] = 'Confirmar migrations, rollback e compatibilidade com produção.';
        }

        if ($items === []) {
            $items[] = 'Nenhuma ação crítica detectada. Use o resumo para triagem rápida e registre nota se necessário.';
        }

        return array_values(array_unique($items));
    }

    private function changedFiles(array $payload): Collection
    {
        return collect(data_get($payload, 'head_commit.modified', []))
            ->merge(data_get($payload, 'head_commit.added', []))
            ->merge(data_get($payload, 'head_commit.removed', []))
            ->merge(collect(data_get($payload, 'commits', []))->flatMap(fn ($commit) => collect(data_get($commit, 'modified', []))->merge(data_get($commit, 'added', []))->merge(data_get($commit, 'removed', []))))
            ->filter()
            ->unique()
            ->values();
    }

    private function branch(array $payload): string
    {
        $ref = (string) data_get($payload, 'ref', '');

        return str_replace('refs/heads/', '', $ref) ?: data_get($payload, 'pull_request.head.ref', 'n/a');
    }

    private function riskLabel(string $risk): string
    {
        return [
            'high' => 'alto',
            'medium' => 'médio',
            'low' => 'baixo',
        ][$risk] ?? $risk;
    }
}