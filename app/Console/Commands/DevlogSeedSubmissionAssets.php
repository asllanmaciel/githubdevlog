<?php

namespace App\Console\Commands;

use App\Support\SubmissionAssets;
use Illuminate\Console\Command;

class DevlogSeedSubmissionAssets extends Command
{
    protected $signature = 'devlog:seed-submission-assets';

    protected $description = 'Cria checklist de screenshots e evidencias para submissao ao GitHub Developer Program.';

    public function handle(): int
    {
        SubmissionAssets::ensureSeeded();

        $this->info('Checklist de assets de submissao criado/atualizado.');
        $this->line('Abra /admin/submission-assets para acompanhar o pacote visual.');

        return self::SUCCESS;
    }
}
