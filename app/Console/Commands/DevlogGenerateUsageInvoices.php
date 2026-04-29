<?php

namespace App\Console\Commands;

use App\Support\OverageBilling;
use Illuminate\Console\Command;

class DevlogGenerateUsageInvoices extends Command
{
    protected $signature = 'devlog:generate-usage-invoices
        {--period= : Periodo no formato YYYY-MM. Padrao: mes atual}
        {--json : Emitir resumo em JSON}';

    protected $description = 'Gera faturas internas de uso excedente a partir dos snapshots mensais.';

    public function handle(): int
    {
        $summary = OverageBilling::generateInvoices($this->option('period') ?: now()->format('Y-m'));

        if ($this->option('json')) {
            $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('Faturas internas de uso geradas.');
        $this->line('Periodo: '.$summary['period']);
        $this->line('Faturas: '.$summary['invoices']);
        $this->line('Valor estimado: R$ '.number_format($summary['amount_cents'] / 100, 2, ',', '.'));

        return self::SUCCESS;
    }
}