<?php

namespace App\Console\Commands;

use App\Support\MercadoPagoSetup;
use Illuminate\Console\Command;

class DevlogMercadoPagoCheck extends Command
{
    protected $signature = 'devlog:mercado-pago-check {--json : Emitir diagnostico em JSON}';

    protected $description = 'Mostra o checklist operacional para ativar Mercado Pago em producao.';

    public function handle(): int
    {
        $setup = MercadoPagoSetup::report();
        $pendingEnv = collect($setup['env'])->where('done', false)->values();
        $pendingSteps = collect($setup['steps'])->where('done', false)->values();

        $payload = [
            'ok' => $pendingEnv->isEmpty() && $pendingSteps->isEmpty(),
            'percent' => $setup['percent'],
            'pending_env' => $pendingEnv,
            'pending_steps' => $pendingSteps,
            'summary' => $setup['summary'],
            'urls' => $setup['urls'],
            'env_snippet' => $setup['env_snippet'],
            'test_plan' => $setup['test_plan'],
        ];

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $payload['ok'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Mercado Pago Production Check');
        $this->newLine();

        $this->line('URLs para configurar no Mercado Pago:');
        foreach ($setup['urls'] as $url) {
            $this->line(' - '.$url['label'].': '.$url['value']);
        }

        $this->newLine();
        $this->line('Variaveis .env:');
        foreach ($setup['env'] as $env) {
            $this->line(' - '.($env['done'] ? '[ok] ' : '[!!] ').$env['key'].': '.$env['value']);
        }

        $this->newLine();
        $this->line('Plano de teste:');
        foreach ($setup['test_plan'] as $item) {
            $this->line(' - '.$item);
        }

        $this->newLine();
        $this->line('Snippet .env sugerido:');
        $this->line($setup['env_snippet']);

        if ($pendingSteps->isNotEmpty()) {
            $this->newLine();
            $this->warn('Pendencias para destravar cobranca real:');
            foreach ($pendingSteps as $step) {
                $this->line(' - '.$step['title'].' ('.$step['detail'].')');
            }
        }

        $this->newLine();
        if ($payload['ok']) {
            $this->info('Mercado Pago pronto para cobranca real controlada.');
        } else {
            $this->error('Mercado Pago ainda possui pendencias obrigatorias.');
        }

        return $payload['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
