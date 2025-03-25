<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RouteServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sprawdzający, czy RouteServiceProvider poprawnie rejestruje trasy
     */
    public function test_provider_registers_routes_correctly(): void
    {
        // Sprawdzenie, czy główna strona jest dostępna
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // Sprawdzenie, czy panel jest dostępny (powinno przekierować na logowanie)
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        
        // Sprawdzenie, czy strona logowania jest dostępna
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test sprawdzający, czy przestrzeń nazw kontrolerów działa poprawnie
     */
    public function test_controller_namespace_works_correctly(): void
    {
        // Sprawdz, czy route() może poprawnie rozpoznać trasę
        $this->assertEquals('/dashboard', route('dashboard'));
        
        // Sprawdz, czy route() może poprawnie rozpoznać trasę z prefiksem
        $this->assertEquals('/admin', route('admin.dashboard'));
    }

    /**
     * Test sprawdzający, czy HOME jest poprawnie ustawione
     */
    public function test_home_is_set_correctly(): void
    {
        $this->assertEquals('/dashboard', RouteServiceProvider::HOME);
        
        $user = User::factory()->create();
        
        // Logowanie powinno przekierować na HOME
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * Test sprawdzający, czy Admin panel jest dostępny tylko dla administratorów
     */
    public function test_admin_routes_are_protected(): void
    {
        // Tworzenie roli administratora
        Role::create(['name' => 'admin']);
        
        // Tworzenie użytkowników
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        $regularUser = User::factory()->create();
        
        // Sprawdzenie, czy admin ma dostęp do panelu administracyjnego
        $response = $this->actingAs($adminUser)
                        ->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Sprawdzenie, czy zwykły użytkownik nie ma dostępu do panelu administracyjnego
        $response = $this->actingAs($regularUser)
                        ->get(route('admin.dashboard'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test sprawdzający, czy trasy z kontrolerami używającymi notacji string działają poprawnie
     */
    public function test_string_controller_routes_work_correctly(): void
    {
        // Tworzenie roli administratora
        Role::create(['name' => 'admin']);
        
        // Tworzenie użytkownika admin
        $adminUser = User::factory()->create();
        $adminUser->assignRole('admin');
        
        // Sprawdzenie, czy trasa testowa działa
        $response = $this->actingAs($adminUser)
                        ->get(route('admin.test'));
        $response->assertStatus(200);
        $response->assertSee('Test działa poprawnie!');
    }
}
