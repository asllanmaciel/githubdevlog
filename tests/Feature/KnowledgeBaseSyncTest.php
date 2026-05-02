<?php

namespace Tests\Feature;

use App\Models\KnowledgeBaseArticle;
use App\Support\KnowledgeBaseCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_knowledge_base_publishes_required_articles(): void
    {
        $this->artisan('devlog:sync-knowledge-base')->assertSuccessful();

        $this->assertSame(
            KnowledgeBaseCatalog::expectedTotal(),
            KnowledgeBaseArticle::where('published', true)->count()
        );

        $this->assertDatabaseHas('knowledge_base_articles', [
            'slug' => 'checklist-github-app-oficial',
            'published' => true,
        ]);
    }
}
