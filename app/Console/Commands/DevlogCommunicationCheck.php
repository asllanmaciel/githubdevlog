<?php

namespace App\Console\Commands;

use App\Support\CommunicationReadiness;
use Illuminate\Console\Command;

class DevlogCommunicationCheck extends Command
{
    protected $signature = 'devlog:communication-check {--json : Emitir JSON}';

    protected $description = 'Verifica prontidao de e-mail, convites e comunicacao transacional.';

    public function handle(): int
    {
        $report = CommunicationReadiness::report();

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $report['ready'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Communication Check');
        $this->line('Prontidao: '.$report['percent'].'%');
        $this->line('Mailer: '.$report['metrics']['mailer']);
        $this->line('Remetente: '.$report['metrics']['from_address']);
        $this->line('Convites pendentes: '.$report['metrics']['pending_invites']);
        $this->line('Falhas de envio: '.$report['metrics']['failed_deliveries']);
        $this->newLine();

        foreach ($report['checks'] as $check) {
            $this->line(' - '.($check['done'] ? '[ok] ' : '[!!] ').$check['title'].': '.$check['detail']);
        }

        $this->newLine();
        $this->line('Proximos passos:');
        foreach ($report['next_steps'] as $step) {
            $this->line(' - '.$step);
        }

        return $report['ready'] ? self::SUCCESS : self::FAILURE;
    }
}
