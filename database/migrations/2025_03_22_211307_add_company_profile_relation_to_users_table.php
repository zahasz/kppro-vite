<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\CompanyProfile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tworzymy profil firmy dla każdego użytkownika, który go nie ma
        $users = User::whereDoesntHave('companyProfile')->get();
        foreach ($users as $user) {
            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => 'Firma ' . $user->name,
                'tax_number' => '0000000000',
                'street' => 'Ulica',
                'city' => 'Miasto',
                'postal_code' => '00-000',
                'country' => 'Polska',
                'phone' => '000000000',
                'email' => $user->email,
                'bank_name' => 'Bank',
                'bank_account' => '00000000000000000000000000',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ta migracja tylko tworzy dane, więc nie ma potrzeby ich cofać
    }
};
