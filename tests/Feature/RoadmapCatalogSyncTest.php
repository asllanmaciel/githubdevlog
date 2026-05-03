<?php

namespace Tests\Feature;

use App\Models\RoadmapItem;
use App\Support\RoadmapCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapCatalogSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_promotes_canonical_done_items(): void
    {
        RoadmapItem::create([
            'title' => 'Metricas de produto com decisoes acionaveis',
            'area' => 'Observabilidade e operacao',
            'status' => 'pending',
            'priority' => 'alta',
            'description' => 'antiga',
            'position' => 50,
        ]);

        RoadmapCatalog::sync();

        $this->assertSame('done', RoadmapItem::where('title', 'Metricas de produto com decisoes acionaveis')->value('status'));
        $this->assertSame('done', RoadmapItem::where('title', 'Fluxo de autenticacao robusto e protecao antifraude')->value('status'));
        $this->assertSame('done', RoadmapItem::where('title', 'Modelo de assinatura com valor percebido por uso')->value('status'));
        $this->assertSame('done', RoadmapItem::where('title', 'Hardening de webhooks e tolerancia a falhas')->value('status'));
    }
}
