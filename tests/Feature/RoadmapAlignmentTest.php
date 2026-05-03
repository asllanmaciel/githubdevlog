<?php

namespace Tests\Feature;

use App\Models\RoadmapItem;
use App\Models\User;
use App\Support\RoadmapAlignment;
use App\Support\RoadmapCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapAlignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_alignment_classifies_local_and_external_pending_items(): void
    {
        RoadmapCatalog::sync();

        $report = RoadmapAlignment::report(RoadmapItem::orderBy('position')->get());

        $this->assertGreaterThan(0, $report['local_pending']->count());
        $this->assertGreaterThan(0, $report['external_pending']->count());
        $this->assertSame('Estrategia de APIs publicas para parceiros', $report['next_focus']->first()['record']->title);
    }

    public function test_super_admin_can_see_aligned_roadmap_evidence(): void
    {
        RoadmapCatalog::sync();
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-roadmap-alignment@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/roadmap')
            ->assertOk()
            ->assertSee('Próximo foco', false)
            ->assertSee('bloqueios externos', false)
            ->assertSee('Evidência:', false);
    }
}
