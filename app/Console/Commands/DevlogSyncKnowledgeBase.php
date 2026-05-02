<?php

namespace App\Console\Commands;

use App\Support\KnowledgeBaseCatalog;
use Illuminate\Console\Command;

class DevlogSyncKnowledgeBase extends Command
{
    protected $signature = 'devlog:sync-knowledge-base';

    protected $description = 'Sincroniza artigos publicos essenciais da base de conhecimento.';

    public function handle(): int
    {
        $synced = KnowledgeBaseCatalog::sync();

        $this->info('Base de conhecimento sincronizada.');
        $this->line('Artigos criados ou atualizados: '.$synced);
        $this->line('Total esperado: '.KnowledgeBaseCatalog::expectedTotal());

        return self::SUCCESS;
    }
}
