<?php

namespace App\Console\Commands;

use App\Support\GitHubAppSetup;
use Illuminate\Console\Command;

class DevlogGithubAppCheck extends Command
{
    protected $signature = 'devlog:github-app-check {--json : Emitir diagnostico em JSON}';

    protected $description = 'Mostra o checklist operacional para configurar e validar o GitHub App oficial.';

    public function handle(): int
    {
        $setup = GitHubAppSetup::report();
        $pendingEnv = collect($setup['env'])->where('done', false)->values();
        $pendingSteps = collect($setup['steps'])->where('done', false)->values();

        $payload = [
            'ok' => $pendingEnv->isEmpty() && $pendingSteps->isEmpty(),
            'pending_env' => $pendingEnv,
            'pending_steps' => $pendingSteps,
            'urls' => $setup['urls'],
            'permissions' => $setup['permissions'],
            'events' => $setup['events'],
        ];

        if ($this->option('json')) {
            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return $payload['ok'] ? self::SUCCESS : self::FAILURE;
        }

        $this->info('GitHub DevLog AI - GitHub App Check');
        $this->newLine();

        $this->line('URLs para configurar no GitHub App:');
        foreach ($setup['urls'] as $url) {
            $this->line(' - '.$url['label'].': '.$url['value']);
        }

        $this->newLine();
        $this->line('Variaveis .env:');
        foreach ($setup['env'] as $env) {
            $this->line(' - '.($env['done'] ? '[ok] ' : '[!!] ').$env['key'].': '.$env['value']);
        }

        $this->newLine();
        $this->line('Permissoes recomendadas:');
        foreach ($setup['permissions'] as $permission) {
            $this->line(' - '.$permission);
        }

        $this->newLine();
        $this->line('Eventos recomendados: '.implode(', ', $setup['events']));

        if ($pendingSteps->isNotEmpty()) {
            $this->newLine();
            $this->warn('Pendencias para destravar o lancamento:');
            foreach ($pendingSteps as $step) {
                $this->line(' - '.$step['title'].' ('.$step['detail'].')');
            }
        }

        $this->newLine();
        if ($payload['ok']) {
            $this->info('GitHub App pronto para validacao ponta a ponta.');
        } else {
            $this->error('GitHub App ainda possui pendencias obrigatorias.');
        }

        return $payload['ok'] ? self::SUCCESS : self::FAILURE;
    }
}