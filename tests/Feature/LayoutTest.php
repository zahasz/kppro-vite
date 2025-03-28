<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    /**
     * Test czy menu boczne jest poprawnie ładowane.
     *
     * @return void
     */
    public function test_sidebar_is_rendered()
    {
        $user = User::factory()->make([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Główny');
    }

    /**
     * Test czy nowe komponenty Alpine.js są poprawnie ładowane.
     *
     * @return void
     */
    public function test_alpine_components_are_loaded()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('x-data', false);
        $response->assertSee(':class', false);
    }

    /**
     * Test czy przycisk zwijania menu działa poprawnie.
     *
     * @return void
     */
    public function test_sidebar_toggle_button_is_rendered()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('button', false);
        $response->assertSee('@click', false);
    }
} 