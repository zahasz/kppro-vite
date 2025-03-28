<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManager extends Command
{
    protected $signature = 'users:manage
                            {action? : Akcja do wykonania (list, show, create, reset-password, check-login)}
                            {identifier? : Email użytkownika dla akcji show/reset-password/check-login}
                            {--password= : Hasło dla akcji reset-password lub create}';

    protected $description = 'Zarządzanie użytkownikami (lista, sprawdzanie, tworzenie, resetowanie hasła)';

    public function handle()
    {
        $action = $this->argument('action') ?? 'list';

        switch ($action) {
            case 'list':
                $this->listUsers();
                break;
            case 'show':
                $this->showUser();
                break;
            case 'create':
                $this->createUser();
                break;
            case 'reset-password':
                $this->resetPassword();
                break;
            case 'check-login':
                $this->checkLogin();
                break;
            default:
                $this->error("Nieznana akcja: {$action}");
                return 1;
        }

        return 0;
    }

    protected function listUsers()
    {
        $users = User::all(['id', 'name', 'email', 'is_active']);
        $this->table(['ID', 'Nazwa', 'Email', 'Aktywny'], $users->toArray());
    }

    protected function showUser()
    {
        $email = $this->argument('identifier');
        if (!$email) {
            $email = $this->ask('Podaj email użytkownika');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Użytkownik o emailu {$email} nie istnieje");
            return;
        }

        $this->info("Informacje o użytkowniku:");
        $this->line("ID: " . $user->id);
        $this->line("Nazwa: " . $user->name);
        $this->line("Email: " . $user->email);
        $this->line("Aktywny: " . ($user->is_active ? 'Tak' : 'Nie'));
        $this->line("Ostatnie logowanie: " . ($user->last_login_at ?? 'Nigdy'));
        $this->line("Blokada konta: " . (($user->locked_until && $user->locked_until > now()) ? 'Tak, do ' . $user->locked_until : 'Nie'));
        $this->line("Nieudane próby logowania: " . $user->failed_login_attempts);
        $this->line("Role: " . implode(', ', $user->getRoleNames()->toArray()));
    }

    protected function createUser()
    {
        $email = $this->argument('identifier');
        if (!$email) {
            $email = $this->ask('Podaj email dla nowego użytkownika');
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Użytkownik o emailu {$email} już istnieje");
            return;
        }

        $name = $this->ask('Podaj nazwę użytkownika');
        $password = $this->option('password') ?? $this->secret('Podaj hasło');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $role = $this->choice('Wybierz rolę', ['admin', 'manager', 'user'], 2);
        $user->assignRole($role);

        $this->info("Użytkownik został utworzony pomyślnie!");
    }

    protected function resetPassword()
    {
        $email = $this->argument('identifier');
        if (!$email) {
            $email = $this->ask('Podaj email użytkownika');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Użytkownik o emailu {$email} nie istnieje");
            return;
        }

        $password = $this->option('password') ?? $this->secret('Podaj nowe hasło');
        
        $user->password = Hash::make($password);
        $user->save();

        $this->info("Hasło zostało zmienione pomyślnie!");
    }

    protected function checkLogin()
    {
        $email = $this->argument('identifier');
        if (!$email) {
            $email = $this->ask('Podaj email użytkownika');
        }

        $password = $this->option('password') ?? $this->secret('Podaj hasło do sprawdzenia');

        $credentials = ['email' => $email, 'password' => $password];
        $guard = \Illuminate\Support\Facades\Auth::guard('web');

        if ($guard->attempt($credentials)) {
            $this->info("Logowanie powiodło się!");
        } else {
            $this->error("Logowanie NIE powiodło się.");
            
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $this->line("Użytkownik istnieje w bazie danych.");
                $this->line("Aktywny: " . ($user->is_active ? 'Tak' : 'Nie'));
                $this->line("Blokada konta: " . (($user->locked_until && $user->locked_until > now()) ? 'Tak, do ' . $user->locked_until : 'Nie'));
                $this->line("Ilość nieudanych prób logowania: " . $user->failed_login_attempts);
            } else {
                $this->line("Użytkownik o emailu {$email} nie istnieje w bazie danych.");
            }
        }
    }
} 