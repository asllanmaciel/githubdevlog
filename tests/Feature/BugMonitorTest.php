<?php

namespace Tests\Feature;

use App\Models\BugReport;
use App\Models\User;
use App\Support\BugMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class BugMonitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_bug_monitor_captures_and_groups_exceptions(): void
    {
        $exception = new RuntimeException('Falha sintetica de teste');

        BugMonitor::capture($exception);
        BugMonitor::capture($exception);

        $this->assertDatabaseCount('bug_reports', 1);
        $this->assertDatabaseHas('bug_reports', [
            'exception_class' => RuntimeException::class,
            'message' => 'Falha sintetica de teste',
            'occurrences' => 2,
        ]);
    }

    public function test_bug_monitor_ignores_non_server_http_exceptions(): void
    {
        BugMonitor::capture(new NotFoundHttpException('Nao encontrado'));

        $this->assertDatabaseCount('bug_reports', 0);
    }

    public function test_super_admin_can_open_bug_monitor(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-bugs@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        BugReport::create([
            'fingerprint' => hash('sha256', 'bug-demo'),
            'level' => 'error',
            'exception_class' => RuntimeException::class,
            'message' => 'Bug demo',
            'occurrences' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/bug-monitor')
            ->assertOk()
            ->assertSee('Monitor interno de bugs', false)
            ->assertSee('Bug demo');
    }
}
