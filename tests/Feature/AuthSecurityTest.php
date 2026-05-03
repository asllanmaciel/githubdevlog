<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Workspace;
use App\Support\AuthSecurity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_logins_are_rate_limited_and_audited(): void
    {
        User::create([
            'name' => 'Cliente',
            'email' => 'cliente-auth@example.com',
            'password' => 'password-correto',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => 'cliente-auth@example.com',
                'password' => 'senha-errada',
            ])->assertRedirect('/login');
        }

        $this->from('/login')->post('/login', [
            'email' => 'cliente-auth@example.com',
            'password' => 'senha-errada',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertSame(5, AuditLog::where('action', 'auth.login_failed')->count());
        $this->assertSame(1, AuditLog::where('action', 'auth.login_rate_limited')->count());
    }

    public function test_successful_login_clears_limiter_and_records_audit(): void
    {
        $user = User::create([
            'name' => 'Cliente',
            'email' => 'cliente-login@example.com',
            'password' => 'password-correto',
        ]);
        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Workspace Auth',
            'slug' => 'workspace-auth',
            'webhook_secret' => 'secret',
        ]);
        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);

        $this->post('/login', [
            'email' => 'cliente-login@example.com',
            'password' => 'password-correto',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, AuditLog::where('action', 'auth.login_success')->where('workspace_id', $workspace->id)->count());
    }

    public function test_register_rate_limit_records_audit(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->from('/register')->post('/register', [
                'name' => 'Cliente '.$attempt,
                'email' => 'cliente-register-'.$attempt.'@example.com',
                'password' => 'password-correto',
                'workspace' => 'Workspace '.$attempt,
            ])->assertRedirect('/dashboard/billing');

            auth()->logout();
        }

        $this->from('/register')->post('/register', [
            'name' => 'Cliente Limite',
            'email' => 'cliente-register-limite@example.com',
            'password' => 'password-correto',
            'workspace' => 'Workspace Limite',
        ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertSame(3, AuditLog::where('action', 'auth.registered')->count());
        $this->assertSame(1, AuditLog::where('action', 'auth.register_rate_limited')->count());
    }

    public function test_super_admin_can_open_auth_security_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-auth-security@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/auth-security')
            ->assertOk()
            ->assertSee('Login com freio e trilha de auditoria', false);
    }

    public function test_auth_security_report_flags_suspicious_ips(): void
    {
        foreach (range(1, 3) as $index) {
            AuditLog::create([
                'actor_type' => 'system',
                'action' => 'auth.login_failed',
                'ip_address' => '10.0.0.10',
                'metadata' => ['email' => 'risk@example.com', 'attempt' => $index],
                'created_at' => now(),
            ]);
        }

        $report = AuthSecurity::report();

        $this->assertSame(3, $report['failed_24h']);
        $this->assertSame('10.0.0.10', $report['suspicious_ips']->first()->ip_address);
    }
}
