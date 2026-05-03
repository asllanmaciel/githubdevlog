<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use App\Support\WorkspaceDataPurge;
use Illuminate\Console\Command;

class DevlogPurgeWorkspaceData extends Command
{
    protected $signature = 'devlog:purge-workspace-data {workspace : ID, UUID ou slug do workspace} {--dry-run : Simula a remocao sem apagar dados} {--force : Confirma a exclusao real} {--json : Retorna JSON}';

    protected $description = 'Remove dados de um workspace para atendimento de exclusao, suporte ou compliance.';

    public function handle(): int
    {
        $identifier = (string) $this->argument('workspace');
        $workspace = Workspace::query()
            ->where('id', $identifier)
            ->orWhere('uuid', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if (! $workspace) {
            $this->error('Workspace nao encontrado: '.$identifier);

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run') || ! (bool) $this->option('force');
        AuditTrail::record($dryRun ? 'workspace.data.purge_simulated' : 'workspace.data.purge_requested', $workspace, $workspace, ['force' => ! $dryRun], null, null, 'system');
        $result = WorkspaceDataPurge::purge($workspace, $dryRun);

        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('GitHub DevLog AI - Exclusao de dados do workspace');
        $this->line('Workspace: '.$workspace->name.' (#'.$workspace->id.')');
        $this->line('Modo: '.($dryRun ? 'simulacao' : 'exclusao real'));
        $this->newLine();

        foreach ($result['deleted'] as $name => $count) {
            $this->line(' - '.$name.': '.$count);
        }

        $this->newLine();
        if ($dryRun) {
            $this->warn('Nada foi apagado. Para excluir de verdade, rode com --force.');
        } else {
            $this->error('Workspace e dados relacionados removidos.');
        }

        return self::SUCCESS;
    }
}
