<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSystemStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_system_status(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/system-status')
            ->assertOk()
            ->assertSee('Status operacional', false);
    }
}
