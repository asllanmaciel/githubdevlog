<?php

namespace App\Console\Commands;

use App\Support\IncidentResponse;
use Illuminate\Console\Command;

class DevlogIncidentCheck extends Command
{
    protected $signature = 'devlog:incident-check {--json : Emitir diagnostico em JSON}';

    protected $description = 'Resume incidentes operacionais de filas, webhooks, billing e suporte.';

    public function handle(): int
    {
        $report = IncidentResponse::report();

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $report['healthy'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Incident Check');
        $this->line('Status: '.($report['healthy'] ? 'saudavel' : 'atencao'));
        $this->newLine();

        foreach ($report['checks'] as $check) {
            $this->line(' - '.($check['done'] ? '[ok] ' : '[!!] ').$check['title'].' ('.$check['detail'].')');
        }

        if ($report['incidents']->isNotEmpty()) {
            $this->newLine();
            $this->warn('Incidentes:');
            foreach ($report['incidents'] as $incident) {
                $this->line(' - ['.$incident['severity'].'] '.$incident['title'].': '.$incident['detail'].' | '.$incident['command']);
            }
        }

        return $report['healthy'] ? self::SUCCESS : self::FAILURE;
    }
}