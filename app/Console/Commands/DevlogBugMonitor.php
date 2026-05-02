<?php

namespace App\Console\Commands;

use App\Support\BugMonitor;
use Illuminate\Console\Command;

class DevlogBugMonitor extends Command
{
    protected $signature = 'devlog:bug-monitor {--json : Emitir resumo em JSON}';

    protected $description = 'Resume bugs capturados automaticamente pelo monitor interno.';

    public function handle(): int
    {
        $report = BugMonitor::report();

        if ($this->option('json')) {
            $this->line(json_encode([
                ...$report,
                'latest' => $report['latest']->values(),
                'top' => $report['top']->values(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $report['open_count'] === 0 ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Bug Monitor');

        if (! $report['available']) {
            $this->warn('Tabela bug_reports ausente. Rode php artisan migrate --force.');

            return self::FAILURE;
        }

        $this->line('Abertos: '.$report['open_count']);
        $this->line('Resolvidos: '.$report['resolved_count']);
        $this->line('Vistos hoje: '.$report['today_count']);

        if ($report['latest']->isNotEmpty()) {
            $this->newLine();
            $this->warn('Ultimos bugs:');

            foreach ($report['latest'] as $bug) {
                $this->line(' - #'.$bug->id.' ['.$bug->level.'] '.$bug->exception_class.' x'.$bug->occurrences.' em '.$bug->last_seen_at?->format('d/m/Y H:i'));
                $this->line('   '.$bug->message);
            }
        }

        return $report['open_count'] === 0 ? self::SUCCESS : self::FAILURE;
    }
}
