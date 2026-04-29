<?php

namespace App\Console\Commands;

use App\Support\WebhookRetention;
use Illuminate\Console\Command;

class DevlogPruneWebhookEvents extends Command
{
    protected $signature = 'devlog:prune-webhook-events {--dry-run : Simula a limpeza sem apagar eventos} {--json : Emitir resultado em JSON}';

    protected $description = 'Remove webhooks antigos conforme a retencao configurada no plano do workspace.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = WebhookRetention::prune($dryRun);

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('GitHub DevLog AI - Retencao de webhooks');
        $this->line('Modo: '.($dryRun ? 'simulacao' : 'limpeza real'));
        $this->line('Workspaces analisados: '.$result['checked_workspaces']);
        $this->line(($dryRun ? 'Eventos que seriam removidos: ' : 'Eventos removidos: ').$result['deleted_events']);

        if ($dryRun) {
            $this->warn('Nada foi apagado. Rode sem --dry-run para executar a limpeza.');
        }

        return self::SUCCESS;
    }
}