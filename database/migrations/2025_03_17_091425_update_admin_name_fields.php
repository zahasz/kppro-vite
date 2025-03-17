<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Aktualizacja danych administratora
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'first_name' => 'Jan',
                'last_name' => 'Wyszomirski',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Przywrócenie pustych wartości
        DB::table('users')
            ->where('email', 'admin@kppro.pl')
            ->update([
                'first_name' => null,
                'last_name' => null,
            ]);
    }
};
