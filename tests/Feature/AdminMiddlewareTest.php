<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Przygotowanie testów
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzenie roli administratora
        Role::create(['name' => 'admin']);
    }

    /**
     * Test sprawdzający, czy użytkownik z rolą admin ma dostęp do panelu administracyjnego
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        // Tworzenie użytkownika z rolą admin
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $response = $this->actingAs($adminUser)
                        ->get('/admin');
        
        $response->assertStatus(200);
    }

    /**
     * Test sprawdzający, czy zwykły użytkownik nie ma dostępu do panelu administracyjnego
     */
    public function test_regular_user_cannot_access_admin_panel(): void
    {
        // Tworzenie zwykłego użytkownika
        $regularUser = User::factory()->create();
        
        $response = $this->actingAs($regularUser)
                        ->get('/admin');
        
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Brak uprawnień do dostępu do panelu administracyjnego.');
    }

    /**
     * Test sprawdzający, czy niezalogowani użytkownicy są przekierowani na stronę logowania
     */
    public function test_guest_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test sprawdzający, czy API zwraca błąd 403 dla nieuprawnionych użytkowników
     */
    public function test_api_returns_error_for_unauthorized_users(): void
    {
        // Tworzenie zwykłego użytkownika
        $regularUser = User::factory()->create();
        
        $response = $this->actingAs($regularUser)
                        ->getJson('/admin');
        
        $response->assertStatus(403);
        $response->assertJson(['error' => 'Brak uprawnień administracyjnych.']);
    }
}
