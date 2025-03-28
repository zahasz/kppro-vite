<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RefreshPermissionsCache extends Command
{
    /**
     * Nazwa i sygnatura komendy.
     *
     * @var string
     */
    protected $signature = 'permissions:refresh-cache';

    /**
     * Opis komendy.
     *
     * @var string
     */
    protected $description = 'Odświeża cache uprawnień oraz upewnia się, że wszyscy administratorzy mają przypisane odpowiednie uprawnienia';

    /**
     * Wykonanie komendy.
     */
    public function handle()
    {
        // Czyszczenie pamięci podręcznej uprawnień
        $this->info('Czyszczenie pamięci podręcznej uprawnień...');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Resetowanie cache dla wszystkich użytkowników
        $this->info('Resetowanie pamięci podręcznej użytkowników...');
        User::all()->each(function ($user) {
            Cache::forget('spatie.permission.cache.user.'.$user->id);
        });
        
        // Upewniamy się, że administratorzy mają wszystkie uprawnienia
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'super-admin')->first();
        
        if ($adminRole && $superAdminRole) {
            $this->info('Upewnianie się, że administratorzy mają odpowiednie uprawnienia...');
            
            // Pobieranie wszystkich uprawnień
            $permissions = Permission::all();
            
            // Przypisywanie wszystkich uprawnień do roli superadmina
            $superAdminRole->syncPermissions($permissions);
            
            // Znajdź użytkowników z rolą administratora i odśwież ich uprawnienia
            $adminUsers = User::role(['admin', 'super-admin'])->get();
            $adminCount = $adminUsers->count();
            
            $this->info("Znaleziono {$adminCount} administratorów w systemie.");
            
            // Ręczne resetowanie cache dla administratorów
            foreach ($adminUsers as $adminUser) {
                $this->info("Resetowanie cache dla administratora: {$adminUser->email}");
                
                // Wymuszenie ponownego załadowania kolekcji uprawnień
                $adminUser->getPermissionsViaRoles();
                
                // Czyszczenie cache dla konkretnego użytkownika
                Cache::forget('spatie.permission.cache.user.'.$adminUser->id);
            }
        } else {
            $this->error('Nie znaleziono roli administratora lub superadministratora!');
            return 1;
        }
        
        $this->info('Pamięć podręczna uprawnień została odświeżona pomyślnie!');
        return 0;
    }
}
