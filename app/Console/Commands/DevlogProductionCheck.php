<?php

namespace App\Console\Commands;

use App\Support\ProductionEnvironment;
use Illuminate\Console\Command;

class DevlogProductionCheck extends Command
{
    protected $signature = 'devlog:production-check {--json : Emitir diagnostico em JSON}';

    protected $description = 'Confere as configuracoes obrigatorias para publicar o SaaS em producao.';

    public function handle(): int
    {
        $report = ProductionEnvironment::report();

        $payload = [
            'ok' => $report['ready'],
            'percent' => $report['percent'],
            'done' => $report['done'],
            'total' => $report['total'],
            'required_pending' => $report['required_pending']->values(),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $payload['ok'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Production Check');
        $this->line('Producao: '.$report['percent'].'% ('.$report['done'].'/'.$report['total'].' checks)');
        $this->newLine();

        foreach ($report['groups'] as $group => $items) {
            $this->line($group.':');
            foreach ($items as $item) {
                $this->line(' - '.($item['done'] ? '[ok] ' : '[!!] ').$item['title'].' ('.$item['detail'].')'.($item['required'] ? ' [obrigatorio]' : ' [recomendado]'));
            }
            $this->newLine();
        }

        if ($report['required_pending']->isNotEmpty()) {
            $this->warn('Pendencias obrigatorias:');
            foreach ($report['required_pending'] as $pending) {
                $this->line(' - '.$pending['title'].' ('.$pending['detail'].')');
            }
            $this->newLine();
            $this->error('Ambiente de producao ainda nao esta pronto.');

            return self::FAILURE;
        }

        $this->info('Ambiente de producao pronto para deploy.');

        return self::SUCCESS;
    }
}
