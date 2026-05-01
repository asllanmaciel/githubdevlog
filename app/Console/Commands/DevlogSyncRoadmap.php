<?php

namespace App\Console\Commands;

use App\Support\RoadmapCatalog;
use Illuminate\Console\Command;

class DevlogSyncRoadmap extends Command
{
    protected $signature = 'devlog:sync-roadmap';

    protected $description = 'Sincroniza o catalogo canonico do roadmap no banco atual.';

    public function handle(): int
    {
        $synced = RoadmapCatalog::sync();

        $this->info('Roadmap sincronizado.');
        $this->line('Itens criados ou atualizados: '.$synced);
        $this->line('Total esperado: '.RoadmapCatalog::expectedTotal());

        return self::SUCCESS;
    }
}
