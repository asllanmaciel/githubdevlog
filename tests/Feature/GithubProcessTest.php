<?php

namespace Tests\Feature;

use Tests\TestCase;

class GithubProcessTest extends TestCase
{
    public function test_github_process_files_exist(): void
    {
        $this->assertFileExists(base_path('.github/workflows/ci.yml'));
        $this->assertFileExists(base_path('.github/pull_request_template.md'));
        $this->assertFileExists(base_path('.github/ISSUE_TEMPLATE/bug_report.yml'));
        $this->assertFileExists(base_path('.github/ISSUE_TEMPLATE/feature_request.yml'));
        $this->assertFileExists(base_path('docs/github-process.md'));
        $this->assertFileExists(base_path('docs/release-checklist.md'));
    }

    public function test_ci_workflow_contains_required_quality_gate_commands(): void
    {
        $workflow = file_get_contents(base_path('.github/workflows/ci.yml'));

        $this->assertStringContainsString('php artisan test', $workflow);
        $this->assertStringContainsString('php artisan route:cache', $workflow);
        $this->assertStringContainsString('php artisan view:cache', $workflow);
        $this->assertStringContainsString('npm run build', $workflow);
        $this->assertStringContainsString('pull_request:', $workflow);
        $this->assertStringContainsString('branches:', $workflow);
        $this->assertStringContainsString('master', $workflow);
    }
}
