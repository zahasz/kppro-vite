<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'admin@kppro.pl';
$password = 'admin123'; // Domyślne hasło zgodnie z seederem

// Próba uwierzytelnienia
$credentials = ['email' => $email, 'password' => $password];
$guard = \Illuminate\Support\Facades\Auth::guard('web');

if ($guard->attempt($credentials)) {
    echo "Logowanie na konto administratora powiodło się!\n";
} else {
    echo "Logowanie na konto administratora NIE powiodło się.\n";
    
    // Pobierz użytkownika by sprawdzić dodatkowe informacje
    $user = \App\Models\User::where('email', $email)->first();
    
    if ($user) {
        echo "Użytkownik istnieje w bazie danych.\n";
        echo "Aktywny: " . ($user->is_active ? 'Tak' : 'Nie') . "\n";
        echo "Blokada konta: " . (($user->locked_until && $user->locked_until > now()) ? 'Tak, do ' . $user->locked_until : 'Nie') . "\n";
        echo "Ilość nieudanych prób logowania: " . $user->failed_login_attempts . "\n";
    } else {
        echo "Użytkownik o emailu {$email} nie istnieje w bazie danych.\n";
    }
} 