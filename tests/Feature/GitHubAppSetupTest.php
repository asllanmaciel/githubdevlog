<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\GitHubAppSetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubAppSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_github_app_setup_exposes_env_snippet_and_profile(): void
    {
        config([
            'app.url' => 'https://ghdevlog.com',
            'services.github_app.name' => 'GitHub DevLog AI',
            'services.github_app.webhook_url' => 'https://ghdevlog.com/webhooks/github-app',
            'services.github_app.callback_url' => 'https://ghdevlog.com/github/callback',
            'services.github_app.setup_url' => 'https://github.com/apps/github-devlog-ai/installations/new',
        ]);

        $report = GitHubAppSetup::report();

        $this->assertSame('GitHub DevLog AI', $report['app_profile']['name']);
        $this->assertStringContainsString('GITHUB_APP_WEBHOOK_SECRET=', $report['env_snippet']);
        $this->assertStringContainsString('GITHUB_APP_WEBHOOK_URL="https://ghdevlog.com/webhooks/github-app"', $report['env_snippet']);
        $this->assertArrayHasKey('percent', $report);
    }

    public function test_github_app_check_command_outputs_json_payload(): void
    {
        $this->artisan('devlog:github-app-check --json')
            ->assertExitCode(1)
            ->expectsOutputToContain('env_snippet');
    }

    public function test_super_admin_can_open_github_readiness_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-github@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/github-readiness')
            ->assertOk()
            ->assertSee('Prontidao para lancamento oficial', false);
    }
}
