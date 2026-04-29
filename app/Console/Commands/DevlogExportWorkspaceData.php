<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use App\Support\WorkspaceDataExport;
use Illuminate\Console\Command;

class DevlogExportWorkspaceData extends Command
{
    protected $signature = 'devlog:export-workspace-data {workspace : ID ou UUID do workspace} {--output= : Caminho relativo em storage/app para salvar o JSON} {--json : Retorna apenas JSON com status e arquivo}';

    protected $description = 'Exporta um pacote JSON de dados do workspace para suporte, auditoria ou portabilidade.';

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

        $path = WorkspaceDataExport::store($workspace, $this->option('output'));
        AuditTrail::record('workspace.data.exported', $workspace, $workspace, ['file' => $path], null, null, 'system');
        $payload = ['ok' => true, 'workspace_id' => $workspace->id, 'file' => $path];

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('Exportacao concluida.');
        $this->line('Workspace: '.$workspace->name.' (#'.$workspace->id.')');
        $this->line('Arquivo: '.$path);

        return self::SUCCESS;
    }
}