<?php

namespace App\Console\Commands;

use App\Support\LaunchReadiness;
use App\Support\ProductionEnvironment;
use App\Support\SecurityPosture;
use App\Support\SystemHealth;
use Illuminate\Console\Command;

class DevlogPreflight extends Command
{
    protected $signature = 'devlog:preflight
        {--json : Emitir resultado em JSON para automacao}
        {--strict : Reprovar quando existir qualquer bloqueador obrigatorio}
        {--min-security=75 : Percentual minimo de seguranca}
        {--min-launch=70 : Percentual minimo de readiness de lancamento}';

    protected $description = 'Executa checks estruturais antes de demo, deploy ou submissao.';

    public function handle(): int
    {
        $health = SystemHealth::report();
        $security = SecurityPosture::report();
        $launch = LaunchReadiness::report();
        $production = ProductionEnvironment::report();

        $minSecurity = (int) $this->option('min-security');
        $minLaunch = (int) $this->option('min-launch');
        $strict = (bool) $this->option('strict');
        $hasBlockers = $launch['blockers']->isNotEmpty();
        $hasProductionPending = ! $production['ready'];

        $ok = $health['ok']
            && $security['percent'] >= $minSecurity
            && $launch['percent'] >= $minLaunch
            && (! $strict || (! $hasBlockers && ! $hasProductionPending));

        $payload = [
            'ok' => $ok,
            'strict' => $strict,
            'min_security' => $minSecurity,
            'min_launch' => $minLaunch,
            'has_blockers' => $hasBlockers,
            'has_production_pending' => $hasProductionPending,
            'health_ok' => $health['ok'],
            'security_percent' => $security['percent'],
            'launch_percent' => $launch['percent'],
            'production_percent' => $production['percent'],
            'checked_at' => now()->toIso8601String(),
            'health' => $health['checks'],
            'security' => $security['checks']->values(),
            'launch_blockers' => $launch['blockers']->values(),
            'production_pending' => $production['required_pending']->values(),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $ok ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - Preflight');
        $this->line('Modo: '.($strict ? 'strict / lancamento oficial' : 'diagnostico / desenvolvimento'));
        $this->line('Health: '.($health['ok'] ? 'OK' : 'ATENCAO'));
        $this->line('Seguranca: '.$security['percent'].'% (minimo '.$minSecurity.'%)');
        $this->line('Lancamento: '.$launch['percent'].'% (minimo '.$minLaunch.'%)');
        $this->line('Ambiente producao: '.$production['percent'].'%');
        $this->newLine();

        $this->line('Checks de saude:');
        foreach ($health['checks'] as $name => $check) {
            $this->line(' - '.($check['ok'] ? '[ok] ' : '[!!] ').$name.': '.$check['label'].' ('.$check['detail'].')');
        }

        if ($launch['blockers']->isNotEmpty()) {
            $this->newLine();
            $this->warn('Bloqueadores de lancamento:');
            foreach ($launch['blockers'] as $blocker) {
                $this->line(' - '.$blocker['title'].' ('.$blocker['detail'].')');
            }
        }

        if ($production['required_pending']->isNotEmpty()) {
            $this->newLine();
            $this->warn('Pendencias obrigatorias de producao:');
            foreach ($production['required_pending'] as $pending) {
                $this->line(' - '.$pending['title'].' ('.$pending['detail'].')');
            }
        }

        $this->newLine();
        if ($ok) {
            $this->info($strict
                ? 'Preflight strict aprovado para lancamento.'
                : 'Preflight aprovado para avancar no desenvolvimento.');
        } else {
            $this->error($strict
                ? 'Preflight strict reprovado. Resolva os bloqueadores antes de lancar.'
                : 'Preflight com pendencias. Revise os itens acima.');
        }

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
