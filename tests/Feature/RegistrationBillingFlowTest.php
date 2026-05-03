<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationBillingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_registration_redirects_to_billing_plan_choice(): void
    {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->post('/register', [
            'name' => 'Usuario Teste',
            'email' => 'usuario-teste@example.com',
            'password' => 'password123',
            'workspace' => 'Workspace Teste',
        ])->assertRedirect('/dashboard/billing');
    }
}
