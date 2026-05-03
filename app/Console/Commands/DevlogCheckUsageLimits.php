<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Workspace;
use App\Support\WorkspaceUsage;
use Illuminate\Console\Command;

class DevlogCheckUsageLimits extends Command
{
    protected $signature = 'devlog:check-usage-limits {--json : Emitir resumo em JSON}';

    protected $description = 'Verifica workspaces perto do limite mensal e cria alertas preventivos.';

    public function handle(): int
    {
        $summary = [
            'checked' => 0,
            'near_80' => 0,
            'near_95' => 0,
            'blocked' => 0,
            'notifications_created' => 0,
        ];

        Workspace::query()->with(['subscription.plan'])->chunkById(100, function ($workspaces) use (&$summary) {
            foreach ($workspaces as $workspace) {
                $summary['checked']++;
                $report = WorkspaceUsage::report($workspace);

                if ($report['limit_reached']) {
                    $summary['blocked']++;
                    $summary['notifications_created'] += $this->createAlert($workspace, 'usage_limit_reached', 'Limite mensal de webhooks atingido', $report) ? 1 : 0;

                    continue;
                }

                if ($report['percent'] >= 95) {
                    $summary['near_95']++;
                    $summary['notifications_created'] += $this->createAlert($workspace, 'usage_limit_95', 'Workspace chegou a 95% do limite mensal', $report) ? 1 : 0;

                    continue;
                }

                if ($report['percent'] >= 80) {
                    $summary['near_80']++;
                    $summary['notifications_created'] += $this->createAlert($workspace, 'usage_limit_80', 'Workspace chegou a 80% do limite mensal', $report) ? 1 : 0;
                }
            }
        });

        if ($this->option('json')) {
            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('Uso mensal verificado.');
        $this->line('Workspaces checados: '.$summary['checked']);
        $this->line('Alertas 80%: '.$summary['near_80']);
        $this->line('Alertas 95%: '.$summary['near_95']);
        $this->line('Bloqueados: '.$summary['blocked']);
        $this->line('Notificacoes criadas: '.$summary['notifications_created']);

        return self::SUCCESS;
    }

    private function createAlert(Workspace $workspace, string $type, string $title, array $report): bool
    {
        $periodKey = now()->format('Y-m');
        $body = 'Uso atual: '.$report['usage'].'/'.$report['limit'].' eventos ('.$report['percent'].'%) no plano '.($report['plan']?->name ?? 'Free').'.';

        $notification = Notification::firstOrCreate(
            [
                'workspace_id' => $workspace->id,
                'title' => $title.' - '.$periodKey,
                'type' => $type,
            ],
            [
                'body' => $body,
                'read_at' => null,
            ]
        );

        return $notification->wasRecentlyCreated;
    }
}
