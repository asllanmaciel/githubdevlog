<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_home_page_uses_portuguese_by_default(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('lang="pt-BR"', false)
            ->assertSee('Criar workspace');
    }

    public function test_home_page_can_render_in_english_from_query_string(): void
    {
        $this->get('/?lang=en')
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Create workspace')
            ->assertSee('Private webhook inbox for GitHub');
    }

    public function test_locale_switch_persists_in_session(): void
    {
        $this->from('/')
            ->get('/locale/en')
            ->assertRedirect('/');

        $this->get('/')
            ->assertOk()
            ->assertSee('Create workspace');
    }
}
