<?php

namespace App\Console\Commands;

use App\Support\GoLiveReadiness;
use Illuminate\Console\Command;

class DevlogGoLiveCheck extends Command
{
    protected $signature = 'devlog:go-live-check {--json : Exibe o relatorio em JSON}';

    protected $description = 'Mostra a prontidao de go-live e os bloqueadores atuais do GitHub DevLog AI.';

    public function handle(): int
    {
        $report = GoLiveReadiness::report();

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $report['ready'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Go-live readiness');
        $this->line('Prontidão: '.$report['percent'].'%');
        $this->line('Critérios atendidos: '.$report['summary']['done'].'/'.$report['summary']['total']);
        $this->line('Bloqueadores: '.$report['summary']['blockers']);
        $this->newLine();

        if ($report['ready']) {
            $this->info('Nenhum bloqueador crítico encontrado.');

            return self::SUCCESS;
        }

        $this->warn('Bloqueadores atuais:');

        foreach ($report['blockers'] as $blocker) {
            $this->line('- '.$blocker['title'].' ['.$blocker['area'].']');
            $this->line('  '.$blocker['detail']);
            $this->line('  Próximo passo: '.$blocker['nextAction']);
        }

        return self::FAILURE;
    }
}
