<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use App\Support\WorkspaceUsage;
use Illuminate\Console\Command;

class DevlogSnapshotUsage extends Command
{
    protected $signature = 'devlog:snapshot-usage
        {--period= : Periodo no formato YYYY-MM. Padrao: mes atual}
        {--json : Emitir resumo em JSON}';

    protected $description = 'Gera snapshots mensais de uso por workspace para auditoria e billing.';

    public function handle(): int
    {
        $period = $this->option('period') ?: now()->format('Y-m');
        $summary = [
            'period' => $period,
            'snapshots' => 0,
            'events_count' => 0,
            'overage_count' => 0,
        ];

        Workspace::query()->chunkById(100, function ($workspaces) use ($period, &$summary) {
            foreach ($workspaces as $workspace) {
                $snapshot = WorkspaceUsage::snapshot($workspace, $period);
                $summary['snapshots']++;
                $summary['events_count'] += $snapshot->events_count;
                $summary['overage_count'] += $snapshot->overage_count;
            }
        });

        if ($this->option('json')) {
            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('Snapshots mensais de uso gerados.');
        $this->line('Periodo: '.$summary['period']);
        $this->line('Workspaces: '.$summary['snapshots']);
        $this->line('Eventos: '.$summary['events_count']);
        $this->line('Excedentes: '.$summary['overage_count']);

        return self::SUCCESS;
    }
}
