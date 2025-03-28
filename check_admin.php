<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@kppro.pl')->first();

if ($user) {
    echo "Użytkownik admin znaleziony:\n";
    echo "ID: " . $user->id . "\n";
    echo "Nazwa: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Aktywny: " . ($user->is_active ? 'Tak' : 'Nie') . "\n";
    echo "Role: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
} else {
    echo "Użytkownik admin nie znaleziony w bazie danych.\n";
} 