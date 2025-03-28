<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    /**
     * Test czy menu boczne zawiera wszystkie wymagane linki nawigacyjne.
     *
     * @return void
     */
    public function test_sidebar_contains_all_required_navigation_links()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/dashboard');
        
        // Weryfikacja głównych elementów nawigacyjnych
        $response->assertSee('Panel Główny', false);
        $response->assertSee('Finanse', false);
        $response->assertSee('Faktury', false);
        $response->assertSee('Magazyn', false);
        $response->assertSee('Kontrahenci', false);
        $response->assertSee('Zadania', false);
        $response->assertSee('Umowy', false);
        $response->assertSee('Kosztorysy', false);
    }

    /**
     * Test czy sidebar-livewire jest używany we wszystkich widokach.
     *
     * @return void
     */
    public function test_sidebar_livewire_is_used_in_views()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('<aside class="fixed left-0 top-0 h-full bg-[#44546A]/90', false);
        $response->assertDontSee('@include(\'layouts.sidebar\'', false);
    }
} 