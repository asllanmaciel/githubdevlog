<?php

namespace App\Console\Commands;

use App\Models\BillingPlan;
use Illuminate\Console\Command;

class DevlogSyncPlans extends Command
{
    protected $signature = 'devlog:sync-plans';

    protected $description = 'Sincroniza planos comerciais padrao com limites de webhooks e AI avancada.';

    public function handle(): int
    {
        $plans = [
            [
                'slug' => 'free',
                'name' => 'Free',
                'price_cents' => 0,
                'event_retention_days' => 7,
                'monthly_event_limit' => 1000,
                'monthly_ai_analysis_limit' => 0,
                'ai_analysis_overage_price_cents' => 0,
                'overage_price_cents' => 0,
                'features' => ['Webhook inbox privado', 'AI gratis local', 'Historico essencial'],
            ],
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price_cents' => 2900,
                'event_retention_days' => 30,
                'monthly_event_limit' => 10000,
                'monthly_ai_analysis_limit' => 25,
                'ai_analysis_overage_price_cents' => 15,
                'overage_price_cents' => 3,
                'features' => ['10.000 eventos/mes', '25 analises AI avancadas/mes', 'Retencao de 30 dias'],
            ],
            [
                'slug' => 'growth-demo',
                'name' => 'Growth Demo',
                'price_cents' => 4900,
                'event_retention_days' => 90,
                'monthly_event_limit' => 25000,
                'monthly_ai_analysis_limit' => 100,
                'ai_analysis_overage_price_cents' => 12,
                'overage_price_cents' => 2,
                'features' => ['25.000 eventos/mes', '100 analises AI avancadas/mes', 'Plano demo para launch'],
            ],
            [
                'slug' => 'growth',
                'name' => 'Growth',
                'price_cents' => 7900,
                'event_retention_days' => 90,
                'monthly_event_limit' => 50000,
                'monthly_ai_analysis_limit' => 250,
                'ai_analysis_overage_price_cents' => 10,
                'overage_price_cents' => 2,
                'features' => ['50.000 eventos/mes', '250 analises AI avancadas/mes', 'Retencao de 90 dias'],
            ],
            [
                'slug' => 'scale',
                'name' => 'Scale',
                'price_cents' => 19900,
                'event_retention_days' => 180,
                'monthly_event_limit' => 200000,
                'monthly_ai_analysis_limit' => 1000,
                'ai_analysis_overage_price_cents' => 8,
                'overage_price_cents' => 1,
                'features' => ['200.000 eventos/mes', '1.000 analises AI avancadas/mes', 'Retencao de 180 dias'],
            ],
        ];

        foreach ($plans as $plan) {
            BillingPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan + ['currency' => 'BRL', 'active' => true],
            );
        }

        $this->info(count($plans).' plano(s) sincronizado(s) com limites de AI avancada.');

        return self::SUCCESS;
    }
}
