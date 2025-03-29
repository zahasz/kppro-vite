<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoginHistory;
use App\Models\User;

class LoginHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdź, czy istnieją użytkownicy
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('Brak użytkowników w bazie danych. Tworzenie testowego użytkownika...');
            // Utwórz testowego użytkownika, jeśli nie istnieje
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);
            $users = collect([$user]);
        }
        
        // Utwórz wpisy historii logowania dla każdego użytkownika
        foreach ($users as $user) {
            // Udane logowanie
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'success',
                'details' => 'Testowe udane logowanie',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
            
            // Nieudane logowanie
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                'status' => 'failed',
                'details' => 'Testowe nieudane logowanie - niepoprawne hasło',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
            
            // Udane logowanie z innej przeglądarki
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => '192.168.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Safari/605.1.15',
                'status' => 'success',
                'details' => 'Testowe udane logowanie z Safari',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);
        }
        
        $this->command->info('Utworzono ' . (count($users) * 3) . ' wpisów historii logowania.');
    }
}
